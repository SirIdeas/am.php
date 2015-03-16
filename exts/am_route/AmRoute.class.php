<?php

/**
 * Clase para la evaluación de las rutas
 */

class AmRoute{

  // Diferentes tipos de datos aceptados
  protected static $TYPES = array(
    "id"            => "[a-zA-Z_][a-zA-Z0-9_-]*", // Identificador
    "any"           => ".*",                      // Cualquier valor
    "numeric"       => "[0-9]*",                  // Numeros
    "alphabetic"    => "[a-zA-Z]*",               // Alfabetico
    "alphanumeric"  => "[a-zA-Z0-9]*",            // Alfanumerico
  );

  protected static $callbacks = array(
    "template"  => "render.template",
    "file"      => "response.file",
    "download"  => "response.download",
    "assets"    => "response.assets",
  );

  // Ruta
  protected $route; // Ruta
  protected $regex; // Regex de evaluacion correspondiente a la ruta
  protected $params = array();  // Parametros detectados

  // Cosntructor
  public function __construct($route){
    $this->route = $route;

    // cmpila la ruta
    list($this->regex, $this->params) = self::compileRoute($route);

  }

  // Indica si una peticion conincide con la ruta actual
  // Devuelve falso si no conincide o un array con los
  // parémtros pasados por la ruta.
  public function match($request){
    $params = array();

    // Si hace match
    if(preg_match($this->regex, $request, $a)){

      // Recorrer los paramétros
      foreach($this->params as $i => $paramName)
        $params[$paramName] = $a[$i + 1];

      return $params;

    }
    return false;
  }

  // Renderiza un template
  private static function template($file, $env){

    // Renderizar vista mediante un callback
    return Am::call("render.template",
      $file,
      array(
        "env" => $env
      )
    );

  }

  // Obtiene la regex correspondientes con una ruta.
  private static function compileRoute($route){

    $params = array();

    // Compila la ruta
    $regex = self::__compileRoute($route, $params, self::$TYPES);

    // Transformar / en  \/
    $regex = str_replace("/", "\\/", $regex);

    // Si no termina en barra entonces agregarla
    if(!preg_match("/\/$/", $regex)) $regex = "{$regex}[\/]{0,1}";

    // Colocar inicio y final para formar regex
    $regex = "/^{$regex}$/";

    return array($regex, $params);

  }

  // Devuelve una regex correspondiente para una ruta dividiendo los parametros
  // y obteniendo el tipo para cada uno.
  private static function __compileRoute($route, array &$params, $types){
    // Obtener el ultimo parámetro
    if(preg_match("/^(.*):({$types["id"]})(.*)$/", $route, $a)){
      array_unshift($params, $a[2]);
      // Determina si el parámetro tiene un tipo asignado (numero, alfanumerico,
      // entre otros)
      if(preg_match("/^\((.*)\)(.*)$/", $a[3], $b)){
        if(isset($types[$b[1]])){
          $type = $types[$b[1]];
        }else{
          $type = $b[1];
        }
        $a[3] = $b[2];

      // Si no tiene un tipo definido entonces admitir cualquier tipo
      }else{
        $type = $types["any"];
      }
      // Realizar llamado para el reto de la ruta.
      return self::__compileRoute($a[1], $params, $types)."({$type}){$a[3]}";

    }

    return $route;

  }

  // Callback para evaluar las rutas
  public static final function evaluate($request, array $env = array()){

    // Si retorna verdadero
    $prev = array("env" => $env);

    if(true === ($lastError = self::evalMatch($request, Am::getAttribute("routes", array()), $prev)))
      return true;

    // No se encontró una ruta valida
    Am::e404($lastError);
    return false;

  }

  // Metodo busca la ruta con la que conincide la peticion actual.
  private static final function evalMatch($request, $routes, array $prev = array()){

    // Si se indicó la ruta como un recurso
    if(isset($routes["resource"])){
      $routes["routes"] = array_merge(
        itemOr("routes", $routes, array()),
        array(
          "/"           => "control => {$routes["resource"]}@index",
          "/new"        => "control => {$routes["resource"]}@new",
          "/:id/detail" => "control => {$routes["resource"]}@detail",
          "/:id/edit"   => "control => {$routes["resource"]}@edit",
          "/:id/remove" => "control => {$routes["resource"]}@remove",
        )
      );
      unset($routes["resource"]);
    }

    $lastError = false;

    $methods = array("template", "file", "download", "assets", "redirect", "goto", "control", "app", "resource");

    if(isset($routes["routes"])){
      foreach($routes["routes"] as $key => $route){

        // Si la ruta es una cadena de caracteres
        // Se parte la cadena con el caracter #
        // el primer paremtro es un key y el segundo
        // el valor
        if(is_string($route)){
          list($prop, $value) = explode(" => ", $route);
          $route = array($prop => $value);
        }

        // Asignar key como ruta si no tiene ruta asignada
        $route["route"] = itemOr("route", $route, $key);

        foreach($routes as $key => $value)
          if(in_array($key, $methods))
            $route[$key] = $value . itemOr($key, $route, "");

          if(true === ($newError = self::evalMatch($request, $route, $routes)))
            return true;
          else if($newError !== false)
            $lastError = $newError;

      }
    }

    // Si no esta indicada la ruta
    // se toma el indice de la ruta como indice
    $routes["route"] = itemOr("route", $prev, "") .
                       itemOr("route", $routes, "");

    $routes["env"] = array_merge(
        itemOr("env", $prev, array()),
        itemOr("env", $routes, array())
    );

    // Crear instancia de la ruta.
    $r = new self($routes["route"]);
    
    // Si hace match con la peticion
    if(false !== ($params = $r->match($request))){

      foreach ($methods as $method) {
        // Renderizar una vista
        if(isset($routes[$method])){

          // Concatenar el valor anterior
          $destiny = $routes[$method];

          // Reemplazar cada parámetro en el destino de la peticion
          // Los parámetros que no esten el destino de la peticion serán
          // los parámetros para la llamada de de respuesta
          foreach($params as $key => $val){
            $newDestiny = str_replace(":{$key}", $val, $destiny);
            if($newDestiny !== $destiny) unset($params[$key]);
            $destiny = $newDestiny;
          }

          if($method === "template")
            $env = array("env" => $routes["env"], "ignore" => true);
          else
            $env = $routes["env"];

          // Callbacks predefinidos
          if(isset(self::$callbacks[$method])){

            if(Am::call(self::$callbacks[$method], $destiny, $env))
              return true;

            $lastError = "not fount {$method} : $destiny";

          // Redireccion de URL
          }else if($method === "redirect" || $method === "goto"){

            $method = itemOr($method, array(
              "redirect" => "redirect",
              "goto" => "gotoUrl",
            ));
            Am::$method($destiny);
            return true;

          // controller
        }else if($method === "control"){

            // Responder como una función
            if(function_exists($destiny)){

              // Llamar funcion
              call_user_func_array($destiny, $params);
              return true;

              // Respuesta como template, file o assets

            }elseif(preg_match("/^(.*)::(.*)$/", $destiny, $a)){
              array_shift($a);
              if(call_user_func_array("method_exists", $a)){
                call_user_func_array($a, $params);
                return true;
              }

              // El callback no existe
              $lastError = "not fount callback {$a[0]}::$a[1]";

            }elseif(preg_match("/^(.*)@(.*)$/", $destiny, $a)){
              array_shift($a);

              // Despachar con controlador
              if(Am::call("response.control",
                $a[0],
                $a[1],
                $params,
                $env
              ) === true)
                return true;

              // La accion en el controlador no existe
              $lastError = "not fount action {$a[0]}@{$a[1]}";

            }

          }

        }
      }

    }

    // Por cada ruta
    // foreach($routes["routes"] as $from => $to){
    //
    //   // Agregar la cadena padre si la cadena hija comienza con "."
    //   if(substr($from, 0, 1) === ".")
    //     $from = $prevMatch . substr($from, 1, strlen($from)-1);
    //
    //   // Si es un grupo de rutas
    //   if(is_array($to)){
    //     if(true === ($lastError = self::evalMatch(
    //       $request,
    //       isset($to["routes"])? $to : array("routes" => $to),
    //       $env,
    //       $from)))
    //         return true;
    //     continue;
    //   }
    //
    //   // Crear instancia de la ruta.
    //   $r = new self($from);
    //
    //   // Si hace match con la peticion
    //   if(false !== ($params = $r->match($request))){
    //
    //     // Calcular destino
    //     $destiny = $to;
    //
    //     // Reemplazar cada parámetro en el destino de la peticion
    //     // Los parámetros que no esten el destino de la peticion serán
    //     // los parámetros para la llamada de de respuesta
    //     foreach($params as $key => $val){
    //       $newDestiny = str_replace(":{$key}", $val, $destiny);
    //       if($newDestiny !== $destiny) unset($params[$key]);
    //       $destiny = $newDestiny;
    //     }
    //
    //     // Responder como una función
    //     if(function_exists($destiny)){
    //
    //       // Llamar funcion
    //       call_user_func_array($destiny, $params);
    //       return true;
    //
    //     // Respuesta como template, file o assets
    //     }elseif(preg_match("/^(.*)::(.*)$/", $destiny, $a)){
    //       array_shift($a);
    //       if(call_user_func_array("method_exists", $a)){
    //         call_user_func_array($a, $params);
    //         return true;
    //       }
    //
    //       // El callback no existe
    //       $lastError = "not fount callback {$a[0]}::$a[1]";
    //
    //     }elseif(preg_match("/^(.*)@(.*)$/", $destiny, $a)){
    //       array_shift($a);
    //
    //       // Despachar con controlador
    //       if(Am::call("response.control",
    //         $a[0],
    //         $a[1],
    //         $params,
    //         $env
    //       ) === true)
    //         return true;
    //
    //       // La accion en el controlador no existe
    //       $lastError = "not fount action {$a[0]}@{$a[1]}";
    //
    //     }elseif(preg_match("/^(redirect|goto)#(.*)$/", $destiny, $a)){
    //
    //       $a[1] = itemOr($a[1], array(
    //         "redirect" => "redirect",
    //         "goto" => "gotoUrl",
    //       ));
    //       Am::$a[1]($a[2]);
    //       return true;
    //
    //     }elseif(preg_match("/^(template|file|assets|download)#(.*)$/", $destiny, $a)){
    //
    //       // Renderizar
    //       if(self::$a[1]($a[2], array_merge($env, $params)))
    //         return true;
    //
    //       // Si vista no existe ir a error 404
    //       $lastError = "not fount {$a[1]}";
    //
    //     // Respuesta como un controlador
    //     }
    //
    //   }
    //
    // }

    // Si ninguna ruta coincide con la petición entonces se devuelve un error.
    return $lastError;

  }

}

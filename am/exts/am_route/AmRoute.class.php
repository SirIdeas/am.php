<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para la evaluación de las rutas
 */
final class AmRoute{

  protected static
    
    /**
     * Tipos de parámetros que puede tener la ruta
     */
    $types = array(
      'id'            => '[a-zA-Z_][a-zA-Z0-9_-]*', // Identificador
      'any'           => '.*',                      // Cualquier valor
      'numeric'       => '[0-9]*',                  // Numeros
      'alphabetic'    => '[a-zA-Z]*',               // Alfabetico
      'alphanumeric'  => '[a-zA-Z0-9]*',            // Alfanumerico
    ),

    /**
     * Alias.
     */
    $aliases = null,

    /**
     * Lista de acciones
     */
    $routes = array(),

    /**
     * Callbacks de preprocesamiento
     */
    $preProcessors = array(),

    /**
     * Callbacks de atención
     */
    $dispatchers = array(),

    /**
     * Instancia de la ruta que atendío la petición
     */
    $dispatched = null;

  protected

    /**
     * Tipo de ruta
     */
    $dispatcherName = null,

    /**
     * Target de la ruta
     */
    $destiny = null,

    /**
     * Ruta solicitada.
     */
    $route = null,

    /**
     * Alias.
     */
    $alias = null,
    
    /**
     * Regex de evaluación correspondiente a la ruta.
     */
    $regex = null,

    /**
     * Parámetros detectados de la ruta
     */
    $params = array();

  /**
   * Instanciación de una ruta.
   * @param string $route Ruta a evaluar
   */
  public function __construct($dispatcherName, $destiny, $route){

    $this->dispatcherName = $dispatcherName;
    $this->destiny = $destiny;
    $this->route = $route;
    $this->alias = $destiny;

    // cmpila la ruta
    $this->regex = self::compileRoute($route, $this->params);

  }

  /**
   * Evalua la ruta hace match con una petición.
   * @param  string     $request  URL de la petición
   * @return array/bool           Si la URL de una petición concuerda con la
   *                              ruta devuelve un array de pares
   *                              parámetros=>valor, de lo contario retorna
   *                              falso.
   */
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

  /**
   * Deveulve la ruta.
   * @return string
   */ 
  public function getRoute(){

    return $this->route;

  }

  /**
   * Deveulve el dstino de la ruta.
   * @return string
   */ 
  public function getDestiny(){

    return $this->destiny;

  }

  /**
   * Deveulve el alias a la ruta.
   * @return string
   */ 
  public function getAlias(){

    return $this->alias;

  }

  /**
   * Asigna el alias a un ruta.
   * @param  string   $alias  alias de la ruta.
   * @return $this
   */ 
  public function setAlias($alias){

    $this->alias = $alias;

    return $this;

  }

  /**
   * Sustituye los parámetros en una cadena
   * @param  string $str    Cadena que se quiere remplazar
   * @param  array  $params Array de argumentos a sustituir.
   * @return string         URL de la petición actual.
   */
  protected static function subParams($str, array $params){
    foreach($params as $key => $val){
      $str = str_replace("{{$key}}", $val, $str);
    }
    return $str;
  }

  /**
   * Intenta despachar una petición
   * @return string 
   */
  public function dispatch(array $params, $env) {
    if(!isset(self::$dispatchers[$this->dispatcherName])) return false;
      
    $dispatcher = self::$dispatchers[$this->dispatcherName];

    // Reemplazar cada parámetro en el destino de la peticion
    $destiny = self::subParams($this->destiny, $params);

    $response = null;
    
    // Buscar el callback de atención para determinado metodo si existe
    if(isValidCallback($dispatcher)){
      $response = call_user_func_array($dispatcher, array($destiny, $env, $params));
    }

    return $response;

  }


  public function getUrl ($url, $params = null) {

    if (!isset($params)) $params = itemOr('params', self::$dispatched, array());

    return self::subParams($url, $params);

  }

  /**
   * Construye la regex para una ruta.
   * @param  string   $route  Ruta de la que se desea obtener la regex.
   * @return array(2)         Array de dos posiciones. la primera es la regex
   *                          obtenia para la ruta, la segunda un array con
   *                          los nombres de los parámetros de la ruta.
   */
  private static function compileRoute($route, array &$params){

    // Si la ruta no tiene indicado la regex para obtener se debe añadirle
    if(!preg_match_all('/^(.+) (.*)$/', $route, $m))
      $route = '.+ '.trim($route);

    // Compila la ruta
    $regex = self::__compileRoute($route, $params);

    // Transformar / en  \/
    $regex = str_replace('/', '\\/', $regex);

    // Si no termina en barra entonces agregarla
    if(!preg_match('/\/$/', $regex))
      $regex = "{$regex}[\/]{0,1}";

    // Colocar inicio y final para formar regex
    $regex = "/^{$regex}$/";


    return $regex;

  }

  /**
   * Devuelve una regex correspondiente para una ruta dividiendo los párametros
   * y obteniendo el tipo para cada uno.
   * @param  string $route   Ruta de la que se desea obtener la regex.
   * @param  array  &$params Array donde se apilarán los nombres de los
   *                         parámetros deectados en la ruta.
   * @return string          La regex obtenia para la ruta.
   */
  private static function __compileRoute($route, array &$params){

    $typeId = self::$types['id'];
    
    // Obtener el ultimo parámetro
    if(preg_match("/^(.*){({$typeId})}(.*)$/", $route, $a)){
      array_unshift($params, $a[2]);
      // Determina si el parámetro tiene un tipo asignado (numero, alfanumerico,
      // entre otros)
      if(preg_match('/^(.*):(.*)$/', $a[3], $b)){
        if(isset(self::$types[$b[1]])){
          $type = self::$types[$b[1]];
        }else{
          $type = $b[1];
        }
        $a[3] = $b[2];

      // Si no tiene un tipo definido entonces admitir cualquier tipo
      }else{
        $type = self::$types['any'];
      }
      // Realizar llamado para el reto de la ruta.
      return self::__compileRoute($a[1], $params)."({$type}){$a[3]}";

    }

    return $route;

  }

  /**
   * Agrega un callback para preprocesar las rutas.
   * El objetivo es que otras extensiones puedan modificar la estructuras de las
   * rutas configuradas antes de que estas sean comparadas con la petición.
   * @param  callback $callback  Callback a agregar
   * 
   */
  public static function addPreProcessor($key, $callback){

    if(!isset(self::$preProcessors[$key]))
      self::$preProcessors[$key] = array();

    self::$preProcessors[$key][] = $callback;

  }

  /**
   * Agrega un callback para atender las rutas.
   * El objetivo es que otras extensiones puedan personalizar como atender las
   * rutas si lka misma tiene determinado key.
   * @param string   $to       Key el cual atenderá el callback.
   * @param callback $callback Callback a agregar.
   * 
   */
  public static function addDispatcher($to, $callback){

    self::$dispatchers[$to] = $callback;

  }

  // /**
  //  * Realiza el llamado de todos los pre calls de rutas.
  //  * @param array $routes Array con las rutas a evaluar.
  //  */
  // public static function callPreProcessors($routes){

  //   foreach ($routes as $type => $value) {
  //   }
  //   return $routes;

  // }

  /**
   * Método que atiende cada uno de lo métodos llamados desde la clase. 
   * Se asume que el nombre del método es el nombre de un despachador o de un
   * procesador.
   * @param string  $dispatcherName   Nombre del dispatcher o de proprocesadsor.
   * @param array   $arguments        Argumentos.
   */
  public final static function __callStatic($name, $arguments = null){

    $destiny = $arguments[0];
    $route = $arguments[1];

    $callbacks = itemOr($name, self::$preProcessors, array());

    if(!empty($callbacks)){
      $arr = array();
      foreach ($callbacks as $callback){
        $arr = array_merge(
          $arr, call_user_func_array($callback, $arguments)
        );
      }
      return $arr;
    }

    if (isset(self::$dispatchers[$name])) {
      $route = new AmRoute($name, $destiny, $route);
      self::$routes[] = $route;
      return $route;
    }

    throw Am::e('AMROUTE_BAD_ROUTE', $name, $arguments);

  }

  /**
   * Callback para evaluar las rutas.
   * @param  string $request Petición para la que se desea obtener la respuesta
   *                         correspondiente.
   * @return bool            Si se encontró o no una respuesta para la petición.
   */
  public static function evaluate(array $request){

    // Obtener string para la consulta.
    $response = self::evalMatch(
      $request['method'].' '.$request['request'],
      Am::getProperty('env', array())
    );

    if(!$response instanceof AmResponse){
      $response = Am::e404(Am::t('AMROUTE_NOT_MATCH'));
    }

    AmResponse::response($response);

  }

  /**
   * Método busca la ruta con la que conincide con una petición realiza el 
   * llamado correspondiente.
   * @param  string      $request Petición a resolver.
   * @param  array       $env     Variables de entorno configuradas.
   * @return bool/string          Retorna verdadero si logra despachar la ruta,
   *                              falso o un string con un mensaje de error de
   *                              lo contario.
   */
  public static function evalMatch($request, array $env = array()){
    $response = null;
    $lastResponse = null;

    require_once 'routing.init.php';

    foreach(self::$routes as $route){
      self::$aliases[$route->getAlias()] = $route;
    }

    foreach (self::$routes as $route) {
      
      // Si hace match con la peticion
      if(false !== ($params = $route->match($request))){
        $response = $route->dispatch($params, $env);

        // Si la respuesta es el valor true
        // Entonces asignar una respuesta vacía
        if($response === true){
          $response = new AmResponse;
        }

        if($response instanceof AmResponse){
          self::$dispatched = array(
            'route' => $route,
            'params' => $params,
            'env' => $env,
          );

          // Se atendió la llamada
          if($response->isResolved()){
            return $response;
          }

          // Error
          $lastResponse = $response;

        } else {
          // De lo contrario se toma un error y se toma el valor por defecto
          $lastResponse = Am::e404(Am::t('AMROUTE_NOT_FOUND_DISPATCHER',
            $type, $request));
        }

      }
    }

    return $lastResponse;

  }

  /**
   * Devuelve una URL para un ruta
   * @param  string $action Acction solcitada
   * @param  array  $parmas Array de argumentos para construir la ruta.
   * @return string         URL de la petición actual.
   */
  public static function url($action, array $params = array()) {

    $route = itemOr($action, self::$aliases);
    if (!$route)
      throw Am::e('AMROUTE_ACTION_NOT_FOUND', $action);

    return self::subParams($route->getRoute(), $params);

  }

  public static function getCurrentRoute () {

    return itemOr('route', self::$dispatched);

  }

  // private static function evalMatch2($request, array $routes, array $env = array(), array $parent = array()){

  //   $routes = self::callPreProcessors($routes);
  //   $lastResponse = false;

  //   $dispatchers = array_keys(self::$dispatchers);

  //   // Si tiene rutas internas
  //   if(isset($routes['routes'])){
  //     foreach($routes['routes'] as $key => $route){

  //       // Si la ruta es una cadena de caracteres
  //       // Se parte la cadena con el caracter # el primer paremtro es un key y
  //       // el segundo el valor
  //       if(is_string($route)){
  //         $route = explode(' => ', $route);
  //         if(count($route) == 1)
  //           $route = array('' => $route[0]);
  //         else
  //           $route = array($route[0] => $route[1]);
  //       }

  //       // Asignar key como ruta si no tiene ruta asignada
  //       $route['route'] = itemOr('route', $route, $key);

  //       // Concatenar los parametros de la ruta parametro con los de la ruta
  //       // hija
  //       foreach($routes as $key => $value)
  //         if(in_array($key, $dispatchers))
  //           $route[$key] = $value . itemOr($key, $route, '');

  //       // Llamar para la ruta interna
  //       $response = self::evalMatch($request, $route, $env, $routes);

  //       if($response instanceof AmResponse){
  //         // Se atendió la llamada
  //         if($response->isResolved())
  //           return $response;
  //         // Error
  //         else
  //           $lastResponse = $response;
  //       }

  //     }
  //   }

  //   // No tiene rutas hijas o ninguna de las rutas hijas atendió la petición
  //   // Se debe verificar si la ruta actual tiene una ruta asignada para 
  //   // evaluarla, de lo contrario salir 
  //   if(!isset($parent['route']) && !isset($routes['route']))
  //     return $lastResponse;

  //   // Si no esta indicada la ruta se toma el indice de la ruta como indice
  //   $routes['route'] = itemOr('route', $parent, '') .
  //                      itemOr('route', $routes, '');

  //   // Crear instancia de la ruta.
  //   $r = new self($routes['route']);

  //   // Si hace match con la peticion
  //   if(false !== ($params = $r->match($request))){

  //     foreach ($routes as $type => $destiny) {
  //       if(isset(self::$dispatchers[$type])){
          
  //         $dispatcher = self::$dispatchers[$type];

  //         // Reemplazar cada parámetro en el destino de la peticion
  //         foreach($params as $key => $val)
  //           $destiny = str_replace("{{$key}}", $val, $destiny);

  //         $response = null;
          
  //         // Buscar el callback de atención para determinado metodo si existe
  //         if(isValidCallback($dispatcher))
  //           $response = call_user_func_array($dispatcher,
  //                                            array($destiny, $env, $params));

  //         // Si la respuesta es el valor true
  //         // Entonces asignar una respuesta vacía
  //         if($response === true)
  //           $response = new AmResponse;

  //         if($response instanceof AmResponse){
  //           // Se atendió la llamada
  //           if($response->isResolved())
  //             return $response;
  //           // Error
  //           else
  //             $lastResponse = $response;

  //         }else
  //           // De lo contrario se toma un error y se toma el valor por defecto
  //           $lastResponse = Am::e404(Am::t('AMROUTE_NOT_FOUND_DISPATCHER',
  //             $type, $request));

  //       }

  //     }

  //   }

  //   // Si ninguna ruta coincide con la petición entonces se devuelve un error.
  //   return $lastResponse;

  // }

}

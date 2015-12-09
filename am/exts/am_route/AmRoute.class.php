<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para la evaluación de las rutas
 * -----------------------------------------------------------------------------
 */

final class AmRoute{

  protected static
    
    /**
     * -------------------------------------------------------------------------
     * Tipos de parámetros que puede tener la ruta
     * -------------------------------------------------------------------------
     */
    $TYPES = array(
      'id'            => '[a-zA-Z_][a-zA-Z0-9_-]*', // Identificador
      'any'           => '.*',                      // Cualquier valor
      'numeric'       => '[0-9]*',                  // Numeros
      'alphabetic'    => '[a-zA-Z]*',               // Alfabetico
      'alphanumeric'  => '[a-zA-Z0-9]*',            // Alfanumerico
    ),

    /**
     * -------------------------------------------------------------------------
     * Callbacks de preprocesamiento
     * -------------------------------------------------------------------------
     */
    $preProcessors = array(),

    /**
     * -------------------------------------------------------------------------
     * Callbacks de atención
     * -------------------------------------------------------------------------
     */
    $dispatchers = array();

  protected

    /**
     * -------------------------------------------------------------------------
     * Ruta solicitada.
     * -------------------------------------------------------------------------
     */
    $route = null,
    
    /**
     * -------------------------------------------------------------------------
     * Regex de evaluación correspondiente a la ruta.
     * -------------------------------------------------------------------------
     */
    $regex = null,

    /**
     * -------------------------------------------------------------------------
     * Parámetros detectados de la ruta
     * -------------------------------------------------------------------------
     */
    $params = array();

  /**
   * ---------------------------------------------------------------------------
   * Instanciación de una ruta.
   * ---------------------------------------------------------------------------
   * @param string $route Ruta a evaluar
   */
  public function __construct($route){

    $this->route = $route;

    // cmpila la ruta
    list($this->regex, $this->params) = self::compileRoute($route);

  }

  /**
   * ---------------------------------------------------------------------------
   * Evalua la ruta hace match con una petición.
   * ---------------------------------------------------------------------------
   * @param  [type] $request URL de la petición
   * @return array/bool      Si la URL de una petición concuerda con la ruta
   *                         devuelve un array de pares parámetros=>valor, de lo
   *                         contario retorna falso.
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
   * ---------------------------------------------------------------------------
   * Construye la regex para una ruta.
   * ---------------------------------------------------------------------------
   * @param  string   $route  Ruta de la que se desea obtener la regex.
   * @return array(2)         Array de dos posiciones. la primera es la regex
   *                          obtenia para la ruta, la segunda un array con
   *                          los nombres de los parámetros de la ruta.
   */
  private static function compileRoute($route){

    $params = array();

    // Compila la ruta
    $regex = self::__compileRoute($route, $params);

    // Transformar / en  \/
    $regex = str_replace('/', "\\/", $regex);

    // Si no termina en barra entonces agregarla
    if(!preg_match('/\/$/', $regex)) $regex = "{$regex}[\/]{0,1}";

    // Colocar inicio y final para formar regex
    $regex = "/^{$regex}$/";

    return array($regex, $params);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una regex correspondiente para una ruta dividiendo los párametros
   * y obteniendo el tipo para cada uno.
   * ---------------------------------------------------------------------------
   * @param  string   $route  Ruta de la que se desea obtener la regex.
   * @param  &array   $params Array donde se appilarán los nombres de los
   *                          parámetros deectados en la ruta.
   * @return array(2)         Array de dos posiciones. la primera es la regex
   *                          obtenia para la ruta, la segunda un array con
   *                          los nombres de los parámetros de la ruta.
   */
  private static function __compileRoute($route, array &$params){

    $typeId = self::$TYPES['id'];
    
    // Obtener el ultimo parámetro
    if(preg_match("/^(.*):({$typeId})(.*)$/", $route, $a)){
      array_unshift($params, $a[2]);
      // Determina si el parámetro tiene un tipo asignado (numero, alfanumerico,
      // entre otros)
      if(preg_match('/^\((.*)\)(.*)$/', $a[3], $b)){
        if(isset(self::$TYPES[$b[1]])){
          $type = self::$TYPES[$b[1]];
        }else{
          $type = $b[1];
        }
        $a[3] = $b[2];

      // Si no tiene un tipo definido entonces admitir cualquier tipo
      }else{
        $type = self::$TYPES['any'];
      }
      // Realizar llamado para el reto de la ruta.
      return self::__compileRoute($a[1], $params)."({$type}){$a[3]}";

    }

    return $route;

  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega un callback para preprocesar las rutas.
   * El objetivo es que otras extensiones puedan modificar la estructuras de las
   * rutas configuradas antes de que estas sean comparadas con la petición.
   * ---------------------------------------------------------------------------
   * @param   callback  $callback  Callback a agregar
   * 
   */
  public static final function addPreProcessor($key, $callback){

    if(!isset(self::$preProcessors[$key]))
      self::$preProcessors[$key] = array();

    self::$preProcessors[$key][] = $callback;

  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega un callback para atender las rutas.
   * El objetivo es que otras extensiones puedan personalizar como atender las
   * rutas si lka misma tiene determinado key
   * ---------------------------------------------------------------------------
   * @param   string    $to         Key el cual atenderá el callback
   * @param   callback  $callback   Callback a agregar
   * 
   */
  public static final function addDispatcher($to, $callback){

    self::$dispatchers[$to] = $callback;

  }

  /**
   * ---------------------------------------------------------------------------
   * Realiza el llamado de todos los pre calls de rutas
   * ---------------------------------------------------------------------------
   * @param  &array &$routes Array con las rutas a evaluar
   */
  public static function callPreProcessors($routes){

    foreach ($routes as $type => $value) {
      $callbacks = itemOr($type, self::$preProcessors, array());
      if(!empty($callbacks)){
        foreach (self::$preProcessors[$type] as $callback)
          $routes = call_user_func_array($callback, array($routes));
        unset($routes[$key]);
      }
    }
    return $routes;

  }

  /**
   * ---------------------------------------------------------------------------
   * Callback para evaluar las rutas.
   * ---------------------------------------------------------------------------
   * @param   string  $request  Petición para la que se desea obtener la
   *                            respuesta correspondiente.
   * @return  bool              Si se encontró o no una respuesta para la
   *                            petición
   */
  public static final function evaluate($request){
    
    if(true === ($lastError = self::evalMatch($request,
      array('routes' => Am::getProperty('routing', array())),
      Am::getProperty('env', array())
    )))
        return true;

    // No se encontró una ruta válida
    Am::e404($lastError);

    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Método busca la ruta con la que conincide con una petición realiza el 
   * llamado correspondiente.
   * ---------------------------------------------------------------------------
   * @param  string $request  Petición a resolver
   * @param  array  $routes   Rutas configuradas
   * @param  array  $env      Variables de entorno configuradas
   * @param  array  $parent   Ruta padre
   * @return bool/string      Retorna verdadero si logra despachar la ruta,
   *                          falso o un string con un mensaje de error de lo
   *                          contario
   */
  private static final function evalMatch($request, array $routes, array $env = array(), array $parent = array()){

    $routes = self::callPreProcessors($routes);
    $lastError = false;

    $dispatchers = array_keys(self::$dispatchers);

    // Si tiene rutas internas
    if(isset($routes['routes'])){
      foreach($routes['routes'] as $key => $route){

        // Si la ruta es una cadena de caracteres
        // Se parte la cadena con el caracter # el primer paremtro es un key y
        // el segundo el valor
        if(is_string($route)){
          list($prop, $value) = explode(' => ', $route);
          $route = array($prop => $value);
        }

        // Asignar key como ruta si no tiene ruta asignada
        $route['route'] = itemOr('route', $route, $key);

        // Concatenar los parametros de la ruta parametro con los de la ruta
        // hija
        foreach($routes as $key => $value)
          if(in_array($key, $dispatchers))
            $route[$key] = $value . itemOr($key, $route, '');

        // Llamar para la ruta interna
        $newError = self::evalMatch($request, $route, $env, $routes);

        // Se atendió la llamada
        if(true === $newError)
          return true;

        // Si se retorno un error reescirbir el error anterior
        if(is_string($newError))
          $lastError = $newError;

      }
    }

    // No tiene rutas hijas o ninguna de las rutas hijas atendió la pentición

    // Si no esta indicada la ruta se toma el indice de la ruta como indice
    $routes['route'] = itemOr('route', $parent, '') .
                       itemOr('route', $routes, '');

    // Crear instancia de la ruta.
    $r = new self($routes['route']);

    // Si hace match con la peticion
    if(false !== ($originalParams = $r->match($request))){

      $params = $originalParams;

      foreach ($routes as $type => $destiny) {
        if(isset(self::$dispatchers[$type])){
          
          $dispatchers = self::$dispatchers[$type];

          // Reemplazar cada parámetro en el destino de la peticion
          // Los parámetros que no esten el destino de la peticion serán
          // los parámetros para la llamada de de respuesta
          foreach($params as $key => $val){
            $newDestiny = str_replace(":{$key}", $val, $destiny);
            if($newDestiny !== $destiny)
              unset($params[$key]);
            $destiny = $newDestiny;
          }

          // Buscar el callback de atención para determinado metodo si existe
          if(isValidCallback($dispatchers))
            $newError = call_user_func_array($dispatchers, array($destiny, $env, $params));

          // Si respondio la pentición retornar verdadero para indicar el exito
          // de la operación
          if(true === $newError)
            return true;
          
          // Si retorna un string entonces esta indicando el mensaje de un error
          if(is_string($newError))
            $lastError = $newError;

          // De lo contrario se toma un error y se toma el valor por defecto
          else
            $lastError = Am::t('NOT_FOUND_ATTEND', $type, $request);

        }

      }

    }

    // Si ninguna ruta coincide con la petición entonces se devuelve un error.
    return $lastError;

  }

}

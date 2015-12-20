<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase principal de Amathista
 * -----------------------------------------------------------------------------
 */

final class Am{

  protected static

    /**
     * -------------------------------------------------------------------------
     * Callbacks de eventos globales del sistema.
     * -------------------------------------------------------------------------
     */
    $callbacks = array(

      // Evalucación de ruta
      'route.evaluate' => null, // $request

      // Agregar un pre procesador de rutas
      'route.addPreProcessor' => null, // $type, $callback

      // Agregar un despachador de ruta en base su key
      'route.addDispatcher' => null, // $type, $callback
      
      // Responder con archivo
      'response.file' => null, // $file

      // Responder con una llamada
      'response.call' => null, // $callback, $env, $params

      // Responde con el renderizado de una vista
      'response.template' => array(), // $template, $vars, $options

      // Responder con una redirección
      'response.go' => null, // $url

      // Responder con controlador
      'response.controller' => null, // $action, $params, $env

      // Responder con un error 404
      'response.e404' => null, // $msg

      // Renderiza de una vista
      'render.template' => array(), // $__tpl, $__vars, $__options
      
      // PENDIENTE: Volver a agregar cuando se agrege una extensión que las use
      
      // // Asignar identificador de la sesion
      // 'session.id' => null, // $index
      
      // // Obtener el todos los datos de sesion
      // 'session.all' => null, // $index
      
      // // Obtener elemento
      // 'session.get' => null, // $index
      
      // // Indica si existe un elemento
      // 'session.has' => null, // $index
      
      // // Asignar un elemento
      // 'session.set' => null, // $index, $value
      
      // // Eliminar un elemento
      // 'session.delete' => null, // $index
      
      // Obtener una instancia del manejador de credenciales
      // 'credentials.handler' => array()
      
    ),

    /**
     * -------------------------------------------------------------------------
     * Definición de callbacks a utilizar para mezclar atributos.
     * -------------------------------------------------------------------------
     */
    $mergeFunctions = array(
      'requires' => 'merge_if_snd_first_not_false',
      'env' => 'merge_if_both_are_array',
      'tasks' => 'array_merge_recursive',
      'formats' => 'array_merge'
    ),

    // PENDIENTE: Revisar
    /**
     * -------------------------------------------------------------------------
     * Exteciones manejadoras de session
     * -------------------------------------------------------------------------
     */
    $aliasExts = array(
      'normalSession' => 'exts/am_normal_session/'
    ),

    /**
     * -------------------------------------------------------------------------
     * Array de extensiones cargadas
     * -------------------------------------------------------------------------
     */
    $loadedExts = array(),

    /**
     * -------------------------------------------------------------------------
     * Instancias unicas de clases
     * -------------------------------------------------------------------------
     */
    $instances = array(),

    /**
     * -------------------------------------------------------------------------
     * Directorios de entorno
     * -------------------------------------------------------------------------
     * Este es un array que contiene d
     */
    $dirs = array(),

    /**
     * -------------------------------------------------------------------------
     * Directorios de tareas
     * -------------------------------------------------------------------------
     * Este es un array que contiene d
     */
    $tasksDirs = array(),

    /**
     * -------------------------------------------------------------------------
     * Archivos de configuración cargados
     * -------------------------------------------------------------------------
     */
    $confsLoaded = array(),  

    /**
     * -------------------------------------------------------------------------
     * Propiedades/Configuraciones globales cargadas
     * -------------------------------------------------------------------------
     */
    $confs = array(
      'formats' => array(
        'AM_NOT_FOUND' => 'Am: Not Found',
        'AM_CLASS_NOT_FOUND' => 'Am: Class "%s" Not Found',
        'AM_NOT_FOUND_EXT' => 'Am: No se encontró la extensión "%s"',
        'AM_NOT_FOUND_FILE_EXTS' => 'Am: No se encontró el archivo "%s" de la extensión "%s"',
        'AM_NOT_FOUND_COMMAND' => 'Am: No se encontró el comando %s',
        'AM_NOT_FOUND_VIEW' => 'Am: No existe view "%s"',
        'AMOBJECT_CANNOT_ACCESS_PROPERTY' => 'Am: No puede acceder al atributo protegido/privado %s::$%s',
      )
    ),

    /**
     * -------------------------------------------------------------------------
     * URL base de la aplicación
     * -------------------------------------------------------------------------
     */
    $urlBase = null,

    /**
     * -------------------------------------------------------------------------
     * Petición realizada
     * -------------------------------------------------------------------------
     */
    $requestStr = null,

    /**
     * -------------------------------------------------------------------------
     * Instancia de AmObject que tiene como propiedades los valores los arrays
     * respectivos.
     * -------------------------------------------------------------------------
     */
    $server = null,
    $get = null,
    $post = null,
    $cookie = null,
    $request = null,
    $env = null;

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una texto con un determinado formato
   * ---------------------------------------------------------------------------
   * @param  string $fmtKey formato a buscar
   * @return string         Texto formateado
   */
  public static function t($fmtKey /* Parametros */){

    $params = func_get_args();

    $formats = self::getProperty('formats', array());
    
    // Obtener formato si existe
    $params[0] = itemOr($fmtKey, $formats, $fmtKey);

    return call_user_func_array('sprintf', $params);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve un error con el mensaje obtenido del llamado del método Am::t con
   * los parámetros de esta función.
   * ---------------------------------------------------------------------------
   * @params   Utilizados para generar el texto del mensaje
   * @return   Una instancia de la clase AmError con el mensaje del texto
   *           obtenido
   */
  public static function e(/* Parametros */){

    return new AmError(call_user_func_array(array('Am', 't'), func_get_args()));

  }

  /**
   * ---------------------------------------------------------------------------
   * Inicializa las variables de AmResponse
   * ---------------------------------------------------------------------------
   */
  public static function start(){

    self::$server = new AmObject($_SERVER);
    self::$get = new AmObject($_GET);
    self::$post = new AmObject($_POST);
    self::$cookie = new AmObject($_COOKIE);
    self::$request = new AmObject($_REQUEST);
    self::$env = new AmObject($_ENV);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve el método de la peticion.
   * ---------------------------------------------------------------------------
   */
  public static function getMethod(){
    return self::$server->REQUEST_METHOD;
  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la instancia de una clase existente. Sino existe la instancia se
   * crea una nueva.
   * ---------------------------------------------------------------------------
   * @param   string  $className  Nombre de la clase de la que se desea obtener
   *                              la instancia.
   * @param   array   $params     Parámetros para instancia la clase.
   * @return  object              Objeto instanciado
   */
  public static function getInstance($className, array $params = array()){

    // Si la clase no existe devolver error
    if(!class_exists($className))
      throw Am::e('AM_CLASS_NOT_FOUND', $className);

    // Si la instancia existe se devuelve
    if(isset(self::$instances[$className]))
      return self::$instances[$className];

    // Si la instancia no existe se crea una instancia de la clase
    return self::$instances[$className] = new $className($params);

  }

  /**
   * ---------------------------------------------------------------------------
   * Asigna un callback de un evento global.
   * ---------------------------------------------------------------------------
   * @param   string    $action   Nombre del evento a atender.
   * @param   callback  $callback Callback a asociar con el evento.
   */
  public static function on($action, $callback){

    self::$callbacks[$action] = $callback;

  }

  /**
   * ---------------------------------------------------------------------------
   * Llamar el callback de un evento global.
   * ---------------------------------------------------------------------------
   * @param   string  $action   Nombre dle evento a llamar.
   * @param   ...               El resto de los parámetros son utilizados
   *                            como argumentos de la llamada del callback.
   * @return  mixed             Lo retornado por el callback correspondiente.
   */
  public static function ring($action /* Resto de los parametros*/){
    
    // Obtener los parámetros
    $options = func_get_args();
    
    // Quitar el primer parametros, corresponde a $action
    array_shift($options);

    // Obtener callback
    $callback = itemOr($action, self::$callbacks);

    // Si existe callbacks definidas para la accion
    if(isValidCallback($callback))

      // Llamar los callbacks
      return call_user_func_array(self::$callbacks[$action], $options);

  }

  /**
   * ---------------------------------------------------------------------------
   * Mezcla nuevos valores ($value) a una propiedad global ($propiedad). Si
   * $extend es verdadero, entonces los valores nuevos no sobrescriben los
   * anteriores.
   * ---------------------------------------------------------------------------
   * @param   string  $property   Nombre de la propiedad a asignar.
   * @param   string  $value      Valor a asignar a la propiedad
   * @param   bool    $extend     Indica si se extenderá los valores o si se
   *                              sobreescribirán
   */
  public static function mergeProperty($property, $value, $extend = false){

    // Obtener funcion callback para mezclar la propiedad solicitada
    $mergeFunctions = itemOr($property, self::$mergeFunctions);

    // Si exite la funcion y existe un valor previo se mezcla a partir de la
    // funcion designada
    if($mergeFunctions !== null && isset(self::$confs[$property])){

      // Si se desea extender. Entonces los valores nuevos son sobreescritos por
      // los viejos
      if($extend === true)
        $params = array($value, self::$confs[$property]);

      // De lo contrario los valores nuevos sobre escriben a los viejos
      else
        $params = array(self::$confs[$property], $value);

      self::$confs[$property] = call_user_func_array($mergeFunctions, $params);

    // Si no existe el callback se devolvera el ultimo valor
    }else
      self::$confs[$property] = $value;

  }

  /**
   * ---------------------------------------------------------------------------
   * Mezcla un conjunto de propieades globales indicadas en $properties. Si
   * $extend es verdadero, entonces los valores nuevos no sobrescriben los
   * anteriores.
   * ---------------------------------------------------------------------------
   * @param   array $properties Array de pares propiedad=>valor a asignar.
   * @param   bool  $extend     Indica si se extenderá los valores o si se
   *                            sobreescribirán
   */
  public static function mergeProperties(array $properties, $extend = false){

    // Recorrer elementos obtenidos para ir
    foreach ($properties as $property => $value)
      self::mergeProperty($property, $value, $extend);

  }

  /**
   * ---------------------------------------------------------------------------
   * Carga la configuración de un archivo. Si $property es recibida entonces la
   * configuración se cargará en dicha propiedad, de lo contrario se recorrerá
   * se mezclará en propiedades separadas
   * ---------------------------------------------------------------------------
   * @param   string  $filename   Nombre del archivo de configuración a cargar.
   * @param   string  $property   Nombre de la propiedad a asignar.
   * @param   bool    $extend     Indica si se extenderá los valores o si se
   *                              sobreescribirán
   */
  public static function mergePropertiesFromFile($filename,
                                                 $property = null,
                                                 $extend = false){

    // Obtener el nombre real del archivo
    $filename = realpath($filename);

    // Si el archivo no existe salir
    if(!is_file($filename) || in_array($filename, self::$confsLoaded))
      return;

    // Agregar el archivo a la lita de archivos de configuracion cargados
    self::$confsLoaded[] = $filename;

    // Cargar archivo de configuracion
    $conf = require $filename;

    // Si la configuración esta destinada a una propiedad especifica
    // mezclar con dicha configuracion
    if(isset($property))
      self::mergeProperty($property, $conf, $extend);

    else
      // Sino se debe agregar las configuraciones una por una.
      self::mergeProperties($conf, $extend);

  }

  /**
   * ---------------------------------------------------------------------------
   * Carga las configuraciones de un archivo buscando en cada una de las
   * carpetas del entorno. Si $property es recibida entonces la
   * configuración se cargará en dicha propiedad, de lo contrario se recorrerá
   * se mezclará en propiedades separadas. Si $extend es verdadero, entonces
   * los valores nuevos no sobrescriben los anteriores.
   * ---------------------------------------------------------------------------
   * @param   string  $filename   Nombre del archivo de configuración a cargar.
   * @param   string  $property   Nombre de la propiedad a asignar.
   * @param   bool    $extend     Indica si se extenderá los valores o si se
   *                              sobreescribirán
   */
  public static function mergePropertiesFromAllFiles($filename = 'am',
                                                     $property = null,
                                                     $extend = false){

    // Recorrer cada uno de las carpetas en el path
    foreach (self::$dirs as $path)
      // Si el archivo cargar la configuracion en la posicion path/property
      self::mergePropertiesFromFile("{$path}/{$filename}.conf.php",
                                    $property, $extend);

  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega un directorio al entorno de la aplicación.
   * ---------------------------------------------------------------------------
   * @param   string  $dir   Directorio a agregar.
   */
  public static function addDir($dir){

    self::$dirs[] = realpath($dir);
    self::$dirs = array_unique(self::$dirs);

  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega un directorio al tareas de la aplicación.
   * ---------------------------------------------------------------------------
   * @param   string  $dir   Directorio a agregar.
   */
  public static function addTasksDir($dir){

    self::$tasksDirs[] = realpath($dir);
    self::$tasksDirs = array_unique(self::$tasksDirs);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve el valor de una propiedad global. Si la propiead no tiene valor
   * asignado se devuelve el valor por defacto ($default)
   * ---------------------------------------------------------------------------
   * @param   string  $property   Nombre de la propiedad a consultar.
   * @param   mixed   $default    Valor por defecto en caso que la propiedad
   *                              aún no esté asignada.
   * @return  mixed               Valor de la propiedad o valor por defecto si
   *                              la primera no existe.
   */
  public static function getProperty($property, $default = null){

    self::mergePropertiesFromAllFiles($property, $property);
    return itemOr($property, self::$confs, $default);

  }

  /**
   * ---------------------------------------------------------------------------
   * Mueve un archivo a la papelera.
   * Este archivo se ubicará en la papelera relativo al directorio en común del
   * mismo con el directorio actual.
   * ---------------------------------------------------------------------------
   * @param   string  $file   Archivo a mover a la papelera
   * @return  bool            Si se pudo mover o no el archivo.
   */
  public static function sendToTrash($file){

    // Obtener nombre verdadero de archivo
    $file = realpath($file);
    // Salir si no existe el archivo
    if(!$file) return;

    // Si AM_TRASH esta definida como false, indica que no se desea tener una
    // papelera
    if(AM_TRASH===false)
      return !!unlink($file);

    // Definir directorio de la papelera
    $trahsFolder = AM_TRASH.'/'.AM_START;

    // Obtener el directorio actual y el directorio del archivo a mover
    $dirBase = getcwd();
    $dirFile = realpath(dirname($file));

    // Obtener el directorio común de ambos.
    while($dirBase != $dirFile)
      if(strlen($dirBase)>strlen($dirFile))
        $dirBase = dirname($dirBase);
      else
        $dirFile = dirname($dirFile);
      
    $relativeFile = substr_replace($file, '', 0, strlen($dirBase));

    // Obtener ubicación real del archivo donde se copiará
    $dest = $trahsFolder.$relativeFile;

    // Crear carpeta
    @mkdir(dirname($dest), 0755, true);

    // Mover archivo
    return !!rename($file, $dest);
    
  }

  /**
   * ---------------------------------------------------------------------------
   * Asigna o sustituye un dispatcher a un tipo de ruta
   * ---------------------------------------------------------------------------
   * @param   string    $type         Nombre del tipo al que se agrega el
   *                                  callback
   * @param   callback  $dispatcher   Despachador a agregar
   * @return  any                     Valor devuelvo por el manejador del
   *                                  evento.
   */
  public static function addRouteDispatcher($type, $dispatcher){
    return Am::ring('route.addDispatcher', $type, $dispatcher);
  }

  /**
   * ---------------------------------------------------------------------------
   * Asigna un pre-procesador de rutas a un tipo
   * ---------------------------------------------------------------------------
   * @param   string    $type           Nombre del tipo al que se agrega el
   *                                    callback
   * @param   callback  $preProcessor   Preprocesador 
   * @return  any                       Valor devuelvo por el manejador del
   *                                    evento.
   */
  public static function addRoutePreProcessor($type, $preProcessor)  {
    return Am::ring('route.addPreProcessor', $type, $preProcessor);
  }

  /**
   * ---------------------------------------------------------------------------
   * Responde con un archivo indicado por parámetro.
   * ---------------------------------------------------------------------------
   * @param   string  $file         Ruta del archivo con el que se responderá.
   * @param   bool    $attachment   Si la ruta se descarga o no.
   * @return  any                   Respuesta de manejador configurado.
   */
  public static function file($filename, $attachment = false){

    return self::ring('response.file', self::findFile($filename), $attachment);

  }

  /**
   * ---------------------------------------------------------------------------
   * Responde la descarga de un archivo indicado por parámetro.
   * ---------------------------------------------------------------------------
   * @param   string  $file Ruta del archivo a descargar.
   * @return  any           Respuesta de manejador configurado.
   */
  public static function download($file){

    return self::file($file, true);

  }

  /**
   * ---------------------------------------------------------------------------
   * Busca una llamada como función, método estático de una clase o llamada
   * a controlador.
   * ---------------------------------------------------------------------------
   * @param   string $callback  String que identifica el controlador a buscar.
   * @param   array  $env       Variables de entorno.
   * @param   array  $params    Argumentos obtenidos de la ruta.
   * @return  any               Respuesta de manejador configurado.
   */
  public static function call($callback, array $env = array(),
                              array $params = array()){

    return self::ring('response.call', $callback, $env, $params);

  }

  /**
   * ---------------------------------------------------------------------------
   * Responde con un error 404
   * ---------------------------------------------------------------------------
   * @param   string  $msg  Mensaje de error a mostrar
   * @return  any           Respuesta de manejador configurado.
   */
  public static function e404($msg = null){

    return self::ring('response.e404', $msg);

  }

  /**
   * ---------------------------------------------------------------------------
   * Llamada de un controlador.
   * ---------------------------------------------------------------------------
   * @param   string $action    String que identifica la acción a ejecutar.
   *                            Tiene el formato 'Controlador@action'
   * @param   array  $env       Variables de entorno.
   * @param   array  $params    Argumentos obtenidos de la ruta.
   * @return  any               Respuesta de manejador configurado.
   */
  public static function controller($action, array $env = array(),
                                    array $params = array()){

    return self::ring('response.controller', $action, $env, $params);

  }

  /**
   * ---------------------------------------------------------------------------
   * Busca un template y lo renderiza.
   * ---------------------------------------------------------------------------
   * @param   string  $tpl      Template a renderizar.
   * @param   array   $env      Variables de entorno
   * @param   array   $params   Parámetros obtenidos de la rutas
   * @return  any               Respuesta de manejador configurado.
   */
  public static function template($tpl, array $env = array(),
                                  array $params = array()){

    // Mezclar variables de entorno con los parametros
    $vars = array_merge($env, $params);

    // Obtener la ruta de la vista
    $tpl = findFileIn($tpl, array_reverse(self::$dirs));

    return self::ring('response.template', $tpl, $vars);

  }

  /**
   * ---------------------------------------------------------------------------
   * Renderiza un a vista.
   * ---------------------------------------------------------------------------
   * @param  string $__tpl      Template a renderizar.
   * @param  array  $__params   Parámetros para la vista.
   * @param  array  $__options  Opciones de la vista.
   */
  public static function render($__tpl, array $__vars = array(),
                                array $__options = array()){
    
    self::$__tpl = self::findFile(self::$__tpl);

    // Generar error si la vista no existe
    if(!is_file(self::$__tpl))
      return false;

    extract(array_merge($__vars, $__options));
    require self::$__tpl;

    return true;

  }

  /////////////////////////////////////////////////////////////////////////////
  // Urls y redicciones
  /////////////////////////////////////////////////////////////////////////////
  /**
   * ---------------------------------------------------------------------------
   * Obtiene la petición realizada. Si se esta en la línea de comandos, entonces
   * la petición es vacía.
   * ---------------------------------------------------------------------------
   * @return  string    Petición.
   */
  public static function getRequest(){

    if(!self::$requestStr){

      // Variable global de argumentos
      global $argv;

      $request = '';

      if(!isset($argv)){

        $urlBase = self::getUrlBase();

        // Obtener peticion
        $request = substr_replace($_SERVER['REQUEST_URI'],
                                  '', 0, strlen($urlBase));

        // Quitar los parametros
        if(!empty($_SERVER['QUERY_STRING']))
          $request = substr_replace($request, '',
            strlen($request) - strlen($_SERVER['QUERY_STRING']) - 1,
            strlen($_SERVER['QUERY_STRING']) + 1);

        // Validacion para cuando este en la peticion no comienze con '/'
        if(empty($request) || $request[0] !== '/')
          $request = '/' . $request;

      }

      self::$requestStr = $request;

    }

    return self::$requestStr;

  }
  
  /**
   * ---------------------------------------------------------------------------
   * Devuelve la url base de la aplicación.
   * ---------------------------------------------------------------------------
   * @return  string  URL Base de la aplicación.
   */
  public static function getUrlBase(){

    if(!isset(self::$urlBase)){

      // Variable global de argumentos
      global $argv;

      $urlBase = '';

      if(!isset($argv)){

        // Definicion de la URL Base
        $urlBase = dirname($_SERVER['PHP_SELF']);

        // Validacion para cuando este en el root
        if($urlBase === '/')
          $urlBase = '';

      }

      self::$urlBase = $urlBase;

    }

    return self::$urlBase;

  }

  /**
   * ---------------------------------------------------------------------------
   * Contruye una URL de la aplicación.
   * ---------------------------------------------------------------------------
   * @param   string $url   URL de la app que se desea obtener.
   * @return  string        URL Construída
   */
  public static function url($url = ''){

    return self::$urlBase.$url;

  }

  /**
   * ---------------------------------------------------------------------------
   * Imprime una URL.
   * ---------------------------------------------------------------------------
   * @param   string $url  URL de la app que se desea imprimir.
   */
  public static function eUrl($url = ''){

    echo self::url($url);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una URL absoluta incluyendo el nombre del servidor y el protocolo
   * de conexión.
   * ---------------------------------------------------------------------------
   * @param   string $url   URL de la app que se desea obtener.
   * @return  string        URL Construída.
   */
  public static function serverUrl($url = ''){

    if(isset($_SERVER['SERVER_NAME']))
      return 'http://' . $_SERVER['SERVER_NAME'] . self::url($url);

    return self::url($url);

  }

  /**
   * ---------------------------------------------------------------------------
   * Imprime una URL absoluta incluyendo el nombre del servidor y el protocolo
   * de conexión.
   * ---------------------------------------------------------------------------
   * @param   string $url   URL de la app que se desea imprimir.
   */
  public static function eServerUrl($url = ''){

    echo self::serverUrl($url);

  }

  /**
   * ---------------------------------------------------------------------------
   * Redirigir a una URL.
   * ---------------------------------------------------------------------------
   * @param   string $url   URL que se desea ir.
   */
  public static function go($url){

    return self::ring('response.go', $url);

  }

  /**
   * ---------------------------------------------------------------------------
   * Redirigir a una URL formateandola con self::url.
   * ---------------------------------------------------------------------------
   * @param   string $path  URL de la app que se desea redirigir.
   */
  public static function redirect($url){

    return self::go(self::url($url));

  }

  /**
   * ---------------------------------------------------------------------------
   * Redirigir a una URL formateandola con self::url si $condition es verdadero
   * ---------------------------------------------------------------------------
   * @param   bool    $condition  Resultado de la condición evaluada.
   * @param   string  $url        URL de la app que se desea redirigir.
   */
  public static function redirectIf($condition, $url){

    if($condition)
      return self::redirect($url);

  }

  /**
   * ---------------------------------------------------------------------------
   * Redirigir a una URL formateandola con self::url si $condition es falsa.
   * ---------------------------------------------------------------------------
   * @param   bool    $condition  Resultado de la condición evaluada.
   * @param   string  $url        URL de la app que se desea redirigir.
   */
  public static function redirectUnless($condition, $url){

    return self::redirectIf(!$condition, $url);

  }

  /////////////////////////////////////////////////////////////////////////////
  // Inclusion de archivos y extenciones
  /////////////////////////////////////////////////////////////////////////////

  /**
   * ---------------------------------------------------------------------------
   * Carga un extensión indicada por parámetro. Devuelve verdadero o false 
   * dependiendo de si se logró cargar o no la extensión. La carga de la
   * extensión se realiza verificando la existencia del archivo "{$file}.php"
   * que indica una extención simple, o la existencia de archivo
   * "{$file}.conf.php" que indica una extensión compuesta.
   * ---------------------------------------------------------------------------
   * @param   string  $file   Ruta de la extensión que se desea cargar.
   * @return  bool            Si se cargo o no la extensión.
   */
  public static function load($file){

    // Si ya fué cargado retornar verdadero.
    if(in_array($realFile = realpath("{$file}.php"), self::$loadedExts))
      return true;

    // Si existe el archivo incluirlo y retornar verdadero
    if(is_file($realFile)){
      self::$loadedExts[] = $realFile;
      require_once $realFile;
      return true;
    }

    // Quitar slash del final de archivo
    $file = preg_replace('/(.*)\/$/', '$1', $file);

    // Si existe el archivo retornar el mismo
    if(in_array($realFileConf = realpath("{$file}/am.conf.php"), self::$loadedExts))
      return true;

    // Si existe el archivo .conf para dicha ruta retornar se intentara incluir como una extension
    if(is_file($realFile = $realFileConf)){

      // Obtener la configuracion de la extencion
      $conf = require($realFile);

      // Obtener las funciones para mezclar que s definirán
      $mergeFunctions = itemOr('mergeFunctions', $conf, array());

      // Los items nuevos no sobreescriben los anteriores
      self::$mergeFunctions = array_merge($mergeFunctions, self::$mergeFunctions);

      // Obtener dependencias
      $requires = itemOr('requires', $conf, array());

      // Incluir las dependencias
      self::requireExt($requires);

      // Extender propiedades por defecto
      $extend = itemOr('extend', $conf, array());
      self::mergeProperties($extend, true);

      // Obtener archivos a agregar de la extencion
      $files = itemOr('files', $conf, array());

      // Llamar archivo de iniciacion en la carpeta si existe.
      foreach ($files as $item)
        if(is_file($realFile = "{$file}/{$item}.php"))
          require_once $realFile;
        else
          throw Am::e('AM_NOT_FOUND_FILE_EXTS', $realFile, $file);

    }

    // Incluir archivo init si existe
    if(is_file($realFile = realpath("{$file}/am.init.php"))){
      $conf = true;
      // Incluir el archivo init.
      $init = require_once($realFile);
      // Si es un array entonces representan parametros que extender del conf global.
      if(is_array($init))
        self::mergeProperties($init, true);

    }

    // Si esta definida la variable $conf, entonces retornar verdadero.
    if(isset($conf)){
      self::$loadedExts[] = $realFileConf;
      return true;
    }

    // De lo contrarion no se pudo cargar la extension
    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Carga una o varias extensión. Antes de cargar se verifica si el nombre
   * recibido por parámetro es un alias de otra extensión. Si la extensión no
   * existe se busca en cada uno de los directorios del entorno
   * ---------------------------------------------------------------------------
   * @param   string/string array() $name Nombre o alias de la extensión que se
   *                                desea cargar.
   */
  public static function requireExt($name){

    // Si se trata de una array recorrer para agregar dependencias una por una.
    if(is_array($name)){

      // Incluir dependencias recursivamente
      foreach ($name as $value)
        self::requireExt($value);

      return;

    }

    // Obtener el nombre del archivo si existe un alias con nombre de la extensión a acargar
    if(isset(self::$aliasExts[$name]))
      $name = self::$aliasExts[$name];

    // Agregar extension
    if(self::load($name))
      return;

    // Las extensiones de deben buscar comenzando el el directorio más
    // recientemente agregado
    $pathsInv = array_reverse(self::$dirs);

    // Buscar un archivo dentro de las carpetas
    foreach($pathsInv as $path)
      if(self::load("{$path}/{$name}"))
        return;

    // No se agregó la extension
    throw Am::e('AM_NOT_FOUND_EXT', $name);

  }

  /////////////////////////////////////////////////////////////////////////////
  // Manejo de credenciales
  /////////////////////////////////////////////////////////////////////////////

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la instancia del manejador de credenciales.
   * ---------------------------------------------------------------------------
   * @return object   Instancia del manejador de credenciales.
   */
  public static function getCredentialsHandler(){

    return self::ring('credentials.handler');

  }
  
  /**
   * ---------------------------------------------------------------------------
   * Devuelve las credenciales actuales.
   * ---------------------------------------------------------------------------
   * @return  object  Instancia de las credenciales actuales.
   */
  public static function getCredentials(){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::getCredentialsHandler()))
      return null;

    // Devuelve la instancia del usuario actual
    return $credentialsManager->getCredentials();

  }
  
  /**
   * ---------------------------------------------------------------------------
   * Devuelve si está autenticado un usuario.
   * ---------------------------------------------------------------------------
   * @return bool
   */
  public static function isAuth(){

    return null !== self::getCredentials();

  }
  
  /**
   * ---------------------------------------------------------------------------
   * Devuelve si hay un usuario autenticado y si tiene las credenciales
   * recividas por parámetro
   * ---------------------------------------------------------------------------
   * @param   string/string array   $credential String o array de strings con
   *                                            las credenciales a consultar
   * @return  bool
   */
  public static function hasCredentials($credential){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::getCredentialsHandler()))
      return false;

    // Verificar si el usuario logeado tiene las credenciales
    return $credentialsManager->hasCredentials($credential);

  }

  /**
   * ---------------------------------------------------------------------------
   * Ejecuta una tarea.
   * Si la tarea tiene configurado targets, y no se indica el target a ejecutar,
   * Se ejecutará todos los targets con los argumentos del array $argv.
   * ---------------------------------------------------------------------------
   * @param  string $task Comando a ejecutar y target a utilizar
   *                      Ejemplo: copy:app, implica que se ejecutará la función
   *                      task_copy con el target indicado en app.
   * @param  array  $argv Argumentos con los que se ejecutará la tarea. 
   * @return string       Mensaje de retorno de la tarea.
   */
  public static function taskArray($task, array $argv = array()){

    // Obtener los targets del archivo de configuracion
    $tasksConfigs = self::getProperty('tasks', array());

      // El comando puede ser indicado con un target especifico:
    // $task='comando:target' Dividir para obtener target
    $target = explode(':', $task);
    $task = array_shift($target);    // La primera parte es el nombre de la tarea
    $target = array_shift($target); // El siguiente elemento es el target. Si no
                                    // existe es null.
    
    // Obtener configuración
    $options = itemOr($task, $tasksConfigs);

    // Si requires alguna extension se carga
    if(isset($options['requires']) && is_array($options['requires'])){
      self::requireExt($options['requires']);
    }

    // Procesar preprar argumentos, si la configuración existe
    if(isset($options['args'])){

      $params = array();
      foreach ($options['args'] as $paramName => $value){

        // Obtener tipo y valor por defecto de la configuración
        $value = explode(':', $value);
        $value[0] = trim(itemOr(0, $value));
        $value[1] = trim(itemOr(1, $value));
        $value[2] = trim(itemOr(2, $value));
        list($p, $t, $v) = $value;

        // Buscar nombre dentro de los argumentos
        $index = array_search($p, $argv);

        if($index!==false && $argv[$index] === $p){
          // Se busca la posicion del index encontrado dentro del array
          // Para buscar el valor que le sigue, tomarlo y luego eliminara
          // ambos de los argumentos
          $keys = array_keys($argv);
          $indexIndex = array_search($index, $keys);
          $nextIndex = itemOr($indexIndex+1, $keys);

          // Se toma el valor
          $v = itemOr($nextIndex, $argv, $v);

          // Se elimina el argumento
          unset($argv[$index]);
          unset($argv[$nextIndex]);
          unset($argv[$paramName]);

        // Si los argumentos tiene un campo con el nombre dle parametro,
        }elseif(isset($argv[$paramName])){
          
          // Se toma el valor
          $v = $argv[$paramName];

          // Se elimina el argumento
          unset($argv[$paramName]);

          $index = true;

        }else{

          $index = false;

        }
        // Guardar valores preparados
        $params[$paramName] = array(
          'loaded' => $index!==false,
          'type' => $t,
          'value' => $v
        );

      }

      // Cargar valores restantes y convertir al tipo de dato adecuado.
      foreach ($params as $paramName => $value){

        // Si no se ha asignado valor al parametro, se toma el siguiente valor
        // de los argumentos recibiso ($argv);
        if(!$value['loaded'] && isset($argv[0]))
          $value['value'] = array_shift($argv);

        switch ($value['type']) {
          case 'int':
            $value = intval($value['value']);
            break;
          case 'float':
            $value = floatval($value['value']);
            break;
          case 'bool':
            $value = in_array(''.$value['value'], array('true', '1'));
            break;
          default:
            $value = $value['value'];
            break;
        }
        $params[$paramName] = $value;

      }
        
      // Obtener solo los valores
      $argv = array_merge($params, $argv);

    } 

    // Si esta definido el attributo $options['compound'], entonces ejecutar
    // como una tarea compuesta.
    if(isset($options['compound']) && is_array($options['compound'])){

      $ret = array();
      // Ejecutar cada comando
      foreach ($options['compound'] as $key => $compoundTask)
        $ret[$key] = self::taskArray($compoundTask['task'],
                             array_merge($argv, $compoundTask['args']));

      return $ret;

    }else{

      // Determinar el nombre de la funcion que ejecuta el comando
      $functionName = "task_{$task}";

      // Incluir el archivo si existe
      $functionFile = self::findTask($task);

      if($functionFile && !function_exists($functionName))
        require_once $functionFile;

      // Si la funcion no existe mostrar error
      if(!function_exists($functionName))
        throw Am::e('AM_NOT_FOUND_COMMAND', $task);

      // Si el target esta indicado
      if(isset($target) && isset($options['targets'][$target])
         && is_array($options['targets'][$target])){

        // argumentos del target
        $targetArgs = $options['targets'][$target];

        // Llamar funcion con los argumentos mezclados
        return call_user_func_array($functionName,
                                    array_merge($argv, $targetArgs));

      // Sino se definió el target, pero existen targets en la configuracion para el comando
      }elseif(isset($options['targets']) && is_array($options['targets'])){

        $ret = array();
        // Ejecutar el comando con todos los targets en la configuracion
        foreach($options['targets'] as $key => $targetArgs)

          // Llamar funcion con los argumentos mezclados
          $ret[$key] = call_user_func_array($functionName,
                                            array_merge($argv, $targetArgs));

        return $ret;

      }else{

        // Llamado de la función
        return call_user_func_array($functionName, $argv);

      }

    }

    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Ejecuta de una tarea  e imprime su resultado
   * ---------------------------------------------------------------------------
   * @param  string $task Nombre de la tarea a ejecutar y/o target/
   * @param         ...  El resto de los parámetros son los argumentos para la
   *                     tarea a ejecutar.
   */
  public static function task($task /*, ... */){

    $arguments = func_get_args();
    array_shift($arguments);
    return self::taskArray($task, $arguments);

  }

  /////////////////////////////////////////////////////////////////////////////
  // Manejo de session
  /////////////////////////////////////////////////////////////////////////////

  /**
   * ---------------------------------------------------------------------------
   * Realiza incializaciones para el manejo de session
   * ---------------------------------------------------------------------------
   */
  // PENDIENTE: Revisar
  public static function startSession(){

    // Si ya esta cargada la clase AmSession es porque
    // ya se realizó la inicializacion.

    if(class_exists('AmSession'))
      return;

    self::requireExt(array(
      // Incluir extension desde el aclias
      self::$confs['sessionManager'],
      // Incluir manejador principal de session
      'core/am_session'
    ));

  }

  /////////////////////////////////////////////////////////////////////////////
  // Inicio de Amathista
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * ---------------------------------------------------------------------------
   * Carga todas las dependencia de una aplicación, y carga el scrit de inicio
   * si este existe.
   * ---------------------------------------------------------------------------
   * @param  string $appRoot directorio raíz de la aplicación.
   */
  public static function app($appRoot = null){

    // Preparar petición
    self::getUrlBase();
    self::getRequest();

    // Cambiar directorio de trabajo si este fue asignado
    if(isset($appRoot)){
      self::addDir($appRoot);
      
      // Moverse a la carpeta de la aplicación
      @chdir($appRoot);

      // Define el directorio de la papelera
      @define('AM_TRASH', realpath($appRoot).'/.trash');

    }

    // Obtener las configuraciones
    self::mergePropertiesFromAllFiles();

    // Obtener el valor
    $errorReporting = self::getProperty('errorReporting');
    @error_reporting($errorReporting);
    
    // Incluir extensiones para peticiones archivos requeridos
    self::requireExt(self::getProperty('requires', array()));
    $files = self::getProperty('files', array());

    foreach ($files as $file)
      require_once self::findFile("{$file}.php");

    // Include init file at app root if exists
    if(is_file($initFilePath = 'am.init.php'))
      require_once $initFilePath;

  }

  /**
   * ---------------------------------------------------------------------------
   * Procede a despachar la petición. Si es una petición HTTP procede a evaluar
   * la ruta. Si se ejecuta por linea de comandos con argumentos se ejecuta como
   * comando de Amathista. Por último sino es ninguno de los casos anteriores se
   * entrara en el intérprete de php con todas las dependencias de la
   * aplicación cargadas.
   * ---------------------------------------------------------------------------
   */
  public static function run(){

    // Variable global de argumentos
    global $argv;

    // Es una peticion desde la consola
    if(isset($argv)){

      if(count($argv) == 1){

        // Entrar el en intérprete
        
        echo 'Amathista '.AM_VERSION.". Command Line\n";

        while(1){
          try{
            // Obtener el comando enviado
            $line = trim(fgets(STDIN));
            // Ejecutarlo
            print_r(eval("return {$line};"));

          // Si se captura un error mostrarlo
          }catch(Exception $e){
            echo $e->getMessage() . "\n";
          }
          echo "\n";
        }

      }else{

        // Ejecutar comando
        array_shift($argv);
        $cmd = array_shift($argv);
        self::taskArray($cmd, $argv);

      }

    }else{

      // Llamado de accion para evaluar ruta
      self::ring('route.evaluate', self::getRequest());
      
    }

  }

  /////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  /////////////////////////////////////////////////////////////////////////////

  /**
   * ---------------------------------------------------------------------------
   * Busca la primera ocurrencia de un archivo dentro de array de directorios.
   * ---------------------------------------------------------------------------
   * @param  string $file Archivo que se desea buscar.
   * @return string       Dirección del primer archivo encontrado
   */
  public static function findFile($file){

    return findFileIn($file, self::$dirs);

  }

  /**
   * ---------------------------------------------------------------------------
   * Busca la primera ocurrencia de un tarea dentro del array de directorios de
   * tareas.
   * ---------------------------------------------------------------------------
   * @param  string $task Tarea a buscar.
   * @return string       Dirección del primer archivo tarea encontrado.
   */
  public static function findTask($task){

    return findFileIn("{$task}.task.php", self::$tasksDirs);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve el tipo MIME de un archivo según su extensión.
   * ---------------------------------------------------------------------------
   * @param  string $filename Nombre del archivo del cual se desea obtener el 
   *                          Tipo MIME
   * @return String           Tipo mime del archivo según la extensión o falso
   *                          si no concuerda con ninguna extensión.
   */
  public static function mimeType($filename) {

    $mimePath = self::findFile('rsc/mime.types');
    $fileext = substr(strrchr($filename, '.'), 1);
    if (empty($fileext)) return false;
    $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
    $lines = file($mimePath);
    foreach($lines as $line) {
      if (substr($line, 0, 1) == '#') continue; // skip comments
      $line = rtrim($line) . ' ';
      if (!preg_match($regex, $line, $matches)) continue; // no match to the extension
      return ($matches[1]);
    }
    return false; // no match at all

  }

}

/**
 * -----------------------------------------------------------------------------
 * Se linkea los callbacks responde con archivo y responder con descargar de
 * archivo.
 * -----------------------------------------------------------------------------
 */
Am::on('render.template',   'Am::render');

Am::addTasksDir(dirname(__FILE__).'/tasks/');
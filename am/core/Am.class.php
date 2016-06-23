<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase principal de Amathista
 */
final class Am{

  protected static

    /**
     * Callbacks de eventos globales del sistema.
     */
    $callbacks = array(

      // Cuando se agrega un nuevo directorio de clases
      'core.loadedPath' => null, // $dir

      // Cuando se incluye un archivo para una clase
      'core.loadedClass' => null, // $class

      // Evalucación de ruta
      'route.evaluate' => null, // $request

      // Agregar un pre procesador de rutas
      'route.addPreProcessor' => null, // $type, $callback

      // Agregar un despachador de ruta en base su key
      'route.addDispatcher' => null, // $type, $callback
      
      // Responder con archivo
      'response.file' => null, // $filename, $attachment, $name, $mimeType

      // Responder con una llamada
      'response.call' => null, // $callback, $env, $params

      // Responde con el renderizado de una vista
      'response.template' => null, // $tpl, $vars, $options, $checkView

      // Responder con una redirección
      'response.go' => null, // $url

      // Responder con un archivo assets compuesto
      'response.assets' => null, // $assets

      // Responder con un error 404
      'response.e404' => null, // $msg

      // Responder con un error 403
      'response.e403' => null, // $msg

      // Responder con controlador
      'response.controller' => null, // $action, $params, $env

      // Renderiza de una vista
      'render.template' => null, // $__tpl, $__vars, $__options

      // Obtener la instancia de una sesion
      'session.get' => null, // $id
      
      // Obtener una instancia del manejador de credenciales
      'credentials.get' => null, // $name
      
    ),

    /**
     * Definición de callbacks a utilizar para mezclar atributos.
     */
    $mergeFunctions = array(
      'autoload' => 'array_merge',
      'requires' => 'merge_if_snd_first_not_false',
      'dirs' => 'merge_unique',
      'env' => 'merge_if_both_are_array',
      'tasks' => 'array_merge_recursive',
      'formats' => 'array_merge',
      'aliases' => 'merge_if_snd_first_not_false',
    ),

    /**
     * Array de extensiones cargadas
     */
    $loadedExts = array(),

    /**
     * Instancias unicas de clases
     */
    $instances = array(),

    /**
     * Directorios de entorno.
     */
    $dirs = array(),

    /**
     * Hash de paths de las clases.
     */
    $pathClasses = array(),

    /**
     * Directorios de tareas
     * Este es un array que contiene d
     */
    $tasksDirs = array(),

    /**
     * Archivos de configuración cargados
     */
    $confsLoaded = array(),

    /**
     * Propiedades/Configuraciones globales cargadas
     */
    $confs = array(),

    /**
     * URL base de la aplicación
     */
    $urlBase = null,

    /**
     * Petición realizada
     */
    $requestStr = null,

    /**
     * Indica si ya se inicialicó la aplicación
     */
    $apped = false,

    /**
     * Hash de instancias de las sesiones.
     */
    $sessions = array(),

    /**
     * Instancia de AmObject que tiene como propiedades los valores los arrays
     * respectivos.
     */
    $server = null,
    $get = null,
    $post = null,
    $cookie = null,
    $request = null,
    $env = null;

  /**
   * Función para cargar clases.
   * @param string $className Nombre de la clase a cargar.
   */
  public static function autoload($className){

    // Si existe el path cargarlo.
    if(isset(self::$pathClasses[$className])){

      // Incluir el archivo
      require_once self::$pathClasses[$className];

      // Emitir evento indicando que se incluyó la clase
      self::emit('core.loadedClass', $className);

    }

  }

  /**
   * Carga todas las clases detectadas.
   * @return hash Hash de clases con listado de clases que no existen.
   */
  public static function loadAllClasses(){

    $ret = array();
    // Recorrer todas las clases detectadas y cargarlas.
    foreach (self::$pathClasses as $className => $path)
      $ret[$className] = class_exists($className);

    return $ret;
    
  }

  /**
   * Agrega los paths de las clases en el directio a hash de path de clases.
   * Las clases son identificadas como archivos con extensión .class.php.
   * @param string/array $dir       Directorio o listado de directosio a
   *                                verificar.
   * @param bool         $recursive Si se busca en directorios internos
   */
  public static function loadPathClases($dir, $recursive = false){

    // Obtener los paths de las clases en el directorios.
    $classes = amGlob($dir, array(
      'recursive' => $recursive,
      'include' => '/(.*)\.class\.php$/',
      'return' => 1
    ));

    // Agregar los paths
    foreach ($classes as $path)
      self::$pathClasses[basename($path)] = $path.'.class.php';

    // Emitir evento indicando que se cargó el directorio de clases
    self::emit('core.loadedPath', $dir);

    return $classes;

  }

  /**
   * Devuelve el directorio donde existe una clase.
   * @param string $className Nombre de la clase a cargar.
   */
  public static function whereIs($className){

    return itemOr($className, self::$pathClasses);

  }

  /**
   * Devuelve una texto con un determinado formato
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
   * Devuelve un error con el mensaje obtenido del llamado del método Am::t con
   * los parámetros de esta función.
   * @params   Utilizados para generar el texto del mensaje
   * @return   Una instancia de la clase AmError con el mensaje del texto
   *           obtenido
   */
  public static function e(/* Parametros */){

    return new AmError(call_user_func_array(array('Am', 't'), func_get_args()));

  }

  /**
   * Devuelve un campo statico interno de la clase o un valor de un key
   * específico de dicho campo.
   * @param   string  $fieldName  Nombre del campo a retornar.
   * @param   string  $key        Key a consultar dentro del campo.
   * @param   mixed   $defValue   Valor a retornar si se pasa pide el valor de
   *                              un campo específico pero no existe.
   * @return  Valor de la variable estática de la clase o valor en el key
   *          en la posicion indicada por key
   */
  public static function g($fieldName, $key = null, $defValue = null){

    if(!isset($key))
      return self::$$fieldName;

    if(isset(self::$$fieldName->$key))
      return self::$$fieldName->$key;

    if(is_array(self::$$fieldName) && isset(self::$$fieldName[$key]))
      return self::$$fieldName[$key];

    return $defValue;

  }

  /**
   * Inicializa las variables de AmResponse
   */
  public static function start(){

    self::loadPathClases(realpath(AM_ROOT . '/core/'));

    self::$server = new AmObject($_SERVER);
    self::$cookie = new AmObject($_COOKIE);
    self::$env = new AmObject($_ENV);

    $vars = array(
      'get' => $_GET,
      'post' => $_POST,
      'request' => $_REQUEST,
    );

    // Hacer los parámetros GET y POST seguros.
    foreach($vars as $key => $arr){

      // PENDIENTE Revisar como hacer los parámetros seguros
      foreach($arr as $i => $value)
        $arr[$i] = $value;

      self::$$key = new AmObject($arr);

    }

  }

  /**
   * Devuelve el método de la peticion.
   */
  public static function getMethod(){
    return strtolower(self::$server->REQUEST_METHOD);
  }

  /**
   * Devuelve la instancia de una clase existente. Sino existe la instancia se
   * crea una nueva.
   * @param  string $className Nombre de la clase de la que se desea obtener
   *                           la instancia.
   * @param  array  $params    Parámetros para instancia la clase.
   * @return object            Objeto instanciado
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
   * Asigna un callback de un evento global.
   * @param string   $action   Nombre del evento a atender.
   * @param callback $callback Callback a asociar con el evento.
   */
  public static function on($action, $callback){

    self::$callbacks[$action] = $callback;

  }

  /**
   * Llamar el callback de un evento global.
   * @param  string $action Nombre dle evento a llamar.
   * @param  ...            El resto de los parámetros son utilizados como
   *                        argumentos de la llamada del callback.
   * @return mixed          Lo retornado por el callback correspondiente.
   */
  public static function emit($action /* Resto de los parametros*/){
    
    // Obtener los parámetros
    $options = func_get_args();
    
    // Quitar el primer parametros, corresponde a $action
    array_shift($options);

    // Obtener callback
    $callback = itemOr($action, self::$callbacks);

    // Si existe callbacks definidas para la accion
    if(isValidCallback($callback))

      // Llamar los callbacks
      return call_user_func_array($callback, $options);

  }

  /**
   * Agrega una extensión de las configuraciones.
   * Estas configuraciones son las tomadas inicialmente al momento de mezclar.
   * Las primeras configuraciones cargadas prevalecen sobre las nuevas.
   * @param string $property Nombre de la propiedad a cargar.
   */
  public static function extendProperties(array $conf, $from){

    // Recorrer la configuracion para ir agregandolas a la extensión.
    foreach ($conf as $property => $propertyValue) {

      // Inicializar la propiedad si no existe.
      if(!isset(self::$confsLoaded[$property]))
        self::$confsLoaded[$property] = array();

      // Empilar la configuración.
      array_unshift(self::$confsLoaded[$property], $propertyValue);

    }

    // Reiniciar las configuraciones globales
    self::$confs = array();
  }

  /**
   * Enlaza varios eventos a determinados callbacks.
   * @param  hash $binds Hash de eventos=>callbacks a enlazar.
   */
  public static function bind(array $binds){

    foreach ($binds as $event => $callback)
      Am::on($event, $callback);

  }

  /**
   * Realiza la mezcla de una propiedad.
   * @param string $property Nombre de la propiedad a cargar.
   */
  public static function mergeProperty($property){

    // Si ya esta mezclado el valor retornar 
    if(isset(self::$confs[$property]))
      return;

    // Obtener las configuraciones iniciales
    $confs = itemOr($property, self::$confsLoaded, array());

    // Recorrer cada uno de las carpetas en el path
    foreach(self::$dirs as $path){
      // Buscar en el archivo de configuración am y en el correspondiente a la
      // propiedad
      foreach(array_unique(array('am', $property)) as $file){
        // Obtener el nombre real del archivo
        $filename = realpath("{$path}/{$file}.conf.php");
        // Si tiene una configuración cargada
        if(isset(self::$confsLoaded[$filename])){
          // Si no es una configuración de am se toma la configuración completa
          if($file !== 'am'){
            $confs[] = self::$confsLoaded[$filename];
          // De lo contrario se toma solo la propiedad deseada si es que existe
          }elseif(isset(self::$confsLoaded[$filename][$property])){
            $confs[] = self::$confsLoaded[$filename][$property];
          }
        }
      }
    }

    // Si no se obtuvo alguna configuración se sale
    if(empty($confs))
      return;

    // Obtener funcion callback para mezclar la propiedad solicitada
    $mergeFunction = itemOr($property, self::$mergeFunctions);

    // Si s obtuvo una sola configuración o no existe una función de mezclado
    // se devuelve la ultima configuración cargada.
    if(count($confs) === 1 || $mergeFunction === null){
      $conf = end($confs);

    }else{
      // Proceder a mezclar
      $conf = array();

      foreach($confs as $newConf)
        $conf = call_user_func_array($mergeFunction, array($conf, $newConf));

    }

    self::$confs[$property] = $conf;

  }

  /**
   * Devuelve el valor de una propiedad de aplicación.
   * @param  string $property Nombre de la propiedad a cargar.
   * @param  mixed  $default  Valor por defecto a devolver sino existe un valor
   *                          para la propiedad.
   * @return mixed            Valor de la propiedad evaluada recursivamente
   *                          después de incluir los archivos de configuración
   *                          adecuados.
   */
  public static function getProperty($property, $default = null){

    // Cargar las configuraciones de las propiedad
    self::loadAllConfFilesOfProperty($property);

    // Realizar la mezcla de las propiedades
    self::mergeProperty($property);

    // Devolver propiedad
    return itemOr($property, self::$confs, $default);

  }

  /**
   * Devuelve el directorio correspondiente a un nombre
   * @param  string $name Nombre del directorio deseado.
   * @return return       Directorio deseado o null si no existe.
   */
  public static function getDir($name){

    $dirs = self::getProperty('dirs');

    return itemOr($name, $dirs);

  }

  /**
   * Carga un archivo de configuración.
   * @param string $filename Archivo de configuración a cargar.
   * 
   */
  public static function loadConfFile($filename){

    // Obtener el nombre real del archivo
    $filename = realpath($filename);

    // Si el archivo no existe salir
    if(!is_file($filename) || isset(self::$confsLoaded[$filename]))
      return;

    // Cargar archivo de configuracion
    $conf = require $filename;

    // Agregar el archivo a la lita de archivos de configuracion cargados
    self::$confsLoaded[$filename] = $conf;

  }

  /**
   * Devuelve el contenido de un archivo de configuración
   */
  public static function getConf($filename){

    return require "{$filename}.conf.php";

  }

  /**
   * Carga los archivos de configuración de una propiedad.
   * @param string $property Nombre de la propiedad a cargar.
   * 
   */
  public static function loadAllConfFilesOfProperty($property = 'am'){

    // Recorrer cada uno de las carpetas en el path
    foreach(self::$dirs as $path)
      // Si el archivo cargar la configuracion en la posicion path/property
      self::loadConfFile("{$path}/{$property}.conf.php");

  }

  /**
   * Agrega un directorio al entorno de la aplicación.
   * @param string $dir Directorio a agregar.
   */
  public static function addDir($dir){

    self::$dirs[] = realpath($dir);
    self::$dirs = array_unique(self::$dirs);

    // Cargar configuraciones de am.conf.file
    self::loadAllConfFilesOfProperty();

    // Reiniciar configuraciones globales.
    self::$confs = array();

  }

  /**
   * Crear un directorio
   * @param  [type] $dir [description]
   * @return [type]      [description]
   */
  public static function mkdir($dir, $perms = 0755, $recursive = true){

    // Se verifica que el directorio no sea una ruta de una archivo
    if(is_file($dir))
      throw Am::e('AM_DIR_IS_FILE', $dir);

    // Se verifica que el directorio sea escribible
    // if(!is_writable($dir))
    //   throw Am::e('AM_DIR_NOT_IS_WRITABLE', $dir);

    // Crear carpeta si no existe
    if(!is_dir($dir))
      return mkdir($dir, $perms, $recursive);

    return true;

  }

  /**
   * Agrega un directorio al tareas de la aplicación.
   * @param string $dir Directorio a agregar.
   */
  public static function addTasksDir($dir){

    self::$tasksDirs[] = realpath($dir);
    self::$tasksDirs = array_unique(self::$tasksDirs);

  }

  /**
   * Mueve un archivo a la papelera.
   * Este archivo se ubicará en la papelera relativo al directorio en común del
   * mismo con el directorio actual.
   * @param  string $file Archivo a mover a la papelera
   * @return bool         Si se pudo mover o no el archivo.
   */
  public static function sendToTrash($file){

    // Obtener nombre verdadero de archivo
    $file = realpath($file);
    // Salir si no existe el archivo
    if(!$file) return;

    // Si AM_TRASH esta definida como false, indica que no se desea tener una
    // papelera
    if(!defined('AM_TRASH') || !AM_TRASH)
      return !!unlink($file);

    // Definir directorio de la papelera
    $trahsFolder = AM_TRASH.'/'.date('Ymd');

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
    
    // Crear el directorio
    Am::mkdir(dirname($dest));

    // Mover archivo
    return !!rename($file, $dest);
    
  }

  /**
   * Asigna o sustituye un dispatcher a un tipo de ruta
   * @param  string   $type       Nombre del tipo al que se agrega el callback.
   * @param  callback $dispatcher Despachador a agregar
   * @return mixed                Valor devuelvo por el manejador del evento.
   */
  public static function addRouteDispatcher($type, $dispatcher){
    return self::emit('route.addDispatcher', $type, $dispatcher);
  }

  /**
   * Asigna un pre-procesador de rutas a un tipo
   * @param  string   $type         Nombre del tipo al que se agrega el
   *                                callback.
   * @param  callback $preProcessor Preprocesador.
   * @return mixed                  Valor devuelvo por el manejador del evento.
   */
  public static function addRoutePreProcessor($type, $preProcessor)  {
    return self::emit('route.addPreProcessor', $type, $preProcessor);
  }

  /**
   * Responde con un archivo indicado por parámetro.
   * @param  string   $file       Ruta del archivo con el que se responderá.
   * @param  bool     $attachment Si la ruta se descarga o no.
   * @param  string   $name       Nombre con el que se entregará el archivo.
   * @param  mimeType $mimeType   Tipo mime para la descarga.
   * @return mixed                Respuesta de manejador configurado.
   */
  public static function file($filename, $attachment = false, $name = null,
    $mimeType = null){

    return self::emit('response.file', self::findFile($filename), $attachment,
      $name, $mimeType);

  }

  /**
   * Responde con la uníon de varios archivos indicados en la propiedad assets.
   * @param  string $name Nombre de assets a devolver
   * @return string       Contenido de los archivos correspondientes al assets
   *                      concatenados.
   */
  public static function assets($name){

    return self::emit('response.assets', $name);

  }

  /**
   * Responde la descarga de un archivo indicado por parámetro.
   * @param  string   $file     Ruta del archivo a descargar.
   * @param  string   $name     Nombre con el que se entregará el archivo.
   * @param  mimeType $mimeType Tipo mime para la descarga.
   * @return mixed              Respuesta de manejador configurado.
   */
  public static function download($file, $name = null, $mimeType = null){

    return self::file($file, true, $name, $mimeType);

  }

  /**
   * Busca una llamada como función, método estático de una clase o llamada
   * a controlador.
   * @param  string $callback String que identifica el controlador a buscar.
   * @param  array  $env      Variables de entorno.
   * @param  array  $params   Argumentos obtenidos de la ruta.
   * @return mixed            Respuesta de manejador configurado.
   */
  public static function call($callback, array $env = array(),
                              array $params = array()){

    return self::emit('response.call', $callback, $env, $params);

  }

  /**
   * Busca un template y lo renderiza.
   * @param  string $tpl       Template a renderizar.
   * @param  array  $vars      Variables de la vista.
   * @param  array  $options   Opciones para la vista.
   * @param  array  $checkView Indica si se desea o no chequear si la vista
   *                           existe.
   * @return mixed             Respuesta de manejador configurado.
   */
  public static function template($tpl, array $vars = array(),
                                  array $options = array(), $checkView = true){

    return self::emit('response.template',
      self::findFile($tpl), $vars, $options, $checkView);

  }

  /**
   * Redirigir a una URL.
   * @param string $url URL que se desea ir.
   */
  public static function go($url){

    return self::emit('response.go', $url);

  }

  /**
   * Redirigir a una URL formateandola con self::url.
   * @param string $path URL de la app que se desea redirigir.
   */
  public static function redirect($url){

    return self::go(self::url($url));

  }

  /**
   * Responde con un error 404.
   * @param  string $msg Mensaje de error a mostrar
   * @return mixed       Respuesta de manejador configurado.
   */
  public static function e404($msg = null){

    return self::emit('response.e404', $msg);

  }

  /**
   * Responde con un error 403.
   * @param  string $msg Mensaje de error a mostrar
   * @return mixed       Respuesta de manejador configurado.
   */
  public static function e403($msg = null){

    return self::emit('response.e403', $msg);

  }

  /**
   * Llamada de un controlador.
   * @param  string $action String que identifica la acción a ejecutar. Tiene
   *                        el formato 'Controlador@action'
   * @param  array  $env    Variables de entorno.
   * @param  array  $params Argumentos obtenidos de la ruta.
   * @return mixed          Respuesta de manejador configurado.
   */
  public static function controller($action, array $env = array(),
                                    array $params = array()){

    return self::emit('response.controller', $action, $env, $params);

  }

  /**
   * Renderiza un a vista.
   * @param string $__tpl     Template a renderizar.
   * @param array  $__params  Parámetros para la vista.
   * @param array  $__options Opciones de la vista.
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
   * Obtiene la petición realizada. Si se esta en la línea de comandos, entonces
   * la petición es vacía.
   * @return string Petición.
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

      self::$requestStr = array(
        'method' => strtoupper($_SERVER['REQUEST_METHOD']),
        'request' => $request,
      );

    }

    return self::$requestStr;

  }
  
  /**
   * Devuelve la url base de la aplicación.
   * @return string URL Base de la aplicación.
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
   * Contruye una URL de la aplicación.
   * @param  string $url URL de la app que se desea obtener.
   * @return string      URL Construída
   */
  public static function url($url = ''){

    return self::$urlBase.$url;

  }

  /**
   * Imprime una URL.
   * @param string $url  URL de la app que se desea imprimir.
   */
  public static function eUrl($url = ''){

    echo self::url($url);

  }

  /**
   * Devuelve una URL absoluta incluyendo el nombre del servidor y el protocolo
   * de conexión.
   * @param  string $url URL de la app que se desea obtener.
   * @return string      URL Construída.
   */
  public static function serverUrl($url = ''){

    if(isset($_SERVER['SERVER_NAME']))
      return 'http://' . $_SERVER['SERVER_NAME'] . self::url($url);

    return self::url($url);

  }

  /**
   * Imprime una URL absoluta incluyendo el nombre del servidor y el protocolo
   * de conexión.
   * @param string $url URL de la app que se desea imprimir.
   */
  public static function eServerUrl($url = ''){

    echo self::serverUrl($url);

  }

  /**
   * Redirigir a una URL formateandola con self::url si $condition es verdadero
   * @param bool   $condition Resultado de la condición evaluada.
   * @param string $url       URL de la app que se desea redirigir.
   */
  public static function redirectIf($condition, $url){

    if($condition)
      return self::redirect($url);

  }

  /**
   * Redirigir a una URL formateandola con self::url si $condition es falsa.
   * @param bool   $condition Resultado de la condición evaluada.
   * @param string $url       URL de la app que se desea redirigir.
   */
  public static function redirectUnless($condition, $url){

    return self::redirectIf(!$condition, $url);

  }

  /////////////////////////////////////////////////////////////////////////////
  // Inclusion de archivos y extenciones
  /////////////////////////////////////////////////////////////////////////////

  /**
   * Carga un extensión indicada por parámetro. Devuelve verdadero o false 
   * dependiendo de si se logró cargar o no la extensión. La carga de la
   * extensión se realiza verificando la existencia del archivo "{$file}.php"
   * que indica una extención simple, o la existencia de archivo
   * "{$file}.conf.php" que indica una extensión compuesta.
   * @param  string $file Ruta de la extensión que se desea cargar.
   * @return bool         Si se cargo o no la extensión.
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

    // Si ya fué cargado retornar verdadero.
    if(in_array($realDir = realpath(dirname("{$file}/am")), self::$loadedExts))
      return true;

    // Si es un directorio incluir dentro de los directorios de clases
    if(is_dir($realDir))
      $conf = self::loadPathClases($realDir, false);

    // Si existe el archivo retornar el mismo
    if(in_array($realFileConf = realpath("{$file}/am.conf.php"), self::$loadedExts))
      return true;

    // Si existe el archivo .conf para dicha ruta retornar se intentará incluir
    // como una extensión
    if(is_file($realFile = $realFileConf)){

      // Obtener la configuracion de la extencion
      $conf = require_once $realFile;

      // Si el valor devuelto es un array
      if(is_array($conf)){

        // Obtener las funciones para mezclar que se definirán
        $mergeFunctions = itemOr('mergeFunctions', $conf, array());

        // Los items nuevos no sobreescriben los anteriores
        self::$mergeFunctions = array_merge($mergeFunctions, self::$mergeFunctions);

        // Obtener dependencias e incluirlas
        self::requireExt(itemOr('requires', $conf, array()));

        // Extender propiedades por defecto
        self::extendProperties(itemOr('extend', $conf, array()), $realFile);

        // Obtener el directorio raíz de la extensión.
        $dirbase = dirname($realFile);

        // Obtener los directorios de clases.
        $autoload = itemOr('autoload', $conf, array());

        foreach ($autoload as $path => $recursive)
          
          // Si es un archivo existente cargarlo.
          if(is_file($realFile = "{$dirbase}/{$path}"))
            require_once $realFile;

          // Cargar paths de clases en el directorio si existe.
          elseif(is_dir($dir = realpath("{$dirbase}/{$path}")))
            self::loadPathClases($dir, $recursive);

        // Linkear callbacks con eventos
        self::bind(itemOr('bind', $conf, array()));

      }

    }

    // Incluir archivo init si existe
    if(is_file($realFile = realpath("{$file}/am.init.php"))){
      
      $conf = true;
      // Incluir el archivo init.
      require_once($realFile);

    }

    // Si esta definida la variable $conf, entonces retornar verdadero.
    if(isset($conf)){
      self::$loadedExts[] = $realDir;
      return true;
    }

    // De lo contrario no se pudo cargar la extension
    return false;

  }

  /**
   * Carga una o varias extensión. Antes de cargar se verifica si el nombre
   * recibido por parámetro es un alias de otra extensión. Si la extensión no
   * existe se busca en cada uno de los directorios del entorno
   * @param string/array $name Nombre o alias de la extensión que se desea
   *                           cargar.
   */
  public static function requireExt($name){

    // Si se trata de una array recorrer para agregar dependencias una por una.
    if(is_array($name)){

      // Incluir dependencias recursivamente
      foreach($name as $value)
        self::requireExt($value);

      return;

    }

    // Obtener el nombre del archivo si existe un alias con nombre de la extensión a acargar
    $aliases = self::getProperty('aliases');
    if(isset($aliases[$name]))
      $name = $aliases[$name];

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
   * Devuelve la instancia del manejador de credenciales.
   * @param   string $name  Nombre del manejador de credenciales que desea
   *                        obtener.
   * @return  object        Instancia del manejador de credenciales.
   */
  public static function getCredentialsHandler($name = ''){

    return self::emit('credentials.get', $name);

  }

  /**
   * Asigna las credenciales para un determinado nombre.
   * @param   string $name  Nombre del manejador de credenciales que desea
   *                        asignar del cual se desea obtener las credenciales
   *                        actuales.
   */
  public static function setAuthenticated($credentials = null, $name = ''){

    self::getCredentialsHandler($name)->setAuthenticated($credentials);

  }
  
  /**
   * Devuelve las credenciales actuales.
   * @param   string $name  Nombre del manejador de credenciales que desea
   *                        obtener del cual se desea obtener las credenciales
   *                        actuales.
   * @return  object        Instancia de las credenciales actuales.
   */
  public static function getCredentials($name = ''){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::getCredentialsHandler($name)))
      return null;

    // Devuelve la instancia del usuario actual
    return $credentialsManager->getCredentials();

  }
  
  /**
   * Devuelve si está autenticado un usuario.
   * @param   string $name  Nombre del manejador de credenciales que desea
   *                        saber si está o no autenticado.
   * @return  bool          Si esta autenticado o no.
   */
  public static function isAuth($name = ''){

    return null !== self::getCredentials($name);

  }
  
  /**
   * Devuelve si hay un usuario autenticado y si tiene las credenciales
   * recividas por parámetro
   * @param   string/array $credential  String o array de strings con las
   *                                    credenciales a consultar.
   * @param   string $name              Nombre del manejador de credenciales del
   *                                    cual se desea saber si tiene una
   *                                    determinada credencial.
   * @return  bool                      Si el usuario autenticado tiene las
   *                                    credenciales consultadas.
   */
  public static function hasCredentials($credential, $name = ''){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::getCredentialsHandler($name)))
      return false;

    // Verificar si el usuario logeado tiene las credenciales
    return $credentialsManager->hasCredentials($credential);

  }

  /**
   * Ejecuta una tarea.
   * Si la tarea tiene configurado targets, y no se indica el target a ejecutar,
   * Se ejecutará todos los targets con los argumentos del array $argv.
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
      foreach($options['args'] as $paramName => $value){

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
      foreach($params as $paramName => $value){

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
      foreach($options['compound'] as $key => $compoundTask)
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
   * Ejecuta de una tarea  e imprime su resultado
   * @param string $task Nombre de la tarea a ejecutar y/o target/
   * @param        ...   El resto de los parámetros son los argumentos para la
   *                     tarea a ejecutar.
   */
  public static function task($task /*, ... */){

    $arguments = func_get_args();
    array_shift($arguments);
    return self::taskArray($task, $arguments);

  }

  /////////////////////////////////////////////////////////////////////////////
  // Inicio de Amathista
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Carga todas las dependencia de una aplicación, y carga el scrit de inicio
   * si este existe.
   * @param string $appRoot directorio raíz de la aplicación.
   */
  public static function app($appRoot = '../app'){

    // Preparar petición
    self::getUrlBase();
    self::getRequest();

    self::$apped = true;

    // Cambiar directorio de trabajo si este fue asignado
    if(isset($appRoot)){
      self::addDir($appRoot);
      
      // Moverse a la carpeta de la aplicación
      if(is_dir($appRoot))
        chdir($appRoot);

    }

    // Obtener las configuraciones
    self::loadAllConfFilesOfProperty();

    // Obtener el valor
    $errorReporting = self::getProperty('errorReporting');
    error_reporting($errorReporting);
    
    // Incluir extensiones para peticiones archivos requeridos
    self::requireExt(self::getProperty('requires', array()));

    // Cargar los paths de las clases dentro de los directorios registrados de
    // la app.
    $autoload = self::getProperty('autoload', array());

    foreach ($autoload as $path => $recursive) {

      // Obtener nombre físico del archivo.
      $file = realpath($path);
          
      // Si es un archivo existente cargarlo.
      if(is_file($file))
        require_once $file;
      
      // Cargar paths de clases en el directorio si existe.
      elseif(is_dir($file))

        self::loadPathClases($file, $recursive);

      else{

        // Obtener el nombre del path si es un archivo.
        $file = self::findFile($path);

        // Incluir si el archivo existe.
        if(is_file($file))
          require_once $file;
        
      }

    }

    // Definir constantes
    $consts = self::getProperty('consts', array());
    foreach($consts as $key => $value)
      if(!defined($key))
        define($key, $value);

    // Include init file at app root if exists
    if(is_file($initFilePath = 'am.init.php'))
      require_once $initFilePath;

  }

  /**
   * Procede a despachar la petición. Si es una petición HTTP procede a evaluar
   * la ruta. Si se ejecuta por linea de comandos con argumentos se ejecuta como
   * comando de Amathista. Por último sino es ninguno de los casos anteriores se
   * entrara en el intérprete de php con todas las dependencias de la
   * aplicación cargadas.
   */
  public static function run(){

    // Variable global de argumentos
    global $argv;

    // Inicializar aplicación si no ha sido inicializada
    if(!self::$apped)
      self::app();

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
      self::emit('route.evaluate', self::getRequest());
      
    }

  }

  /////////////////////////////////////////////////////////////////////////////
  // SESSIONES
  /////////////////////////////////////////////////////////////////////////////
    
  /**
   * Retorna la sessión del usuario para un identificador.
   * @param  string   $type Tipo de la sesión que desea obtener.
   * @param  string   $id   ID de la sesión que desea obtener. Si no se indica
   *                        se devuelve toma el ID de la aplicación.
   * @return mixed          Instancia de la sesión.
   */
  public static function session($type = 'user', $id = null){

    if(!isset($id))
      $id = self::getProperty('id');

    $id .= "_{$type}";

    if(!isset(self::$sessions[$id]))
      self::$sessions[$id] = Am::emit('session.get', $id);

    return self::$sessions[$id];

  }

  /////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  /////////////////////////////////////////////////////////////////////////////

  /**
   * Busca la primera ocurrencia de un archivo dentro de array de directorios.
   * @param  string $file Archivo que se desea buscar.
   * @return string       Dirección del primer archivo encontrado
   */
  public static function findFile($file){

    return findFileIn($file, self::$dirs);

  }

  /**
   * Busca la primera ocurrencia de un tarea dentro del array de directorios de
   * tareas.
   * @param  string $task Tarea a buscar.
   * @return string       Dirección del primer archivo tarea encontrado.
   */
  public static function findTask($task){

    return findFileIn("{$task}.task.php", self::$tasksDirs);

  }

  /**
   * Devuelve el tipo MIME de un archivo según su extensión.
   * @param  string $filename Nombre del archivo del cual se desea obtener el 
   *                          Tipo MIME
   * @return String           Tipo mime del archivo según la extensión o falso
   *                          si no concuerda con ninguna extensión.
   */
  public static function mimeType($filename) {

    $mimePath = self::findFile('rsc/mime.types');
    $fileext = substr(strrchr($filename, '.'), 1);
    if (empty($fileext)) return false;
    $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*({$fileext}\s)/i";
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
 * Se linkea los callbacks responde con archivo y responder con descargar de
 * archivo.
 */
Am::on('render.template',   'Am::render');

Am::addTasksDir(dirname(__FILE__).'/tasks/');
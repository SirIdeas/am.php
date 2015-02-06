<?php

/**
 * Clase principal de Amathista
 */

final class Am{

  public static

    // Define las callbacks del sistema
    $callbacks = array(
      // route.eval (request, routes)                   : Evalucación del route
      // response.file (file, env)                      : Responder con archivo
      // response.download (file, env)                  : Responder con descarga de archivo
      // response.assets (file, assets, env)            : Responder con archivo
      // render.form (file, tpl)                        : Renderizar formulario
      // response.control (control, action, params, env): Responder con controlador
      // render.template (templete, paths, options)     : Renderizar vista
    ),

    // Callbacks para mezclar atributos
    $mergeCallbacks = array(
      "requires" => "array_merge",
      "routes" => "array_merge_recursive",
      "assets" => "array_merge",
      "errorReporting" => null,
      "timezone" => null,
      "session" => null,
      "commands" => null,
      "control" => "array_merge_recursive",
      "smtp" => "array_merge",
      "mails" => "array_merge",
      "sources" => "array_merge",
      "validators" => "array_merge_recursive",
    ),

    $instances = array(),   // Instancias unicas de clases
    $paths = array(),       // Herarquia de carpetas
    $confsLoaded = array(), // Archivos de configuración cargados.
    $confs = array(),       // Configuraciones cargadas
    $urlBase = "";          // URL base para el sitio

    // Valores seteados por el usuario
    // $userConf = array(
    //   "routes" => array(  // Rutas definidas
    //     "env" => array(),
    //     "routes" => array()
    //   )
    // );         
    
  // Devuelve la instancia de una clase existente. Sino existe la instancia se crea una nueva
  public final static function getInstance($className, array $params = array()){

    // Si la clase no existe devolver error
    class_exists($className) or die("Am: No existe clase '{$className}'");
    
    // Si la instancia existe se devuelve
    if(isset(self::$instances[$className]))
      return self::$instances[$className];

    // Si la instancia no existe se crea una instancia de la clase
    return self::$instances[$className] = new $className($params);

  }

  // Agrega un callback a una accion
  public static function addCallback($action, $callback){

    // Inicializar si no existe
    if(!isset(self::$callbacks[$action]))
      self::$callbacks[$action] = array();

    // Agregar callback
    self::$callbacks[$action][] = $callback;

  }

  // Asignar un callbak rescribiendo los demas
  public static function setCallback($action, $callback){
    self::$callbacks[$action] = array();
    self::addCallback($action, $callback);
  }

  // Llamada de una acción. Devuelve verdadero su todos los calbacks devuelve verdadero
  public static function call($action, $options){
    $return = true;

    // Si existe callbacks definidas para la accion
    if(isset(self::$callbacks[$action]))
      // Llamar los callbacks
      foreach(self::$callbacks[$action] as $callback)
        $return = $return && call_user_func_array($callback, $options);

    return $return;

  }

  // Asignar/Mezcla valor con propiedad.
  public static function mergeProperty($property, $value){

    // Obtener funcion callback para mezclar la propiedad solicitada
    $mergeCallback = self::itemOr($property, self::$mergeCallbacks);

    // Si exite la funcion y existe un valor previo se mezcla a partir de la funcion designada
    if($mergeCallback !== null && isset(self::$confs[$property]))
      self::$confs[$property] = call_user_func_array($mergeCallback,
        array(self::$confs[$property], $value));

    // Si no existe el callback se devolvera el ultimo valor
    else
      self::$confs[$property] = $value;

  }

  // Carga un archivo de configuración
  public static function mergePropertiesFromFile($filename, $property = null){
    
    // Obtener el nombre real del archivo
    $filename = realpath($filename);

    // Si el archivo no existe salir
    if(!is_file($filename) || in_array($filename, self::$confsLoaded)) return;

    // Agregar el archivo a la lita de archivos de configuracion cargados
    self::$confsLoaded[] = $filename;

    // Cargar archivo de configuracion
    $conf = require $filename;

    // Si la configuración esta destinada a una propiedad especifica
    // mezclar con dicha configuracion
    if(isset($property))
      self::mergeProperty($property, $conf);

    else
      // Sino se debe agregar las configuraciones una por una.
      // Recorrer elementos obtenidos para ir 
      foreach ($conf as $property => $value)
        self::mergeProperty($property, $value);

  }

  // Cargar propiedades de todos los archivos de coniguracion en las carpetas
  // del ambito
  public static function mergePropertiesFromAllFiles($filename, $property = null){

    // Recorrer cada uno de las carpetas en el path
    foreach (self::$paths as $path)
      // Si el archivo cargar la configuracion en la posicion path/property
      self::mergePropertiesFromFile("{$path}/{$filename}.conf.php", $property);

  }

  // Agrega una carpeta al final de la lista de paths
  public static function addPath($path){
    self::$paths[] = $path;
  }

  // Agregar una ruta a la lista de rutas
  public static function setRoute($route, $to){    
    // Agregar nueva ruta
    self::$confs["routes"]["routes"][$route] = $to;
  }

  // Obtener un atributo de la confiuguracion
  public static function getAttribute($property){
    self::mergePropertiesFromAllFiles("conf/{$property}", $property);
    return self::itemOr($property, self::$confs);
  }

  // Responder con descarga de archivos
  final public static function downloadFile($file, array $env = array()){
    self::respondeWithFile($file, null, null, true);
  }

  // Responder con archivo
  final public static function respondeFile($file, array $env = array()){
    self::respondeWithFile($file);
  }
  

  // Devuelve la url base del sitio
  public static function urlBase(){
    return self::$urlBase;
  }

  // Devuelve una URL concatenando la url base al inicio
  public static function url($path = ""){
    return self::$urlBase.$path;
  }

  // Devuelve una URL absoluta incluyendo el nombre del servidor
  // y el tipo de conexion
  public static function serverUrl($path = ""){
    return "http://" . $_SERVER["SERVER_NAME"] . self::url($path);
  }

  public static function eServerUrl($path){
    echo self::serverUrl($path);
  }

  // Imprime una URL
  public static function eUrl($path = ""){
    echo self::url($path);
  }

  // Redirigir a una URL
  public static function redirect($url){
    if(!empty($url)){
      header("location: ". self::url($url));
      exit();
    }
  }

  // Inicio del Amathista
  public static function task(){

    // Obtener las configuraciones
    self::mergePropertiesFromAllFiles("conf/conf");

    // Obtener el valor 
    $errorReporting = self::getAttribute("errorReporting");
    error_reporting($errorReporting);
    
    // Variable global de argumentos
    global $argv;

    // Peticion
    $request = "";

    // determinar peticion
    if(isset($argv)){ // Es una peticion desde la consola

      // La URL Base es vacía
      self::$urlBase = "";

      // La peticion es la concatenacion de todos los parametros
      $request = implode("/", $argv);

    }else{ // Es una peticion HTTP

      // Definicion de la URL Base
      self::$urlBase = dirname($_SERVER["PHP_SELF"]);
      
      // Validacion para cuando este en el root
      if(self::$urlBase === "/")
        self::$urlBase = "";
      
      // Obtener peticion
      $request = substr_replace($_SERVER["REQUEST_URI"], "", 0, strlen(self::$urlBase));

      // Validacion para cuando este en la peticion no comienze con "/"
      if($request[0] !== "/")
        $request = "/" . $request;

    }

    // Incluir extensiones para peticiones
    // Archivos requeridos
    $requires = self::getAttribute("requires");

    // Incluir archivo
    foreach($requires as $file){
      self::requireFile($file);
    }

    // Llamado de accion para evaluar ruta
    self::call("route.eval", array(
      $request,
      self::getAttribute("routes")
    ));

  }

  // Obtener la contenido de un archivo de configuración
  public static function getConfig($file){
    return require self::findFileIn("$file.conf.php", self::$paths);
  }

  //
  private static function requireExt($file){

    // Si existe el archivo retornar el mismo
    if(is_file($realFile = "{$file}.php")){
      require_once $realFile;
      return true;
    }

    // Incluir como extensión
    if(is_file($realFile = "{$file}.conf.php")){
      // Obtener la configuracion de la extencion
      $conf = require $realFile;

      // Obtener archivos a agregar de la extencion
      $files = self::itemOr("files", $conf, array());

      // Eliminar el item de los archivos necesarios de la configuración
      unset($conf["files"]);

      // Llamar archivo de iniciacion en la carpeta si existe.
      foreach ($files as $item)
        if(is_file($realFile = "{$file}{$item}.php"))
          require_once $realFile;
        else
          die("Am: Not fount Exts file: '{$realFile}'");

      
      // Incluir archivo init si existe
      if(is_file($realFile = "{$file}.init.php"))
        require_once $realFile;

      return true;

    }

  }

  // Funcion para incluir un archivo
  public static function requireFile($file){

    // Agregar extension
    if(self::requireExt($file))
      return true;

    // Buscar un archivo dentro de las carpetas
    foreach(self::$paths as $path)
      if(self::requireExt("{$path}{$file}"))
        return true;

    // No se agregó la extension
    die("Am: Not fount Exts '{$file}'");
    
    return false;

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  ///////////////////////////////////////////////////////////////////////////////////

  // Indica si un callback es válido o no.
  public static function isValidCallback($callback){
    // Si es un array evaluar como metodo
    if(is_array($callback))
      return call_user_func_array("method_exists", $callback);
    // Si es string evaluar como function
    if(is_string($callback))
      return function_exists($callback);
    // Es un callback invalido
    return false;
  } 

  // Devuele un valor de una posicion del array. Si el valor
  // no existe devuelve el valor por $def
  public static function itemOr($index, $arr, $def = null){
    return isset($arr[$index])? $arr[$index] : $def;
  }

  // Indica si es una array asociativo o no
  public static function isAssocArray(array $array){
    $j = 0;
    foreach($array as $i => $_){
      if($j !== $i)
        return true;
      $j++;
    }
    return false;
  }

  // funcion para mezclar una lista de valores
  public static function mergeValues($callback, array $values, $def){

    // Primer valor es el valor por defecto
    $ret = $def;

    // Si no existe el callback se devolvera el ultimo valor
    if($callback === null){
      while(!empty($values) && $def === ($ret = array_pop($values)));
      return $ret;
    }

    // Mezclar todos los valores
    foreach ($values as $value) {
      // Mezclar con la configuracion en la propiedad conf/name
      $ret = call_user_func_array($callback, array($ret, $value));

    }

    return $ret;

  }

  // Busca un archivo en los paths indicados
  public static function findFileIn($file, array $paths){
    
    // Si existe el archivo retornar el mismo
    if(is_file($file)) return $file;

    // Buscar un archivo dentro de las carpetas
    foreach($paths as $path)
      if(is_file($realPath = "{$path}{$file}"))
        return $realPath;

    return false;

  }

  // Obtienen tipo mime de un determinado archivo.
  public final static function mimeType($filename, $mimePath = null) {
    $mimePath = isset($mimePath)? $mimePath : AM_FOLDER . "resources";
    $fileext = substr(strrchr($filename, "."), 1);
    if (empty($fileext)) return (false);
    $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
    $lines = file("$mimePath/mime.types");
    foreach($lines as $line) {
      if (substr($line, 0, 1) == "#") continue; // skip comments
      $line = rtrim($line) . " ";
      if (!preg_match($regex, $line, $matches)) continue; // no match to the extension
      return ($matches[1]);
    }
    return (false); // no match at all
  }

  // Responer con un archivo
  final public static function respondeWithFile($file, $mimeType = null, $name = null, $attachment = false){

    // Obtener el mime-type del archivo con el que se responderá
    if(!isset($mimeType)) $mimeType = self::mimeType($file);
    if(!isset($name)) $name = basename($file);

    $attachment = $attachment ? "attachment;" : "";

    // Colocar cabeceras
    header("content-type: {$mimeType}");
    header("Content-Disposition: $attachment filename=\"$name\"");
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");
    header("Content-Length: " . filesize($file));

    // Leer archivo
    readfile($file);

  }
  
}

// Callbacks por defecto
Am::setCallback("response.file",   "Am::respondeFile");
Am::setCallback("response.download",   "Am::downloadFile");

Am::addPath(AM_FOLDER);
Am::addPath(getcwd()."/");

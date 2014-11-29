<?php

/**
 * Clase principal de Amathista
 */

final class Am{

  protected static

    // Define las callbacks del sistema
    $callbacks = array(
      
      
      // route.eval (request, routes)  : Evalucación del route
      // response.file (file, env)     : Responder con archivo
      // response.download (file, env) : Responder con descarga de archivo
      // render.form (file, tpl)       : Renderizar formulario

      // render.template:   Renderizar vista
      // response.assets:   Responder con archivo
      // response.control:  Responder con controlador

    ),

    // URL base para el sitio
    $urlBase = "",

    $init = null,         // Define las cargas iniciales. Archivo conf/routes.conf.php
    $instances = array(), // Instancias unicas de clases
    $control = null,      // Definiciones de controladores
    $commands = null,     // Target para los comandos
    $routes = null,       // Rutas definidas
    $assets = null;       // Archivos de recursos
    
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
    if(isset(self::$callbacks[$action])){

      // Llamar los callbacks
      foreach(self::$callbacks[$action] as $callback){
        $return = $return && call_user_func_array($callback, $options);
      }

    }
    
    return $return;

  }

  // Incluir extensiones
  protected static function includeExts($type){

    // Obtener contenido de los archivos
    $init = self::getAttribute("init");

    if(!isset($init[$type])) return;

    // Incluir archivos
    foreach($init[$type] as $file){
      require_once AM_FOLDER . "exts/{$file}.php";
    }

  }

  // Agregar una ruta a la lista de rutas
  public static function setRoute($route, $to){
    // Obtener las rutas
    $routes = self::getAttribute("routes");
    // Agregar nueva ruta
    $routes["routes"][$route] = $to;
    // Guardar ruta en el atributo static
    self::$routes = $routes;
  }

  // Devuelve la url base del sitio
  public static function urlBase(){
    return self::$urlBase;
  }

  // Devuelve una URL concatenando la url base al inicio
  public static function url($path = ""){
    return self::$urlBase.$path;
  }

  // Imprime una URL
  public static function eUrl($path = ""){
    echo self::url($path);
  }

  // Inicio del Amathista
  public static function task(){
    
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
      
      // Obtener peticion
      $request = substr_replace($_SERVER["REDIRECT_URL"], "", 0, strlen(self::$urlBase));

    }
    
    // Incluir extensiones para peticiones
    self::includeExts("request");

    // Llamado de accion para evaluar ruta
    self::call("route.eval", array(
      $request,
      self::getAttribute("routes")
    ));

  }

  // Obtener la contenido de un archivo de configuración
  public static function getConfig($file, $type = "conf"){
    return require "$file.$type.php";
  }

  // Obtener el valor de una propiedad stática de Am.
  public static function getAttribute($property, $folder = "./conf"){

    // Si la propiedad es igual a null
    if(self::$$property === null)
      // Entonces se carga desde la configuración
      self::$$property = self::getConfig("$folder/$property");

    // Devolver propiedad
    return self::$$property;

  }

  // Responder con descarga de archivos
  final public static function downloadFile($file, array $env){
    self::respondeWithFile($file, null, null, true);
  }

  // Responder con archivo
  final public static function respondeFile($file, array $env){
    self::respondeWithFile($file);
  }

  // Renderizar formulario. $file: parametros del formulario, $tpl: plantilla del formulario
  final public static function form($file, $tpl){

    // Evaluar ruta
    return self::call("render.form", array(
      $file,
      $tpl
    ));

  }

  // Responer con un archivo
  final public static function respondeWithFile($file, $mimeType = null, $name = null, $attachment = false){

    // Obtener el mime-type del archivo con el que se responderá
    if(!isset($mimeType)) $mimeType = self::mimeType($file);
    if(!isset($name)) $name = basename($file);

    $attachment = $attachment ? "attachment;" : "";

    // Colocar cabeceras
    header("content-type: {$mimeType}");
    header("Content-Disposition: $attachment filename=\"\"");
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");
    header("Content-Length: " . filesize($file));

    // Leer archivo
    readfile($file);

  }

  // Obtienen tipo mime de un determinado archivo.
  public final static function mimeType($filename, $mimePath = null) {
    $mimePath = isset($mimePath)? $mimePath : AM_FOLDER . "resources";
    $fileext = substr(strrchr($filename, '.'), 1);
    if (empty($fileext)) return (false);
    $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
    $lines = file("$mimePath/mime.types");
    foreach($lines as $line) {
      if (substr($line, 0, 1) == '#') continue; // skip comments
      $line = rtrim($line) . " ";
      if (!preg_match($regex, $line, $matches)) continue; // no match to the extension
      return ($matches[1]);
    }
    return (false); // no match at all
  }

}

// Callbacks por defecto
Am::setCallback("response.file",   "Am::respondeFile");
Am::setCallback("response.download",   "Am::downloadFile");

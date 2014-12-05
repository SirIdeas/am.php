<?php

/**
 * Clase principal de Amathista
 */

final class Am{

  protected static

    // Define las callbacks del sistema
    $callbacks = array(
      // route.eval (request, routes)                   : Evalucación del route
      // response.file (file, env)                      : Responder con archivo
      // response.download (file, env)                  : Responder con descarga de archivo
      // response.assets (file, assets, env)            : Responder con archivo
      // render.form (file, tpl)                        : Renderizar formulario
      // response.control (control, action, params, env): Responder con controlador
      // render.template (templete, paths, env, ignore) : Renderizar vista
    ),

    // Callbacks para mezclar atributos
    $mergeCallbacks = array(
      // Nombre del atributo => array(callback, valor_por_defecto)
      "requires" => "array_merge",
      "routes" => "array_merge_recursive",
      "assets" => "array_merge",
      "timezone" => null,
      "commands" => null,
      "control" => "array_merge_recursive",
      "smtp" => "array_merge",
      "mails" => "array_merge",
      "sources" => "array_merge",
      "validators" => "array_merge_recursive",
    ),

    // Valores por defecto de las propiedades
    $confsDef = array(
      "conf" => array(),
      "requires" => array(),
      "routes" => array(),
      "assets" => array(),
      "timezone" => null,
      "commands" => array(),
      "control" => array(),
      "smtp" => array(),
      "mails" => array(),
      "sources" => array(),
      "validators" => array(),
    ),

    $instances = array(), // Instancias unicas de clases
    $paths = array(),     // Herarquia de carpetas
    $confs = array(),     // Configuraciones cargadas
    $urlBase = "",        // URL base para el sitio

    // Valores seteados por el usuario
    $userConf = array(
      "routes" => array(  // Rutas definidas
        "env" => array(),
        "routes" => array()
      )
    ),

    $init = null,         // Define las cargas iniciales. Archivo conf/routes.conf.php
    $control = null,      // Definiciones de controladores
    $commands = null,     // Target para los comandos
    $assets = null,       // Archivos de recursos
    $mails = null,        // Configuraciones de los mails
    $smtp = null;         // Configuraciones SMTP
    
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
  
  // Obtener la configuracion para un parametro
  public static function loadConf($property){

    // Obtener del callback el valor predeterminado
    $def = self::$confsDef[$property];

    // Recorrer cada uno de las carpetas en el path
    foreach (self::$paths as $path) {

      // Si ya fue cargada la configuracion pasar a la siguiente
      if(isset(self::$confs[$path][$property])) continue;

      // Si no existe la configuracion en el path actual crear un array vacío
      if(!isset(self::$confs[$path])) self::$confs[$path] = array();

      if(file_exists($filename = "{$path}/conf/{$property}.conf.php")){
        // Si el archivo cargar la configuracion en la posicion path/property
        self::$confs[$path][$property] = require($filename);
      }else{
        // Si el archivo no existe guardar true en la posicion indicando que ya intento cargar
        self::$confs[$path][$property] = $def;
      }

    }

  }

  // Agrega una carpeta al final de la lista de paths
  public static function addPath($path){
    self::$paths[] = $path;
  }

  // Agregar una ruta a la lista de rutas
  public static function setRoute($route, $to){
    
    // Agregar nueva ruta
    self::$userConf["routes"]["routes"][$route] = $to;
    
  }

  // Devuelve la ruta del archivo donde sea encontrado por primera vez 
  public static function findFile($file){
    return self::findFileIn($file, self::$paths);
  }

  // Obtener un atributo de la confiuguracion
  public static function getAttribute($property){
    self::loadConf($property); // Cargar configuraciones de require

    // Obtener funcion callback para mezclar la propiedad solicitada
    $mergeCallback = self::$mergeCallbacks[$property];
    $def           = self::$confsDef[$property];

    // Para ir encolando el lista de valores segun su prevalencia
    $ret = array();

    foreach (self::$confs as $value) {

      // Obtener valor dentro del conf (del archivo conf.conf.php)
      $confValue = isset($value["conf"][$property])? $value["conf"][$property] : $def;

      // Obtener el valor del archivo ($property.conf.phh)
      $nameValue = isset($value[$property])? $value[$property] : $def;

      // Agregarlos al array
      $ret[] = $confValue;
      $ret[] = $nameValue;

    }

    // Si existen los valores de usuario de la propiedad tambien se agregan
    if(isset(self::$userConf[$property])){
      $ret[] = self::$userConf[$property];
    }

    // Mezclar valores
    return self::mergeValues($mergeCallback, $ret, $def);

  }

  // Incluir extensiones
  protected static function requireExts(){

    // Archivos requeridos
    $requires = self::getAttribute("requires");

    // Incluir archivo
    foreach($requires as $file){
      self::requireFile($file);
    }

  }

  // Responder con descarga de archivos
  final public static function downloadFile($file, array $env){
    self::respondeWithFile($file, null, null, true);
  }

  // Responder con archivo
  final public static function respondeFile($file, array $env){
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

    // Obtener las configuraciones
    self::loadConf("conf");

    // Incluir extensiones para peticiones
    self::requireExts();

    // Llamado de accion para evaluar ruta
    self::call("route.eval", array(
      $request,
      self::getAttribute("routes")
    ));

  }

  // Obtener la contenido de un archivo de configuración
  public static function getConfig($file){
    return require self::findFile("$file.conf.php");
  }

  // Funcion para incluir un archivo
  public static function requireFile($file){
    ($realFile = self::findFile("$file.php")) or die("Am: Not fount Exts '{$file}'");
    require_once $realFile;
  }

  // Obtener una instancia de un Mail con su respectiva configuraion tomada de
  // 
  public static function getMail($name, array $options = array()){
    
    // Incluir clase para Mails
    self::requireFile("exts/mailer/AmMailer.class");

    // Obtener configuraciones de mails
    $mails = self::getAttribute("mails");

    // Combinar opciones recibidas en el constructor con las
    // establecidas en el archivo de configuracion
    $options = array_merge(
      isset($mails[$name])? $mails[$name] : array(),
      $options
    );

    // Si no es un array se buscará la configuracion en
    // el archivo de configuracion SMTP
    if(!is_array($options["smtp"])){

      // Obtener configuraciones STMP
      $smtpConfs = self::getAttribute("smtp");

      // Si se debe tomar la configuracion por defecto
      if($options["smtp"] === true) $options["smtp"] = "default";

      // Asignar configuraio
      $options["smtp"] = $smtpConfs[$options["smtp"]];
            
    }

    // Crear instancia del mailer
    return new AmMailer("test", $options);

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  ///////////////////////////////////////////////////////////////////////////////////

  // Crea un directoro si no existe
  public final static function mkdir($path, $permisions = 0755, $recursive = true){
    // Si no existe el directori se cre
    if(!is_dir($path))
      return mkdir($path, $permisions, $recursive);
    return true;
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

  // Devuelve la cadena "s" convertida en formato under_score
  public static function underscor($s) {

    // Primer caracter en miniscula
    if(!empty($s)){
      $s[0] = strtolower($s[0]);
    }
    
    // Crear funcion para convertir en minuscula
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');

    // Operar
    return preg_replace_callback("/([A-Z])/", $func, str_replace(" ", "_", $s));
    
  }

  // Devuelve una cadena "s" en formato camelCase. Si "cfc == true" entonces
  // el primer caracter tambien es convertido en mayusculas
  public static function camelCase($s, $cfc = false){
    
    // Primer caracter en mayuscula o en miniscula
    if(!empty($s)){
      if($cfc){
        $s[0] = strtoupper($s[0]);
      }else{
        $s[0] = strtolower($s[0]);
      }
    }

    // Funcion para convertir cada caracter en miniscula
    $func = create_function('$c', 'return strtoupper($c[1]);');

    // Operar
    return preg_replace_callback("/_([a-z])/", $func, $s);

  }

  public static function isNameValid($string){
    return preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $string) != 0;
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
    if(file_exists($file)) return $file;

    // Buscar un archivo dentro de las carpetas
    foreach($paths as $path){
      if(file_exists($realPath = "{$path}{$file}")) return $realPath;
    }

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
    header("Content-Disposition: $attachment filename=\"\"");
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

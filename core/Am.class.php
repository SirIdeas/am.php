<?php

/**
 * Clase principal de Amathista
 */

final class Am{

  protected static

    // Define las callbacks del sistema
    $callbacks = array(

      /**** Rutas ****/
      // route.evaluate (request)                       : Evalucación del route
      
      /**** Respuestas ****/
      // response.file (file, env)                      : Responder con archivo
      // response.download (file, env)                  : Responder con descarga de archivo
      // response.assets (file, env)                    : Responder con archivo
      // response.control (control, action, params, env): Responder con controlador

      /**** Session ****/
      // session.id(index)          : Asignar identificador de la sesion
      // session.all()              : Obtener el todos los datos de sesion
      // session.get(index)         : Obtener elemento
      // session.has(index)         : Indica si existe un elemento
      // session.set(index, value)  : Asignar un elemento
      // session.delete(index)      : Eliminar un elemento

      /**** Otras ****/
      // render.template (templete, paths, options)     : Renderizar vista
      // command.addPath (path)                         : Agregar una carpeta de comandos
      // credentials.instance()                         : Obtener una instancia del manejador de credencales
    ),

    // Callbacks para mezclar atributos
    $mergeFunctions = array(
      "requires" => "merge_if_snd_first_not_false"
    ),

    // Exteciones manejadoras de session
    $aliasExts = array(
      "normalSession" => "exts/am_normal_session/"
    ),

    $instances = array(),       // Instancias unicas de clases
    $paths = array(),           // Herarquia de carpetas
    $confsLoaded = array(),     // Archivos de configuración cargados.
    $confs = array(),           // Configuraciones cargadas
    $urlBase = "";              // URL base para el sitio     
    
  // Devuelve la instancia de una clase existente. Sino existe la instancia se crea una nueva
  public final static function getInstance($className, array $params = array()){
    // Si la clase no existe devolver error
    if(!class_exists($className))
      return null;
    
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

  // Llamar los callbacks de un accion procesando los argumentos
  // recibidos.
  public static function call($action/* Resto de los parametros*/){
    $options = func_get_args();
    array_shift($options);  // Quitar el primer parametros, corresponde a $action
    return self::callArray($action, $options);
  }

  // Llamada de una acción. Devuelve verdadero su todos los calbacks devuelve verdadero
  public static function callArray($action, $options){
    $return = array();

    // Si existe callbacks definidas para la accion
    if(isset(self::$callbacks[$action]))
      // Llamar los callbacks
      foreach(self::$callbacks[$action] as $callback)
        $return[] = call_user_func_array($callback, $options);

    // Si el array tiene un solo elementos retornar dicho elemento
    if(count($return) === 1)
      return $return[0];

    // Eliminar valores vacíos
    $return = array_filter($return);

    // Si el array tiene un solo elementos retornar dicho elemento
    if(count($return) === 1)
      return $return[0];

    // Si no se genero ninguna llamada simplemente salir
    if(empty($return))
      return;

    return $return;

  }

  // Asignar/Mezcla valor con propiedad.
  public static function mergeProperty($property, $value){

    // Obtener funcion callback para mezclar la propiedad solicitada
    $mergeFunctions = itemOr($property, self::$mergeFunctions);

    // Si exite la funcion y existe un valor previo se mezcla a partir de la funcion designada
    if($mergeFunctions !== null && isset(self::$confs[$property]))
      self::$confs[$property] = call_user_func_array($mergeFunctions,
        array(self::$confs[$property], $value));
    
    // Si no existe el callback se devolvera el ultimo valor
    else
      self::$confs[$property] = $value;

  }

  // Mezcla un conjunto de propiedades
  public static function mergeProperties(array $conf){
    // Recorrer elementos obtenidos para ir 
    foreach ($conf as $property => $value)
      self::mergeProperty($property, $value);
  }

  // Carga un archivo de configuración
  public static function mergePropertiesFromFile($filename, $property = null){
    
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
      self::mergeProperty($property, $conf);

    else
      // Sino se debe agregar las configuraciones una por una.
      self::mergeProperties($conf);

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

  // Obtener la contenido de un archivo de configuración
  public static function getConfig($file){
    return require self::findFileIn("$file.conf.php", self::$paths);
  }

  // Obtener un atributo de la confiuguracion
  public static function getAttribute($property, $default = null){
    self::mergePropertiesFromAllFiles("conf/{$property}", $property);
    return itemOr($property, self::$confs, $default);
  }

  // Responder con descarga de archivos
  public static function downloadFile($file, array $env = array()){
    return self::respondeWithFile($file, null, null, true);
  }

  // Responder con archivo
  public static function respondeFile($file, array $env = array()){
    // Devolver archivo
    return self::respondeWithFile($file);
  }

  ///////////////////////////////////////////////////////////////////////////////////
  // Urls y redicciones
  ///////////////////////////////////////////////////////////////////////////////////
  
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

  public static function eServerUrl($path = ""){
    echo self::serverUrl($path);
  }

  // Imprime una URL
  public static function eUrl($path = ""){
    echo self::url($path);
  }

  // Redirigir a una URL
  public static function gotoUrl($url){
    if(!empty($url)){
      header("location: ". $url);
      exit();
    }
  }

  // Redirigir a una URL
  public static function redirect($url){
    self::gotoUrl(self::url($url));
  }

  // Redirige si la condifcion es verdadera
  public static function redirectIf($condition, $url){
    if($condition)
      $this->redirect($url);
  }

  // Redirige se la condifcion es falsa.
  public static function redirectUnless($condition, $url){
    self::redirectIf(!$condition, $url);
  }

  ///////////////////////////////////////////////////////////////////////////////////
  // Inclusion de archivos y extenciones
  ///////////////////////////////////////////////////////////////////////////////////

  // Cargador de Amathista
  public static function load($file){

    // Si existe el archivo retornar el mismo
    if(is_file($realFile = "{$file}.php")){
      require_once $realFile;
      return true;
    }

    // Si no existe el archivo .conf para dicho ruta retornar, de 
    // lo contrario se intentara incluir como una extension
    if(!is_file($realFile = "{$file}.conf.php"))
      return false;

    // Obtener la configuracion de la extencion
    $conf = require $realFile;

    // Obtener las funciones para mezclar que s definirín
    $mergeFunctions = itemOr("mergeFunctions", $conf, array());

    // Obtener archivos a agregar de la extencion
    $files = itemOr("files", $conf, array());

    // Obtener dependencias
    $requires = itemOr("requires", $conf, array());

    // Obtener dependencias
    $init = itemOr("init", $conf, array());

    // Los items nuevos no sobreescriben los anteriores
    self::$mergeFunctions = array_merge($mergeFunctions, self::$mergeFunctions);

    // Incluir las dependencias
    self::requireFiles($requires);
    
    // Llamar archivo de iniciacion en la carpeta si existe.
    foreach ($files as $item)
      if(is_file($realFile = "{$file}{$item}.php"))
        require_once $realFile;
      else
        die("Am: Not fount Exts file: '{$realFile}'");
    
    // Incluir archivo init si existe
    if(is_file($realFile = "{$file}.init.php"))
      require_once $realFile;

    // Sino se debe agregar las configuraciones una por una.
    self::mergeProperties($init);

    return true;
      
  }

  // Funcion para incluir una extension
  public static function requireExt($file){

    // Agregar extension
    if(self::load($file))
      return true;

    // Buscar un archivo dentro de las carpetas
    foreach(self::$paths as $path)
      if(self::load($path.$file))
        return true;

    // No se agregó la extension
    die("Am: Not fount Exts '{$file}'");

  }

  // Incluya una extension desde un alias. Si la extencion
  // no existe se intentara incluir el alias
  public static function requireAliasExts($alias){

    // Obtener el manejador de session
    if(isset(self::$aliasExts[$alias]))
      $alias = self::$aliasExts[$alias];
    
    // Incluir extension    
    return self::requireExt($alias);

  }

  // Incluye varias extensiones o archivos
  public static function requireFiles(array $requires){
    // Incluir dependencias recursivamente
    foreach ($requires as $value)
      self::requireExt($value);
  }

  ///////////////////////////////////////////////////////////////////////////////////
  // Manejo de session
  ///////////////////////////////////////////////////////////////////////////////////

  // Realiza incializaciones para el manejo de session
  public static function startSession(){
    
    // Si ya esta cargada la clase AmSession es porque
    // ya se realizó la inicializacion. 
    if(class_exists("AmSession"))
      return;

    // Incluir extension desde el aclias
    self::requireAliasExts(self::$confs["sessionManager"]);

    // Incluir manejador principal de session
    Am::requireExt("core/am_session/");

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // Manejo de credenciales
  ///////////////////////////////////////////////////////////////////////////////////

  // Obtener manejador de credencailes
  public static function getCredentialsHandler(){
    return self::call("credentials.instance");
  }

  // Devuelve las credenciales del usuario logeado
  public static function getCredentials(){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::call("credentials.instance")))
      return null;

    // Devuelve la instancia del usuario actual
    return $credentialsManager->getCredentials();

  }

  // Indica si existe o no un usuario logeado.
  public static function isAuth(){
    return null !== self::getCredentials();
  }

  // Inidica si el usuario logeado tiene las credenciales solicitadas
  public static function hasCredentiales($credential){

    // Si no existe la instancai del manejador de credenciales
    if(!($credentialsManager = self::call("credentials.instance")))
      return false;

    // Verificar si el usuario logeado tiene las credenciales
    return $credentialsManager->hasCredentials($credential);

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // Manejo de credenciales
  ///////////////////////////////////////////////////////////////////////////////////

  // Inicio del Amathista
  public static function task(){

    // Obtener las configuraciones
    self::mergePropertiesFromAllFiles("conf/");

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
    self::requireFiles(self::getAttribute("requires"));

    // Llamado de accion para evaluar ruta
    self::call("route.evaluate", $request);

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  ///////////////////////////////////////////////////////////////////////////////////

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
  public static function respondeWithFile($file, $mimeType = null, $name = null, $attachment = false){

    // Si el archivo no esite retornar false
    if(!is_file($file)) return false;

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

    return true;

  }
  
}

// Callbacks por defecto
Am::setCallback("response.file",   "Am::respondeFile");
Am::setCallback("response.download",   "Am::downloadFile");

Am::addPath(AM_FOLDER);
Am::addPath(getcwd()."/");

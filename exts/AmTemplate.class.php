<?php

/**
 * Clase renderizar vistas
 */

final class AmTemplate{

  // Carpeta donde se guardan los compilados de las vistas
  const BUILD_FOLDER = "gen/";

  protected
    $file = null,             // Vista a buscar
    $realFile = null,         // Ruta real de la vista
    $content = "",            // Contenido del archivo
    $env = array(),           // Entorno
    $params = array(),        // Variables definidas en la vista 
    $parent = null,           // Vista padre
    $openSections = array(),  // Lista de secciones abiertas
    $sections = array(),      // Lista de secciones y su contenido
    $child = null,            // Contenido de la vista hija
    $dependences = array(),   // Lista de vistas de las que depende (padre, hijas y anidadas)
    $paths = array(),         // Lista de directorios donde se buscará la vista
    $ignore = false;          // Bandera que indica si se ignoran las vistas inexistentes sin generar error

  private function __construct($file, $paths, array $env, $ignore = false){
    
    // setear paths
    if(is_array($paths)){
      $this->paths = $paths;
    }else{
      $this->paths[] = $paths;
    }

    // Asignar atributos
    $this->file = $file;
    $this->env = $env;
    $this->ignore = $ignore;
    $this->realFile = $this->findFile($file);

    // Leer archivo
    if($this->realFile !== false)
      $this->content = file_get_contents($this->realFile);

    // Obtener padre
    preg_match_all("/\(# parent:(.*) #\)/", $this->content, $parents);
    $this->parent = array_pop($parents[1]);
    
    // Quitar sentencias de padres
    $this->content = implode("", preg_split("/\(# parent:(.*) #\)/", $this->content));

    // Obtener lista de hijos
    preg_match_all("/\(# place:(.*) #\)/", $this->content, $this->dependences);
    $this->dependences = $this->dependences[1];

    // Instanciar padre dentro de las dependencias
    if(null !== $this->parent){
      array_unshift($this->dependences, $this->parent);
    }

    // Convertir el array de dependencias a un array asociativo
    // donde todos los valores sean false
    if(0<count($this->dependences)){
      $this->dependences = array_combine($this->dependences, array_fill(0, count($this->dependences), false));
    }

  }

  // Busca una vista en los paths definidos
  public function findFile($file){
    // Si no existe la vista mostrar error
    ($file = AmUtils::findFile($file, $this->paths)) !== false or $this->ignore or die("Am: No existe view '{$file}'");
    return $file;
  }

  public function compile($child = null, array $sections = array()){

    // Asignar secciones recibidas
    $this->sections = $sections;
    $this->child = $child;  // Contenido de un vista hija

    // Dividir por comandos
    $parts = preg_split("/\(# (.*) #\)/", $this->content);

    // Obtener comando
    preg_match_all("/\(# (.*) #\)/", $this->content, $cmds);
    $cmds = $cmds[1];

    ob_start(); // Para optener todo lo que se imprima durante el compilad

    // Recorrer las partes entre los comando
    foreach($parts as $i => $part){
      echo $part; // Imprimir la parte actual
      if(isset($cmds[$i])){ // Si existe un comando en la misma posicion

        // Obtener parametros del comando
        list($method, $param) = array_merge(explode(":", $cmds[$i]), array("", null));

        // Si no existe un metodo con el mismo nombre del comando mostrar error
        method_exists($this, $method) or die("Am: unknow method AmTemplate->$method");

        // Si el metodo es set
        if($method == "set")
          // Se divide el argumento en dos parametros
          $param = explode("=", $param);
        else
          $param = array($param);

        // Llamado el metodo
        call_user_func_array(array($this, $method), $param);

      }

    }

    // Si la vista tiene un padre
    if(null !== $this->parent){
      // Obtener instancia de vista del padre
      $parentView = $this->getSubView($this->parent)
        // Compilar padre
        ->compile(ob_get_clean(), $this->sections);

      // Mezclar generadas en el padre con las definidas en la vista acutal
      $this->params = $parentView["vars"] = array_merge($parentView["vars"], $this->params);
      return $parentView;
    }

    return array(
      "content" => ob_get_clean(),    // Todo lo impreso
      "sections" => $this->sections,  // Devolver secciones definidas
      "vars" => $this->getVars()      // Variables definidas
    );

  }

  // Obtiene una vista con el mismo entorno de la vista actual
  public function getSubView($name){
    // Si no esta definida la dependiencia mostrar error
    isset($this->dependences[$name]) or die("Am: not found subview \"{$name}\" in \"{$this->realFile}\"");
    // Si la dependencia no es una instancia de AmView
    if(!$this->dependences[$name] instanceof self){
      // Se instancia la vista 
      $this->dependences[$name] = new self($name, $this->paths, $this->getVars(), $this->ignore);
    }
    // Devolver instancia de la vista
    return $this->dependences[$name];
  }

  // Inserta una vista anidada
  public function place($view){
    $view = $this->getSubView($view)->compile("", $this->sections);
    echo $view["content"];
    $this->sections = array_merge($view["sections"], $this->sections);
    $this->params = array_merge($view["vars"], $this->params);
  }

  // Imprimir una seccion
  public function put($name){
    $section = isset($this->sections[$name])? $this->sections[$name] : "";
    echo $section;
  }

  // Abrir una seccion
  public function section($name){
    $this->openSections[] = $name;
    ob_start();
  }

  // Cerrar seccion
  public function endSection(){
    // Si no existen secciones abiertas entonces mostrar error
    !empty($this->openSections) or die("Am: closing section unopened");

    // Obtener lo impreso hasta hora
    $content = ob_get_clean();

    // Obtener el nombre de la ultima seccion abierta
    $name = array_pop($this->openSections);

    // Agregar seccion si no existe
    // if(!isset($this->sections[$name])){
      $this->sections[$name] = $content;
    // }

  }

  // Imprimir el contenido de la vista hija
  public function child(){
    echo $this->child;
  }

  // Agregar variable
  public function set(){
    extract($this->getVars());
    eval("\$this->params['".func_get_arg(0)."'] = ".func_get_arg(1).";");
  }

  // Obtener variables de la vista. Cinluye el entorno + las variables definidas en la vista
  public function getVars(){
    return array_merge($this->env, $this->params);
  }

  // Obtener lista de dependencas
  public function dependences(){

    // La primera dependencia es el archivo pripio de la vista
    $dependences = array($this->realFile);

    // Sedebe agregar las dependencias de las vistas relacionadas (padre hijas, y anidadas)
    foreach ($this->dependences as $key => $value) {
      $dependences = array_merge($dependences, $this->getSubView($key)->dependences());
    }

    return $dependences;

  }

  // Devuelve la ruta donde se guardara la vista compilada
  public function getCompiledFile(){
    if($this->realFile === false) return false;
    return self::BUILD_FOLDER . $this->realFile;
  }

  // Indica si la vista esta acutalizada.
  // Para esto se verifica que ninguna de las vista dependientes 
  // haya sido modificada despues de la fecha de la ultima fecha
  // de compilacion de la vista actual 
  public function isUpdate($compiledView){

    // Si no se ha compilado no esta actualizado
    if(!is_file($compiledView)) return false;

    // Obtener fecha de creacion de la vista compilada
    $compiledTime = filemtime($compiledView);

    // Obtener dependencias
    $dependences = $this->dependences();

    // Si alguna fue modificada despues de la fecha de compilacion
    // No esta actualizada
    foreach ($dependences as $file) {
      if($compiledTime<filemtime($file)) return false;
    }

    return true;

  }

  // Generar vista
  public function save(){

    // Obtener vista generada
    if(false === $compiledView = $this->getCompiledFile()) return;

    // Si esta actualizada salir
    // if($this->isUpdate($compiledView)) return;

    // Carpeta donde se ubicara la vista compilada
    $compileFolder = dirname($compiledView);

    // Si no existe el directorio se crea, y sino se puede crear se muestra un error
    is_dir($compileFolder) or mkdir($compileFolder, 0755, true) or die("Am: can't to create folder \"{$compileFolder}\"");
    
    // Obtener contenido compilado de la vista
    $result = $this->compile();

    // Guardar vista minificada
    file_put_contents($compiledView, self::htmlMinify($result["content"]));

  }

  // incluye la vista compilada
  public function includeView(){
    if(file_exists($filename = $this->getCompiledFile())){
      extract($this->getVars());  // Crear variables
      include $filename;          // Inluir vista
    }
  }

  // Minifca el HTML de la vista. basicamente consiste en colocar todo en una sola linea
  public static function htmlMinify($html){
    $lines = explode("\n", $html);
    
    $html = "";
    foreach ($lines as $line) {
      $html .= trim($line);
    }
    return $html;
  }

  // Funcion para atender el llamado de render.tempalte
  public static function render($file, $paths, array $env, $ignore = false){
    $view = new self($file, $paths, $env, $ignore); // Instancia vista
    $view->save();        // Compilar y guardar
    $view->includeView(); // Incluir vista
    return true;
  }

}

// Atender el llamado a renderizaa vistas
Am::setCallback("render.template", "AmTemplate::render");

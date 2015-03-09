<?php

/**
 * Clase renderizar vistas
 */

final class AmRenderize extends AmObject{

  protected
    $env = array(),           // Entorno
    $params = array(),        // Variables definidas en la vista
    $parent = null,           // Vista padre
    $openSections = array(),  // Lista de secciones abiertas
    $sections = array(),      // Lista de secciones y su contenido
    $child = null,            // Contenido de la vista hija
    $dependences = array(),   // Lista de vistas de las que depende (padre, hijas y anidadas)
    $ignore = false,          // Bandera que indica si se ignoran las vistas inexistentes sin generar error
    $errors = array();        // Indica si se generó o no un error durante el renderizado

  protected
    $content = array(),       // Contenido del archivo
    $file = null,             // Vista a buscar
    $realFile = null,         // Ruta real del archivo
    $paths = array(),         // Lista de directorios donde se buscará la vista
    $options = array(),       // Guarda los parametros con los que se inicializó la vista
    $filters = array();

  public function __construct($file, $paths, $options = array()){
    parent::__construct($options);

    $this->filters[] = new AmRenderizeFilterBlockPHP();
    $this->filters[] = new AmRenderizeFilterInlinePHP();
    $this->filters[] = new AmRenderizeFilterExtend();
    $this->filters[] = new AmRenderizeFilterInclude();
    $this->filters[] = new AmRenderizeFilterBlock();
    $this->filters[] = new AmRenderizeFilterJS();
    $this->filters[] = new AmRenderizeFilterCSS();
    $this->filters[] = new AmRenderizeFilterTag();

    // setear paths
    if(is_array($paths)){
      $this->paths = $paths;
    }else{
      $this->paths[] = $paths;
    }

    // Asignar atributos
    $this->options = $options;
    $this->file = $file;
    $this->realFile = $this->findView($file);

    // Leer archivo
    if($this->realFile !== false)
      $this->content = file($this->realFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  }

  // Busca una vista en los paths definidos
  public function findView($file){
    // Si no existe la vista mostrar error
    if(false === ($fileRet = Am::findFileIn($file, $this->paths))){
      $this->errors[] = "Am: No existe view '{$file}.'";
      $this->ignore or die(implode(" ", $this->errors));
    }
    return $fileRet;
  }

  public function render(){

    $out = array();
    $in = $this->content;

    while(!empty($in)){

      $line = array_shift($in);

      preg_match("/^([ ]*)(.*)/", $line, $matches);

      $filter = null;

      foreach ($this->filters as $filter) {
        if(false !== ($params = $filter->match($matches[2]))){
          break;
        }
        $filter = null;
      }

      $out[] = array();
      
      exit;

    }

    header("content-type: text/plain");
    print_r($arr);

  }

  public function anidation(array &$arr){

    $last = false;
    $ret = array();
    while(!empty($arr)){
      $item = array_shift($arr);
      if($last===false || $last["ident"]===$item["ident"]){
        $ret[] = $last = $item;
      }else if($last["ident"] < $item["ident"]){
        array_unshift($arr, $item);
        $ret[count($ret)-1]["childs"] = $this->anidation($arr);
      }else{
        array_unshift($arr, $item);
        break;
      }
    }

    return $ret;

  }

  // Método que indica si se generó algun error al renderizar la vista
  public function hasError(){
    return count($this->errors)>0;
  }

  public static function renderize($file, $paths, $options = array()){

    // Obtener configuraciones del controlador
    $confs = Am::getAttribute("views", array());

    // Obtener valores por defecto
    $defaults = itemOr("defaults", $confs, array());

    // Si no existe configuracion para la vista
    $conf = isset($confs[$file])? $confs[$file] : array();

    // Mezclar todas las opciones
    $options = array_merge_recursive($defaults, $conf, $options);

    $view = new self($file, $paths, $options); // Instancia vista
    $view->render();        // Compilar y guardar
    return !$view->hasError();

  }

}

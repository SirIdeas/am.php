<?php 

/**
 * Clase para controlador estandar. Basado en el objeto estandar de Amathista
 */

class AmControl extends AmObject{

  protected
    $path = null,   // Carpeta contenedora del controlador
    $views = null,  // Carpeta contenedora de las vistas para el controlador
    $view = null,   // Nombre de la vista a renderizar
    $get = null,
    $post = null,
    $request = null,
    $server = null;

  public function __construct($data = null){
    parent::__construct($data);

    $this->get = new AmObject($_GET);
    $this->post = new AmObject($_POST);
    $this->request = new AmObject($_REQUEST);
    $this->server = new AmObject($_SERVER);

  }

  // Propiedad para get/set para render
  public function getView(){ return $this->view; }
  public function setView($value){ $this->view = $value; return $this; }

  // Asigna la vista que se renderizará.
  // Es un Alias de la funcion setView que agrega .view.php al final
  // del valore recibido.
  public function render($value){
    // Las vista de las acciones son de extencion .view.php
    return $this->setView(self::getViewName($value));
  }

  // Devuelve la carpeta de ambito del controlador
  public function getPath(){
    return $this->path;
  }

  // Obtener la carpeta de de las vistas
  public function getViewsPath(){
    // Si no tiene carpeta asignada se retorna null
    if(!$this->views)
      return null;
    return $this->getPath() . $this->views;
  }

  public function getPaths(){
    $ret = array();
    // Agregar path principal si existe
    if(null !== ($folder = $this->getPath()))
      $ret[] = $folder;
    // Agregar path de vistas principal si existe
    if(null !== ($folder = $this->getViewsPath()))
      $ret[] = $folder;
    return $ret;
  }

  // Devuelve el método de la peticion
  public function getMethod(){
    return strtolower($this->server->REQUEST_METHOD);
  }

  public static function getViewName($value){ 
    return "{$value}.view.php";
  }

  // Funcion para atender las respuestas por controlador.
  // Recive el nombre del controlador, la accion a ejecutar,
  // Los parametros y el entorno
  public static function response($control, $action, array $params, array $env){

    // Obtener el nombre de la clases control a instanciar
    $controlClassName = "{$control}Control";

    // Obtener configuraciones del controlador
    $confs = Am::getAttribute("control");

    // Si no existe configuracion para el controlador
    $conf = isset($confs[$control])? $confs[$control] : array();

    // Si no es un array, entonces el valor indica el path del controlador
    if(is_string($conf)) $conf = array("path" => $conf);

    // Valores por defecto
    $conf = array_merge(array(
      "path" => "", 
      "view" => self::getViewName($action) // Asignar vista que se mostrará
    ), $conf);
    
    // Obtener la ruta del controlador
    $controlFile = "{$conf["path"]}{$control}.control.php";
    
    // Incluir controlador si existe el archivo
    if(file_exists($controlFile)) require_once $controlFile;
    
    // Obtener instancia del controlador
    $obj = Am::getInstance($controlClassName, $conf);

    // Si el metodo existe llamar
    if(method_exists($obj, "action"))
      $obj->action($params);
    
    // Si el metodo existe llamar
    if(method_exists($obj, $actionMethod = "action_{$action}"))
      call_user_func_array(array($obj, $actionMethod), $params);

    // Si el metodo existe llamar correspondiente al metodo de la peticion
    if(method_exists($obj, $actionMethod = "{$obj->getMethod()}_{$action}"))
      call_user_func_array(array($obj, $actionMethod), $params);
    
    // Renderizar vista mediante un callback
    Am::call("render.template", array(
      
      // Obtener vista a renderizar
      $obj->getView(),

      // Obtener carpetas de ambito para el controlador
      $obj->getPaths(),
      
      // Paths para las vistas
      array(
        // Variables en la vista
        "env" => array_merge(
          $env,             // Entorno: prioridad 3
          $params,          // Paremetros de rutra: prioridad 2
          $obj->toArray()   // Atributos de contorlaodr: prioridad 1
        ),
        "ignore" => true
      )
      
    ));

    return true;

  }
  
}

// Atender las respuestas por controlador
Am::setCallback("response.control", "AmControl::response");

<?php 

/**
 * Clase para controlador estandar. Basado en el objeto estandar de Amathista
 */

class AmControl extends AmObject{

  protected
    $path = null,       // Carpeta contenedora del controlador
    $views = null,      // Carpeta contenedora de las vistas para el controlador
    $view = null,       // Nombre de la vista a renderizar
    $get = null,        // Variables recibidas por GET
    $post = null,       // Variables recibidas por POST
    $request = null,    // Variables GET y POST conbinadas
    $server = null,     // Variables de SERVER
    $filters = array(), // Filtros agregados
    $paths = array();   // Carpetas de ambito del controlador

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
    $ret = $this->paths;
    // Agregar path principal si existe
    if(null !== ($folder = $this->getPath()))
      $ret[] = $folder;
    // Agregar path de vistas principal si existe
    if(null !== ($folder = $this->getViewsPath()))
      $ret[] = $folder;
    return array_unique($ret);
  }

  // Devuelve el método de la peticion
  public function getMethod(){
    return strtolower($this->server->REQUEST_METHOD);
  }

  // Devuelve el nombre normal de una vista
  public static function getViewName($value){ 
    return "{$value}.view.php";
  }

  // Devuelve el prefijo para determinado elemento
  final protected function prefixs($key = null){
    return isset($this->prefixs[$key]) ? $this->prefixs[$key] : "";
  }

  // Devuelve la configuracion para un controlador
  // Ademas incluye el archivo conrrespondiente
  public static function includeControl($control){

    // Obtener configuraciones del controlador
    $confs = Am::getAttribute("control");

    // Obtener valores por defecto
    $defaults = itemOr("defaults", $confs, array());

    // Si no existe configuracion para el controlador
    $conf = itemOr($control, $confs, array());

    // Si no es un array, entonces el valor indica el path del controlador
    if(is_string($conf))
      $conf = array("path" => $conf);

    // Valores por defecto
    $conf = array_merge($defaults, $conf);

    if(is_file($realFile = "{$conf["path"]}.control.php"))
      $conf = array_merge(
        $conf,
        require($realFile)
      );

    // Si tiene un padre se incluye
    // y se mezcla con la configuracion actual
    if(isset($conf["parent"]) && !empty($conf["parent"])){
      $parentConf = self::includeControl($conf["parent"]);
      $parentConf["paths"][] = $conf["path"];
      $conf = array_merge($parentConf, $conf);
    }else
      $conf["paths"] = array($conf["path"]);

    // Obtener la ruta del controlador
    // Incluir controlador si existe el archivo
    if(is_file($controlFile = "{$conf["path"]}{$control}.control.php"))
      require_once $controlFile;

    // Retornar la configuracion obtenida
    return $conf;

  }

  // Funcion para atender las respuestas por controlador.
  // Recive el nombre del controlador, la accion a ejecutar,
  // Los parametros y el entorno
  public static function response($control, $action, array $params, array $env){

    // Valores por defecto
    $conf = array_merge(
      // Incluye el controlador y devuelve la configuracion para el mismo
      self::includeControl($control),
      // Asignar vista que se mostrará
      array(
        "view" => self::getViewName($action),
      )
    );

    // Obtener el nombre de la clases control a instanciar
    $controlClassName = "{$control}Control";
    
    // Obtener instancia del controlador
    $obj = Am::getInstance($controlClassName, $conf);
    
    // Si el metodo existe llamar
    if(method_exists($obj, $actionMethod = "action"))
      call_user_func_array(array($obj, $actionMethod), $params);

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

  // Manejo de filtros para las acciones de los controladores

  // Agregar un filtro
  final protected function addFilter($name, $cls, $to = "all", $except = array(), $redirect = null){
    
    // Filtro "only" para ciertos métodos
    if(is_array($to)){
      $scope = "only";
      $redirect = $except;
      $except = array();

    // Filtro para "all" métodos o para "except"
    }else{
      $scope = $to;
      $to = array();
    }
    
    // Si no se ha creado el contenedor del filtro, se crea
    if(!isset($this->filters[$state][$name])){

      // Crear array vacío en el state si no existe.
      if(!isset($this->filters[$state]))
        $this->filters[$state] = array();
      
      // Agregar filtro vacío
      $this->filters[$state][$name] = array(
        
        // A que metodo se aplicara el filtro: "all", "only" o "except"
        "scope" => $scope,
        
        // A quienes se aplicara el filtro en caso de que scope=="only"
        "to" => array(),

        // A quienes no se aplicará el filtro en caso de que scope=="except"
        "except" => $except,

        // Si la peticion no pasa el filtro rediriguir a la siguiente URL
        "redirect" => $redirect

      );
      
    }
    
    // Mezclar los métodos a los que se aplicará el filtro con los que 
    // ya habian sido agregados y obtener los valores unicos
    $this->filters[$state][$name]["to"] = array_unique(array_merge(
      $this->filters[$state][$name]["to"],
      $to
    ));

  }
  
  // Agregar un filtro antes de la ejecucion de metodos
  final protected function addBeforeFilter($name, $to = "all", $except = array(), $redirect = null){
    $this->addFilter($name, "before", $to, $except, $redirect);
  }
  
  // Agregaun filtro antes de la ejecucion de métodos GET
  final protected function addBeforeGetFilter($name, $to = "all", $except = array(), $redirect = null){
    $this->addFilter($name, "before_get", $to, $except, $redirect);
  }
  
  // Agregaun filtro antes de la ejecucion de métodos POST
  final protected function addBeforePostFilter($name, $to = "all", $except = array(), $redirect = null){
    $this->addFilter($name, "before_post", $to, $except, $redirect);
  }
  
  // Agregaun filtro despues de la ejecucion de métodos
  final protected function addAfterFilter($name, $to = "all", $except = array()){
    $this->addFilter($name, "after", $to, $except);
  }
  
  // Agregaun filtro despues de la ejecucion de métodos GET
  final protected function addAfterGetFilter($name, $to = "all", $except = array()){
    $this->addFilter($name, "after_get", $to, $except);
  }
  
  // Agregaun filtro despues de la ejecucion de métodos POST
  final protected function addAfterPostFilter($name, $to = "all", $except = array()){
    $this->addFilter($name, "after_post", $to, $except);
  }
  
  // Ejecuta los filtros correspondiente para un método.
  // state: Indica el estado que se ejecutara: before, before_get, bofore_post, after, after_get, after_post
  // methodName: Nombre del metodo del que se desea ejecutar los filtros.
  // estraParams: Parámetros extras para los filtros.
  final protected function executeFilters($state, $methodName, $extraParams){
    
    // Si hay filtro a ejecutar para dicha peticion
    if(isset($this->filters[$state])){
      
      // Recorrer los filtros del peditoestado
      foreach($this->filters[$state] as $filterName => $filter){
        
        // Si el filtro se aplica a todos, o si el metodo solicitado esta dentro de los
        // métodos a los que se aplicará el filtro actual.
        if(($filter["scope"] == "all" || in_array($methodName, $filter["to"]))){
          // Si el método no esta dentro de las excepciones del filtro
          if(!isset($filter["except"]) || !in_array($methodName, $filter["except"])){
            
            $ret = call_user_func_array(array(&$this, $this->prefixs("filters") . $filterName), $extraParams);
            
            if($ret === false && $state == "before"){
              
              if(isset($filter["redirect"])){
                $this->redirect(url($filter["redirect"]));
              }else{
                return false;
              }


            }

          }
        }
        
      }
      
    }
    
    // Si todos los filtros pasaron retornar verdadero.
    return true;
    
  }

}

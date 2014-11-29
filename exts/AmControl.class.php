<?php 

/**
 * Clase para controlador estandar. Basado en el objeto estandar de Amathista
 */

class AmControl extends AmObject{

  protected
    $path = null,   // Carpeta contenedora del controlador
    $views = null,  // Carpeta contenedora de las vistas para el controlador
    $render = null; // 

  // Propiedad para get/set para render
  public function render($value = null){
    return $this->attr("render", $value);
  }

  // Obtener la carpeta de de las vistas
  public function getViewsPath(){
    return $this->attr("path") . $this->attr("views");
  }

  // Funcion para atender las respuestas por controlador.
  // Recive el nombre del controlador, la accion a ejecutar,
  // Los parametros y el entorno
  public static function response($control, $action, array $params, array $env){

    // Obtener configuraciones del controlador
    $confs = Am::getAttribute("control");

    // Si no existe configuracion para el controlador
    $conf = isset($confs[$control])? $confs[$control] : array();

    // Si no es un array, entonces el valor indica el path del controlador
    if(is_string($conf)) $conf = array("path" => $conf);

    // Valores por defecto
    $conf = array_merge(array(
      "path" => "", 
      "render" => $action // Asignar vista que se mostrará
    ), $conf);
    
    // Obtener la ruta del controlador
    $controlFile = "{$conf['path']}{$control}.control.php";
    
    // Incluir controlador si existe el archivo
    if(file_exists($controlFile)) require_once $controlFile;
    
    // Obtener instancia del controlador
    $obj = Am::getInstance($control, $conf);

    // Determinar nombre dle metodo a ejecutar
    $actionMethod = "action_$action";

    // Si el metodo no existe salir
    if(!method_exists($obj, $actionMethod)) return false;
    
    // Llamar metodos
    call_user_func_array(array($obj, $actionMethod), $params);
    
    // Renderizar vista mediante un callback
    Am::call("render.template", array(
      "{$obj->render()}.view.php",  // Las vista de las acciones son de extencion .view.php
      array($obj->getViewsPath()),  // Paths para las vistas

      // Variables en la vista
      array_merge(
        $env,             // Entorno: prioridad 3
        $params,          // Paremetros de rutra: prioridad 2
        $obj->toArray()   // Atributos de contorlaodr: prioridad 1
      )
    ));

    return true;

  }
  
}

// Atender las respuestas por controlador
Am::setCallback("response.control", "AmControl::response");

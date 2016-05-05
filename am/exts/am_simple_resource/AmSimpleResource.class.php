<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmSimpleResource extends AmResource{

  public function action(){

    $this->msgsKeys = array('success', 'danger');
    
  }

  // Accion para mostrar el listado de un modelo
  public function action_index(){}
  public function get_index(){}
  public function post_index(){}

  // Acción para agregar un nuevo usuario
  public function action_new(){
    $this->r = new $this->model;
  }
  // Procesamiento del formulario new
  public function get_new(){}
  public function post_new(){
    return self::handleActionSimple(
      parent::post_new(),
      'Registro agregado satisfactoriamente',
      'Errores al intentar agregar el registro'
    );
  }

  // Accion para modificar los datos del registro
  public function action_edit($id){
    // if(method_exists($this, 'format_edit'))
    //   $this->r = $this->format_edit($this->r);
  }
  public function get_edit($id){}
  public function post_edit($id){
    return self::handleActionSimple(
      parent::post_edit($id),
      'Registro actualizado satisfactoriamente',
      'Errores al intentar actualizar el registro'
    );
  }

  // Accion para eliminar un registro
  public function action_delete($id){}
  public function get_delete($id){}
  public function post_delete($id){
    return self::handleActionSimple(
      parent::post_delete($id),
      'Registro eliminado satisfactoriamente',
      'Errores al intentar eliminar el registro'
    );
  }

  // Crear o actualizar un registro
  public function action_cou(){}
  public function get_cou(){}
  public function post_cou(){
    return self::handleActionSimple(
      parent::post_cou(),
      'Registro creado/actualizado satisfactoriamente',
      'Errores al intentar creado/actualizado el registro'
    );
  }

  // Accion para mostrar el detalle de los registros
  public function action_detail($id){}
  public function get_detail($id){}

  private function handleActionSimple(array $ret, $msgSuccess, $msgFail){
    if($ret['success']){
      AmFlash::success($msgSuccess);
      return Am::redirect($this->url);
    }else{
      AmFlash::danger($msgFail);
      $this->errors = $ret['errors'];
    }
  }

  public function filter_loadRecord($id){

    $ret = parent::filter_loadRecord($id);

    if($ret['success'] !== true){
      AmFlash::danger('No se encontró el registro');
      return Am::redirect($this->url);
    }

    return true;

  }

}

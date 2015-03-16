<?php

class AmResourceControl extends AmControl{

  public function __construct($data = null){
    parent::__construct($data);

    // Obtener la instancida del modelo
    $this->classModel = AmORM::model($this->model);

  }

  // Accion para mostrar el listado de un modelo
  public function get_index(){
    $this->r = new $this->classModel;
    var_dump($this->r->save());
    var_dump($this->r->getErrors());
    $this->render(null);
  }

  protected function action_data(){

    // Am::requireExt("exts/dinamicTableServer");
    //
    // $columns = array_keys($this->columns);
    //
    // $q = AmORM::table($this->model)
    //   ->all()
    //   ->setSelects(array_combine($columns, $columns));
    //
    // return dinamicTableServer($this->request, $q, array($this, "format_list"), false);

  }

  // El formateador agregará una clase al registro dependiendo
  // del estado de la inscripcion
  public function format_list($r){
    $r["cls"] = "";
    return $r;

  }

  // Acción para agregsar un nuevo usuario
  public function action_new(){
    $this->r = new Inscrito();
  }

  public function post_new(){

    // Obtener los datos recibidos por post del formulario
    $data = $this->post->toArray();
    $fields = $this->getFields();

    $this->r->setValues($data, $fields);
    if($this->r->save()){
      AmFlash::success("Registro agregado satisfactoriamente");
      Am::gotoUrl($this->getUrl());
    }else{
      AmFlash::danger("Errores al intentar agregar el registro");
      $this->errors = $this->r->getErrors();
    }

  }

  // Accion para mostrar el detalle de los registros
  public function get_detail($id){
    if(method_exists($this, "format_detail"))
      $this->r = $this->format_detail($this->r);
  }

  // Accion para modificar los datos del registro
  public function action_edit($id){
    if(method_exists($this, "format_edit"))
      $this->r = $this->format_edit($this->r);
  }
  public function get_edit($id){}
  public function post_edit($id){

    // Obtener los datos recibidos por post del formulario
    $data = $this->post->toArray();
    $fields = $this->getFields();

    $this->r->setValues($data, $fields);
    if($this->r->save()){
      AmFlash::success("Registro actualizado satisfactoriamente");
      Am::gotoUrl($this->getUrl());
    }else{
      AmFlash::danger("Errores al intentar actualizar el registro");
      $this->errors = $this->r->getErrors();
    }

  }

  // Accion para eliminar un registro
  public function action_remove($id){}
  public function get_remove($id){}
  public function post_remove($id){

    if($this->r->delete()){
      AmFlash::success("Registro eliminado satisfactoriamente");
      Am::gotoUrl($this->getUrl());
    }else{
      AmFlash::danger("Errores al intentar eliminar el registro");
      $this->errors = $this->r->getErrors();
    }

  }

  // Devuelve los campos
  public function getFields(){
    $ret = array();
    foreach ($this->fields as $key => $value)
      $ret = array_merge($ret, array_keys($value));
    return $ret;
  }

  public function filter_loadRecord($id){

    // Obtener le model con que trabaja el controlador
    $this->r = AmORM::table($this->model)->find($id);

    // Si no se encontró el registro mostrar un mensaje
    if(!$this->r){
      AmFlash::danger("No se encontró el registro");
      return false;
    }
    return true;

  }

}

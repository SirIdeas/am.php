<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para controlador estandar. 
 */
class AmResource extends AmController{

  public function __construct($data = null){
    parent::__construct($data);

    // Obtener la instancida del modelo
    $model = $this->model;
    
    if($model)
      $this->table = $model::me();

    // Obtener las columnas si no se han indicado
    if(empty($this->columns) && $this->table){
      $columns = array_keys($this->table->getFields());
      $this->columns = array_combine($columns, $columns);
    }

    $this->columnsNames = array_keys($this->columns);

    // Los campos a setear son los campos de la tabla - los ocultos
    if(empty($this->fields))
      $this->fields = array_diff($this->columnsNames, $this->hides);

    if(empty($this->form))
      $this->form = $this->model;

  }

  public function callback_newRecord(AmModel &$r){}

  public function callback_setValuesRecord(AmModel &$r){}

  private static function handleAction(AmModel $r, $actionResult = true){

    $ret = array('success' => $actionResult);
    if(!$actionResult)
      $ret['errors'] = $r->getErrors();
    return $ret;

  }

  // Procesamiento para guardar un formulario
  private function handleForm(AmModel $r){

    $params = Am::g('request');
    // Obtener los datos recibidos por post del formulario
    $data = $params[$this->form];
    $r->setValues($data, $this->fields);
    $this->callback_setValuesRecord($r);
    return self::handleAction($r, $r->save());

  }

  // Procesamiento del formulario new
  public function post_new(){

    $r = new $this->model;
    $this->callback_newRecord($r);
    $this->r = $r;
    return $this->handleForm($this->r);

  }

  // Procesamiento del formulario edit
  public function post_edit($id){

    return $this->handleForm($this->r);

  }

  // Accion para eliminar un registro
  public function post_delete($id){

    return self::handleAction($this->r, $this->r->delete());

  }

  public function filter_loadRecord($id){

    // Obtener le model con que trabaja el controlador
    $this->r = $this->table->find($id);

    // Si no se encontró el registro mostrar un mensaje
    if(!$this->r){
      AmFlash::danger('No se encontró el registro');
      return false;
    }
    return true;

  }

  // // Procesamiento del formulario edit
  // public function post_cou(){
  //   $classModel = $this->model;
  //   $table = $classModel::me();
  //   $pkValues = AmObject::mask($this->request[$this->form], $table->getPks());
  //   $this->r = $table->find($pkValues, $classModel);
  //   if(!$this->r){
  //     $r = new $classModel;
  //     $this->callback_newRecord($r);
  //     $this->r = $r;
  //   }
  //   return $this->handleForm($this->r);
  // }

  // // Obtener los datos de un registro
  // public function post_detail($id){
  //   return array(
  //     'success' => true,
  //     'data' => $this->r->getValues($this->fields)
  //   );
  // }

  // public function action_data(){

  //   // Obtener el listado de elementos
  //   $q = $this->table->all()
  //       ->setSelects(array_combine($this->columnsNames, $this->columnsNames));

  //   // Return el objeto para la tabla dinamica
  //   return dinamicTableServer($this->request, $q,
  //     array($this, 'callback_formatList'), false
  //   );

  // }

  // public function action_search(){
  //   $classModel = $this->model;

  //   // Obtener el texto a buscar
  //   $txtSearch = strtolower($this->request->search);

  //   // Obtener la posicion a cargar
  //   $offset = $this->request->offset * $this->request->limit;
  //   $q = $classModel::qSearch($txtSearch, $this->request->limit, $offset);
  //   if(!$q)
  //     $q = $classModel::q($this->request->limit, $offset);
    
  //   $this->callback_querySearchSetup($q);
  //   $haveNext = $q->haveNextPage();

  //   return array(
  //     'success'   => true,
  //     'items'     => $q->getResult('array', array($this, 'callback_formatSearch')),
  //     'haveNext'  => $haveNext,
  //   );

  // }

  // public function callback_querySearchSetup(AmQuery $q){}

  // // El formateador agregará una clase al registro dependiendo
  // // del estado de la inscripcion
  // public function callback_formatList($r){
  //   $r['cls'] = '';
  //   return $r;
  // }

  // // El formateador agregará una clase al registro dependiendo
  // // del estado de la inscripcion
  // public function callback_formatSearch($r){
  //   return $r;
  // }

}

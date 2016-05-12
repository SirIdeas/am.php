<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Controlador con acciones CRUDs para un modelo. 
 */
// PENDIENTE: Documentar
class AmResource extends AmController{

  public function __construct($data = null){
    parent::__construct($data);

    // Obtener la instancida del modelo
    $model = $this->model;
    
    if($model)
      $this->table = $model::me();

    if(empty($this->form))
      $this->form = $this->model;

    $this->forms = array(
      'new' => array(),
      'edit' => array(),
      'cou' => array(),
      'detail' => array(),
      'delete' => array(),
      'list' => array(),
    );

    if($this->table){

      // Obtener las columnas si no se han indicado
      if(empty($this->fields)){
        $fields = array_keys($this->table->getFields());
        $this->fields = array_combine($fields, array_fill(0, count($fields), true));
      }

      foreach($this->fields as $fieldName => $field){
        $this->fields[$fieldName] = $field = array_merge(
          array(
            'label' => $fieldName,
            'type' => 'text',
            'required' => false,
            'new' => true,
            'edit' => true,
            'cou' => true,
            'detail' => true,
            'delete' => true,
            'list' => true,
          ),
          $field === true ? array() : $field
        );

        foreach($this->forms as $key => $_){
          if($field[$key]){
            $this->forms[$key][$fieldName] = array_merge(
              $field,
              $field[$key] === true ? array() : $field[$key]
            );
          }
        }

      }

    }

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
  private function handleForm(AmModel $r, $formType){

    $params = Am::g('request');

    // Obtener los datos recibidos por post del formulario
    $data = $params[$this->form];

    $r->setValues($data, array_keys($this->forms[$formType]));

    $this->callback_setValuesRecord($r);

    return self::handleAction($r, $r->save());

  }

  // Procesamiento del formulario new
  public function post_new(){

    $r = new $this->model;
    $this->callback_newRecord($r);
    $this->r = $r;

    return $this->handleForm($this->r, 'new');

  }

  // Procesamiento del formulario edit
  public function post_edit($id){

    return $this->handleForm($this->r, 'edit');

  }

  // Accion para eliminar un registro
  public function post_delete($id){

    return self::handleAction($this->r, $this->r->delete());

  }

  // Procesamiento del formulario edit
  public function post_cou(){

    $params = Am::g('request');

    $model = $this->model;
    $table = $model::me();
    $pkValues = AmObject::mask($params[$this->form], $table->getPks());

    $this->r = $table->find($pkValues, $model);

    if(!$this->r){
      $r = new $model;
      $this->callback_newRecord($r);
      $this->r = $r;
    }

    return $this->handleForm($this->r, 'cou');

  }

  // Obtener los datos de un registro
  public function get_detail($id){

    return array(
      'success' => true,
      'data' => AmObject::mask($this->r->toArray(), array_keys($this->forms['detail'])),
    );

  }

  public function filter_loadRecord($id){

    // Obtener le model con que trabaja el controlador
    $this->r = $this->table->find($id);

    // Si no se encontró el registro mostrar un mensaje
    if($this->r)
      return true;

    return $this->responseService(array(
      'success' => false,
      'notFound' => true,
    ));


  }

  public function action_data(){

    $this->columnNames = array_keys($this->forms['list']);

    // Obtener el listado de elementos
    $q = $this->table->all()
      ->setFormatter(array($this, 'callback_formatList'))
      ->setSelects(array_combine($this->columnNames, $this->columnNames));

    // Return el objeto para la tabla dinamica
    return dinamicTableServer(Am::g('request'), $q, false);

  }

  // El formateador agregará una clase al registro dependiendo
  // del estado de la inscripcion
  public function callback_formatList($record, $realRecord){
    $record['cls'] = '';
    return $record;
  }

}

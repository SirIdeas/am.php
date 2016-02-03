<?php

class AmTable extends AmQuery{

  protected static
    $defCreatedAtFieldName = 'created_at',
    $defUpdatedAtFieldName = 'updated_at';

  // Constructor para la clase
  public function __construct($params = null){

    // Obtener los parametros parseados
    $params = AmObject::parse($params);

    // Obtener la instancia del esquema
    $scheme = is_string($params['scheme']) ? AmScheme::get($params['scheme']) :
              $params['scheme'];

    // Aaignar modelo
    $params['scheme'] = $scheme;

    $params = array_merge(array(
      'createdAtField' => self::$defCreatedAtFieldName,
      'updatedAtField' => self::$defUpdatedAtFieldName,
    ), $params);

    // Llamar al constructor heredado
    parent::__construct($params);

    // Describir tabla
    $this->describeTable(
      itemOr('fields', $params, array()),
      itemOr('referencesTo', $params, array()),
      itemOr('referencesBy', $params, array()),
      itemOr('uniques', $params, array())
    );

  }

  public function getTableName(){

    return $this->tableName;

  }

  public function getReferencesTo(){

    return $this->referencesTo;

  }
  
  public function getReferencesBy(){

    return $this->referencesBy;

  }
  
  public function getUniques(){

    return $this->uniques;

  }

  public function getPks(){

    return $this->pks;

  }

  public function getEngine(){

    return $this->engine;

  }

  public function getCharset(){

    return $this->charset;

  }

  public function getCollage(){

    return $this->collage;

  }
  
  public function getFields(){

    return $this->fields;

  }
  
  public function getField($name){

    return $this->hasField($name)? $this->fields->$name : null;

  }

  public function hasField($name){

    return isset($this->fields->$name);
    
  }

  // Cargar columnas, referencias y PKs a la tabla
  public function describeTable(
    array $fields = array(),
    array $referencesTo = array(),
    array $referencesBy = array(),
    array $uniques = array()
  ){

    // Preparar campos
    $this->pks = array();
    $this->fields = new stdClass;
    foreach($fields as $column)
      // Agregar instancia del campo
      $this->addField(new AmField($column));

    // Agregar campos unicos
    $this->uniques = $uniques;

    // Preparar referencias
    $this->referencesTo = new stdClass;
    foreach ($referencesTo as $name => $values)
      $this->referencesTo->$name = new AmRelation($values);

    // Preparar referencias a
    $this->referencesBy = new stdClass;
    foreach ($referencesBy as $name => $values)
      $this->referencesBy->$name = new AmRelation($values);

  }

  // Indica su un campo forma o no parte del primary key de la tabla
  public function isPk($fieldName){

    return in_array($fieldName, $this->getPks());
    
  }

  // Agregar el nombre del campo a la lista de
  // claves primarias
  public function addPk($fieldName){

    // Agregar el campo a la lista de campos
    // primarios si este no existe en la tabla
    if(!in_array($fieldName, $this->getPks()))
      $this->pks[] = $fieldName;

    // Marcar el campo como primario
    $this->getField($fieldName)->isPrimaryKey(true);

  }

  // Asigna un campo
  public function setField($name, AmField $field){

    $this->fields->$name = $field;
    return $this;

  }

  // Agregar una campo a la lista de campos
  public function addField(AmField $f, $as = null){

    // Obtener el nombre para el campo
    $fieldName = $f->getName();
    $name = empty($as) ? $fieldName : $as;

    // Asignar nombre al campo
    if(empty($fieldName))
      $f->getName($name);

    // Agregar campo a la lista de campos
    $this->setField($name, $f);

    // Si es campo primario se agrega a la
    // lista de campos primarios
    if($f->isPrimaryKey())
      $this->addPk($name);

  }

  // Convertir la tabla a Array
  public function toArray(){

    // Convertir campos
    $fields = array();
    foreach($this->getFields() as $offset => $field){
      $fields[$offset] = $field->toArray();
    }

    // Convertir refencias
    $referencesTo = array();
    foreach($this->getReferencesTo() as $offset => $field){
      $referencesTo[$offset] = $field->toArray();
    }

    // Convertir referencias a
    $referencesBy = array();
    foreach($this->getReferencesBy() as $offset => $field){
      $referencesBy[$offset] = $field->toArray();
    }

    // Unir todas las partes
    return array(
      'tableName' => $this->getTableName(),
      // 'modelName' => $this->getModelName(),
      'engine' => $this->getEngine(),
      'charset' => $this->getCharset(),
      'collage' => $this->getCollage(),
      'fields' => $fields,
      'pks' => $this->getPks(),
      'uniques' => $this->getUniques(),
      'referencesTo' => $referencesTo,
      'referencesBy' => $referencesBy,
    );

  }

}
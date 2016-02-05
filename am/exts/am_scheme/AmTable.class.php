<?php

class AmTable extends AmQuery{

  protected static
    $defCreatedAtFieldName = 'created_at',
    $defUpdatedAtFieldName = 'updated_at';


  // Propiedades de la tabla
  protected
    $createdAtField = null,   // Nombre del campo para la fecha de creación
    $updatedAtField = null,   // Nombre del campo para la fecha de actualización
    $schemeName = null,       // Nombre del esquema de conexión BD
    $tableName = null,        // Nombre en la BD
    $fields = null,           // Lista de campos
    $engine = null,           // Motor
    $charset = null,          // Set de caracteres
    $collage = null,          // Coleción de caracteres
    $pks = array(),           // Array de nombres de campos que forman el PK
    $referencesTo = array(),  // Tablas a las que hace referencia
    $referencesBy = array(),  // Tablas que le hacen referencia
    $uniques = array(),       // Array de indeces unicos
    $a;

  // Constructor para la clase
  public function __construct($params = null){

    // Obtener los parametros parseados
    $params = AmObject::parse($params);

    // Obtener la instancia del esquema
    $scheme = AmScheme::get($params['schemeName']);

    // Obtener configuracion del modelo
    if(isset($params['tableName']))
      $params = array_merge(
        $params,
        $scheme->getBaseModelConf($params['tableName'])
      );

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

  public function getSchemeName(){

    return $this->schemeName;

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

  public function getCreatedAtField(){

    return $this->createdAtField;

  }

  public function getUpdatedAtField(){

    return $this->updatedAtField;

  }

  public function setCreatedAtField($value){

    $this->createdAtField = $value; return $this;
    return $this;

  }

  public function setUpdatedAtField($value){

    $this->updatedAtField = $value; return $this;
    return $this;

  }


  public function hasCreatedAtField(){

    return $this->hasField($this->getCreatedAtField());

  }

  public function hasUpdatedAtField(){

    return $this->hasField($this->getUpdatedAtField());

  }

  // Agregar fecha al campo de fecha de creacion
  public function setAutoCreatedAt($values){

    // Si la tabla tiene un campo llamado 'created_at'
    // Se asigna a todos los valores la fecha now
    if($this->hasCreatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getCreatedAtField());
  }

  // Agregar fecha al campo de fecha de mpdificacion
  public function setAutoUpdatedAt($values){

    // Si la tabla tiene un campo llamado 'updated_at'
    // Se asigna a todos los valores la fecha now
    if($this->hasUpdatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getUpdatedAtField());
  }

  private static function setNowDateValueToAllRecordsInField($values, $field){

    $now = date('c');

    if($values instanceof AmQuery){
      // Agregar campo a la consulta
      $values->selectAs("'{$now}'", $field);

    }elseif(is_array($values)){

      // Agregar created_ad a cada registro
      foreach (array_keys($values) as $i)
        $values[$i][$field] = $now;

    }

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

  public function create($isNotExists = true){

    return $this->getScheme()->createTable($this, $isNotExists);

  }

  public function drop($isExists = true){

    return $this->getScheme()->dropTable($this, $isExists);

  }

  public function exists(){

    return $this->getScheme()->existsTable($this);

  }

  public function truncate($ignoreFK = false){

    return $this->getScheme()->truncate($this, $ignoreFK);

  }

  // Insertar valores
  public function insertInto($values, array $fields = array()){
    return $this->getScheme()->insertInto($values, $this, $fields);
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

  // Devuelve un Query que devuelve todos los registros de la Tabla
  public function all($alias = 'q', $withFields = false){

    // por si se obvio el primer parametro
    if(is_bool($alias)){
      $withFields = $alias;
      $alias = 'q';
    }

    $scheme = $this->getScheme();

    // Crear consultar
    $q = $scheme->q($this, $alias);
    
    // Obtener como retornará los resultados y asignarlo a la consulta
    $q->setAs(':'.$this->getTableName().'@'.$scheme->getName());

    // Asignar campos
    if($withFields){
      $fields = array_keys((array)$this->getFields());
      $fields = array_combine($fields, $fields);
      $q->setSelects($fields);
    }

    return $q;

  }
  
  public function q($limit = null, $offset = null, $alias = 'q',
    $withFields = false){

    $q = $this->all($alias, $withFields);

    if($limit)
      $q->limit($limit);

    if($limit && $offset)
      $q->offset($offset);

    return $q;

  }

  // Obtener consulta para buscar por un campos
  public function findBy($field, $value, $alias = 'q', $withFields = false){

    return $this->all($alias, $withFields)->where("{$field}='{$value}'");

  }

  // Obtener todos los registros de buscar por un campos
  public function findAllBy($field, $value, $type = null){

    return $this->findBy($field, $value)->get($type);

  }

  // Obtener el primer registro de la busqueda por un campo
  public function findOneBy($field, $value, $type = null){

    return $this->findBy($field, $value)->row($type);
    
  }

  // Obtener la consulta para encontrar el registro con un determinado ID
  public function findById($id, $alias = 'q', $withFields = false){

    // Obtener consultar para obtener todos los registros
    $q = $this->all($alias, $withFields);
    $pks = $this->getPks();  // Obtener el primary keys

    // Si es un array no asociativo
    if(is_array($id) && !isHash($id)){
      // Si la cantidad de campos del PKs es igual
      // a la cantidad de valores recibidos del ID
      if(count($pks) === count($id)){
        $id = array_combine($pks, $id);
      }else{
        // No es valido
        return null;
      }
    }

    // El primary key es un solo campo y los valores del ID no son un array
    if(1 == count($pks) && !is_array($id)){
      $id = array($pks[0] => $id);
    }

    // Recorrer los campos del PK
    foreach($pks as $pk){

      // Si no existe el valor para el campo devolver null
      if(!isset($id[$pk]) && !array_key_exists($pk, $id))
        return null;

      // Agregar condicion
      $fieldName = $this->getField($pk)->getName();
      $q->where("{$fieldName}='{$id[$pk]}'");

    }

    return $q;

  }

  // Regresa un objeto con AmModel con el registro solicitado
  public function find($id, $type = null){

    $q = $this->findById($id);
    $r = isset($q)? $q->row($type) : false;

    return $r === false ? null : $r;

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
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para representar las tablas de las BD
 */
class AmTable extends AmObject{

  protected static
    
    /**
     * [$defCreatedAtFieldName description]
     */
    $defCreatedAtFieldName = 'created_at',
    
    /**
     * [$defCreatedAtFieldName description]
     */
    $defUpdatedAtFieldName = 'updated_at';


  protected
    
    /**
     * Formato de respuesta del los registros
     */
    $model = 'array',
    
    /**
     * Nombre del campo para la fecha de creación
     */
    $createdAtField = null,
    
    /**
     * Nombre del campo para la fecha de actualización
     */
    $updatedAtField = null,
    
    /**
     * Instancia del esquema
     */
    $scheme = '',
    
    /**
     * Nombre del esquema de conexión BD
     */
    $schemeName = '',
    
    /**
     * Nombre en la BD
     */
    $tableName = null,
    
    /**
     * Lista de campos
     */
    $fields = null,
    
    /**
     * Lista de campos
     */
    $schemeStruct = false,
    
    /**
     * Validadores
     */
    $validators = null,
    
    /**
     * Motor
     */
    $engine = null,
    
    /**
     * Set de caracteres
     */
    $charset = null,
    
    /**
     * Coleción de caracteres
     */
    $collage = null,
    
    /**
     * Array de nombres de campos que forman el PK
     */
    $pks = array(),
    
    /**
     * Tablas a las que hace referencia
     */
    $referencesTo = array(),
    
    /**
     * Tablas que le hacen referencia
     */
    $referencesBy = array(),
    
    /**
     * Array de indeces unicos
     */
    $uniques = array();

  // Constructor para la clase
  /**
   * [__construct description]
   * @param [type] $params [description]
   */
  public function __construct($params = null){

    // Obtener los parametros parseados
    $params = AmObject::parse($params);

    // Obtener la instancia del esquema
    $scheme = AmScheme::get($params['schemeName']);

    // Asignar nombre de los campos de fecha
    $params = array_merge(array(
      'createdAtField'  => self::$defCreatedAtFieldName,
      'updatedAtField'  => self::$defUpdatedAtFieldName,
    ), $params);

    // Obtener configuracion del modelo
    $conf = $scheme->getBaseModelConf($params['tableName']);

    // Si se pudo obtener la configuración mazclarla con la recibida por
    // parámetros
    if($conf)
      $params = array_merge($conf, $params, array('schemeStruct' => true));

    // Aaignar modelo
    $params['scheme'] = $scheme;

    // Llamar al constructor heredado
    parent::__construct($params);

    // Obtener como retornará los resultados y asignarlo a la consulta
    if(!$this->getModel())
      $this->model = $scheme->getSchemeModelName($params['tableName']);

    // Preparar campos
    $fields = $this->fields;
    $pks = $this->pks;

    $this->pks = array();
    $this->fields = array();

    if(is_array($fields))
      foreach($fields as $fieldName => $column)
        // Agregar instancia del campo
        $this->addField($fieldName, array_merge(
          is_string($column)?array('type' => $column) : $column,
          array('pk' => in_array($fieldName, $pks))
        ));

    $this->pks = merge_unique($this->pks, $pks);

    // Preparar referencias
    if(!is_array($this->referencesTo))
      $this->referencesTo = array();

    foreach($this->referencesTo as $name => $relation)
      if(!$relation instanceof AmRelation)
        $this->referencesTo[$name] = new AmRelation($relation);

    // Preparar referencias a
    if(!is_array($this->referencesBy))
      $this->referencesBy = array();

    foreach($this->referencesBy as $name => $relation)
      if(!$relation instanceof AmRelation)
        $this->referencesBy[$name] = new AmRelation($relation);

    // Asignar tabla
    $scheme->addTable($this);

  }

  /**
   * [getModel description]
   * @return [type] [description]
   */
  public function getModel(){

    return $this->model;

  }

  /**
   * [getTableName description]
   * @return [type] [description]
   */
  public function getTableName(){

    return $this->tableName;

  }

  /**
   * [getSchemeName description]
   * @return [type] [description]
   */
  public function getSchemeName(){

    return $this->schemeName;

  }

  /**
   * [getScheme description]
   * @return [type] [description]
   */
  public function getScheme(){

    return $this->scheme;

  }


  /**
   * [setModel description]
   * @param [type] $value [description]
   */
  public function setModel($value){

    $table = $this;

    if(isset($this->model) && isset($value) && $this->model !== $value){

      $model = $this->model;
      $this->model = $value;
      $table = clone($this);
      $this->model = $model;

    }elseif(!isset($this->model) && isset($value)){
      $this->model = $value;

    }

    // Asignar tabla
    $table->getScheme()->addTable($table);

    return $table;

  }


  /**
   * [getReferencesTo description]
   * @return [type] [description]
   */
  public function getReferencesTo(){

    return $this->referencesTo;

  }
  
  /**
   * [getReferencesBy description]
   * @return [type] [description]
   */
  public function getReferencesBy(){

    return $this->referencesBy;

  }
  
  /**
   * [getUniques description]
   * @return [type] [description]
   */
  public function getUniques(){

    return $this->uniques;

  }

  /**
   * [getPks description]
   * @return [type] [description]
   */
  public function getPks(){

    return $this->pks;

  }
  
  /**
   * [getValidators description]
   * @return [type] [description]
   */
  public function getValidators(){

    return $this->validators;

  }

  /**
   * [isSchemeStruct description]
   * @return boolean [description]
   */
  public function isSchemeStruct(){

    return $this->schemeStruct;

  }


  /**
   * [getEngine description]
   * @return [type] [description]
   */
  public function getEngine(){

    return $this->engine;

  }

  /**
   * [getCharset description]
   * @return [type] [description]
   */
  public function getCharset(){

    return $this->charset;

  }

  /**
   * [getCollage description]
   * @return [type] [description]
   */
  public function getCollage(){

    return $this->collage;

  }
  
  /**
   * [getFields description]
   * @return [type] [description]
   */
  public function getFields(){

    return $this->fields;

  }
  
  /**
   * [getField description]
   * @param  [type] $name [description]
   * @return [type]       [description]
   */
  public function getField($name){

    return itemOr($name, $this->fields, null);

  }

  /**
   * [hasField description]
   * @param  [type]  $name [description]
   * @return boolean       [description]
   */
  public function hasField($name){

    return isset($this->fields[$name]);
    
  }

  /**
   * [getCreatedAtField description]
   * @return [type] [description]
   */
  public function getCreatedAtField(){

    return $this->createdAtField;

  }

  /**
   * [getUpdatedAtField description]
   * @return [type] [description]
   */
  public function getUpdatedAtField(){

    return $this->updatedAtField;

  }

  /**
   * [setCreatedAtField description]
   * @param [type] $value [description]
   */
  public function setCreatedAtField($value){

    $this->createdAtField = $value; return $this;
    return $this;

  }

  /**
   * [setUpdatedAtField description]
   * @param [type] $value [description]
   */
  public function setUpdatedAtField($value){

    $this->updatedAtField = $value; return $this;
    return $this;

  }

  /**
   * [hasCreatedAtField description]
   * @return boolean [description]
   */
  public function hasCreatedAtField(){

    return $this->hasField($this->getCreatedAtField());

  }

  /**
   * [hasUpdatedAtField description]
   * @return boolean [description]
   */
  public function hasUpdatedAtField(){

    return $this->hasField($this->getUpdatedAtField());

  }

  /**
   * [addCreatedAtField description]
   * @param [type] $name [description]
   */
  public function addCreatedAtField($name = null){

    if(!isset($name))
      $name = self::$defCreatedAtFieldName;
    
    $this->setCreatedAtField($name);

    return $this->addField($name, 'datetime');

  }

  /**
   * [addUpdatedAtField description]
   * @param [type] $name [description]
   */
  public function addUpdatedAtField($name = null){

    if(!isset($name))
      $name = self::$defUpdatedAtFieldName;
    
    $this->setUpdatedAtField($name);

    return $this->addField($name, 'datetime');

  }

  /**
   * [addCreatedAndUpdateAtFields description]
   * @param [type] $createAtFieldName [description]
   * @param [type] $updateAtFieldName [description]
   */
  public function addCreatedAndUpdateAtFields($createAtFieldName = null,
    $updateAtFieldName = null){

    return $this
      ->addCreatedAtField($createAtFieldName)
      ->addUpdatedAtField($updateAtFieldName);

  }

  // Agregar fecha al campo de fecha de creacion
  /**
   * [setAutoCreatedAt description]
   * @param [type] $values [description]
   */
  public function setAutoCreatedAt($values){

    // Si la tabla tiene un campo llamado 'created_at'
    // Se asigna a todos los valores la fecha now
    if($this->hasCreatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getCreatedAtField());

    return $this;

  }

  // Agregar fecha al campo de fecha de mpdificacion
  /**
   * [setAutoUpdatedAt description]
   * @param [type] $values [description]
   */
  public function setAutoUpdatedAt($values){

    // Si la tabla tiene un campo llamado 'updated_at'
    // Se asigna a todos los valores la fecha now
    if($this->hasUpdatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getUpdatedAtField());

    return $this;

  }

  /**
   * [setNowDateValueToAllRecordsInField description]
   * @param [type] $values [description]
   * @param [type] $field  [description]
   */
  private static function setNowDateValueToAllRecordsInField($values, $field){

    $now = date('c');

    if($values instanceof AmQuery){

      if($values->getType() == 'update')
        $values->set($field, $now);

      else
        // Agregar campo a la consulta
        $values->selectAs("'{$now}'", $field);

    }elseif(is_array($values)){


      // Agregar created_ad a cada registro
      foreach (array_keys($values) as $i)
        $values[$i][$field] = $now;

    }

  }

  // Métodos SET para algunas propiedades
  /**
   * [setScheme description]
   * @param [type] $value [description]
   */
  public function setScheme($value){

    $this->scheme = $value;
    return $this;
    
  }
  
  // Indica su un campo forma o no parte del primary key de la tabla
  /**
   * [isPk description]
   * @param  [type]  $fieldName [description]
   * @return boolean            [description]
   */
  public function isPk($fieldName){

    return in_array($fieldName, $this->getPks());
    
  }

  // Agregar el nombre del campo a la lista de
  // claves primarias
  /**
   * [addPk description]
   * @param [type] $fieldName [description]
   */
  public function addPk($fieldName){

    // Agregar el campo a la lista de campos
    // primarios si este no existe en la tabla
    if(!in_array($fieldName, $this->getPks()))
      $this->pks[] = $fieldName;

    // Marcar el campo como primario
    $field = $this->getField($fieldName);

    if(isset($field) && !$field->isPk())
      $this->fields[$fieldName] = $this->fields[$fieldName]->cp(array(
        'pk' => true
      ));

    return $this;

  }

  /**
   * [create description]
   * @param  boolean $isNotExists [description]
   * @return [type]               [description]
   */
  public function create($isNotExists = true){

    return $this->getScheme()->createTable($this, $isNotExists);

  }

  /**
   * [drop description]
   * @param  boolean $isExists [description]
   * @return [type]            [description]
   */
  public function drop($isExists = true){

    return $this->getScheme()->dropTable($this, $isExists);

  }

  /**
   * [exists description]
   * @return [type] [description]
   */
  public function exists(){

    return $this->getScheme()->existsTable($this);

  }

  /**
   * [truncate description]
   * @param  boolean $ignoreFK [description]
   * @return [type]            [description]
   */
  public function truncate($ignoreFK = false){

    return $this->getScheme()->truncate($this, $ignoreFK);

  }

  // Insertar valores
  /**
   * [insertInto description]
   * @param  [type] $values [description]
   * @param  array  $fields [description]
   * @return [type]         [description]
   */
  public function insertInto($values, array $fields = array()){

    return $this->getScheme()->insertInto($values, $this, $fields);

  }

  // Agregar una campo a la lista de campos
  /**
   * [addField description]
   * @param [type] $name  [description]
   * @param [type] $field [description]
   */
  public function addField($name = null, $field = null){

    if(is_string($field))
      $field = array('type' => $field);

    if($name instanceof AmField || is_array($name)){
      $field = $name;
      if($name instanceof AmField)
        $name = $field->getName();
      else
        $name = itemOr('name', $field);
    }

    if(is_array($field))
      $field = new AmField(array_merge(array('name' => $name), $field));

    $name = $field->getName();

    if($this->hasField($name))
      throw Am::e('AMSCHEMA_TABLE_ALREADY_HAVE_A_FIELD_NAMED',
        $this->getTableName(), $name);

    $this->fields[$name] = $field;

    // Si es campo primario se agrega a la
    // lista de campos primarios
    if($field->isPk())
      $this->addPk($name);

    return $this;

  }

  // Devuelve un Query que devuelve todos los registros de la Tabla
  /**
   * [all description]
   * @param  string  $alias      [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
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
    $q->setModel($this->getModel());

    // Asignar campos
    if($withFields){
      $fields = array_keys($this->getFields());
      $fields = array_combine($fields, $fields);
      $q->setSelects($fields);
      
    }

    return $q;

  }

  // Obtener consulta para buscar por un campos
  /**
   * [findBy description]
   * @param  [type]  $field      [description]
   * @param  [type]  $value      [description]
   * @param  string  $alias      [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public function findBy($field, $value, $alias = 'q', $withFields = false){

    return $this->all($alias, $withFields)->where("{$field}='{$value}'");

  }

  // Obtener todos los registros de buscar por un campos
  /**
   * [findAllBy description]
   * @param  [type]  $field      [description]
   * @param  [type]  $value      [description]
   * @param  [type]  $type       [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public function findAllBy($field, $value, $type = null, $withFields = false){

    return $this->findBy($field, $value, $withFields)->get($type);

  }

  // Obtener el primer registro de la busqueda por un campo
  /**
   * [findOneBy description]
   * @param  [type]  $field      [description]
   * @param  [type]  $value      [description]
   * @param  [type]  $type       [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public function findOneBy($field, $value, $type = null, $withFields = false){

    return $this->findBy($field, $value, $withFields)->row($type);
    
  }

  // Obtener la consulta para encontrar el registro con un determinado ID
  /**
   * [findById description]
   * @param  [type]  $id         [description]
   * @param  string  $alias      [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
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

      $fieldName = $pk;

      // Agregar condicion
      $field = $this->getField($pk);
      if($field)
        $fieldName = $field->getName();

      $q->where("{$fieldName}='{$id[$pk]}'");

    }

    return $q;

  }

  // Regresa un objeto con AmModel con el registro solicitado
  /**
   * [find description]
   * @param  [type]  $id         [description]
   * @param  [type]  $type       [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public function find($id, $type = null, $withFields = false){

    $q = $this->findById($id, $withFields);
    $r = isset($q)? $q->row($type) : false;

    return $r === false ? null : $r;

  }

  /**
   * [prepare description]
   * @param  array  $r [description]
   * @return [type]    [description]
   */
  public function prepare(array $r){

    foreach ($this->fields as $key => $field)
      $r[$key] = $field->parseValue(itemOr($key, $r));

    return $r;

  }

  // Convertir la tabla a Array
  /**
   * [toArray description]
   * @return [type] [description]
   */
  public function toArray(){

    // Convertir campos
    $fields = array();
    foreach($this->getFields() as $offset => $field)
      $fields[$offset] = $field->toArray();

    // Convertir refencias
    $referencesTo = array();
    foreach($this->getReferencesTo() as $offset => $field)
      $referencesTo[$offset] = $field->toArray();

    // Convertir referencias a
    $referencesBy = array();
    foreach($this->getReferencesBy() as $offset => $field)
      $referencesBy[$offset] = $field->toArray();

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
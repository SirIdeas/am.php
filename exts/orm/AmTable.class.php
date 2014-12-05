<?php

/**
 * Clase para las tablas de la BD
 */

class AmTable extends AmObject{

  // Propiedades de la tabla
  protected
    $name = null,             // Alias
    $tableName = null,        // Nombre en la BD
    $fields = null,           // Lista de campos
    $engine = null,           // Motor
    $charset = null,          // Set de caracteres
    $collate = null,          // Coleción de caracteres
    $pks = array(),           // Array de nombres de campos que forman el PK
    $referencesTo = array(),  // Tablas a las que hace referencia
    $referencesBy = array(),  // Tablas que le hacen referencia
    $validators = array(),    // Validadores
    $source = null,           // Fuente de datos
    $modelName = null;        // Nombre del model

  // Método para inicializar clases implementadas
  public function initialize(){}

  // Métodos GET de algunas propiedades
  public function getTableName(){ return $this->tableName; }
  public function getModelName(){ return $this->modelName; }
  public function getSource(){ return $this->source; }
  public function gerReferencesTo(){ return $this->referencesTo; }
  public function getReferencesBy(){ return $this->referencesBy; }
  public function getPks(){ return $this->pks; }
  public function getEngine(){ return $this->engine;}
  public function getCharset(){ return $this->charset;}
  public function getCollate(){ return $this->collate;}
  public function getFields(){ return $this->fields; }
  public function getField($name){ return $this->fields->$name; }

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
    $this->getField($fieldName)->getPrimaryKey(true);
      
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
    if($f->getPrimaryKey())
      $this->addPk($name);
      
  }

  // Obtener un listado de los campos primarios de una tabla
  public function sqlGetTablePrimaryKey(){  return $this->getSource()->sqlGetTablePrimaryKey($this); }
  public function getTablePrimaryKey(){     return $this->getSource()->getTablePrimaryKey($this); }

  // Obtener un listado de las columnas de una tabla
  public function sqlGetTableColumns(){ return $this->getSource()->sqlGetTableColumns($this); }
  public function getTableColumns(){    return $this->getSource()->getTableColumns($this); }

  // Obtener un listados de las referencias de la tabla
  public function sqlGetTableForeignKeysSql(){ return $this->getSource()->sqlGetTableForeignKeysSql($this); }
  public function getTableForeignKeys(){       return $this->getSource()->getTableForeignKeys($this); }
  
  // Obtener un listado de las referencias a la tabla
  public function sqlGetTableReferences(){  return $this->getSource()->sqlGetTableReferences($this); }
  public function getTableReferences(){     return $this->getSource()->getTableReferences($this); }

  // Crea la tabla
  public function sqlCreate(){  return $this->getSource()->sqlCreateTable($this); }
  public function create(){     return $this->getSource()->createTable($this); }

  // Elimina tabla de la Base de datos
  public function sqlDrop(){  return $this->getSource()->sqlDropTable($this); }
  public function drop(){     return $this->getSource()->dropTable($this) !== false; }

  // Vaciar una tabla
  public function sqlTruncate(){  return $this->getSource()->sqlTruncate($this); }
  public function truncate(){     return $this->getSource()->truncate($this) !== false; }

  // Insertar registros en la tabla
  public function sqlInsertInto($values, array $fields = array()){  return $this->getSource()->sqlInsertInto($this, $values, $fields); }
  public function insertInto($values, array $fields = array()){     return $this->getSource()->insertInto($this, $values, $fields); }

  // Devuelve si la tabla existe o no en la BD
  public function exist(){      return $this->getSource()->existsTable($this); }

  // Carga los columnas, referencias y PKs de la tabla desde la BD
  public function describeTable(){

    // Realizar asignacion
    $this->describeTableInner(
      $this->getTableForeignKeys(),
      $this->getTableReferences(),
      $this->getTablePrimaryKey(),
      $this->getTableColumns()
    );

  }

  // Cargar columnas, referencias y PKs a la tabla
  protected function describeTableInner(array $referencesTo = array(), array $referencesBy = array(), array $pks = array(), array $fields = array()){

    // Preparar referencias
    $this->referencesTo = new stdClass;
    foreach ($referencesTo as $name => $values){
      $this->referencesTo->$name = new AmRelation($values);
    }
    
    // Preparar referencias a
    $this->referencesBy = new stdClass;
    foreach ($referencesBy as $name => $values){
      $this->referencesBy->$name = new AmRelation($values);
    }
    
    // Preparar campos
    $this->fields = new stdClass;
    foreach($fields as $column){
      
      // Determinar si es o no parte del primary key
      $column['primaryKey'] = in_array(isset($column["name"])? $column["name"] : null, $pks);
      
      // Obtener el tipo
      $type = $this->getSource()->getTypeOf(isset($column["type"])? $column["type"] : null);
      
      // Sino es un tipo reconocido
      // volver al anterior
      if(false !== $type)
        $column['type'] = $type;
      
      // Agregar instancia del campo
      $this->addField(new AmField($column));
        
    }
      
  }

  // Convertir la tabla a Array
  public function toArray(){
    
    // Convertir campos
    $fields = array();
    foreach($this->fields() as $offset => $field){
      $fields[$offset] = $field->toArray();
    }
    
    // Convertir refencias
    $referencesTo = array();
    foreach($this->referencesTo() as $offset => $field){
      $referencesTo[$offset] = $field->toArray();
    }
    
    // Convertir referencias a
    $referencesBy = array();
    foreach($this->referencesBy() as $offset => $field){
      $referencesBy[$offset] = $field->toArray();
    }
    
    // Unir todas las partes
    return array(
      'tableName' => $this->tableName(),
      'modelName' => $this->modelName(),
      'engine' => $this->engine(),
      'charset' => $this->charset(),
      'collate' => $this->collate(),
      'fields' => $fields,
      'pks' => $this->getPks(),
      'referencesTo' => $referencesTo,
      'referencesBy' => $referencesBy,
    );
      
  }

  // Devuelve un Query que devuelve todos los registros de la Tabla
  public function qAll($as = 'q', $withFields = false){
      
    // por si se obvio el primer parametro
    if(is_bool($as)){
      $withFields = $as;
      $as = 'q';
    }

    // Crear consultar
    $q = $this->getCource()->newQuery($this, $as);

    // Asignar campos
    if($withFields){
      $fields = array_keys((array)$this->getFields());
      $fields = array_combine($fields, $fields);
      $q->setSelects($fields);
    }
    
    return $q;
      
  }

  // Obtener consulta para buscar por un campos
  public function findBy($field, $value){
    return $this->qAll()->where("$field='$value'");
  }

  // Obtener todos los registros de buscar por un campos
  public function findAllBy($field, $value, $type = null){
    return $this->findBy($field, $value)->getResult($type);
  }

  // Obtener el primer registro de la busqueda por un campo
  public function findOneBy($field, $value, $type = null){
    return $this->findBy($field, $value)->getRow($type);
  }

  // Obtener la consulta para encontrar el registro con un determinado ID
  public function findById($id){
    
    $q = $this->qAll();   // Obtener consultar para obtener todos los registros  
    $pks = $this->getPks();  // Obtener el primary keys
    
    // Si es un array no asociativo
    if(is_array($id) && !Am::isAssocArray($id)){
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
      $q->where("{$this->fields($pk)->getName()}='{$id[$pk]}'");
      
    }
    
    return $q;
      
  }

  // Regresa un objeto con AmModel con el registro solicitado
  public function find($id, $type = null){
    
    $q = $this->findById($id);
    $r = isset($q)? $q->getRow($type) : false;
    
    return $r === false ? null : $r;
    
  }

  // Devuelve la carpeta de la tabla actual
  public function getFolder(){
    return $this->getSource()->getTableFolder($this);
  }

}
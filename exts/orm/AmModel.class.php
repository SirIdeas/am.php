<?php

/**
 * Clase base para los modelos
 */

class AmModel extends AmObject implements Reflector{
  
  // Propiedades
  protected
    $table = null,        // Instancia de la tabla
    $isNew = true,        // Indica si es un registro nuevo
    $errors = array(),    // Listados de errores
    $realValues =array(), // Valores reales
    $errorsCount = 0;     // Cantidad de errores

  // El constructor se encarga de asignar la instancia de la tabla correspondiente al model
  public function __construct($params = null) {
    
    $params = AmObject::parse($params);
    
    // Obtener el nombre de la fuente
    $source = is_string($params['source']) ? $params['source'] : $params['source']->getName();

    // Obtener el nombre de la tabla
    $this->table = AmORM::getTable($params['tableName'], $source);
    
    unset($params['source']);
    unset($params['tableName']);
    
    $fields = (array)$this->getTable()->getFields();
    
    foreach($fields as $fieldName => $field){
      
      $fieldNameBD = $field->getName();
      
      $methodFieldName = "set_$fieldName";
      
      $value = null;
          
      if(isset($params[$fieldNameBD])){
        $value = $params[$fieldNameBD];
        unset($params[$fieldNameBD]);
      }else{
        $value = $field->defaultValue();
      }

      $this->$methodFieldName($field->parseValue($value));
      
    }
    
    $this->clearErrors();
    
    parent::__construct($params);
    
    $this->realValues = $this->toArray();
    
    $this->init();
    
  }
  
  public static function export() {
  }
  
  public function init(){
  }
  
  public function pkToString($encode = false){
    $ret = array();
    foreach($this->index() as $index){
      $ret[] = ($encode===true)? urlencode($index) : $index;
    }
    return implode('/', $ret);
  }
  
  public function __toString() {
    return $this->pkToString();
  }
  
  public function getTable(){ return $this->table; }
  public function isNew($value = null){ return $this->attr('isNew', $value); }
  public function errorsCount(){ return $this->errorsCount; }
  
  public function isValid($field = null){
    $this->validate($field);
    return $this->errorsCount() === 0;
  }
  
  /* Convierte solo la informacion en un array
   */
  public function dataToArray($withAutoIncrementFields = true){

    $ret = array();
    
    foreach($this->getTable()->getFields() as $fieldName => $field){
      if($withAutoIncrementFields || !$field->autoIncrement())
      $ret[$fieldName] = $this->$fieldName;
    }

    return $ret;

  }
  
  public function realValues($name = null){
    
    if(isset($name)){
      return itemOr($this->realValues, $name);
    }
    
    return $this->realValues;
    
  }
  
  public function hasChanged($name){
    
    return $this->realValues($name) != $this->$name;
    
  }

  public function changes(){
    $values = array();
    $changes = array();
    foreach($this->realValues as $name => $value){
      if($this->hasChanged($name)){
        $values[$name] = $value;
        $changes[$name] = $this->$name;
      }
    }
    return array(
      'from' => $values,
      'to' => $changes,
    );
  }

  /* Devuelve el indice correspondiente al registro
   */
  public function index(){
    
    $ret = array();

    foreach($this->getTable()->pks() as $pk){
      $ret[$pk] = $this->realValues($pk);
    }
    
    return $ret;
    
  }
  
  public function clearErrors(){
    
    $this->errors = array();
    $this->errorsCount = 0;

  }

  public function errors($field = null, $errorName = null, $errorMsg = null){
    
    // Se retorna todo los errores
    if(!isset($field)){

      return $this->errors;

    }elseif(!isset($this->errors[$field])){

      // Si no existe se crea el hash de errores
      $this->errors[$field] = array();

    }

    // Se devuelve el hash de errores del campo consultado
    if(!isset($errorName)){

      return $this->errors[$field];

    }

    // Se devuelve el error especifico del campo consultado
    if(!isset($errorMsg)){

      return $this->errors[$errorName];

    }

    // Se asigna el error
    $this->errors[$field][$errorName] = $errorMsg;
    $this->errorsCount++;
    return $this;

  }
  
  public function validateAll(){
    
    $this->clearErrors();
      
    foreach($this->getTable()->validators() as $field => $_){
      $this->validate($field);
    }
    
  }
  
  public function validate($field = null){
    
    if(!isset($field)){
      
      return $this->validateAll();
      
    }else{
      
      foreach($this->getTable()->validators($field) as $nameValidator => $validator){
        if(!$validator->isValid($this)){
          $this->errors($field, $nameValidator, $validator->message());
        }
      }
      
    }
    
  }

  public function save(){
    
    if($this->isValid()){
      
      if($this->isNew()){
        
        $ret = $this->insertInto();

        if($ret !== false){

          $this->isNew(false);
          
          $fields = $this->getTable()->getFields();

          foreach($fields as $f){
            
            $field = $f->getName();
            
            if($f->autoIncrement()){
              $this->$field($ret);
            }
            
          }
          
        }
        
        $this->realValues = $this->toArray();
        
        return $ret === 0 ? true : $ret;
        
      }
      
      if($this->update()){
        
        $this->realValues = $this->toArray();
        
        return true;
        
      }else{
        
        $this->errors('__global__',
            $this->getTable()->source()->errNo(),
            $this->getTable()->source()->error());
        
      }
      
    }
    
    return false;
    
    
  }
  
  public function getQuerySelectItem(){
    
    return $this->getTable()->queryFind($this->index());
    
  }
  
  protected function getQueryUpdate(){
    
    $t = $this->getTable();
    $q = $this->getQuerySelectItem();
    
    foreach($t->getFields() as $fieldName => $field){
      
      if($this->hasChanged($fieldName)){
        
        $q->set($field->getName(), $this->$fieldName);
        
      }
      
    }
    
    return $q;
  }
  
  public function insertIntoSql(){ return $this->getTable()->insertIntoSql(array($this)); }
  public function insertInto(){
    
    return $this->getTable()->insertInto(array($this));
    
  }
  
  public function updateSql(){ return $this->getQueryUpdate()->updateSql(); }
  public function update(){ return $this->getQueryUpdate()->update(); }
  
  public function deleteSql(){ return $this->getQuerySelectItem()->deleteSql(); }
  public function delete(){ return $this->getQuerySelectItem()->delete() !== false; }
  
  public function setValues($values, array $fields = array()){
    
    if(empty($fields)){
      $fields = array_keys((array)$this->getTable()->getFields());
    }
    
    foreach($this->getTable()->referencesTo() as $rel){
      foreach(array_keys($rel->columns()) as $from){
        
        $value = trim(itemOr($values, $from, ''));
        $values[$from] = empty($value) ? null : $value;
        
      }
    }
    
    foreach($fields as $fieldName){
      $field = $this->getTable()->fields($fieldName);
      if($field === false || !$field->autoIncrement()){
        $this->$fieldName = itemOr($values, $fieldName);
      }
    }
    
  }

}

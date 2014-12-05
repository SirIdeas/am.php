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

  // Indica su un campo forma o no parte del primary key de la tabla
  public function isPk($fieldName){
    return in_array($fieldName, $this->pks());
  }

  // Retornar todos los campos
  public function getFields(){
    return $this->fields;
  }

  //Retorna un campo en específico
  public function getField($name){
    return $this->fields->$name;
  }

  // Asigna un campo
  public function setField($name, AmField $field){
    $this->fields->$name = $field;
    return $this;
  }

}
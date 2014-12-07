<?php

/**
 * Validacion de un campo unico
 */

class UniqueValidator extends AmValidator{
  
  protected
      $extraFields = array(); //
    
  protected function validate(AmModel &$model){
    
    // Crear una consulta de todos los registro en la tabla del model
    // Con el mismo valor de actual del modelo en el campo evaluado
    $result = $model->getTable()->qAll()
      ->where("{$this->getFieldName()}='{$this->value($model)}'");

    // Se agrega una condicion and por cada campo extra configurado
    foreach($this->extraFields as $f){
      $fn = $model->getTable()->getField($f)->getName();
      $result->andWhere("{$fn}='{$model->get_{$f}()}'");
    }

    // Crear una consulta de seleccion del modelo evaluado
    $q = $model->getQuerySelectItem();

    // Si se creó una consulta satisfactoriamente
    if(isset($q)){

      // Seleccionar el campo evaluado 
      $q->select($this->getFieldName());

      // Para evitar de que cuente al registro actual
      $result->andWhere('not', array($this->getFieldName(), 'in', $q));

    }
    
    // Si la consulta devuelve 0 registro entonces el modelo
    // tiene un valor unico
    return $result->count() == 0;

  }

  // Campos extras del unique
  public function getExtraFields(){ return $this->extraFields; }
  public function setExtraFields($value){ $this->extraFields = $value; return $this; }
  

}

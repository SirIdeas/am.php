<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de un campo unico
 */
class UniqueValidator extends AmValidator{

  protected

    /**
     * Lista de nombres de campos que forman parte de la clave única.
     * @var array
     */
    $fields = array(),

    /**
     * Condiciones para los índices.
     * @var array
     */
    $conditions = array();

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Obtener la tabla para el modelo
    $table = $model->getTable();

    // Crear una consulta de todos los registro en la tabla del model
    // Con el mismo valor de actual del modelo en el campo evaluado
    $query = $table->all()->where($this->conditions);

    // Obtener los campos del indice unico
    $fields = $this->fields;

    // Agregamos el campo que se evalua a la lista de campos
    if($table->getField($this->getFieldName()))
      array_unshift($fields, $this->getFieldName());

    // Eliminar campos repetidos
    $fields = array_unique($fields);

    // Se agrega una condicion and por cada campo extra configurado
    foreach($fields as $field){
      // Agregar la condicion
      $query->andWhere("{$field}='{$model->$field}'");
    }
    
    // Agregar condiciones para excluir el registro evaluado
    $index = $table->indexOf($model);
    foreach ($index as $key => $value)
      $index[$key] = "{$key}='{$value}'";

    // Agregar condiciones para excluir el registro evaluado
    $query->andWhere('not', array_values($index));

    // Si la consulta devuelve 0 registro entonces el modelo
    // tiene un valor unico
    return $query->count() == 0;

  }

  /**
   * Devuelve la lista de nombre de campos que conforman la llave única.
   * @return array Lista de nombre de campos.
   */
  public function getFields(){

    return $this->fields;

  }
  
  /**
   * Asigna la lista de nombre de campos que conforman la llave única.
   * @param  array $value Lista de nombre de campos.
   * @return $this
   */
  public function setFields($value){

    $this->fields = $value;
    return $this;

  }

  /**
   * Devuelve la lista de condiciones extras a aplicar la llave única.
   * @return array Lista de condiciones.
   */
  public function getConditions(){

    return $this->conditions;

  }

  /**
   * Asigna la lista de condiciones extras a aplicar la llave única.
   * @param  array $value Lista de condiciones.
   * @return $this
   */
  public function setConditions($value){

    $this->conditions = $value;
    return $this;

  }


}

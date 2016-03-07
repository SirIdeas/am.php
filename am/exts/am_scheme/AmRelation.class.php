<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para las relaciones entre las tablas.
 */
class AmRelation extends AmObject{

  protected
    
    /**
     * Modelo al que apunta la relación.
     */
    $model = '',

    /**
     * Joins de la relación
     */
    $joins = array(),
    
    /**
     * Hash de columnas relacionadas.
     */
    $cols = array();
    
  /**
   * Devuelve el nombre del model a la que referencia.
   * @return string Nombre del model.
   */
  public function getModel(){
    
    return $this->model;

  }

  /**
   * Devuelve el hash con las columnas relacionadas.
   * @return hash Hash con las columnas relacionadas.
   */
  public function getCols(){
    
    return $this->cols;

  }

  /**
   * Devuelve el hash con las relaciones extras.
   * @return hash Hash con las relaciones extras.
   */
  public function getJoins(){
    
    return $this->joins;

  }
    
  /**
   * Devuelve el intancia de la tabla a la que apunta la realación.
   * @return AmTable Instancia de la tabla.
   */
  public function getTable(){

    // Obtener el modelo
    $classModel = $this->model;
    
    // Devolver la tabla
    return $classModel::me();

  }

  /**
   * Generador de la consulta para la relación basado en un modelo.
   * @param  AmModel $model Instancia de AmModel a la que se quiere obtener
   *                        la relación.
   * @return AmQuery        Consulta generada.
   */
  public function getQuery(AmModel $model){

    // Obtener la tabla
    $table = $this->getTable();

    // Obtener el nombre de la tabla.
    $tableName = $table->getTableName();

    // Obtener una consulta con todos los elmentos.
    $query = $table->all($tableName)
      ->select("{$tableName}.*");

    // Obtener las columnas
    $cols = $this->getCols();

    // Obtener las joins
    $joins = $this->getJoins();

    foreach ($joins as $refTableName => $on) {
      foreach ($on as $from => $to) {
        $on[$from] = "{$from}={$to}";
      }
      $query->innerJoin($refTableName, implode('-', $on), $refTableName);
    }

    // Agregar condiciones de la relacion
    foreach($cols as $from => $to)
      $query->where("{$to}='{$model->$from}'");

    // Devolver query
    return $query;

  }

  /**
   * Devuelve una array con los datos de la relación.
   * @return array Relación como un array.
   */
  public function toArray(){

    return array(
      'model' => $this->model,
      'cols' => $this->cols,
      'joins' => $this->joins,
    );

  }

}

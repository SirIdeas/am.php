<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para instancia las claves foráneas entre las tablas.
 */
class AmRelation extends AmObject{

  protected
    
    /**
     * Instancia del registro al que pertenece el modelo relación
     */
    $record = null,

    /**
     * Instancia de la relación
     */
    $foreign = null;
    
  /**
   * Devuelve el nombre del model a la que referencia.
   * @return string Nombre del model.
   */
  public function getRecord(){
    
    return $this->record;

  }
    
  /**
   * Instancia de la la clave foránea.
   * @return string Nombre del model.
   */
  public function getForeign(){
    
    return $this->foreign;
    
  }

  /**
   * Devuelve la query para obtener los registros relacionados
   * @return AmQuery Instancia del query
   */
  protected function getQuery(){

    return $this->foreign->getQuery($this->record);

  }

  public function updateRecord(){

  }

  /**
   * [create description]
   * @param  [type] $record  [description]
   * @param  [type] $foreign [description]
   * @return [type]          [description]
   */
  public static function create($record, $foreign){

    $className = 'Am'.camelCase($foreign->getType(), true).'Relation';

    return new $className(array(
      'record' => $record,
      'foreign' => $foreign,
    ));

  }

}
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
abstract class AmRelation extends AmObject{

  protected

    /**
     * Método permitidos
     */
    $allodMethods = true,

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

  public function __call($method, $arguments){

    if($this->allodMethods === true || (is_array($this->allodMethods) &&
      in_array($method, $this->allodMethods))){

      $callback = array($this->_get(), $method);

      if(isValidCallback($callback)){

        return call_user_func_array($callback, $arguments);

      }


    }

  }

  abstract public function _get();

  public static function create($record, $foreign){

    $className = 'Am'.camelCase($foreign->getType(), true).'Relation';

    return new $className(array(
      'record' => $record,
      'foreign' => $foreign,
    ));

  }

}
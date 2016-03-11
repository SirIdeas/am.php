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
class AmHasManyAndBelongToRelation extends AmRelation{

  protected
    $collection = array();

  public function _get($reload = false){

    return $this->getQuery();

  }

  public function add(AmModel $record){

    return $this;

  }

  public function remove(AmModel $record){

    return $this;

  }

  /**
   * Método que se ejecuta antes de guardar del registro propietario.
   */
  public function beforeSave(){}

  /**
   * Método que se ejecuta después de guardar del registro propietario.
   */
  public function afterSave(){}
  
}
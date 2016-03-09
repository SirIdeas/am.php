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
class AmHasManyRelation extends AmRelation{

  protected
    $current = null;

  /**
   * [get description]
   * @return [type]           [description]
   */
  public function get(){

    if(!$this->current){
      $this->current = $this->current = $this->getQuery()->get();
    }

    return $this->current;

  }

}
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * 
 */
// PENDIENTE
class AmRaw{

  protected $value = null;

  public function __construct($value){

    $this->value = $value;
    
  }

  public function __toString(){

    return $this->value;

  }

}
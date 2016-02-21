<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacion de valores enteros
 */

AmORM::validator('regex');

class BitValidator extends RegexValidator{

  public function __construct($options = array()){
    $options['regex'] = '/^[\01]+$/';
    parent::__construct($options);
  }

}

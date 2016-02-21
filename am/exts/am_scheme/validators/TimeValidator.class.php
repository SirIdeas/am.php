<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacion de capos tipos telefono
 */

AmORM::validator("regex");

class TimeValidator extends RegexValidator{

  public function __construct($options = array()){
    $options["regex"] = "/^([01]?[0-9]|2[0-3]):[0-5][0-9](|:[0-5][0-9])$/";
    parent::__construct($options);
  }

}

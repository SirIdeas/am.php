<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacion para un campo tipo fecha
 */

AmORM::validator("regex");

class DateValidator extends RegexValidator{

  public function __construct($options = array()){
    $options["regex"] = "/^[0-9]{4}-(0[0-9]?|1[0-2]?)-([012]?[0-9]?|3[01]?)$/";
    parent::__construct($options);
  }

}


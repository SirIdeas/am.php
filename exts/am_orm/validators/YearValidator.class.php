<?php

/**
 * Validacion de valores enteros
 */

AmORM::validator('range');

class YearValidator extends AmValidator{

  protected
    $rangeLarge = null, // Validator for range large
    $rangeShort = null, // Validator for range short

  public function __construct($options = array()){
    parent::__construct($options);
    $this->rangeLarge = new RangeValidator($options);
    $this->rangeLarge->setMin(1901);
    $this->rangeLarge->setMax(2155);

    $this->rangeShort = new RangeValidator($options);
    $this->rangeShort->setMin(0);
    $this->rangeShort->setMax(99);
  }

  // La validacion consiste en cumplir con los dos validadores
  protected function validate(AmModel &$model){
    return $this->rangeShort->validate($model) || $this->rangeLarge->validate($model);
  }

}

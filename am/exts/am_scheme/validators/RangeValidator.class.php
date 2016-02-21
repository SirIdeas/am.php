<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validacion del rango de un campo
 */

AmORM::validator("min_value");
AmORM::validator("max_value");

class RangeValidator extends AmValidator{

  protected
    $minValidator = null, // Instancia del validador inferior
    $maxValidator = null, // Instancia del validador superior
    $min = null,
    $max = null;

  public function __construct($options = array()){
    $this->setSustitutions("max", "max");
    $this->setSustitutions("min", "min");

    // Get attrs of validators
    $min = isset($options["min"])? $options["min"] : null;
    $max = isset($options["max"])? $options["max"] : null;
    unset($options["min"]);
    unset($options["max"]);

    $this->minValidator = new MinValueValidator($options);
    $this->maxValidator =new MaxValueValidator($options);

    $this->setMin($min);
    $this->setMax($max);

    parent::__construct($options);
  }

  // La validacion consiste en cumplir con los dos validadores
  protected function validate(AmModel &$model){
    return $this->getMinValidator()->validate($model) && $this->getMaxValidator()->validate($model);
  }

  // Al setear el valor de validador se debe cambiar tambien a los validadores internos
  public function setFieldName($value = null){
    $this->getMinValidator()->setFieldName($value);
    $this->getMaxValidator()->setFieldName($value);
    return parent::setFieldName($value);
  }

  // Limite inferior
  public function getMinValidator(){ return $this->minValidator; }
  public function getMin(){ return $this->getMinValidator()->getMin(); }
  public function setMin($value){ return $this->getMinValidator()->setMin($value); }

  // Limite superior
  public function getMaxValidator(){ return $this->maxValidator; }
  public function getMax(){ return $this->getMaxValidator()->getMax(); }
  public function setMax($value){ return $this->getMaxValidator()->setMax($value); }

}

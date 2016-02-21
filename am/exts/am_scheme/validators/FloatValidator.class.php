<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacion de valores flotantes
 */

AmORM::validator("regex");

class FloatValidator extends RegexValidator{

  protected
    $precision = null,  // Amount of digits
    $decimals = null;  // Amount of digits of decimal part

  public function __construct($options = array()){
    $this->setSustitutions("precision", "precision");
    $this->setSustitutions("decimals", "decimals");
    parent::__construct($options);
  }

  protected function validate(AmModel &$model){
    $this->setRegex($this->buildRegex());
    return parent::validate($model);
  }

  // Amount of digits
  public function getPresicion(){ return $this->precision; }
  public function setPresicion($precision){ $this->precision; return $this; }

  // Amount of decimal part
  public function getDecimals(){ return $this->decimals; }
  public function setDecimals($decimals){ $this->decimals; return $this; }

  // Build regex for evaluate value
  private function buildRegex(){
    $s = $this->scale;
    $p = $this->precision;
    $p = !empty($p) && !empty($s)? $p - $s : $p;
    $s = empty($s)? "" : "\.?[0-9]{0,{$s}}";
    $p = empty($p)? ".*" : "{0,{$p}}";
    $regex = "/^[0-9]".$p.$s."$/";
    return $regex;
  }

}

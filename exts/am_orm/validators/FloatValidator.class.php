<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/
 
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

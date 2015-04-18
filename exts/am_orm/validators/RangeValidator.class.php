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
 * Validacion del rango de un campo
 */

AmORM::validator("min_value");
AmORM::validator("max_value");

class RangeValidator extends AmValidator{

  protected
    $minValidator = null, // Instancia del validador inferior
    $maxValidator = null, // Instancia del validador superior
    $min = null,
    $max = nulll;

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

    $this->min($min);
    $this->max($max);

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

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
 * Validacion por un método especifico del modelo
 */

class CustomValidator extends AmValidator{

  protected
    $fnName = null; // Nombre del metodo con el que se realizará la validacion

  public function __construct($options = array()){
    $options["force"] = itemOr("force", $options, true);
    parent::__construct($options);
  }

  protected function validate(AmModel &$model){
    $fnName = $this->getFnName();
    // Si no se asigna el nombre del validator
    // se tomara como metodo como prefijo "validator_$campoValidando"
    if(!$fnName)
      $fnName = "validator_{$this->getFieldName()}";
    return method_exists($model, $fnName)? $model->$fnName($this) : false;
  }

  public function getFnName(){ return $this->fnName; }
  public function setFnName($value){ return $this->fnName = $value; }

}

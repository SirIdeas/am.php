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
 * Validacion con regex
 */

class RegexValidator extends AmValidator{

  // Propiedades
  protected
    $regex = null,      // Regex con la que se evaluará la validez
    $canBlank = false;  // Si permite valores vacios

  // Constructor
  public function __construct($options = array()){

    // Obtner si permiterá valores blancos
    $blank = isset($options["blank"])? $options["blank"] : false;
    unset($options["blank"]);
    $this->setCanBlank($blank === true);

    parent::__construct($options);
  }

  // Condicion de validacion
  protected function validate(AmModel &$model){
    return (preg_match($this->getRegex(), $this->value($model)) ||
        ($this->getCanBlank() && trim($this->value($model) == "")));
  }

  // Regex
  public function getRegex(){     return $this->regex; }
  public function setRegex($value){ $this->regex = $value; return $this; }

  // Can Blank
  public function getCanBlank(){  return $this->canBlank; }
  public function setCanBlank($value){ $this->canBlank = $value; return $this; }

}

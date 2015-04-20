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
 * Validacion de campo referencia de otra tabla
 */

class InQueryValidator extends AmValidator{

  protected
    $query = null,  // Indica la cosulta que tiene todo los posibles valores para el campo
                    // Por lo general es una consulta qAll de un tabla, a menos q se
                    // se requiran otras condiciones
    $field = null;  // campo de la consulta por la que se buscara


  public function __construct($options = array()){

    // Agregar los dos campos al sustitucion
    $this->setSustitutions("query", "query");
    $this->setSustitutions("field", "field");

    // Llamar constructor de la metaclase
    parent::__construct($options);

    // Agregar el campo a la consulta por si no existe aun
    $this->query()->select($this->field());

  }

  protected function validate(AmModel &$model){

    $field = $this->field();
    $value = $this->value($model);
    $q = $this->query();

    $qq = $q->source()->newQuery($q, "qq")
        ->select($field)
        ->where("$field = $value");

    return $qq->getRow('array') !== false;
  }

  // Consulta en la que se basará la referencia a validar
  public function getQuery(){ return $this->query; }
  public function setQuery($value){ $this->query = $value; return $this; }

  // Campo de la consulta al que referencia el campo
  public function getField(){ return $this->field; }
  public function setField($value){ $this->field = $value; return $this; }

}

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
 * Validacion para un campo tipo fecha
 */

class DateValidator extends AmValidator{

  protected
    $format = array("year", "month", "day");  // Formato de la fecha valida

  protected function validate(AmModel &$model){

    // Obtener un array asociativo con los valores de la fecha
    $date = $this->parseDate($this->value($model));

    // Si algun campo está vacío se asigna 0 en dicha posicion
    foreach($date as $i => $v)
      if(trim($v) == "")
        $date[$i] = 0;

    // Mezclar con valores por defecto
    $date = array_merge(array("month" => 0, "day" => 0, "year" => 0), $date);

    // Se valida si se trata de una fecha valida
    return checkdate($date["month"], $date["day"], $date["year"]);

  }

  // Campo con el formato
  public function getFormat(){ return $this->format; }
  public function setFormat($value){ $this->format = $value; return $this; }

  private function parseDate($date){

    $format = $this->getFormat();
    $date = explode("/", $date);  // Dividir la fecha por los "/"

    // Crea un array asociativo con los valores
    // de la fecha
    $ret = array();
    foreach($date as $i => $v)
      $ret[$format[$i]] = $date[$i];

    return $ret;

  }

}

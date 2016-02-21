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

class DateValidator extends RegexValidator{

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

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validacion de valores enteros
 */

class DatetimeValidator extends AmValidator{

  // Condicion de validacion
  protected function validate(AmModel &$model){
    return self::checkTimestamp($this->value($model)) || trim($this->value($model) == "");
  }

  // Verifica si una fecha tiempo es valida.
  public static function checkTimestamp($date){
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $date, $matches))
      if (checkdate($matches[2], $matches[3], $matches[1]))
        return true;
    return false;
  }

}

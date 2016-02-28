<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

AmScheme::validator('datetime');

/**
 * Validación de campos don formato de Fecha y Hora.
 */
class TimestampValidator extends DatetimeValidator{

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    return

      // Es válido si está vacío
      trim($this->value($model) == '')

      // Chequear la fecha.
      || self::checkTimestamp($this->value($model));

  }

  // Verifica si una fecha tiempo es valida.
  public static function checkTimestamp($date){

    // Obtener las partes de la fecha.
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $date, $matches))
      // Chequear la fecha.
      // 
      if (checkdate($matches[2], $matches[3], $matches[1]))
        return true;
    return false;

  }

}

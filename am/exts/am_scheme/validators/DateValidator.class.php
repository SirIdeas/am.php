<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de campos con formao de fechas simples.
 */
class DateValidator extends AmValidator{

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){
    
    $value = $this->value($model);
    return !empty($value) && !!strtotime($value.' 00:00:00');

  }

}


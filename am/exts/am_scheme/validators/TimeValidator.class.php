<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de campos con formato de hora simple.
 */
class TimeValidator extends AmValidator{

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){
    
    $value = $this->value($model);
    return !empty($value) && !!strtotime('2000/01/01 '.$value);

  }

}

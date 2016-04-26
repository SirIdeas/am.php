<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de campos no nulos.
 */
class NotNullValidator extends AmValidator{
  
  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Validar que el valor no se nulo.
    return $this->value($model) !== null;
    
  }

}

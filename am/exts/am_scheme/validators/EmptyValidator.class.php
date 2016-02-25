<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

AmScheme::validator("null");

/**
 * Validación de campos no vacíos.
 */
class EmptyValidator extends NullValidator{

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Validar que el valor no se nulo ni vacío.
    return parent::validate($model) && trim($this->value($model)) != "";

  }

}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacion para valores nulos
 */

class NullValidator extends AmValidator{

  protected function validate(AmModel &$model){
    return $this->value($model) !== null;
  }

}

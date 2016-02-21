<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validacion para valores vacíos
 */

AmORM::validator("null");

class EmptyValidator extends NullValidator{

  protected function validate(AmModel &$model){
    return parent::validate($model) && trim($this->value($model)) != "";
  }

}

<?php

/**
 * Validacion de valores enteros
 */

AmORM::validator('regex');

class DatetimeValidator extends AmValidator{

  // Condicion de validacion
  protected function validate(AmModel &$model){
    return checkTimestamp($this->value($model)) || trim($this->value($model) == "");
  }

}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de años.
 */
class YearValidator extends RangeValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){
    parent::__construct($options);

    // Asignar valor mínimo o máximo.
    $this->setMin(1901);
    $this->setMax(2155);

  }
}

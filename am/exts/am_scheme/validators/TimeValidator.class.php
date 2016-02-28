<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator('regex');

/**
 * Validación de campos con formato de hora simple.
 */
class TimeValidator extends RegexValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Asignar REGEX para validar enteros.
    $options['regex'] = '/^([01]?[0-9]|2[0-3]):[0-5][0-9](|:[0-5][0-9])$/';

    // Constructor padre.
    parent::__construct($options);

  }

}

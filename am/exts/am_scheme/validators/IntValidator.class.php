<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de campos enteros.
 */
class IntValidator extends RegexValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Asignar REGEX para validar enteros.
    $options['regex'] = '/^[\-0-9]+$/';

    // Constructor padre.
    parent::__construct($options);

  }

}

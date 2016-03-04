<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de valores enteros
 */
class BitValidator extends RegexValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar la REGEX para validar binarios.
    $options['regex'] = '/^[01]+$/';

    // Constructor padre.
    parent::__construct($options);

  }

}

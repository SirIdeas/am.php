<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator('regex');

/**
 * Validación de campos con formato de teléfono.
 */
class PhoneValidator extends RegexValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar la REGEX para validar teléfonos.
    $options['regex'] = '/^(\(?[0-9]{3,3}\)?|[0-9]{3,3}[-. ]?)[ ][0-9]{3,3}[-. ]?[0-9]{4,4}$/';

    // Constructor padre.
    parent::__construct($options);

  }

}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator("regex");

/**
 * Validación de campos con formao de fechas simples.
 */
class DateValidator extends RegexValidator{

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Asignar REGEX para validar fechas simples.
    $options["regex"] = "/^[0-9]{4}-(0[0-9]?|1[0-2]?)-([012]?[0-9]?|3[01]?)$/";

    // Constructor padre.
    parent::__construct($options);

  }

}


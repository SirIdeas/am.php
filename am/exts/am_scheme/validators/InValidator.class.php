<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación de posibles valores para un campo.
 */
class InValidator extends AmValidator{

  protected
    /**
     * Lista de valores válidos para el campos.
     */
    $values = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar campo a las sustituciones.
    $this->setSustitutions('values', 'values');

    // Constructor padre.
    parent::__construct($options);

  }

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    return in_array($this->value($model), $this->values());

  }

  /**
   * Devuelve el listado de valores válidos para el campo.
   * @return array Listado de valores.
   */
  public function getValues(){

    return $this->values;

  }

  /**
   * Asigna el listado de valores válidos para un campo.
   * @param  string $value Listado de valores.
   * @return $this
   */
  public function setValues($value){

    $this->values = $value;
    return $this;

  }


}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación del tamano máximo de un campo
 */
class MaxLengthValidator extends AmValidator{

  protected

    /**
     * Tamaño máximo del campo.
     */
    $max = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar un nuevo campo a las sustituciones.
    $this->setSustitutions('max', 'max');

    // Constructor padre.
    parent::__construct($options);

  }
  
  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){
    $max = $this->getMax();
    return $max != null && strlen(trim($this->value($model))) <= $max;
  }

  /**
   * Devuelve el tamaño máximo.
   * @return int Tamaño máximo.
   */
  public function getMax(){

    return $this->max;

  }

  /**
   * Asigna el tamaño máximo.
   * @param  int   $value Tamaño máximo.
   * @return $this
   */
  public function setMax($value){

    $this->max = $value;
    return $this;

  }

}

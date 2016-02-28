<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator('float');

/**
 * Validación del límite superior de un campo.
 */
class MaxValueValidator extends FloatValidator{

  protected

    /**
     * Límite superior del campo
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

    // Obtener el valor máximo.
    $max = $this->getMax();

    // Si tenía el valor máximo asignado y verficar que sea menor.
    return $max != null && $this->value($model) <= $max;

  }

  /**
   * Devuelve el límite superior.
   * @return int Límite superior.
   */
  public function getMax(){

    return $this->max;

  }

  /**
   * Asigna el límite superior.
   * @param  int   $value Límite superior.
   * @return $this
   */
  public function setMax($value){

    $this->max = $value;
    return $this;

  }

}

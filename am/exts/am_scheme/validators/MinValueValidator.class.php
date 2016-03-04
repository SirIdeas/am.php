<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validación del límite inferior de un campo.
 */
class MinValueValidator extends FloatValidator{

  protected

    /**
     * Límite inferior del campo
     */
    $min = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar un nuevo campo a las sustituciones.
    $this->setSustitutions('min', 'min');

    // Constructor padre.
    parent::__construct($options);

  }
  
  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Obtener el valor mínimo.
    $min = $this->getMin();

    // Si tenía el valor mímino asignado y verficar que sea menor.
    return $min != null && $this->value($model) >= $min;

  }

  /**
   * Devuelve el límite inferior.
   * @return int Límite inferior.
   */
  public function getMin(){

    return $this->min;

  }

  /**
   * Asigna el límite inferior.
   * @param  int   $value Límite inferior.
   * @return $this
   */
  public function setMin($value){

    $this->min = $value;
    return $this;

  }

}

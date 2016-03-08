<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validación del tamano minimo de un campo
 */
class MinLenValidator extends AmValidator{

  protected

    /**
     * Tamaño mínimo del campo.
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
    $min = $this->getMin();
    return $min != null && strlen(trim($this->value($model))) >= $min;
  }

  /**
   * Devuelve el tamaño mínimo.
   * @return int Tamaño mínimo.
   */
  public function getMin(){

    return $this->min;

  }

  /**
   * Asigna el tamaño mínimo.
   * @param  int   $value Tamaño mínimo.
   * @return $this
   */
  public function setMin($value){

    $this->min = $value;
    return $this;

  }

}

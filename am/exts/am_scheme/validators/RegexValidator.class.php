<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Validacación con REGEX.
 */
class RegexValidator extends AmValidator{

  protected

    /**
     * String con la expresión regular para validar.
     */
    $regex = null,

    /**
     * Si permite valore vacíos.
     */
    $allowEmpty = false;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Obtner si permiterá valores blancos
    $blank = isset($options['blank'])? $options['blank'] : false;
    unset($options['blank']);
    $this->setAllowEmpty($blank === true);

    parent::__construct($options);
  }
  
  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    return
    
      // Es válido si hace match con la regex
      preg_match($this->getRegex(), $this->value($model))

      // o si se permite valores vacío y el campo está vacío.
      || ($this->getAllowEmpty() && trim($this->value($model)) == '');

  }

  /**
   * Devulve la REGEX utilizara para validar.
   * @return string REGEX para validar
   */
  public function getRegex(){

    return $this->regex;

  }

  /**
   * Devuelve si se permiten valores vacíos.
   * @return bool Si se permite valores vacíos.
   */
  public function getAllowEmpty(){

    return $this->AllowEmpty;

  }

  /**
   * Asigna la REGEX a utilizar para validar.
   * @param  string $value REGEX para validar.
   * @return $this
   */
  public function setRegex($value){

    $this->regex = $value;
    return $this;

  }

  /**
   * Asigna si se permite valores vacíos.
   * @param bool   $value Si se permiten valores vacíos.
   * @return $this
   */
  public function setAllowEmpty($value){

    $this->AllowEmpty = $value;
    return $this;

  }

}

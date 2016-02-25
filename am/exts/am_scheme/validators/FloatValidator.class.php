<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator("regex");

/**
 * Validación de valores flotantes
 */
class FloatValidator extends RegexValidator{

  protected
    /**
     * Cantidad de dígitos para la parte entera.
     */
    $precision = null,

    /**
     * Cantidad de dígitos para la parte decimal.
     */
    $decimals = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar nuevos campos a las sustituciones.
    $this->setSustitutions("precision", "precision");
    $this->setSustitutions("decimals", "decimals");

    // Constructor padre.
    parent::__construct($options);

  }

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Crea la REGEX para evaluar y la asigna.
    $this->setRegex($this->buildRegex());

    // Realiza la validación.
    return parent::validate($model);

  }

  /**
   * Devuelve la cantidad de dígitos para la parte entera.
   * @return int Cantidad de dígitos
   */
  public function getPresicion(){

    return $this->precision;

  }

  /**
   * Asigna la cantidad de digitos para la parte entera.
   * @param  int $precision Cantidad de dígitos para la parte entera.
   * @return $this
   */
  public function setPresicion($value){

    $this->precision = $value;
    return $this;

  }

  /**
   * Devuelve la cantidad de dígitos para la parte decimal.
   * @return int Cantidad de dígitos
   */
  public function getDecimals(){

    return $this->decimals;

  }

  /**
   * Asigna la cantidad de digitos para la parte decimal.
   * @param  int $decimals Cantidad de dígitos para la parte decimal.
   * @return $this
   */
  public function setDecimals($value){

    $this->decimals = $value;
    return $this;

  }

  /**
   * Construye la REGEX para validar.
   * @return string REGEX para evaluar.
   */
  private function buildRegex(){
    $s = $this->scale;
    $p = $this->precision;
    $p = !empty($p) && !empty($s)? $p - $s : $p;
    $s = empty($s)? "" : "\.?[0-9]{0,{$s}}";
    $p = empty($p)? ".*" : "{0,{$p}}";
    $regex = "/^[0-9]".$p.$s."$/";
    return $regex;
  }

}

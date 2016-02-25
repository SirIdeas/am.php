<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
AmScheme::validator('min_length');
AmScheme::validator('max_length');

/**
 * Validación del tamano minimo y maximo
 */
class LengthValidator extends AmValidator{

  protected
    /**
     * Instancia del validador inferior.
     */
    $minValidator = null,

    /**
     * Instancia del validador superior.
     */
    $maxValidator = null,

    /**
     * Tamaño mínimo del campo.
     */
    $min = null,

    /**
     * Tamaño máximo del campo.
     */
    $max = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar nuevos campos a las sustituciones.
    $this->setSustitutions("max", "max");
    $this->setSustitutions("min", "min");

    // Instancia los validadores de los límites.
    $this->minValidator = new MinLengthValidator($options);
    $this->maxValidator = new MaxLengthValidator($options);

    // Constructor padre.
    parent::__construct($options);

  }

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Debe cumplir con los dos validadores
    return
      $this->getMinValidator()->validate($model) &&
      $this->getMaxValidator()->validate($model);
  }

  /**
   * Asigna el nombre del campo que evalará el validador.
   * Sobre carga para asignar el nombre del campo a las instancias de los
   * validadores internos.
   * @param string $value Nombre del campo.
   */
  public function setFieldName($value = null){

    $this->getMinValidator()->setFieldName($value);
    $this->getMaxValidator()->setFieldName($value);

    return parent::setFieldName($value);

  }

  /**
   * Devuelve el tamaño máximo.
   * @return int Tamaño máximo.
   */
  public function getMax(){

    return $this->max;

  }

  /**
   * Devuelve el tamaño mínimo..
   * @return int Tamaño mínimo..
   */
  public function getMin(){

    return $this->min;

  }

  /**
   * Asigna el tamaño máximo.
   * @param  int   $value Tamaño máximo.
   * @return $this
   */
  public function setMax($alue){

    // Asignar límite a la instancia de validadore correspondiente.
    $this->getMaxValidator()->setMax($value);
    $this->max = $alue;
    return $this;

  }

  /**
   * Asigna el tamaño mínimo..
   * @param  int   $value Tamaño mínimo..
   * @return $this
   */
  public function setMin($value){

    // Asignar límite a la instancia de validadore correspondiente.
    $this->getMinValidator()->setMin($value);
    $this->min = $value;
    return $this;

  }

  /**
   * Devuelve la instancia del validador superior.
   * @return MaxValueValidator Instancia del validador.
   */
  public function getMaxValidator(){

    return $this->maxValidator;

  }

  /**
   * Devuelve la instancia del validador inferior.
   * @return MinValueValidator Instancia del validador.
   */
  public function getMinValidator(){

    return $this->minValidator;

  }

}

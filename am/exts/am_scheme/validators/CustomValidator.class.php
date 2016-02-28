<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validación por un método especifico del modelo.
 */
class CustomValidator extends AmValidator{

  protected

    /**
     * Si se fuerza la evaluación de la validación.
     * Se sobre escribe la propiedad para que por defecto sea true.
     */
    $force = true,

    /**
     * Nombre del método con el que se realizará la validación.
     */
    $fnName = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Si no se asignó la propiedad force
    $options['force'] = itemOr('force', $options, true);

    // Constructor padre.
    parent::__construct($options);

  }

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Obtener el nombre del método.
    $fnName = $this->getFnName();

    // Si no se asigna el nombre del validator
    // se tomara como metodo como prefijo "validator_$campoValidando"
    if(!$fnName)
      $fnName = "validator_{$this->getFieldName()}";

    // Si el método no existe entonces no pasa la validación, de lo contrario
    // retornar el llamado del método.
    return method_exists($model, $fnName)? $model->$fnName($this) : false;

  }

  /**
   * Devuelve le nombre del método con el que se realiza la validación.
   * @return string Nombre del método
   */
  public function getFnName(){

    return $this->fnName;

  }

  /**
   * Asigna el nombre del método con el que se realiza la validación.
   * @param  string $value Nombre del método a utilizar.
   * @return $this
   */
  public function setFnName($value){

    $this->fnName = $value;
    return $this;

  }

}

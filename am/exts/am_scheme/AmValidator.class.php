<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase base para los validadores de los modelos
 */
class AmValidator extends AmObject{

  protected static

    /**
     * Configuraciones globales de los validadores.
     */
    $conf = null;

  protected
    /**
     * Nombre del campo mediante el cual se aplica la validación.
     */
    $name = null,

    /**
     * Formato de mensaje de error.
     */
    $message = null,

    /**
     * Callback para verificar si el validador se debe evaluar.
     */
    $if = null,

    /**
     * Si se fuerza la evaluación de la validación.
     */
    $force = false,

    /**
     * Sustituciones de atributos en el mensaje.
     */
    $sustitutions = array('value' => 'value', 'fieldname' => 'name');

  /**
   * Constructor de validador
   * @param [type] $data [description]
   */
  public function __construct($data = null) {

    if(!isset($data['messages'])){

      // Obtener la configuraciones de los validators
      if(!isset(self::$conf))
        self::$conf = Am::getProperty('validators', array());

      // Obtener el nombre del validador
      $validatorName = strtolower($this->getValidatorName());

      // Obtener el mensaje de la configuracion
      if(isset(self::$conf['messages'][$validatorName]))
        $data['messages'] = self::$conf['messages'][$validatorName];

    }

    // Llamar constructor
    parent::__construct($data);

  }

  /**
   * Obtener el devuelve el callback para saber si se evalua.
   * @return callback Callback para evaluar.
   */
  public function getFnIf(){

    return $this->if;

  }

  /**
   * Obtener si se debe forzar la evaluación del validador.
   * @return bool Si se fuerza la evaluación.
   */
  public function getForce(){

    return $this->force;

  }

  /**
   * Obtener el nombre del campo del modelo al cual se aplica la validación.
   * @return string Nombre del campo.
   */
  public function getFieldName(){

    return $this->name;

  }

  /**
   * Hash de sustituciones de los campos en el mensaje de error.
   * @return hash Hash de sustituciones.
   */
  public function getSustitutions(){

    return $this->sustitutions;

  }

  /**
   * Asigna el callback para saber si se evalua el validador.
   * @param  callback $value Callback a asignar.
   * @return $this
   */
  public function setFnIf($value){

    $this->if = $value;
    return $this;

  }

  /**
   * Asigna si se forzará la evaluación del validador.
   * @param  bool  $value Si se fuerza la evluación del validador.
   * @return $this
   */
  public function setForce($value){

    $this->force = $value;
    return $this;

  }

  /**
   * Asignar el nombre del campo del modelo al que se aplica el validador.
   * @param string $value Nombre del campo.
   * @return $this
   */
  public function setFieldName($value){

    $this->name = $value;
    return $this;

  }

  /**
   * Asigna la cadena con el formato del mensaje de error.
   * @param string $value Cadena con el formato del mensaje de error.
   * @return $this
   */
  public function setMessage($value){

    $this->message = $value;
    return $this;

  }

  /**
   * Asigna las sustituciones para la validación.
   * @param  string $substr Parámetro en el formato.
   * @param  string $for    Propiedad por la que se sustituye.
   * @return $this
   */
  public function setSustitutions($substr, $for){

    $this->sustitutions[$substr] = $for;
    return $this;

  }

  /**
   * Devuelve el nombre simple del validador por el tipo.
   * @return string Nombre simple del validador.
   */
  protected function getValidatorName(){

    return preg_replace("/(.*)Validator$/", "$1", get_class($this));

  }

  /**
   * Prepara el mensaje de error.
   * Se realizan todas las sustituciones de los valores en el formato.
   * @param  AmModel $model Modelo al que se aplicó la validación.
   * @return string         Mensaje evaluado
   */
  public function getMessage(AmModel $model){

    $ret = $this->message;  // Obtener mensaje

    // Obtener las sustituciones a realizar
    $substitutions = $this->getSustitutions();

    // Sustituir valores de las propiedades en el mensaje
    foreach($substitutions as $substr => $for){
      $value = $this->$for;
      if(is_array($value)){
        $value = implode(',', $value);
      }
      $ret = str_replace("[{$substr}]", $value, $ret);
    }

    return $ret;

  }

  /**
   * Obtener el valor del modelo del campo asignado al validador.
   * @param  AmModel $model Model del que se desea obtener el valor.
   * @return any            Valor obtenido.
   */
  protected function value(AmModel $model){

    return $model->getValue($this->getFieldName());

  }

  /**
   * Devuelve si el modelo cumple con la validación actual.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  public function isValid(AmModel &$model){

    // Obtener propiedades necesarias
    $fnIf = $this->getFnIf();
    $fnIfExists = method_exists($model, $fnIf);
    $field = $model->getTable()->getField($this->getFieldName());
    $allowNull = $field? !$field->allowNull() : true;
    $value = $this->value($model);
    $force = $this->getForce();

    // Condiciones para no validar
    if(($allowNull && null === $value && !$force) ||
        ($fnIfExists && !$model->$fnIf($this)))
      return true;

    // Realizar validacion
    $this->value = $this->value($model);
    return $this->validate($model);

  }

  /**
   * Indica si el modelo cumple con el validador actual.
   * En este método se la implementaación de la validación para cada variante.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    return true;
    
  }

}

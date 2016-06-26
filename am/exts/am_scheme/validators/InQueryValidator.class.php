<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validación de campo referencia de otra tabla
 */
class InQueryValidator extends AmValidator{

  protected

    /**
     * Instancia del query que tiene todo los posibles valores para el campo.
     * Por lo general es una consulta qAll de un tabla, a menos que se requiran
     * otras condiciones.
     */
    $query = null,

    /**
     * Campo de la consulta por la que se buscará.
     */
    $field = null;

  /**
   * Sobrecarga del constructor par inicializar las propiedades específicas.
   * @param hash $data Hash de propieades.
   */
  public function __construct($options = array()){

    // Agregar los dos campos al sustitución.
    $this->setSustitutions('query', 'query');
    $this->setSustitutions('field', 'field');

    // Llamar constructor de la metaclase.
    parent::__construct($options);

    // Agregar el campo a la consulta por si no existe aún.
    $this->getQuery()->select($this->getField());

  }

  /**
   * Implementación de la validación.
   * @param  AmModel &$model Model que se validará.
   * @return bool            Si es válido o no.
   */
  protected function validate(AmModel &$model){

    // Obtener el campo de la consulta donde se buscará los valores.
    $field = $this->getField();

    // Obtener el valor modelo para el campo evaluado.
    $value = $this->value($model);

    // Obtener la consulta.
    $q = $this->getQuery();

    // Preparar consulta.
    $qq = $q->encapsulate('qq')
        ->select($field)            // Selecionar campo.
        ->where("{$field} = {$value}"); // Agregar consulta.

    // Es válido si devuelve al menos un registro.
    return false !== $qq->row();

  }

  /**
   * Devuelve el query en el que se basará la validación.
   * @return AmQuery Instancia de la consulta.
   */
  public function getQuery(){

    return $this->query;

  }

  /**
   * Asigana el query en el que se basará la validación.
   * @param  AmQuery $value Instancia del query.
   * @return $this
   */
  public function setQuery($value){

    $this->query = $value;
    return $this;

  }

  /**
   * Devuelve el nombre del campo del query de donde se buscará los valores.
   * @return string Nombre del campo
   */
  public function getField(){

    return $this->field;

  }

  /**
   * Asigna el nombre del campo del query donde se buscará los valores.
   * @param string $value Nombre del campo
   * @return $this
   */
  public function setField($value){

    $this->field = $value;
    return $this;

  }


}

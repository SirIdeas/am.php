<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Validacion de campo referencia de otra tabla
 */

class InQueryValidator extends AmValidator{

  protected
    $query = null,  // Indica la cosulta que tiene todo los posibles valores para el campo
                    // Por lo general es una consulta qAll de un tabla, a menos q se
                    // se requiran otras condiciones
    $field = null;  // campo de la consulta por la que se buscara


  public function __construct($options = array()){

    // Agregar los dos campos al sustitucion
    $this->setSustitutions("query", "query");
    $this->setSustitutions("field", "field");

    // Llamar constructor de la metaclase
    parent::__construct($options);

    // Agregar el campo a la consulta por si no existe aun
    $this->getQuery()->select($this->getField());

  }

  protected function validate(AmModel &$model){

    $field = $this->getField();
    $value = $this->value($model);
    $q = $this->getQuery();

    $qq = $q->getScheme()->q($q, "qq")
        ->select($field)
        ->where("$field = $value");

    return false !== $qq->row();

  }

  // Consulta en la que se basará la referencia a validar
  public function getQuery(){

    return $this->query;

  }

  public function setQuery($value){

    $this->query = $value;
    return $this;

  }


  // Campo de la consulta al que referencia el campo
  public function getField(){

    return $this->field;

  }

  public function setField($value){

    $this->field = $value;
    return $this;

  }


}

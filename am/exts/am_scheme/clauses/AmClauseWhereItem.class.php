<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseWhereItem extends AmClause{

  protected
    $field = null,
    $operator = null,
    $value = null,
    $union = null,
    $wheres = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->union = strtoupper($this->union);

    if(!isset($this->value) && isset($this->operator)){
      $this->value = $this->operator;
      $this->operator = '=';
    }

  }

  public function sql(){

    $field = $this->scheme->nameWrapperAndRealScapeComplete($this->field);
    $operator = $this->scheme->realScapeString($this->operator);
    $value = $this->scheme->stringWrapperAndRealScape($this->value);

    return $this->scheme->_sqlWhere($this->union, $field, $operator, $value);

  }

  public function addAnd(){

  }

}
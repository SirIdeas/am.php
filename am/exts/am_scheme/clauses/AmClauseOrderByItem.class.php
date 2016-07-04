<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseOrderByItem extends AmClause{

  protected
    $field = null,
    $dir = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->dir = strtoupper($this->dir);

    if(!is_string($this->field) || empty($this->field)){
      throw Am::e('AMSCHEME_FIELD_INVALID', var_export($this->field, true), 'ORDER BY');
    }

  }

  public function getDir(){

    return $this->dir;

  }

  public function getField(){

    return $this->field;

  }

  public function sql(){

    $field = $this->scheme->nameWrapperAndRealScapeComplete($this->field);

    return $this->scheme->_sqlOrderBy($field, $this->dir);

  }

}
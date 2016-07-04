<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseGroupByItem extends AmClause{

  protected
    $field = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    if(!is_string($this->field) || empty($this->field)){
      throw Am::e('AMSCHEME_FIELD_INVALID', var_export($this->field, true), 'GROUP BY');
    }

  }

  public function getField(){

    return $this->field;

  }

  public function sql(){

    return $this->scheme->nameWrapperAndRealScapeComplete($this->field);

  }

}
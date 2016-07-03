<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseGroupByItem extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $field = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();

    if(!is_string($this->field) || empty($this->field)){
      throw Am::e('AMSCHEME_FIELD_INVALID', var_export($this->field, true), 'GROUP BY');
    }

  }

  public function getQuery(){

    return $this->query;

  }

  public function getField(){

    return $this->field;

  }

  public function __toString(){

    return $this->sql();

  }

  public function sql(){

    $sql = $this->scheme->nameWrapperAndRealScapeComplete($this->field);

    // SQLSQLSQL
    return $sql;

  }

}
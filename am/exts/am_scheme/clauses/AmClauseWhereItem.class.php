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
    $wheres = null,
    $not = null,
    $cond = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

  }

  public function sql(){

    list($field, $operator, $value) = array_merge($this->cond, array(null,null,null));

    $field = $this->scheme->nameWrapperAndRealScapeComplete($field);
    $operator = $this->scheme->realScapeString($operator);

    if($operator === 'IN'){
      if($value instanceof AmQuery){
        $value = $this->scheme->_sqlWrapperSql($value->sql());
      }else{
        $value = '('.implode(',', $value).')';
      }
    }else{
      $value = $this->scheme->stringWrapperAndRealScape($value);
    }

    if(!isset($operator)){
      $operator = '';
      $value = '';
    }

    $not = '';
    if($this->not){
      $not = $this->scheme->_sqlNot();
    }

    return trim($this->scheme->_sqlWhereItem($not, $field, $operator, $value));

  }

}
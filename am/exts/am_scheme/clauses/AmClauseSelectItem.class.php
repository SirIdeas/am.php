<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseSelectItem extends AmClause{

  protected
    $field = null,
    $alias = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    if(empty($this->alias)){
      $field = $this->field;
      
      if(is_string($field)){
        $this->alias = str_replace('.', '_', $field);
      }

    }

    if(empty($this->alias)){
      throw Am::e('AMSCHEME_EMPTY_ALIAS', var_export($this->field, true));
    }

    $this->alias = $this->scheme->alias($this->alias, $this->query->getSelects());

  }

  public function getAlias(){

    return $this->alias;

  }

  public function getField(){

    return $this->field;

  }

  public function sql(){

    $field = $this->field;

    // Si es una consulta se incierra entre parentesis
    if($field instanceof AmQuery){
      $field = $this->scheme->_sqlSqlWrapper($field->sql());

    }elseif(is_string($field)){
      $field = $this->scheme->nameWrapperAndRealScapeComplete($field);

    }elseif($field instanceof AmRaw){
      $field = (string)$field;

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($field, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);
    
    return $this->scheme->_sqlElementWithAlias($field, $alias);;

  }

}
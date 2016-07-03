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

    // Si es una consulta se incierra entre parentesis
    if($this->field instanceof AmQuery){
      // SQLSQLSQL
      $sql = '(' . $this->field->sql() . ')';

    }elseif(is_string($this->field)){
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($this->field);

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($this->field, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);
    
    // SQLSQLSQL
    return "{$sql} AS {$alias}";

  }

}
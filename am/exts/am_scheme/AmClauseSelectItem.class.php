<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseSelectItem extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $field = null,
    $alias = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    if(empty($this->alias) && is_string($this->field)){
      $this->alias = str_replace('.', '_', $this->field);
    }

    if(empty($this->alias)){
      throw Am::e('AMSCHEME_EMPTY_ALIAS', var_export($this->field, true));
    }

    $this->scheme = $this->query->getScheme();

    $this->alias = $this->scheme->alias($this->alias, $this->query->getSelects());

  }

  public function sqlField(){

    // Si es una consulta se incierra entre parentesis
    if($this->field instanceof AmQuery){
      // SQLSQLSQL
      $sql = '(' . $this->field->sql() . ')';

    }elseif(is_string($this->field)){
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($this->field);

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($this->field, true));

    }

    return $sql;

  }

  public function getAlias(){

    return $this->alias;

  }

  public function sql(){

    $fieldSql = $this->sqlField();
    $aliasSql = $this->scheme->nameWrapperAndRealScape($this->alias);
    // SQLSQLSQL
    return "{$fieldSql} AS {$aliasSql}";

  }

  public function __toString(){

    return $this->sql();

  }

}
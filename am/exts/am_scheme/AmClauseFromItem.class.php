<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseFromItem extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $from = null,
    $alias = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();

    if(empty($this->alias)){
      $from = $this->from;

      if($from instanceof AmQuery){
        $from = $from->getTable();
      }

      if($from instanceOf AmTable){
        $from = $this->from->getModel();
      }
      
      if(is_string($from)){
        $this->alias = str_replace('.', '_', $from);
      }

    }

    if(empty($this->alias)){
      throw Am::e('AMSCHEME_EMPTY_ALIAS', var_export($this->from, true));
    }

    $this->alias = $this->scheme->alias($this->alias, $this->query->getFroms());

  }

  public function getQuery(){

    return $this->query;

  }

  public function getFrom(){

    return $this->from;

  }

  public function getAlias(){

    return $this->alias;

  }

  public function __toString(){

    return $this->sql();

  }

  public function sql(){

    // Si es una consulta se incierra entre parentesis
    if($this->from instanceof AmQuery){
      // SQLSQLSQL
      $sql = '(' . $this->from->sql() . ')';

    }elseif($this->from instanceOf AmTable){
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($this->from->getTableName());

    }elseif(is_string($this->from)){
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($this->from);

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($this->from, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);

    // SQLSQLSQL
    return "{$sql} AS {$alias}";

  }

}
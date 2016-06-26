<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseJoinItem extends AmClauseFromItem{

  protected
    $type = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();
    $from = $this->from;

    if(empty($this->alias)){

      if($from instanceof AmQuery){
        $from = $from->getTable();
      }

      if($from instanceOf AmTable){
        $from = $from->getModel();
      }
      
      if(is_string($from)){
        $this->alias = str_replace('.', '_', $from);
      }

    }

    if(empty($this->alias)){
      throw Am::e('AMSCHEME_EMPTY_ALIAS', var_export($from, true));
    }

    $this->alias = $this->scheme->alias($this->alias, $this->query->getFroms());

  }

  public function sql(){

    $from = $this->from;

    // Si es una consulta se incierra entre parentesis
    if($from instanceof AmQuery){
      // SQLSQLSQL
      $sql = '(' . $from->sql() . ')';

    }elseif($from instanceOf AmTable){
      $tableName = $from->getTableName();
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($tableName);

    }elseif(is_string($from)){

      if(is_subclass_of($from, 'AmModel')){
        $tableName = $from::me()->getTableName();
        $sql = $this->scheme->nameWrapperAndRealScapeComplete($tableName);
      }else{
        $sql = $this->scheme->nameWrapperAndRealScapeComplete($from);
      }

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($from, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);
    $type = $this->type;
    $type = !empty($type)? "{$type} " : '';

    // SQLSQLSQL
    return "{$type}JOIN {$sql} AS {$alias}";

  }

}
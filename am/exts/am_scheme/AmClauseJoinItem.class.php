<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseJoinItem extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $from = null,
    $alias = null,
    $on = null,
    $type = null,
    $model = null;

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

    $this->alias = $this->scheme->alias($this->alias, array_merge(
      $this->query->getFroms(),
      $this->query->getJoins()
    ));

    if(is_string($from)){
      $this->makeFromPossibleJoins();
    }

  }

  public function makeFromPossibleJoins(){

    $from = $this->from;
    $alias = $this->alias;
    $tables = $this->query->getPossibleJoins();
    
    foreach ($tables as $aliasTable => $table){
      $posibleJoins = $table->getPossibleJoins();
      foreach ($posibleJoins as $aliasJoin => $conf){
        // var_dump([$from, $aliasJoin, "{$aliasTable}.{$aliasJoin}"]);
        if(in_array($from, array($aliasJoin, "{$aliasTable}.{$aliasJoin}"))){
          $this->model = $conf['model'];
          $on = array();
          foreach ($conf['cols'] as $colFrom => $colTo) {
            $on[] = "{$aliasTable}.{$colFrom} = {$aliasJoin}.{$colTo}";
          }
          if(!empty($on)){
            $this->on = '('.implode(') AND (', $on).')';
          }
          return;
        }
      }
    }

  }

  public function getQuery(){

    return $this->query;

  }

  public function getAlias(){

    return $this->alias;

  }

  public function getFrom(){

    return $this->from;

  }

  public function getOn(){

    return $this->on;

  }

  public function getType(){

    return $this->type;

  }

  public function getModel(){

    return $this->model;

  }

  public function __toString(){

    return $this->sql();

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

      $tableName = $from;

      if(isset($this->model)){
        $tableName = $this->model;
      }

      if(is_subclass_of($tableName, 'AmModel')){
        $tableName = $tableName::me()->getTableName();
      }
      $sql = $this->scheme->nameWrapperAndRealScapeComplete($tableName);

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($from, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);
    $type = $this->type;
    $type = !empty($type)? "{$type} " : '';

    $on = $this->on;
    $on = !empty($on)? " ON {$on} " : '';

    // SQLSQLSQL
    return "{$type}JOIN {$sql} AS {$alias}{$on}";

  }

}
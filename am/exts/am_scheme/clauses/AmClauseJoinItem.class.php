<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseJoinItem extends AmClause{

  protected
    $table = null,
    $alias = null,
    $on = null,
    $type = null,
    $model = null,
    $postAddedCallback = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $table = $this->table;

    if(empty($this->alias)){

      if($table instanceof AmQuery){
        $table = $table->getTable();
      }

      if($table instanceOf AmTable){
        $table = $table->getModel();
      }
      
      if(is_string($table)){
        $this->alias = str_replace('.', '_', $table);
      }

    }

    if(empty($this->alias)){
      throw Am::e('AMSCHEME_EMPTY_ALIAS', var_export($table, true));
    }

    $this->alias = $this->generateAlias($this->alias);

    if(is_string($table)){
      if(is_subclass_of($table, 'AmModel')){
        $this->model = $table;
      }
      $this->makeFromPossibleJoins();
    }

  }

  public function getAlias(){

    return $this->alias;

  }

  public function getTable(){

    return $this->table;

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

  protected function generateAlias($alias){

    return $this->scheme->alias($alias, array_merge(
      $this->query->getFroms(),
      $this->query->getJoins()
    ));
    
  }

  public function addPossibleJoins(){

    $table = null;
    if($this->table instanceof AmTable){
      $table = $this->table;
    }else{
      $model = $this->model;
      if(isset($model)){
        $table = $model::me();
      }
    }

    $this->query->addPossibleJoins($table, $this->alias);

    if(isValidCallback($this->postAddedCallback)){
      call_user_func($this->postAddedCallback);
    }

  }

  public function makeFromPossibleJoins(){

    $table = $this->table;
    $alias = $this->alias;
    $tables = $this->query->getPossibleJoins();

    foreach ($tables as $aliasTable => $tbl){
      $posibleJoins = $tbl->getPossibleJoins();
      foreach ($posibleJoins as $aliasJoin => $conf){
        if(in_array($table, array($aliasJoin, "{$aliasTable}.{$aliasJoin}"))){
          $this->model = $conf['model'];

          if($conf['type'] == 'hasManyAndBelongTo'){
            $this->table = $conf['table'];
            $alias = $this->generateAlias($conf['table']);
            $this->model = itemOr('model', $conf['through']);
            $prevAlias = $alias;
            $prevModel = $conf['model'];

            $prevOn = new AmClauseOn(array(
              'query' => $this->query,
              'cols' => $conf['through']['cols'],
              'from' => $alias,
              'to' => $prevAlias,
            ));

            $this->postAddedCallback = function() use ($prevModel, $prevAlias, $prevOn) {
              $this->query->join($prevModel, $prevAlias, $prevOn, null, $item);
              $prevOn->setAliasTo($item->getAlias());
            };
            
          }

          $on = new AmClauseOn(array(
            'query' => $this->query,
            'cols' => $conf['cols'],
            'from' => $aliasTable,
            'to' => $alias,
          ));
          $this->on = $on;

          $this->alias = $alias;

          return;

        }
      }
    }

  }

  public function sql(){

    $table = $this->table;

    // Si es una consulta se incierra entre parentesis
    if($table instanceof AmQuery){
      $table = $this->scheme->_sqlSqlWrapper($table->sql());

    }elseif($table instanceOf AmTable){
      $tableName = $table->getTableName();
      $table = $this->scheme->nameWrapperAndRealScapeComplete($tableName);

    }elseif(is_string($table)){

      $tableName = $table;

      if(isset($this->model)){
        $tableName = $this->model;
      }

      if(is_subclass_of($tableName, 'AmModel')){
        $tableName = $tableName::me()->getTableName();
      }
      $table = $this->scheme->nameWrapperAndRealScapeComplete($tableName);

    }else{
      throw Am::e('AMSCHEME_INVALID_FIELD', var_export($table, true));

    }

    $alias = $this->scheme->nameWrapperAndRealScape($this->alias);
    $table = $this->scheme->_sqlElementWithAlias($table, $alias);

    $on = '';
    if(!empty($this->on)){
      $on = $this->scheme->_sqlOn($this->on);
    }

    return $this->scheme->_sqlJoin($this->type, $table, $on);

  }

}
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseLimit extends AmClause{

  protected
    $limit = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    if(!is_int($this->limit)){
      throw Am::e('AMSCHEME_INT_INVALID', $this->limit, 'LIMIT');
    }

  }

  public function getLimit(){

    return $this->limit;

  }

  public function sql(){

    // SQLSQLSQL
    return intval($this->limit);

  }

}
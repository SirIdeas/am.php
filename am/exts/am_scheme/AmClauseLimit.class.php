<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseLimit extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $limit = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();

    if(!is_int($this->limit)){
      throw Am::e('AMSCHEME_INT_INVALID', $this->limit, 'LIMIT');
    }

  }

  public function getQuery(){

    return $this->query;

  }

  public function getLimit(){

    return $this->limit;

  }

  public function __toString(){

    return $this->sql();

  }

  public function sql(){

    // SQLSQLSQL
    return intval($this->limit);

  }

}
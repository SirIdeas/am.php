<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseOffset extends AmObject{

  protected
    $scheme = null,
    $query = null,
    $offset = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();

    if(!is_int($this->offset)){
      throw Am::e('AMSCHEME_INT_INVALID', $this->offset, 'OFFSET');
    }

  }

  public function getQuery(){

    return $this->query;

  }

  public function getOffset(){

    return $this->offset;

  }

  public function __toString(){

    return $this->sql();

  }

  public function sql(){

    // SQLSQLSQL
    return intval($this->offset);

  }

}
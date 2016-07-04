<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseOffset extends AmClause{

  protected
    $offset = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    if(!is_int($this->offset)){
      throw Am::e('AMSCHEME_INT_INVALID', $this->offset, 'OFFSET');
    }

  }

  public function getOffset(){

    return $this->offset;

  }

  public function sql(){

    return intval($this->offset);

  }

}
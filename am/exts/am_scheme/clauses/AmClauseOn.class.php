<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmClauseOn extends AmClause{

  protected
    $cols = null,
    $from = null,
    $to = null;

  public function getCols(){

    return $this->cols;

  }

  public function getAliasFrom(){

    return $this->from;

  }

  public function getAliasTo(){

    return $this->to;

  }

  public function setAliasFrom($from){

    $this->from = $from;
    return $this;

  }

  public function setAliasTo($to){

    $this->to = $to;
    return $this;

  }

  public function sql(){

    $where = new AmClauseWhere(array(
      'query' => $this->query,
    ));

    $cols = array('AND');
    $scheme = $this->scheme;
    $to = $scheme->realScapeString($this->to);
    foreach ($this->cols as $colFrom => $colTo) {
      $colTo = $scheme->realScapeString($colTo);
      $colTo = $scheme->nameWrapperAndRealScapeComplete("{$to}.{$colTo}");
      $cols[] = array("{$this->from}.{$colFrom}", Am::raw($colTo));
    }

    $where->add($cols);

    return (string)$where;

  }

}
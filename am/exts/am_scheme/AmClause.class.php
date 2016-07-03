<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
abstract class AmClause extends AmObject{

  protected
    $scheme = null,
    $query = null;

  public function __construct(array $data = array()){
    parent::__construct($data);

    $this->scheme = $this->query->getScheme();

  }

  public function getQuery(){

    return $this->query;

  }

  public function __toString(){

    return $this->sql();

  }

  abstract public function sql();

}
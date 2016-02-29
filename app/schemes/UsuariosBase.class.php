<?php

class UsuariosBase extends AmModel{

  protected
    $schemeName = '',
    $tableName  = 'usuarios';

  // METHOD FOR INIT MODEL
  protected function start(){

    $this->setValidator('name', 'max_length',array('max' => 128));
    $this->setValidator('email', 'max_length',array('max' => 128));
    $this->setValidator('sueldo', 'float', array('precision' => 10, 'decimals' => 0));
    $this->setValidator('created_at', 'timestamp');
    $this->setValidator('updated_at', 'timestamp');

  }

}
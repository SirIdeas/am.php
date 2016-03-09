<?php

class Persona extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'ci' => array(
        'type' => 'varchar',
        'len' => '10',
        'pk' => true,
      ),
      'nombre' => 'varchar',
      'email' => array(
        'type' => 'varchar',
        'unique' => true
      ),
    ),
    'uniques' => array(
      'nombre_email' => array(
        'fields' => array('nombre', 'email')
      ),
    ),
  );

}
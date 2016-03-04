<?php

class Person extends AmModel{

  public static 
    $autoMigrate = true;

  protected
    $pks = 'id',
    $fields = array(
      'id' => 'id',
      'age' => 'int',
      'height' => 'float',
      'born_date' => 'date',
      'register_date' => 'datetime',
      'last_session' => 'timestamp',
      'check_in' => 'time',
      'dni' => array(
        'type' => 'char',
        'len' => 12
      ),
      'name' => 'varchar',
      'email' => 'varchar',
      'bio' => 'text',
      'marriage' => 'year',
      'permissions' => 'bit',
      'children' => 'unsigned',
    );

}
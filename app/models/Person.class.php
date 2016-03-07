<?php

class Person extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id_person' => 'id',
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
      'email' => array(
        'type' => 'varchar',
        'validators' => array(
          'email' => true
        )
      ),
      'bio' => 'text',
      'marriage_year' => 'year',
      'permissions' => array(
        'type' => 'bit',
        'validators' => false,
      ),
      'children' => 'unsigned',
    ),
    'hasMany' => array(
      'departaments' => 'Departament',
      'invoices' => 'Invoice',
    )
  );

}
<?php

class Person extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'ci' => array(
        'type' => 'varchar',
        'len' => '10',
        'pk' => true,
      ),
      'age' => 'int',
      'height' => 'float',
      'born_date' => 'date',
      'register_date' => 'datetime',
      'last_session' => 'timestamp',
      'check_in' => 'time',
      'name' => 'varchar',
      'last_name' => 'varchar',
      'email' => array(
        'type' => 'varchar',
        'unique' => true,
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
    'uniques' => array(
      'name_lastName' => array(
        'fields' => array('name', 'last_name')
      ),
    ),
    'hasMany' => array(
      'departaments' => array(
        'model' => 'Departament',
        'cols' => array(
          'id_chief' => 'ci'
        )
      ),
      'secretaryIn' => array(
        'model' => 'Departament',
        'cols' => array(
          'id_secretary' => 'ci'
        )
      ),
      // 'invoices' => 'Invoice',
    )
  );

}
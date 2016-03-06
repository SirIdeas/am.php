<?php

class Departament extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id_departament' => 'id',
      'name' => 'varchar',
    ),
    'belongTo' => array(
      'chief' => 'Person',
      'secretary' => array(
        'model' => 'Person',
        'cols' => array(
          'secretary_id' => 'id_person'
        )
      )
    ),
    'hasMany' => array(
      'sections' => 'Section'
    )
  );


}
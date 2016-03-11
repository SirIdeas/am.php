<?php

class Departament extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id' => 'id',
      'name' => 'varchar',
    ),
    'belongTo' => array(
      'chief' => array(
        'model' => 'Person',
        'cols' => array(
          'id_chief' => 'ci'
        )
      ),
      'secretary' => array(
        'model' => 'Person',
        'cols' => array(
          'id_secretary' => 'ci'
        )
      )
    ),
    'hasMany' => array(
      'sections' => array(
        'model' => 'Section',
        'cols' => array(
          'id_departament' => 'id'
        )
      )
    )
  );


}
<?php

class Section extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id' => 'id',
      'name' => 'varchar',
    ),
    'belongTo' => array(
      'departament' => array(
        'model' => 'Departament',
        'cols' => array(
          'id_departament' => 'id'
        )
      ),
    )
  );


}
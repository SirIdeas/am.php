<?php

class Section extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id_section' => 'id',
      'name' => 'varchar',
    ),
    'belongTo' => array(
      'departament' => 'Departament',
    )
  );


}
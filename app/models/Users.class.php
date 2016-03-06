<?php

class Usuario extends AmModel{

  public $sketch = array(
    'fields' => array(
      'id_user' => 'id',
      'name' => 'varchar',
      'email' => 'varchar',
      'height' => 'float',
    )
  );

}
<?php

class Usuario extends AmModel{

  protected
    $fields = array(
      'id_user' => 'id',
      'name' => 'varchar',
      'email' => 'varchar',
      'height' => 'float',
    ),
    $createdAtField = true,
    $updatedAtField = true;

}
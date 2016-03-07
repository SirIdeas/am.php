<?php

class Product extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id' => 'id',
      'descripcion' => 'varchar',
      'cant' => 'unsigned'
    ),
    'hasManyAndBelongTo' => array(
      'invoices' => array(
        'model' => 'Invoice',
        'through' => 'invoicedetails'
      ),
    )
  );

}
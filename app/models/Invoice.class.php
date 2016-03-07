<?php

class Invoice extends AmModel{

  public static 
    $autoMigrate = true;

  public $sketch = array(
    'fields' => array(
      'id' => 'id',
      'invoice_date' => 'timestamp',
    ),
    'belongTo' => array(
      'belongTo' => 'Person'
    ),
    'hasManyAndBelongTo' => array(
      'products' => array(
        'model' => 'Product',
        'through' => 'invoicedetails',
        'select' => array(
          'amount' => 'invoicedetails.id_invoices'
        )
      ),
    )
  );

}
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
      'own' => array(
        'model' => 'Person',
        'cols' => array(
          'id_own' => 'ci'
        )
      ),
    ),
    'hasManyAndBelongTo' => array(
      'products' => array(
        'model' => 'Product',
        'through' => 'invoicedetails',
        'cols' => array(
          'id_invoices' => 'id'
        ),
        'select' => array(
          'amount' => 'amount'
        )
      ),
    )
  );

}
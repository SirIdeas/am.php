// /app/models/InvoiceDetail.class.php
<?php
class InvoiceDetail extends AmModel{

  protected $sketch = array(
    'pks' => array(
      'id_invoice',
      'id_item'
    ),
  );

}
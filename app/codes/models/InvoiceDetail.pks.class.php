// /app/models/InvoiceDetail.class.php
<?php
class InvoiceDetail extends AmModel{

  // Clave primaria compuesta
  protected $pks = array(
    'id_invoice',
    'id_item'
  );

}
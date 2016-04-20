// /app/models/Person.class.php
<?php
class Person extends AmModel{

  protected $sketch = array(
    'fields' => array(
      'id_person' => 'id',   // Campo ID.
      'dni' => 'varchar',    // Campo de cadena variable
      'name' => 'varchar',   // Campo de cadena variable
      'email' => array(      // Campo de cadena variable
        'type' => 'varchar',
        'len' => 25,
      ),
      'born_date' => 'date', // Campo de fecha
      'bio' => 'text',       // Campo de cadena larga
      'height' => 'float',   // Altura
    )
  );

}
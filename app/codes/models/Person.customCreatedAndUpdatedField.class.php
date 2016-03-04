// /app/models/Person.class.php
<?php
class Person extends AmModel{

  // Campo para fecha de creación: 'date_created'
  protected $createdAtField = 'date_created';

  // Campo para fecha de actualización: 'date_modified'
  protected $updatedAtField = 'date_modified';

}
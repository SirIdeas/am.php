// /app/models/Person.class.php
<?php
class Person extends AmModel{

  protected $sketch = array(
    'createdAtField' => 'date_created',
    'updatedAtField' => 'date_modified',
  );

}
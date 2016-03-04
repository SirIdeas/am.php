// Crear la instancia del modelo
$person = new Person;

// Asignar valores a los campos
$person->email  = 'someone@somedomain.org';
$person->name   = 'Peter Joseph';
$person->born_date   = '1989/09/16';
$person->height = 1.75;

// Insertar
echo $peson->save()? 'inserted' : 'errors inserting';
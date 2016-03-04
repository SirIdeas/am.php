// Busca por ID del modelo
$person = Person::find(20); 

// Validar que el registro exista
if($person){

  // Realizar modificaciones
  $person->email  = 'newemail@somedomain.org';

  // Guardar
  echo $peson->save()? 'updated' : 'errors updating';

}
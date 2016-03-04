// Busca por ID del modelo
$person = Person::find(20);

// Validar que el registro exista
if($person){

  // Eliminar
  echo $peson->delete()? 'saved' : 'errors deleting';

}
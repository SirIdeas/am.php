// Despachador
function customMyTypeDispatcher($destiny, $env, $params){
  echo 'Decir '.$destiny.' en '.$params['lang'];
  return true;
}

// Agregar el despachador al tipo 'myType'
Am::call('route.addDispatcher', 'myType', 'customMyTypeDispatcher');
// Despachador
function customMyTypeDispatcher($destiny, $env, $params){
  echo 'Decir '.$destiny.' en '.$params['lang'];
  return false;
}

// Agregando despachador al tipo 'myType'
Am::call('route.addDispatcher', 'myType', 'customMyTypeDispatcher');
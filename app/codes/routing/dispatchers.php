// Despachador
function customMyTypeDispatcher($target, $env, $params){
  echo 'Decir '.$target.' en '.$params['lang'];
  return true;
}

// Agregar el despachador al tipo 'myType'
Am::addRouteDispatcher('myType', 'customMyTypeDispatcher');
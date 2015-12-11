<?php

// Despachador
function customMyTypeDispatcher($target, $env, $params){
  echo 'Decir '.$target.' en '.$params['lang'];
  return true;
}

function f($ruta){
  var_dump($ruta);
}

// Agregar el despachador al tipo 'myType'
Am::ring('route.addDispatcher', 'myType', 'customMyTypeDispatcher');

return array(
  
  '/' => 'template => views/pages/index.php',
  '/:view' => 'template => views/pages/:view.php',

  '/echo/:lang/:msg' => 'myType => :msg2',
    
);
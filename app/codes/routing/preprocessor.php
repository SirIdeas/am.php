// /app/am.init.php
// Preprocesador de ruta
function customRoutePreProcessor($route){
  $route['route'] = '/admin/'.$route['route'];
  $route['call'] = 'Admin::'.$route['myAdmin'];
  return $route;
}

// Agregando preprocesador
Am::addRoutePreProcessor('myAdmin', 'customRoutePreProcessor');
// /app/am.init.php
// Pre-procesador de ruta
function customRoutePreProcessor($route){
  $route['route'] = '/admin/'.$route['route'];
  $route['call'] = 'Admin::'.$route['myAdmin'];
  return $route;
}

// Agregando pre-procesador
Am::addRoutePreProcessor('myAdmin', 'customRoutePreProcessor');
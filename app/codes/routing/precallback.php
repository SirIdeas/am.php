// Pre callback
function customRoutePreCallback($route){
  $route['route'] = '/admin/'.$route['route'];
  $route['call'] = 'Admin::'.$route['myAdmin'];
  return $route;
}

// Agregaando pre-callback
Am::call('route.addPreCallback', 'myAdmin', 'customRoutePreCallback');
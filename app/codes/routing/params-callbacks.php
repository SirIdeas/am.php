// /app/am.init.php
function myCallback($model, $id, $env){
  // Code
}

// /app/routing.conf.php
return array(
  // ...
  '/models/{model}/{id}' => 'call => myCallback',
  // ...
);
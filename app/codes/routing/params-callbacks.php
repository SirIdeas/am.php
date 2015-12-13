function myCallback($model, $id, $env){
  // Code
}

// routing.conf.php
return array(
  ...
  '/models/{model}/{id}' => 'call => myCallback',
  ...
);
<?php

function f($lang, $msg, $env){
  var_dump($lang);
  var_dump($msg);
  var_dump($env);
}

return array(
  
  '/' => 'template => views/pages/index.php',
  '/{view}' => 'template => views/pages/{view}.php',

  '/call/{lang}/{msg}' => 'call => {msg}',
    
);
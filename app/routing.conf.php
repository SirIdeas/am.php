<?php

return array(
  
  '/' => 'template => views/pages/index.php',
  '/{view}' => 'template => views/pages/{view}.php',

  '/controller/{p1}/{p2}' => 'Index@index',
    
);
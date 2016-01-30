<?php

return array(

  'env' => array(
    'siteName' => 'Amathista | PHP Framework'
  ),

  'requires' => array(
    'exts/am_route',
    'exts/am_tpl',
    'exts/am_controller',
    'exts/am_model',
  ),

  'routing' => array(

    '/' => 'template => views/pages/index.php',
    '/{view}' => 'template => views/pages/{view}.php',

    '/controller/{p1}/{p2}' => 'Index@index2',
    '/main' => 'Main@index',
    '/model' => 'Main@model',
    
  ),

  'controllers' => array(
    'Index' => array(
      'root' => 'controllers/index',
      // 'allows' => array('index' => false)
    ),
  ),

  'sources' => array(
    '' => array(
      'driver' => 'mysql',
      'server' => 'localhost',
      'port' => '',
      'database' => 'tvadmin',
      'user' => 'tvadmin',
      'pass' => 'aUuf8QEHZLH4VQ3f',
      'prefix' => '',
    ),
  )

);
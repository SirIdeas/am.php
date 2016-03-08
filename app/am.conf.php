<?php

return array(

  'env' => array(
    'siteName' => 'Amathista | PHP Framework',
  ),

  'requires' => array(
    'helpers/sync.helpers',
  ),

  'routing' => array(

    '/' => 'template => views/pages/index.php',
    '/{view}' => 'template => views/pages/{view}.php',
    
    '/model' => 'Main@model',

    '/controller/{p1}/{p2}' => 'Index@index2',
    '/main' => 'Main@index',

  ),

  'controllers' => array(
    'Index' => array(
      'root' => 'controllers/index',
      // 'allows' => array('index' => false)
    ),
  ),

  'schemes' => array(
    '' => array(
      'driver' => 'mysql',
      'database' => 'am',
      'server' => 'localhost',
      // 'port' => '',
      'user' => 'root',
      'pass' => '',
      'charset' => 'utf8',
      'collation' => 'utf8_unicode_ci',
      // 'prefix' => '',
    ),
  ),

  'requires' => array(
    'exts/am_route',
    'exts/am_tpl',
    'exts/am_controller',
    'exts/am_scheme',
  ),

);
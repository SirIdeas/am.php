<?php

return array(

  'env' => array(
    'siteName' => 'Amathista | PHP Framework'
  ),

  'controllers' => array(
    'Index' => array(
      'root' => 'controllers/index',
      // 'allows' => array('index' => false)
    ),
  ),

  'requires' => array(
    'exts/am_route',
    'exts/am_tpl',
    'exts/am_controller',
  ),

);
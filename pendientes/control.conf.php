<?php

return array(

  'Auth' => array(
    'root' => 'control/auth',
    'parent' => 'AmAuth',
    'authClass' => 'User',
    'keyPrivate' => KEY_PRIVATE,
    'keyPublic' => KEY_PUBLIC,
    'keyPassPhrase' => KEY_PASSPHRASE,
  ),

  'Authed' => array(
    'parent' => 'AmResource',
    'root' => 'control/authed',
    'filters' => array(
      'before' => array(
        'checkToken' => array('scope' => 'all')
      )
    ),
  ),
  
  'Index' => array(
    'root' => 'control/index',
  ),

  'Services' => array(
    'root' => 'control/services',
    'headers' => array(
      'Access-Control-Allow-Origin: *',
      'Access-Control-Allow-Methods: GET, POST',
      'Access-Control-Allow-Headers: x-requested-with',
    )
  ),

  'Programas' => array(
    'root' => 'control/programas',
    'formName' => 'data',
    'parent' => 'Authed',
    'model' => 'Programa',
    'fields' => array(
      'nombre', 'imagen', 'video', 'categorias', 'horario', 'descripcion'
    )
  ),

  'Rejillas' => array(
    'root' => 'control/rejillas',
    'formName' => 'data',
    'parent' => 'Authed',
    'model' => 'Rejilla',
    'fields' => array(
      'nombre', 'desde', 'dias'
    )
  ),

  'Users' => array(
    'root' => 'control/users',
    'formName' => 'data',
    'parent' => 'Authed',
    'model' => 'User',
    'fields' => array(
      'display_name', 'email',
    )
  ),

  'RejillaItems' => array(
    'root' => 'control/rejillaitems',
    'formName' => 'data',
    'parent' => 'Authed',
    'model' => 'RejillaItem',
    'fields' => array(
      'duracion', 'nombre', 'imagen', 'video', 'categorias', 'horario', 'descripcion'
    ),

    // Filtros
    'filters' => array(
      'before' => array(
        'loadRecord' => array(
          'to' => array('sort', 'move')
        )
      )
    ),
  ),

  'defaults' => array(
    'paths' => array(
      realPath('control')
    )
  )

);

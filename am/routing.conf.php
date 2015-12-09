<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
/**
 * -----------------------------------------------------------------------------
 * Configuración de rutas
 * -----------------------------------------------------------------------------
 */
return array(/*

  // ---------------------------------------------------------------------------
  // Responder con archivo
  // ---------------------------------------------------------------------------

  '/doc.pdf' => 'file => pdf/documento.pdf',

  array(
    'route' => '/doc.pdf',
    'file' => 'pdf/documento.pdf'
  ),

  // ---------------------------------------------------------------------------
  // Responder con descarga de archivo
  // ---------------------------------------------------------------------------

  '/download/doc.zip' => 'download => zips/documento.zip',

  array(
    'route' => '/download/doc.zip',
    'download' => 'zips/documento.zip'
  ),
  
  // ---------------------------------------------------------------------------
  // Rediriguir a una url interna
  // ---------------------------------------------------------------------------

  '/rutaConRedirecion' => 'redirect => /otraRuta',

  array(
    'route' => '/rutaConRedirecion',
    'redirect' => '/otraRuta'
  ),

  // ---------------------------------------------------------------------------
  // Rediriguir a una url externa
  // ---------------------------------------------------------------------------

  '/otroSitio' => 'goto => http://google.com',

  array(
    'route' => '/otroSitio',
    'goto' => 'http://google.com'
  ),
  
  // ---------------------------------------------------------------------------
  // Renderizar un template
  // ---------------------------------------------------------------------------

  '/rutaIndex' => 'template => views/index.php',

  array(
    'route' => '/rutaIndex',
    'template' => 'views/index.php'
  ),

  // ---------------------------------------------------------------------------
  // Respuesta con una funcion controlador
  // ---------------------------------------------------------------------------

  '/ruta' => 'call => home', // 'hola' es el nombre de la función

  array(
    'route' => '/function',
    'call' => 'hola'
  ),

  // ---------------------------------------------------------------------------
  // Respuesta con un método estático controlador
  // ---------------------------------------------------------------------------

  '/ruta' => 'call => A::hola',

  array(
    'route' => '/ruta',
    'call' => 'A::hola'
  ),

  // ---------------------------------------------------------------------------
  // Respuesta con un método de un controlador
  // ---------------------------------------------------------------------------

  '/ruta' => 'control => Index@home',

  array(
    'route' => '/ruta',
    'control' => 'Index@home'
  ),

  // ---------------------------------------------------------------------------
  // Ruta anidadas
  // ---------------------------------------------------------------------------
  
  '/admin' => array(
    'call' => 'Admin::',
    'routes' => array(
      '/action1' => 'call => action1',
      array(
        'route' => '/action2',
        'call' => 'action2'
      )
    )
  ),

  // Es lo mismo que
  
  array(
    'route' => '/admin',
    'call' => 'Admin::',
    'routes' => array(
      '/action1' => 'call => action1',
      array(
        'route' => '/action2',
        'call' => 'action2'
      )
    )
  ),

  // y es lo mismo que

  '/admin/action1' => 'call => Admin::action1',
  '/admin/action2' => 'call => Admin::action2',

*/);

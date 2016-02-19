// /app/controllers.conf.php
<?php
return array(

  // directorios donde se buscarán las vistas
  // - /app/controllers/foo/vistas  (Directorio principal)
  // - /app/views                   (Directorio secundario)
  // - /app/templates               (Directorio secundario)
  'Foo' => array(
    'root' => 'controllers/foo',
    'views' => 'vistas',
    'paths' => array(
      'views',
      'templates'
    )
  ),

  // directorios donde se buscarán las vistas
  // - /app/ctrl_bar/tpls           (Directorio principal)
  // - /custom/tpls                 (Directorio secundario)
  // - /app/controllers/foo/vistas  (Directorio principal del padre)
  // - /app/views                   (Directorio secundario del padre)
  // - /app/templates               (Directorio secundario del padre)
  'Bar' => array(
    'root' => 'ctrl_bar',
    'views' => 'tpls',
    'paths' => array(
      '/custom/tpls'
    )
  )

);
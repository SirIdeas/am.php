// /app/controllers.conf.php
<?php
return array(

  // directorios donde se buscarán las vistas
  // - /app/controllers/vistas  (Directorio principal)
  // - /app/views               (Directorio secundario)
  // - /app/templates           (Directorio secundario)
  'Foo' => array(
    'views' => 'vistas',
    'paths' => array(
      'views',
      'templates'
    )
  ),

  // directorios donde se buscarán las vistas
  // - /app/controllers/tpls    (Directorio principal)
  // - /app/custom/tpls         (Directorio secundario)
  // - /app/controllers/vistas  (Directorio principal del padre)
  // - /app/views               (Directorio secundario del padre)
  // - /app/templates           (Directorio secundario del padre)
  'Bar' => array( // Este controlador hereda de Foo
    'views' => 'tpls',
    'paths' => array(
      'custom/tpls'
    )
  )

);
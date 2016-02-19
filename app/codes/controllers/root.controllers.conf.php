// /app/controllers.conf.php
<?php
return array(

  // Configuración directa (ruta relativa)
  'Foo' => 'ctrl/foo', // /app/ctrl/foo

  // Configuración como parámetro (ruta relativa)
  'Bar' => array(
    'root' => 'controllers/bar' // /app/controllers/bar
  ),

  // Configuración directa (ruta absoluta)
  'Baz' => '/custom/controllers/bar', // /custom/controllers/bar

);
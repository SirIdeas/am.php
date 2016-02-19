// /app/controllers.conf.php
<?php
return array(
  
  'Foo' => 'ctrls',

  'Bar' => array(
    'parent' => 'Foo',
    'root' => 'ctrls/bar'
  ),

  'Baz' => array( 'parent' => 'Foo' ),

  'Qux' => array( 'parent' => 'Bar' )

);
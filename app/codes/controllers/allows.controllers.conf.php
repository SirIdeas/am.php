// /app/controllers.conf.php
<?php
return array(
  
  'Foo' => array(
    'allows' => array(

      // Por defecto todas las acciones permitidas por cualquier método.
      '' => true,

      // Por defecto ninguna las acciones permitidas por cualquier método.
      '' => false

      // Por defecto todas las acciones se permite el método GET
      // pero no el POST
      '' => array(
        'get' => true,
        'post' => false,
      ),

      // Para 'accion1' se permite el método POST pero no el GET
      'accion1' => array(
        'get' => false,
        'post' => false,
      )

    )
  )

);

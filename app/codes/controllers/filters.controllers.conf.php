// /app/controllers.conf.php
<?php
return array(

  'filters' => array(
    
    // Filtros a ejecutarse antes de la acción
    'before' => array(

      'filtro1' => 'all', // Para todas las acciones

      'filtro2' => array(
        'to' => 'all',  // Para todas las acciones
      ),
      
      'filtro3' => array(
        // Solo para las acciones 'inicio' y 'editar'
        'to' => array('inicio', 'editar')
      ),

      'filtro4' => array(
        // Para todas las acciones excepto 'inicio'
        'except' => array('inicio')
      )

    ),

    // Filtros a ejecutarse antes de una acción por GET
    'before_get' => array(/*...*/),

    // Filtros a ejecutarse antes de una acción por POST
    'before_post' => array(/*...*/),

    // Filtros a ejecutarse después de una acción por GET
    'after_get' => array(/*...*/),

    // Filtros a ejecutarse después de una acción por POST
    'after_post' => array(/*...*/),

    // Filtros a ejecutarse después de una acción 
    'after' => array(/*...*/),

  )

);
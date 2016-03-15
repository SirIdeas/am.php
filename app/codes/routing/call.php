// Con una función
// 'funcionHola' es el nombre de una función a llamar
'/ruta' => 'call => funcionHola', 

// Con un método estático
// Se llama el método estático 'estaticoHola' del la clase 'A'
'/ruta' => 'call => A::estaticoHola',

// ó
'/ruta' => array(
  'call' => array('A', 'estaticoHola')
),

// Con un método de un objeto
// Se llama el método 'metodoHola' del objeto '$obj'
array(
  'route' => '/ruta',
  'call' => array($obj, 'metodoHola')
),
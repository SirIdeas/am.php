// -------------------------------------------------------------------------
// Con una función
// 'funcionHola' es el nombre de una función a llamar

// forma simple
'/ruta' => 'call => funcionHola', 

// forma explícita
array(
  'route' => '/function',
  'call' => 'funcionHola'
),

// -------------------------------------------------------------------------
// Con un método estático
// Se llama el método estático 'estaticoHola' del la clase 'A'

// forma simple
'/ruta' => 'call => A::estaticoHola',

// formas explícitas
array(
  'route' => '/ruta',
  'call' => 'A::estaticoHola'
),

array(
  'route' => '/ruta',
  'call' => array('A', 'estaticoHola')
),

// -------------------------------------------------------------------------
// Con un método de un objeto
// Se llama el método 'metodoHola' del objeto $obj

// forma simple
'/ruta' => 'call => A::metodoHola',

// forma explícita
array(
  'route' => '/ruta',
  'call' => array($obj, 'metodoHola')
),
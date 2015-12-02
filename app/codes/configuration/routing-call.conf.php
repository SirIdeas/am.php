// -------------------------------------------------------------------------
// Con una función
// -------------------------------------------------------------------------

// forma simple
'/ruta' => 'call => hola', // 'hola' es el nombre de la función

// forma explícita
array(
  'route' => '/function',
  'call' => 'hola'
),

// -------------------------------------------------------------------------
// Con un método estático
// -------------------------------------------------------------------------

// formas simples
'/ruta' => 'call => A::hola',

// formas explícitas
array(
  'route' => '/ruta',
  'call' => 'A::hola'
),

array(
  'route' => '/ruta',
  'call' => array('A', 'hola')
),
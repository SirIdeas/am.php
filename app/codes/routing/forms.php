// La ruta
'/myRoute' => 'myType => myValue',

// sería lo mismo que
'/myRoute' => array(
  'myType' => 'myValue'
),

// y cuya forma explícita sería
array(
  'route' => '/myRoute',
  'myType' => 'myValue'
),

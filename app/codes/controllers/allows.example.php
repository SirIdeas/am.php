// Acción foo permitida por todos los requests methods
'foo' => true,

// GET permitido, POST prohibido.
'bar' => array(
  'get' => true,
  'post' => false,
)
'/echo/{lang}/{msg}' => 'myType => {msg}',

// Entonces con una petición HTTP: /echo/es/hello-world
// Se llamaría el despachador 'customMyTypeDispatcher' con los argumentos:
$destiny = 'hello-world';
$env = array(...);
$params = array('lang' => 'es');
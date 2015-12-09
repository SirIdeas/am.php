'/echo/{lang}/{msg}' => 'myType => {msg}',

// Entonces una petición HTTP: /echo/es/hello-world
// Se llama el despachador 'customMyTypeDispatcher' con los argumentos:
$destiny = 'hello-world';
$env = array(...);
$params = array('lang' => 'es');
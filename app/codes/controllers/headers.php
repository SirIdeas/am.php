// Agrega una cabecera con el tipo mime
$this->addHeader('content-type: text/plain');

// Agrega una cabecera en 'contentType' (Solo para identificar)
$this->addHeader('content-type: text/plain', 'contentType');

// Eliminar la cabecera en 'contentType'
$this->removeHeader('contentType');
// Agrega un header final
$this->addHeader('content-type: text/plain');

// Agrega una cabecera cabecera en 'contentType' (Solo para identificar)
$this->addHeader('content-type: text/plain', 'contentType');

// Eliminar la cabecera en 'contentType'
$this->removeHeader('contentType');
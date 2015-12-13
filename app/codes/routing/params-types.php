// Identificador válido:
// - Alfanumerico, -, y _
// - Primer caracter alfabético
'/route/{param:id}' => '...',

// Solo números
'/route/{param:numeric}' => '...',

// Solo letras mayúsculas y/o minúsculas
'/route/{param:alphabetic}' => '...',

// Números, letras mayúsculas y/o minúsculas
'/route/{param:alphanumeric}' => '...',

// Con regex personalizada
'/models/{param:[dfg]{2}[a-z0-2]*}' => '...',

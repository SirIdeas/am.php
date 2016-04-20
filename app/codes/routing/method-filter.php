// Ejecutar solo por GET
'GET /foo' => 'call => Bar::get',

// Ejecutar solo por POST
'POST /foo' => 'call => Bar::post',

// Ejecutar solo por DELETE
'DELETE /foo' => 'call => Bar::delete',

// Se ejecuta por todos los métodos
'/admin/foo' => 'call => Bar::admin',
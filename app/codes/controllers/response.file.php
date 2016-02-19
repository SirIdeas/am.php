public function action_foo(){
  // Entrega de archivo
  return Am::file('/files/doc.pdf');
}

public function action_baz(){
  // Entrega de archivo para descarga
  return Am::download('/files/doc.pdf');
}

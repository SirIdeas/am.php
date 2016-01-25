public function action_foo(){
  // Error 403
  return Am::e403();
}

public function action_bar(){
  // Error 403
  return Am::e403('No autorizado');
}

public function action_baz(){
  // Error 404
  return Am::e404();
}

public function action_qux(){
  // Error 404
  return Am::e404('No encontrado');
}

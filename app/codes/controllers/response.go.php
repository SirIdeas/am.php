public function action_foo(){
  // Redirección externa
  return Am::go('http://sirideas.com');
}

public function action_bar(){
  // Redireccion interna
  return Am::go('/inicio');
}

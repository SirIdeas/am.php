public function action_foo(){
  // Vista simple
  return Am::template('foo.php');
}

public function action_bar(){
  // Vista con variables
  return Am::template('foo.php', array(
    'var1' => 'valor1',
    'var2' => 'valor2'
  ));
}

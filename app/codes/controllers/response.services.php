public function action_foo(){
  return array(
    'foo', 'bar', 'baz'
  );
}

public function action_bar(){
  return array(
    'success' => true,
    'data' => array(1,2,3,4,5)
  );
}

public function action_baz(){
  return $this->responseService(array(
    'data' => array(1,2,3,4,5)
  ));
}

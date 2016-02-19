public function action_foo(){
  // Al terminar el ejecución de la acción se renderizará
  // /tpls/bar.php
  $this->setRender('/tpls/bar.php');
}
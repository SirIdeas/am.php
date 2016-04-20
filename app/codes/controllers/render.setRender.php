public function action_foo(){
  // Al terminar el ejecución de la acción se renderizará
  // /app/tpls/bar.php
  $this->setRender('tpls/bar.php');
}
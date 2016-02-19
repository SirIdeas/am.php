public function action_foo(){
  // Al terminar el ejecución de la acción se renderizará
  // /app/controllers/views/bar.php
  $this->setView('bar');
}
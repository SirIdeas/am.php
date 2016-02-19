// /app/controllers/Foo.php
<?php
class Foo controllers{

  public function action_inicio(){
    // Se ejecuta con la acción 'inicio' sin importar
    // el request methos por el que venga.
  }

  public function get_inicio(){
    // Se ejecuta con la acción 'inicio' cuando se
    // soliita por el request method GET.
  }

  public function post_inicio(){
    // Se ejecuta con la acción 'inicio' cuando se
    // solicita por el request method POST.
  }

  public function filter_verificar(){
    // Filtro 'verificar'
  }

}

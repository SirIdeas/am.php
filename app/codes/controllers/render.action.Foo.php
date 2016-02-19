// /app/controllers/Foo.php
<?php
class Foo extends AmController{

  public function action_bar(){
    $this->baz = 'Esta es una variable para la vista';
    $this->date = date('Y-m-d')
  }

}
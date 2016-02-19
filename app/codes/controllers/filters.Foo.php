// /app/controllers/Foo.php
<?php
class Foo extends AmController{

  public function filter_filter1(){

    if(!isset($this->titulo))
      return false;

  }

  public function filter_filter2(){

    if(!isset($this->titulo))
      return Am::e404('Se debe indicar el título');

  }

}
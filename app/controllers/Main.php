<?php

class Main extends AmController{

  public function action_index(){
    
  }

  public function action_model(){
    
    // $this->addHeader('content-type:text/plain');
    $sch = AmScheme::get();

    // var_dump($sch->generateScheme());
    var_dump($sch->getTableInstance('programas'));

  }

}
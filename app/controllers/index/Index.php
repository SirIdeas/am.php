<?php

class Index extends AmController{

  public function action(){
    var_dump('action');
  }

  public function action_index2(){
    var_dump('index');
    // return array(1,2,3,4,5,6);
  }

  public function get_index2(){
    var_dump('get_index');
  }

  public function post_index(){
    var_dump('post_index');
  }

}
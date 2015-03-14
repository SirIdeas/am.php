<?php

var_dump(Am::requireExt("exts/am_orm/"));

$s = AmORM::source();
$t = AmORM::table("user");

var_dump($s->createClassModels());
UserBase::me()->truncate();

$r = new UserBase(array(
  ""
));

var_dump($r->toArray());
// var_dump($r->save());

exit;

class AmResourceControl extends AmControl{

  public function action_index(){
  }

}

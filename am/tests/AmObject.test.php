<?php

class A extends AmObject{
  private $a = 1;
  protected $b = 2;
  public $c = 3;
  public function t(){
    return get_object_vars($this);//get_class_vars(get_class($this));
  }
}


$o = new A(array('d' => 4));

var_dump($o);
var_dump($o->t());
var_dump(get_object_vars($o));
var_dump(get_class_vars(get_class($o)));

// $o->a = 11;
// $o->b = 22;
$o->c = 33;
$o->d = 44;

var_dump($o);
// $o['a'] = 111;
// $o['b'] = 222;
$o['c'] = 333;
$o['d'] = 444;

var_dump($o);
// var_dump($o->a);
// var_dump($o->b);
var_dump($o->c);
var_dump($o->d);
var_dump($o->e);

var_dump($o);
// var_dump($o['a']);
// var_dump($o['b']);
var_dump($o['c']);
var_dump($o['d']);
var_dump($o['e']);

// var_dump(isset($o->a));
// var_dump(isset($o->b));
var_dump(isset($o->c));
var_dump(isset($o->d));
var_dump(isset($o->e));

// var_dump(isset($o['a']));
// var_dump(isset($o['b']));
var_dump(isset($o['c']));
var_dump(isset($o['d']));
var_dump(isset($o['e']));

// unset($o->a);
// unset($o->b);
// unset($o->c);
unset($o->d);
unset($o->e);

// unset($o['a']);
// unset($o['b']);
unset($o['c']);
unset($o['d']);
unset($o['e']);


<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar
abstract class AmSession implements Iterator, Countable, ArrayAccess{

  protected
    $_collect = array(),
    $_id = null,
    $_code = 'serialize',
    $_decode = 'unserialize';
  
  // Función para crear una variable de sesión
  public function __construct($id){

    $this->_id = $id;
    
  }

  public function id(){

    return $this->_id;

  }

  public function __get($name){

    return $this[$name];

  }

  public function __set($name, $value){

    $this[$name] = $value;

  }

  public function __isset($name){

    return isset($this[$name]);

  }

  public function __unset($name){

    unset($this[$name]);

  }

  public function rewind(){

    $this->_collect = $this->all();
    return $this;

  }

  public function end(){

    end($this->_collect);
    return $this;

  }

  public function key(){

    return current($this->_collect);

  }

  public function current(){

    $key = current($this);
    return $this[$key];

  }

  public function next(){

    next($this->_collect);
    return $this;

  }

  public function prev(){

    prev($this->_collect);
    return $this;

  }

  public function valid(){

    $key = current($this);
    return isset($this[$key]);

  }

  public function count(){

    $collect = $this->all();
    return count($collect);

  }

  abstract public function all();

  abstract public function offsetGet($key);
  abstract public function offsetSet($key, $value);
  abstract public function offsetExists($key);
  abstract public function offsetUnset($key);

  public static function get($id){

    $class = Am::getProperty('session');
    Am::requireExt($class);

    return new $class($id);

  }

  public static function __callStatic($name, $arguments){

    $session = Am::session();

    if(!empty($arguments)){
      $session[$name] = $arguments[0];
      return $session;
    }
    
    return $session[$name];

  }
  
}

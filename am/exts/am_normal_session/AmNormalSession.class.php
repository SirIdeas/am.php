<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar
final class AmNormalSession extends AmSession{

  public function __construct($id){
    parent::__construct($id);
      
    // Crear contendor de la sesion   
    if(!isset($_SESSION[$this->id()]))
      $_SESSION[$this->id()] = array();

  }
  
  // Devuelve un array con todas las variables de sesion
  public function all(){

    $ret = $_SESSION[$this->id()];

    foreach ($ret as $key => $value) 
      $ret[$key] = $this[$key];

    return $ret;
    
  }

  public function offsetGet($key){

    $decode = $this->_decode;
    return isset($this[$key]) ? $decode($_SESSION[$this->id()][$key]) : null;

  }

  // Función para crear una variable de sesión
  public function offsetSet($key, $value){

    $code = $this->_code;
    $_SESSION[$this->id()][$key] = $code($value);

  }

  public function offsetExists($key){

    return isset($_SESSION[$this->id()][$key]);

  }

  public function offsetUnset($key){

    unset($_SESSION[$this->id()][$key]);

  }

}

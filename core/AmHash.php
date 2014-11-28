<?php

/**
 * Clase para objetos de Amathista
 */
class AmObject implements Iterator, Countable, ArrayAccess{ //Reflector, 
  
  /**
   * Coleccion de datos
   **/
  protected $_f = array();

  /**
   * Llamada del constructor. Inicializa 'data'
   **/
  public function __construct($data = null){
    
    $data = self::parse($data);
    
    foreach($data as $attr => $value){
      $this->$attr = $value;
    }
    
  }
  
  /**
   * Funciones Get y Set. Si 'value!='null' de retorna la propiedad 'key',
   * sino se asignara 'value' a la propiedad 'key'
   **/
  protected function attr($key, $value = null){

    if(isset($value)){
      
      $this->$key = $value;
      return $this;
      
    }
    
    return $this->$key;
    
  }
  
  /**
   * Llamada de la consulta a una propiedad
   **/
  public function __get($name){
    
    if(in_array($name, $this->_f) && property_exists($this, $name)){
      
      return $this->$name;
      
    }
    
    return null;
  }

  /**
   * Llamada de la asignacion a una propiedad
   **/
  public function __set($name, $value){
    
    if(!empty($name) && !property_exists($this, $name)){
      
      $this->_f[] = $name;
      
    }
    
    if(in_array($name, $this->_f)){
      
      $this->$name = $value;
      
    }
    
  }

  /**
   * Llamda de "isset" a una propiedad
   **/
  public function __isset($name){
    
    return isset($this->$name);
  
  }

  /**
   * Llamada de "unset" a una propiedad
   **/
  public function __unset($name){
    
    if(in_array($key, $this->_f)){
      
      $this->_f = array_diff($this->_f, array($key));
      unset($this->$key);
      
    }
    
    return $this;
    
  }
  
  /**
   * Llamada de la asignacion de valores en el objeto como un array.
   **/
  public function offsetSet($name, $value){ $this->$name = $value; }

  /**
   * Llamada de la funcion isset sobre el objeto utilizado como un array
   **/
  public function offsetExists($name){ return isset($this->$name); }

  /**
   * Llamada de la funcion unset sobre el objeto como un array.
   **/
  public function offsetUnset($name){ unset($this->$name); }
  
  /**
   * Llamada de consulta del objeto como un array.
   **/
  public function offsetGet($name){ return $this->$name; }

  /**
   * Mueve el puntero al primer elemento de la collecion
   **/
  public function rewind() {
    
    reset($this->_f);
    return $this;
    
  }
  
  /**
   * Obtiene el valor actual de la coleccion
   **/
  public function current() {
    
    $name = $this->key();
    return $this->$name;
    
  }

  /**
   * Mueve el puntero de la collecion al ultimo elemento
   **/
  public function end() {
    
    end($this->_f);
    return $this;
    
  }

  /**
   * Obtiene el indice del la posicion actual de la coleccion
   **/
  public function key() {
    
    return current($this->_f);
  
  }

  /**
   * Mueve el puntero de la coleccion al siguiente elemento
   **/
  public function next() {
    
    next($this->_f);
    return $this;
  
  }

  /**
   * Mueve el puntero de la coleccion al elemento previo
   **/
  public function prev() {
    
    prev($this->_f);
    return $this;
    
   }

  /**
   * Indica si el elemento actual de la collecion es valido
   **/
  public function valid() {
    
    $field = $this->key();
    return isset($this->$field);
    
  }

  /**
   * Devuelve el numero de objetos de la coleccion
   **/
  public function count() {
    
    return count($this->_f);
    
  }
  
  /**
   * Funcion para obtener o cambiar la data completa. Si se pasa un array en 
   * 'data' se cambiara el contenido de la colecion por la recibida por
   * parametro. Si valor recibido es nulo, entonces solo se retorna la
   * informacion completa sin reescribir los datos.
   */
  public function toArray(){

    $ret = array();
      
    foreach($this->_f as $field){
      
      if(property_exists($this, $field)){
        $ret[$field] = $this->$field;
      }
      
    }

    return $ret;
    
  }
  
  /**
   * Convierte una colecion en un AmObject
   */
  public static function parse($collection){

    if(is_array($collection)){
      return $collection;
    }
    
    if($collection instanceof AmObject){
      return $collection->toArray();
    }
    
    return array();
    
  }
  
}

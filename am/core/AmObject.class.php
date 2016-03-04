<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para objetos en Amathista
 */
class AmObject implements Iterator, Countable, ArrayAccess{

  /**
   * Lista de propiedades creados dinamicamente
   */
  private $_f = array();

  /**
   * Llamada del constructor. Inicializa los atributos indicados en el array
   * data.
   * @param colección $data Coleción de pares atributo=>valor.
   */
  public function __construct($data = null){

    $this->extend($data);

  }

  /**
   * Extiende los atributos del objeto.
   * @param  colección $data Coleción de pares atributo=>valor.
   * @return $this
   */
  protected function extend($data = null){

    // Transformar atributos a array
    $data = self::parse($data);

    // Asignar atributos
    foreach($data as $attr => $value)
      $this->$attr = $value;

    return $this;

  }

  /**
   * Devuelve si un attributo es publico o no.
   * @param  string/int  $name Nombre del atributo consultado.
   * @return bool              Si el atributo es público.
   */
  public function isPublicAttr($name){

    return !property_exists($this, $name) || $this->haveAttr($name);

  }

  /**
   * Indica si el objeto tiene o no un atributo.
   * @param  string/int $name Nombre del atributo a consultar.
   * @return bool             Si el posee el atributo.
   */
  public function haveAttr($name){

    $vars = get_object_vars($this);
    return in_array($name, array_keys($vars));

  }

  /**
   * Llamada para obtener el valor de un atributo del objeto: $this->name.
   * Devuelve el valor si este es público.
   * @param  string/int $name Nombre del atributo a conultar.
   * @return mixed            Valor del atributo o null si este no existe.
   */
  public function __get($name){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    // Si existe el parametro devolver el valor
    if(isset($this->$name))
      return $this->$name;

    // Si no existe el parámetro devolver null
    return null;

  }

  /**
   * Llamada de la asignacion a una propiedad: $this->name = $value.
   * Asigna un valor a un atributo si este es público.
   * @param string/int $name  Nombre de la propiedad a setear.
   * @param mixed      $value Valor a asignar.
   */
  public function __set($name, $value){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    // Agregar propiedad a lista de propiedades dinámicas si no se ha agregado
    if(!$this->haveAttr($name))
      $this->_f[] = $name;

    // Asignar valor
    $this->$name = $value;

  }

  /**
   * Llamada de isset a un atributo del objeto: isset($this->name).
   * Devuelve el resultado la llamada isset al atributo si este es público.
   * @param  string/int $name Nombre de la propiedad a consultar.
   * @return bool             Si pose dicho propiedad y no es null.
   */
  public function __isset($name){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    return isset($this->$name);

  }

  /**
   * Llamada de 'unset' a una propiedad: unset($this->name).
   * @param string/int $name Nombre de la propiedad a consultar.
   */
  public function __unset($name){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    // Eliminar atributo dinamico
    $this->_f = array_diff($this->_f, array($name));
    unset($this->$name);

  }

  /**
   * Llamada de consulta del objeto como un array: $this[$key].
   * @param  string/int $key Key que desea obtener.
   * @return mixed           Valor de la array en dicho key.
   */
  public function offsetGet($key){

    return $this->__get($key);

  }

  /**
   * Llamada de la asignacion de valores como un array: $this[$key] = $value.
   * @param  string/int $key   Key solicitado.
   * @param  mixed      $value Valor del array en ducho key.
   */
  public function offsetSet($key, $value){

    $this->__set($key, $value);

  }

  /**
   * Llamada de la funcion isset sobre el objeto utilizado como un array:
   * isset($this[$key]).
   * @param  string/int $key Key solicitado.
   * @return mixed           Valor de la array en dicho key.
   */
  public function offsetExists($key){

    return $this->__isset($key);

  }

  /**
   * Llamada de la funcion unset sobre el objeto como un array:
   * unset($this[$key]).
   * @param string/int $key Key solicitado.
   */
  public function offsetUnset($key){

    $this-> __unset($key);

  }

  /**
   * Mueve el puntero al primer elemento de la coleción: rewind($this).
   * @return $this
   */
  public function rewind() {

    reset($this->_f);
    return $this;

  }


  /**
   * Obtiene el valor actual de la colección: current($this).
   * @return mixed Valor en la posición actual.
   */
  public function current() {

    $name = $this->key();
    return $this->$name;

  }

  /**
   * Mueve el puntero de la coleción al ultimo elemento: end($this).
   * @return $this
   */
  public function end() {

    end($this->_f);
    return $this;

  }

  /**
   * Obtiene el indice del la posicion actual de la colección: key($this).
   * @return $this Key de la posición actual.
   */
  public function key() {

    return current($this->_f);

  }

  /**
   * Mueve el puntero de la colección al siguiente elemento: next($this).
   * @return $this
   */
  public function next() {

    next($this->_f);
    return $this;

  }

  /**
   * Mueve el puntero de la colección al elemento previo: prev($this).
   * @return $this
   */
  public function prev() {

    prev($this->_f);
    return $this;

   }

  /**
   * Indica si el elemento actual de la coleción es válido: isValid($this).
   * @return bool Si posee otro elemento.
   */
  public function valid() {

    $field = $this->key();
    return isset($this->$field);

  }

  /**
   * Devuelve el numero de objetos de la colección: count($this).
   * @return int cantidad de propieades.
   */
  public function count() {
    return count($this->_f);
  }

  /**
   * Devuelve un hash con los valores de los atributos dinámicos.
   * @return hash Hash de propiedades públicas del objeto.
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
   * Crea una copia del objeto actual agregando las propiedades,
   * @param  hash  $params Hash de propiedades a agregar.
   * @return mixed         Copia del objeto actual con las propiedades
   *                       modificadas.
   */
  public function cp(array $params = array()){

    $className = get_class($this);

    return new $className(array_merge($this->toArray(), $params));

  }

  /**
   * Convierte una coleción (stdClass, AmObject) a un array. Si la coleción
   * No es un array ni es una instancia de stdClass o AmObject, retorna un
   * array vacío.
   * @param  hash $collation Hash de propiedades. Puede ser un array o una
   *                         instancia de stdClass o AmObject, o instancia de
   *                         AmObject.
   * @return array           Propiedades en forma de array (hash).
   */
  public static function parse($collation){

    if(is_array($collation)){
      return $collation;
    }

    if($collation instanceof stdClass){
      return (array)$collation;
    }

    if($collation instanceof AmObject){
      return $collation->toArray();
    }

    return array();

  }

  /**
   * Devuelve un hash de los valores $arr con los keys $properties.
   * @param  hash  $arr         Hash de donde se obtiene los valores
   * @param  array  $properties Lista de propiedades a tomar.
   * @return hash               Hash resultante.
   */
  public static function mask(array $arr, array $properties){
    $ret = array();
    foreach ($properties as $value)
      $ret[$value] = itemOr($value, $arr);
    return $ret;
  }

}

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
   * data
   * @param coleccion $data Colleción de pares atributo=>valor.
   */
  public function __construct($data = null){

    $this->extend($data);

  }

  /**
   * Extiende los atributos del objeto
   * @param coleccion $data Colleción de pares atributo=>valor.
   */
  protected function extend($data = null){

    // Transformar atributos a array
    $data = self::parse($data);

    // Asignar atributos
    foreach($data as $attr => $value)
      $this->$attr = $value;

  }

  /**
   * Devuelve si un attributo es publico o no
   * @param  string  $name Nombre del atributo consultado
   * @return bool
   */
  public function isPublicAttr($name){

    return !property_exists($this, $name) || $this->haveAttr($name);

  }

  /**
   * Indica si el objeto tiene o no un atributo
   * @param  string $name Nombre del atributo a consultar
   * @return bool         
   */
  public function haveAttr($name){

    $vars = get_object_vars($this);
    return isset($vars[$name]);

  }

  /**
   * Llamada para obtener el valor de un atributo del objeto: $this->name.
   * Devuelve el valor si este es público.
   * @param  string $name Nombre del atributo a conultar
   * @return mixed        Valor del atributo o null si este no existe.
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
   * @param string $name  Nombre de la propiedad a setear.
   * @param mixed  $value Valor a asignar.
   **/
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
   * @param  string  $name Nombre de la propiedad a consultar.
   * @return bool
   */
  public function __isset($name){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    return isset($this->$name);

  }

  /**
   * Llamada de 'unset' a una propiedad: unset($this->name)
   * @param string $name Nombre de la propiedad a consultar.
   **/
  public function __unset($name){

    // Si existe una propiedad de clase en el objeto generar error
    if(!$this->isPublicAttr($name))
      throw Am::e('AMOBJECT_CANNOT_ACCESS_PROPERTY', get_class($this), $name);

    // Eliminar atributo dinamico
    $this->_f = array_diff($this->_f, array($name));
    unset($this->$name);

  }

  /**
   * Llamada de consulta del objeto como un array: $this[$name]
   **/
  public function offsetGet($name){

    return $this->__get($name);

  }

  /**
   * Llamada de la asignacion de valores como un array: $this[$name] = $value
   **/
  public function offsetSet($name, $value){

    $this->__set($name, $value);

  }

  /**
   * Llamada de la funcion isset sobre el objeto utilizado como un array:
   * isset($this[$name])
   **/
  public function offsetExists($name){

    return $this->__isset($name);

  }

  /**
   * Llamada de la funcion unset sobre el objeto como un array:
   * unset($this[$name])
   **/
  public function offsetUnset($name){

    $this-> __unset($name);

  }

  /**
   * Mueve el puntero al primer elemento de la collecion: rewind($this)
   **/
  public function rewind() {

    reset($this->_f);
    return $this;

  }


  /**
   * Obtiene el valor actual de la coleccion: current($this)
   **/
  public function current() {

    $name = $this->key();
    return $this->$name;

  }

  /**
   * Mueve el puntero de la collecion al ultimo elemento: end($this)
   **/
  public function end() {

    end($this->_f);
    return $this;

  }

  /**
   * Obtiene el indice del la posicion actual de la coleccion: key($this)
   **/
  public function key() {

    return current($this->_f);

  }

  /**
   * Mueve el puntero de la coleccion al siguiente elemento: next($this)
   **/
  public function next() {

    next($this->_f);
    return $this;

  }

  /**
   * Mueve el puntero de la coleccion al elemento previoo: prev($this)
   **/
  public function prev() {

    prev($this->_f);
    return $this;

   }

  /**
   * Indica si el elemento actual de la collecion es valido: isValid($this)
   **/
  public function valid() {

    $field = $this->key();
    return isset($this->$field);

  }

  /**
   * Devuelve el numero de objetos de la coleccion: count($this)
   **/
  public function count() {
    return count($this->_f);
  }

  /**
   * Devuelve un hash con los valores de los atributos dinamicos
   **/
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
   * @param  hash     $params Hash de propiedades a agregar.
   * @return Any              Copia del objeto actual con las propiedades
   *                          modificadas.
   */
  public function cp(array $params = array()){

    $className = get_class($this);

    return new $className(array_merge($this->toArray(), $params));

  }

  /**
   * Convierte una coleción (stdClass, AmObject) a un array. Si la colleción
   * No es un array ni es una instancia de stdClass o AmObject, retorna un
   * array vacío.
   **/
  public static function parse($collection){

    if(is_array($collection)){
      return $collection;
    }

    if($collection instanceof stdClass){
      return (array)$collection;
    }

    if($collection instanceof AmObject){
      return $collection->toArray();
    }

    return array();

  }

  /**
   * Devuelve los valores $arr con los keys $properties
   */
  public static function mask(array $arr, array $properties){
    $ret = array();
    foreach ($properties as $value) {
      $ret[$value] = itemOr($value, $arr);
    }
    return $ret;
  }

}

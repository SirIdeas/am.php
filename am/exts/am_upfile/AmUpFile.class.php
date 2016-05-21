<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Subir
class AmUpFile extends AmObject{

  public function __construct($path){
    if(!is_array($path))
      $path = explode('/', $path);

    $name = array_shift($path);

    $options = itemOr($name, $_FILES, array());

    foreach(array('name', 'type', 'tmp_name', 'error', 'size') as $field)
      $this->$field = self::getItemOf($path, $options[$field]);

    $ext = explode('.', $this->name);
    $this->ext = array_pop($ext);
    
  }

  public function exists(){

    return is_file($this->tmp_name);

  } 

  public function move($dest){

    $realDest = realpath($dest);

    if(is_dir($realDest))
      $realDest .= '/'.$this->name;
    else
      $realDest = $dest;

    return copy($this->tmp_name, $realDest);

  }

  public static function get($name){

    return new self($name);

  }

  private static function getItemOf(array $path, $value){
    
    if(empty($path))
      return $value;
    
    $key = array_shift($path);

    if(isset($value[$key]))
      return self::getItemOf($path, $value[$key]);

    return null;

  }
  
}
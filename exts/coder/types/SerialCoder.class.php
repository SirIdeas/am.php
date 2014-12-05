<?php

/**
 * Clase para la lectura y escritura de configuraciones serializadas
 */

// PENDIENTE DESARROLLAR

class SerialCoder extends AmCoder{
    
  public function decodeData($data) {
    $ret = unserialize($data, true);
    return is_array($ret)? $ret : array();
  }
  
  public function encode($data){
    return serialize($data);
  }
  
}

<?php

/**
 * Clase para la lectura y escritura de configuraciones en JSON
 */

// PENDIENTE DESARROLLAR
class JsonCoder extends AmCoder{

  public function decodeData($data) {
    $ret = json_decode($data, true);
    return is_array($ret)? $ret : array();
  }
  
  public function encode($data){
    return json_encode($data);
  }
  
}

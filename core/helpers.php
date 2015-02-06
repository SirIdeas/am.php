<?php

// Indica si un callback es válido o no.
function isValidCallback($callback){
  // Si es un array evaluar como metodo
  if(is_array($callback))
    return call_user_func_array("method_exists", $callback);
  // Si es string evaluar como function
  if(is_string($callback))
    return function_exists($callback);
  // Es un callback invalido
  return false;
} 

// Devuele un valor de una posicion del array. Si el valor
// no existe devuelve el valor por $def
function itemOr($index, $arr, $def = null){
  return isset($arr[$index])? $arr[$index] : $def;
}

// Indica si es una array asociativo o no
function isAssocArray(array $array){
  $j = 0;
  foreach($array as $i => $_){
    if($j !== $i)
      return true;
    $j++;
  }
  return false;
}

<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

// Convierte un valor a booleano
function parseBool($value){
  if(in_array($value, array(true, 1, 'true', '1'))) return true;
  if(in_array($value, array(false, 0, 'false', '0'))) return false;
  return null;
}

// Verifica si una fecha tiempo es valida.
function checkTimestamp($date){
  if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $date, $matches))
    if (checkdate($matches[2], $matches[3], $matches[1]))
      return true;
  return false;
}


// Devuelve la diferencia entre dos valores.
// Si los elementos on array se compara recursivamente
// Devuelve true si no hay diferencia. De lo contrario
// devuelve un valores que son diferentes.
function diff($v1, $v2){
  if(is_array($v1) && is_array($v2)){
    $ret = array();
    $ks = array_unique(array_merge(array_keys($v1), array_keys($v2)));
    foreach($ks as $k){
      $vk1 = isset($v1[$k])?$v1[$k]:null;
      $vk2 = isset($v2[$k])?$v2[$k]:null;
      if(true !== ($diff = diff($vk1, $vk2)))
        $ret[$k] = $diff;
    }
    return count($ret)>0? $ret : true;
  }else if($v1 === $v2){
    return true;
  }
  return array($v1, $v2);

}

// Indica si un callback es válido o no.
function isValidCallback($callback){
  // Si es un array evaluar como metodo
  if(is_array($callback))
    return call_user_func_array('method_exists', $callback);
  // Si es string evaluar como function
  if(is_string($callback))
    return function_exists($callback);
  // Es un callback invalido
  return false;
}

// Devuele un valor de una posicion del array. Si el valor
// no existe devuelve el valor por $def
function itemOr($index, array $arr, $def = null){
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

// Mezclar dos array si la primera posicion del segundo array
// es diferentes de falso, sino retornar el segundo array
function merge_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge($arr1, $arr2);
}

// Mezclar dos array si la primera posicion del segundo array
// es diferentes de falso, sino retornar el segundo array.
// Devuelve los valores unicos
function merge_if_snd_first_not_false_unique(array $arr1, array $arr2){
  return array_unique(merge_if_snd_first_not_false($arr1, $arr2));
}

// Mezclar dos array recursivamente si la primera posicion
// del segundo array. es diferentes de falso, sino retornar
// el segundo array
function merge_r_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge_recursive($arr1, $arr2);
}

// Mezcla dos array si ambos parametros son arrays.
// De lo contrario se conservará el segundo elemento
function merge_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge($arr1, $arr2);
  return $arr2;
}

// 
function merge_if_both_are_array(array $arr1, array $arr2){
  
  if(!isAssocArray($arr1) && !isAssocArray($arr2))
    return array_merge($arr1, $arr2);

  $ret = array();
  foreach ($arr1 as $key => $value) {
    $value2 = itemOr($key, $arr2);

    if(is_array($value) && is_array($value2))
      $value = merge_if_are_array($value, $value2);
    elseif(isset($value2))
      $value = $value2;

    $ret[$key] = $value;
  }

  return $ret;

}

// Mezvla dos array recursivamente si ambos parametros son arrays
// De lo contrario se conservará el segundo elemento
function merge_r_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge_recursive($arr1, $arr2);
  return $arr2;
}

// Mezcla dos arrays sin ambos parametros son arrays y si
// el primer elemento del segundo parametro no es falso.
function merge_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    merge_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

// Mezcla recursivamente dos arrays sin ambos parametros son
// arrays y si el primer elemento del segundo parametro no es falso.
function merge_r_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    merge_r_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

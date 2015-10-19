<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Devuelve el valor del $arr en la posición $index si existe, de lo contrario
 * devuelve $def.
 * -----------------------------------------------------------------------------
 * @param   string  $index  Indice que se desea consultar.
 * @param   array   $arr    Array de donde se desea obtener el valor.
 * @param   mixed   $def    Valor a devolver si $arr no contiene la posicion
 *                          $index.
 * @return  mixed           Valor en el array de la posición consultada o $def.
 */
function itemOr($index, array $arr, $def = null){

  return isset($arr[$index])? $arr[$index] : $def;

}

/**
 * -----------------------------------------------------------------------------
 * Devuelve si un $callback es un callback válido o no.
 * -----------------------------------------------------------------------------
 * @param   callback $callback Callback a verificar
 */
function isValidCallback($callback){

  // Si es un array evaluar como metodo
  if(is_array($callback))
    return call_user_func_array('method_exists', $callback);

  // Si es string evaluar como function
  if(is_string($callback)){
    $callback = explode('::', $callback);

    if(count($callback)==2)
      return isValidCallback($callback);

    elseif(count($callback)==1)
      return function_exists($callback[0]);

  }
  
  // Es un callback invalido
  return false;

}


/**
 * -----------------------------------------------------------------------------
 * Busca un archivo dentro de los directorios indicados hasta encontrar el
 * uno donde exista.
 * -----------------------------------------------------------------------------
 * @param  string $file  Archivo a buscar.
 * @param  array  $paths array de directorios donde se buscará el archivo.
 * @return string        Devuelve la ruta del primer archivo encontrado.
 */
function findFileIn($file, array $paths){

  // Si existe el archivo retornar el mismo
  if(is_file($file)) return $file;

  // Invertir el array
  $pathsInv = array_reverse($paths);

  // Buscar un archivo dentro de las carpetas
  foreach($pathsInv as $path)
    if(is_file($realPath = "{$path}/{$file}"))
      return $realPath;

  return false;

}

/**
 * -----------------------------------------------------------------------------
 * Devuelve si es una array asociativo o no.
 * -----------------------------------------------------------------------------
 * @param   array $arr  Array a virificar si es asociativo.
 * @return  bool        Si $arr es o no un array asociativo.
 */
function isAssocArray(array $arr){

  $j = 0;
  foreach($arr as $i => $_){
    if($j !== $i)
      return true;
    $j++;
  }

  return false;
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve un array con los valores únicos de la mezcla de dos arrays.
 * -----------------------------------------------------------------------------
 * @param array $arr1
 * @param array $arr2
 * @return array        
 */
function merge_unique(array $arr1, array $arr2){
  return array_unique(array_merge($arr1, $arr2));
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla de dos array si ambos parámetros son arrays, de lo
 * contrario devuelve el segundo parámetro ($arr2).
 * -----------------------------------------------------------------------------
 * @param mixed $arr1
 * @param mixed $arr2
 * @return mixed
 */
function merge_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge($arr1, $arr2);
  return $arr2;
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla recursiva de dos array si ambos parámetros son arrays, de
 * lo contrario devuelve el segundo parámetro ($arr2).
 * -----------------------------------------------------------------------------
 * @param mixed $arr1
 * @param mixed $arr2
 * @return mixed
 */
function merge_r_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge_recursive($arr1, $arr2);
  return $arr2;
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla de dos array si el segundo array en la posición 0 
 * ($arr[0]) es diferente de falso, de lo contrario devuelve el segundo
 * array ($arr2).
 * -----------------------------------------------------------------------------
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function merge_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge($arr1, $arr2);
}

/**
 * -----------------------------------------------------------------------------
 * Deveulve la mezcla dos arrays con merge_if_snd_first_not_false y elimina los
 * valores duplicados.
 * -----------------------------------------------------------------------------
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function merge_if_snd_first_not_false_unique(array $arr1, array $arr2){
  return array_unique(merge_if_snd_first_not_false($arr1, $arr2));
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla recursiva de dos arrays si el segundo array en la
 * posición 0 ($arr[0]) es diferente de falso (false), de lo contrario devuelve
 * el segundo array ($arr2).
 * -----------------------------------------------------------------------------
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function merge_r_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge_recursive($arr1, $arr2);
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla de dos arrays con merge_if_snd_first_not_false si ambos
 * son arrays, de lo contrario devuelve el segundo parámetro ($arr2).
 * -----------------------------------------------------------------------------
 * @param mixed $arr1
 * @param mixed $arr2
 * @return mixed
 */
function merge_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return merge_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

/**
 * -----------------------------------------------------------------------------
 * Devuelve la mezcla de dos arrays con merge_r_if_snd_first_not_false si ambos
 * son arrays, de lo contrario devuelve el segundo parámetro ($arr2).
 * -----------------------------------------------------------------------------
 * @param mixed $arr1
 * @param mixed $arr2
 * @return mixed
 */
function merge_r_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return merge_r_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

/**
 * -----------------------------------------------------------------------------
 * Mezcla recursivamente dos arrays. Si una posición existe en ambos arrays se
 * mezclan recursivamente si ambos son arrays, de lo contrario prevalece el del
 * segundo array.
 * -----------------------------------------------------------------------------
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function merge_if_both_are_array(array $arr1, array $arr2){
  
  // Si no son arrays asociativos se mezclan normalmente.
  if(!isAssocArray($arr1) && !isAssocArray($arr2))
    return array_merge($arr1, $arr2);

  $ret = array();
  foreach ($arr1 as $key => $value) {

    if(!isset($arr2[$key])) continue;

    // Si ambos valores son arrays se mezclan recursivamente
    if(is_array($value) && is_array($arr2[$key]))
      $arr1[$key] = merge_if_both_are_array($value, $arr2[$key]);

    // Sino el valor del segundo array sobreescribe el del primero
    else
      $arr1[$key] = $arr2[$key];

    // Eliminar el elmento del segundo array
    unset($arr2[$key]);

  }

  // Agregar los valores restantes en el segundo array
  return array_merge($arr1, $arr2);

}

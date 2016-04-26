<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Devuelve el valor del $arr en la posición $index si existe, de lo contrario
 * devuelve $def.
 * @param  string $index Indice que se desea consultar.
 * @param  array  $arr   Array de donde se desea obtener el valor.
 * @param  mixed  $def   Valor a devolver si $arr no contiene la posición
 *                       $index.
 * @return mixed         Valor en el array de la posición consultada o $def.
 */
function itemOr($index, array $arr, $def = null){

  return isset($arr[$index])? $arr[$index] : $def;

}

/**
 * Devuelve si un $callback es un callback válido o no.
 * @param  callback $callback Callback a verificar
 * @return bool               Si existe el callback.
 */
function isValidCallback($callback){

  // Si es una función retornar true
  if(is_callable($callback))
    return true;

  // convetir en array
  if(is_string($callback))
    $callback = explode('::', $callback);

  // Si el array tiene 2 elemento buscar callback como metodo
  if(count($callback)==2)
    return call_user_func_array('method_exists', $callback);

  // Si el array tiene un solo elemento buscar como funcion
  elseif(count($callback)==2)
    return function_exists($callback[0]);
  
  // Es un callback invalido
  return false;

}

/**
 * Busca un archivo dentro de los directorios indicados hasta encontrar el
 * uno donde exista.
 * @param  string $file  Archivo a buscar.
 * @param  array  $paths array de directorios donde se buscará el archivo.
 * @return string        Devuelve la ruta del primer archivo encontrado.
 */
function findFileIn($file, array $paths, $reverse = true){

  // Si existe el archivo retornar el mismo
  if(is_file($file)) return $file;

  // Invertir el array
  if($reverse)
    $paths = array_reverse($paths);

  // Buscar un archivo dentro de las carpetas
  foreach($paths as $path)
    if(is_file($realPath = "{$path}/{$file}"))
      return $realPath;

  return false;

}

/**
 * Devuelve si es un hash o no.
 * @param  array $arr Array a virificar si es un hash.
 * @return bool       Si $arr es o no un hash.
 */
function isHash(array $arr){

  $j = 0;
  foreach($arr as $i => $_){
    if($j !== $i)
      return true;
    $j++;
  }

  return false;
}

/**
 * Devuelve un array con los valores únicos de la mezcla de los arrays recibidos
 * por parámetro.
 * @return array        
 */
function merge_unique(/* Lista de arrays */){
  $args = func_get_args();
  return array_unique(call_user_func_array('array_merge', $args));
}

/**
 * Devuelve la mezcla de dos array si ambos parámetros son arrays, de lo
 * contrario devuelve el segundo parámetro ($arr2).
 * @param  mixed $arr1
 * @param  mixed $arr2
 * @return mixed
 */
function merge_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge($arr1, $arr2);
  return $arr2;
}

/**
 * Devuelve la mezcla recursiva de dos array si ambos parámetros son arrays, de
 * lo contrario devuelve el segundo parámetro ($arr2).
 * @param  mixed $arr1
 * @param  mixed $arr2
 * @return mixed
 */
function merge_r_if_are_array($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return array_merge_recursive($arr1, $arr2);
  return $arr2;
}

/**
 * Devuelve la mezcla de dos array si el segundo array en la posición 0 
 * ($arr[0]) es diferente de falso, de lo contrario devuelve el segundo
 * array ($arr2).
 * @param  array $arr1
 * @param  array $arr2
 * @return array
 */
function merge_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge($arr1, $arr2);
}

/**
 * Deveulve la mezcla dos arrays con merge_if_snd_first_not_false y elimina los
 * valores duplicados.
 * @param  array $arr1
 * @param  array $arr2
 * @return array
 */
function merge_if_snd_first_not_false_unique(array $arr1, array $arr2){
  return array_unique(merge_if_snd_first_not_false($arr1, $arr2));
}

/**
 * Devuelve la mezcla recursiva de dos arrays si el segundo array en la
 * posición 0 ($arr[0]) es diferente de falso (false), de lo contrario devuelve
 * el segundo array ($arr2).
 * @param  array $arr1
 * @param  array $arr2
 * @return array
 */
function merge_r_if_snd_first_not_false(array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return array_merge_recursive($arr1, $arr2);
}

/**
 * Devuelve la mezcla de dos arrays con merge_if_snd_first_not_false si ambos
 * son arrays, de lo contrario devuelve el segundo parámetro ($arr2).
 * @param  mixed $arr1
 * @param  mixed $arr2
 * @return mixed
 */
function merge_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return merge_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

/**
 * Devuelve la mezcla de dos arrays con merge_r_if_snd_first_not_false si ambos
 * son arrays, de lo contrario devuelve el segundo parámetro ($arr2).
 * @param  mixed $arr1
 * @param  mixed $arr2
 * @return mixed
 */
function merge_r_if_are_array_and_snd_first_not_false($arr1, $arr2){
  if(is_array($arr1) && is_array($arr2))
    return merge_r_if_snd_first_not_false($arr1, $arr2);
  return $arr2;
}

/**
 * Mezcla recursivamente dos arrays. Si una posición existe en ambos arrays se
 * mezclan recursivamente si ambos son arrays, de lo contrario prevalece el del
 * segundo array.
 * @param  array $arr1
 * @param  array $arr2
 * @return array
 */
function merge_if_both_are_array(array $arr1, array $arr2){
  
  // Si no son hashes se mezclan normalmente.
  if(!isHash($arr1) && !isHash($arr2))
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

/**
 * Devuelve la mezcla de dos array conla funcion merge_if_both_are_array si el
 * segundo array en la posición 0 ($arr[0]) es diferente de falso, de lo
 * contrario devuelve el segundo.
 * array ($arr2).
 * @param  array $arr1
 * @param  array $arr2
 * @return array
 */
function merge_if_both_are_array_and_snd_first_not_false(
  array $arr1, array $arr2){
  if(isset($arr2[0]) && $arr2[0] === false)
    return $arr2;
  return merge_if_both_are_array($arr1, $arr2);
}

/**
 * Devuelve una cadena convertida en formato under_score. Agrega un underscore
 * antes de cada lera mayúscula y convierte esta letra a minúsculas.
 * @param  string $s Cadena a convertir.
 * @return string    Cadena en unserscore.
 */
function underscore($s) {

  // Primer caracter en miniscula
  if(!empty($s))
    $s[0] = strtolower($s[0]);

  // Crear funcion para convertir en minuscula
  $func = create_function('$c', 'return "_" . strtolower($c[1]);');

  // Operar
  return preg_replace_callback('/([A-Z])/', $func, str_replace(' ', '_', $s));

}

/**
 * Devuelve el plurar según la gramática inglesa de una cadena según como
 * termina.
 * @param  string $cad cadena a pluralizar.
 * @return string      Cadena en plurar.
 */
function pluralize($cad){
  if(preg_match('/.*(sh|ch|s|x|z)$/i', $cad, $m))
    return $m[0].'es';
  if(preg_match('/(.*[bcdfghjklmnpqrstvwxyz]{1})y$/i', $cad, $m))
    return $m[1].'ies';
  return $cad.'s';
}

/**
 * Devuelve una cadena  en formato camelCase. Elimina todos los underscore
 * seguidos de una letra minúscula y los convierte la letra en mayúsulas. Si
 * $cfc es true entonces el primer caracter tambien es convertido en mayúsculas.
 * @param  string  $cad Cadena a convertir.
 * @param  boolean $cfc Indica si el primer caracter de la cadena tambien debe
 *                      ser mayuscula.
 * @return string       Cadena en camelCase.
 */
function camelCase($cad, $cfc = false){

  // Primer caracter en mayuscula o en miniscula
  if(!empty($cad)){
    if($cfc)
      $cad[0] = strtoupper($cad[0]);
    else
      $cad[0] = strtolower($cad[0]);
  }

  // Funcion para convertir cada caracter en miniscula
  $func = create_function('$c', 'return strtoupper($c[1]);');

  // Operar
  return preg_replace_callback('/_([a-z])/', $func, $cad);

}

/**
 * Convierte un valor al tipo booleano. El valor será verdadero si $value es
 * el literal true, el entero 1 o los strings 'true' o '1'; false si $value es 
 * el literal false, el entero 0, o los strings 'false' o '0'; de lo contrario
 * retornará null.
 * @param  mixed $value Valor a convertir
 * @return bool         Valor convertido
 */
function parseBool($value){

  if(in_array($value, array(true, 1, 'true', '1'))) return true;
  if(in_array($value, array(false, 0, 'false', '0'))) return false;
  
  return null;

}

/**
 * Indica si una cadena es un identificador válido. Un identificador válido es
 * una cadena que comienza con cualquier letra o el caracter underscore y esta
 * seguido de cualquier cantidad de letras, números y caracteres underscore.
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function isNameValid($str){

  return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $str) != 0;

}

/**
 * Lista el contenido de un o varios directorios.
 * @param  mixed $folders Directorio o lista de directorios de los que se desea
 *                        listar el contenido.
 * @param  array $options Opciones para elaborar el listado:
 *                        - files|bool|true: Indica si se debe incluir los
 *                          archivos.
 *                        - dirs|bool|true: Indica si se debe incluir los
 *                          directorios.
 *                        - recursive|bool|true: Indica si se debe buscar en los
 *                          niveles internos del directorio.
 *                        - inlude|string: Regex que indica que elementos se
 *                          deben incluir. Se aplican a la ruta absoluta de los
 *                          elementos listados.
 *                        - exclude|string: Regex que indica que elementos se
 *                          deben exluir. Se aplican a la ruta absoluta de los
 *                          elementos listados.
 *                        - return|int|0: Indica que elemento resultado de la
 *                          comparación con el parametro 'include' se retornará.
 *                          Por defecto retornará el resultado de la posición 0
 *                          que el contenido completo que hizo match.
 *                        - root|string|null: Indica la raíz donde se buscará.
 *                          Si este parámetro es indicado los parmátros
 *                          'include' y 'exclude' compararán la ruta del
 *                          elemento removiendo del inicio el contenido del este
 *                          parámetro.
 *                        - callback|callback|nul: Callback que se llamará por
 *                          cada item que se incluirá en el resultado.
 * @return array          Lista de archivo y/o carpetas listados.
 */
function amGlob($folders, array $options = array()){

  // Convertir en array si no es un array.
  if(!is_array($folders))
    $folders = array($folders);

  // Opciones por defcto
  $options = array_merge(array(
    'files' => true,
    'dirs' => false,
    'recursive' => true,
    'include' => '/.*/',
    'exclude' => '/^$/',
    'return' => 0,
    'root' => null,
    'callback' => null,
  ), $options);

  if(isset($options['root'])){
    $options['root'] = realpath($options['root']);
  }

  // Variablle para el retorno.
  $ret = array();

  // recorer las careptas
  foreach ($folders as $folder) {

    $folder = realpath($folder);

    if(!$folder)
      continue;

    $list = glob("{$folder}/*");

    foreach ($list as $item) {

      $item = realpath($item);

      // Si cumple con la regex
      if(preg_match_all($options['include'], $item, $m) && !preg_match($options['exclude'], $item)){

        if((is_file($item) && $options['files'] === true) ||
          (is_dir($item) && $options['dirs'] === true)){
          $path = $m[$options['return']][0];

          if($options['root'])
            $path = substr_replace($path, '', 0, strlen($options['root'])+1);

          if(is_callable($options['callback'])){
            $key = null;
            $callback = $options['callback'];
            $value = $callback($path, $key);

            if(isset($key))
              $ret[$key] = $value;
            else
              $ret[] = $value;

          }else{
            $ret[] = $path;
          }

        }
        
      }

      // Si es un directorio se pide explorar recursivamente
      if(is_dir($item) && $options['recursive'] === true){

        $ret = array_merge($ret, amGlob($item, $options));

      }

    }

  }

  return $ret;

}
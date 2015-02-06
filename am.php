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

// Cargador de Amathista
function amLoader($file){

  // Si existe el archivo retornar el mismo
  if(is_file($realFile = "{$file}.php")){
    require_once $realFile;
    return true;
  }

  // Incluir como extensión
  if(is_file($realFile = "{$file}.conf.php")){
    // Obtener la configuracion de la extencion
    $conf = require $realFile;

    // Obtener archivos a agregar de la extencion
    $files = itemOr("files", $conf, array());

    // Eliminar el item de los archivos necesarios de la configuración
    unset($conf["files"]);

    // Llamar archivo de iniciacion en la carpeta si existe.
    foreach ($files as $item)
      if(is_file($realFile = "{$file}{$item}.php"))
        require_once $realFile;
      else
        die("Am: Not fount Exts file: '{$realFile}'");

    
    // Incluir archivo init si existe
    if(is_file($realFile = "{$file}.init.php"))
      require_once $realFile;

    return true;

  }

}

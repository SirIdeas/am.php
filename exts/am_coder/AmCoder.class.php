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
 
/**
 * Clase para la lectura y escritura de configuraciones
 */

class AmCoder{

  // Decodifica un archivo de configuracion con el mismo formato
  public static function decode($path){

    // Si el archivo exite retornar lo que devuelva el mismo
    if(is_file($path))
      return require $path;

    // Si no existe el archivo retornan un array vacío
    return array();

  }

  // Leer el archivo
  public static function read($path) {

    // Si existe decodificar el contenido
    if(self::exists($path))
      return self::decode($path);

    return array();
  }

  // Escribir contenido del archivo
  public static function write($path, $data, $prepare = true) {

    // Preparar la data si es necesario
    if($prepare)
      $data = self::prepare($data);

    // Crear directorio donde se ubicará el archivo
    if(!is_dir($dir = dirname($path)))
      mkdir($dir, 0775, true);

    // Si el archivo no existe se crea el archivo
    file_put_contents($path, self::encode($data));

  }

  // Indica si el archivo existe
  public static function exists($path){
    return is_file($path);
  }

  // Preparar la data.
  // Consiste en crear array anidados en aquellas posiciones
  // cuya key tenga el caractere _
  public static function prepare(array $data){

    $ret = array();
    foreach($data as  $key => $value){
      $key = explode("_", $key);
      self::prepareInner($ret, $key, $value);
    }

    return $ret;

  }

  // Funcion auxiliar para preparar la data
  private static function prepareInner(array &$data, array $path, $value){

    if(empty($path)){
      if(count($data)>1)
        return 1;
      if(isset($data["_"]) || empty($data))
        return 2;
    }

    $key = array_shift($path);

    if(!isset($data[$key])){
      $data[$key] = array();
    }else{
      if(!is_array($data[$key])){
        $data[$key] = array(
          "_" => $data[$key]
        );
      }
    }

    switch (self::prepareInner($data[$key], $path, $value)){
      case 1:
        $data[$key]["_"] = $value;
        break;
      case 2:
        $data[$key] = $value;
        break;
    }

    return 0;

  }

  // Método que codifica la data
  public static function encode($data){
    return "<?php\n\nreturn " . self::_encode($data, "", ";") . "\n";
  }

  // Algoritmo para codificar la data.
  public static function _encode($data, $prefix = "", $subfix = ",") {

    if (!isset($data)) {
      return "null$subfix";
    }elseif(is_numeric($data)){
      return "$data$subfix";
    }elseif(is_string($data)){
      return "\"$data\"$subfix";
    }elseif($data === true){
      return "true$subfix";
    }elseif($data === false){
      return "false$subfix";
    }elseif(is_array($data) || is_object($data)){

      $data = (array)$data;

      $isAssocArray = isAssocArray($data);

      if(!$isAssocArray){

        $haveArray = false;
        $dataFormated = array();

        foreach($data as $i => $v){
          if(is_array($v) || is_object($v)){
            $haveArray = true;
          }else{
            $dataFormated[] = self::_encode($v, "", "");
          }
        }

        if(!$haveArray){

          $str = "array(" . implode(",", $dataFormated) . ")$subfix";

          return $str;

        }

      }

      $str = "array(\n";
      $prefixI = "  $prefix";

      foreach($data as $i => $v){
        $encode = self::_encode($v, $prefixI);
        if($isAssocArray){
          $str .= "$prefixI\"$i\" => $encode\n";
        }else{
          $str .= "{$prefixI}{$encode}\n";
        }

      }

      $str .= "$prefix)$subfix";

      return $str;

    }

    return "$data$subfix";

  }

}

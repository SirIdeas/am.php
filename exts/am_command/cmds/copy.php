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

// Does not support flag GLOB_BRACE
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

// Copiar archivos: PENDIENTE DESARROLLAR
function am_command_copy($target, $params, $config, $file, $argv){

  // Si no se especificó el src entonces se buscará en cada elemento
  if(!isset($config["src"])){
    foreach ($config as $child)
      am_command_copy($target, $params, $child, $file, $argv);
    return;
  }

  // Asignar valores por defecto
  $config = array_merge(array(
    "cwd" => "",
    "dest" => ""
  ), $config);

  // Obtener rutas reales
  $cwd = realPath($config["cwd"]);
  $dest = $config["dest"];

  foreach($config["src"] as $src){
    $result = rglob($cwd."/".$src);
    foreach($result as $file){

      // Obtener path destino
      $fileNoCwd = substr_replace($file, "", 0, strlen($cwd)+1);
      $destPath = $dest.$fileNoCwd;

      // Si el archivo ya existe continuar
      if(is_file($destPath) || is_dir($file))
        continue;

      // Crear carpeta si no existe
      if(!is_dir($dir = dirname($destPath)))
        mkdir($dir, 0775, true);

      // Copiar archivos
      if(copy($file, $destPath)){
        echo "\nCopied: $destPath";
      }else{
        echo "\nError coping: $destPath";
      }

    }
  }

}

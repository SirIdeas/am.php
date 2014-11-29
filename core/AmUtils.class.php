<?php

/**
 * Clase con utilidades
 */

final class AmUtils{

  // Busca un archivo en los paths indicados
  public static function findFile($file, array $paths){
    
    // Si existe el archivo retornar el mismo
    if(file_exists($file)) return $file;

    // Buscar un archivo dentro de las carpetas
    foreach($paths as $path){
      if(file_exists($realPath = "{$path}{$file}")) return $realPath;
    }
    return false;

  }
  
}
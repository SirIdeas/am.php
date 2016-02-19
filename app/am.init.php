<?php 

// Devuelve el contenido de un archivo para mostrar como código.
function getCodeFile($fileName){
  return htmlentities(file_get_contents(dirname(__FILE__)."/codes/{$fileName}"));
}
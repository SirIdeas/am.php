<?php

function am_error_handler($code, $message, $file, $line){
  if (0 == error_reporting()){
    return;
  }
  throw new ErrorException($message, 0, $code, $file, $line);
}

function am_exception_handler(Exception $e){
  echo "Excepción no capturada: " , $e->getMessage(), "\n";
}

set_error_handler("am_error_handler");
set_exception_handler("am_exception_handler");

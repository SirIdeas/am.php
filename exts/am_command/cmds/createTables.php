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
 
// Crea las tablas para una BD
function am_command_createTables($target, $params, $config, $file, $argv){

  echo "\n";

  $model = trim(array_shift($argv));
  $source = trim(array_shift($argv));

  // Si no se recibió el model se buscará el modelo por defecto
  if(!$source)
    $source = "default";

  // Si no existe la configuración para la fuente
  if(null === AmORM::getSourceConf($source)){
    echo "Fuente de datos inválida";
    return;
  }

  // Obtener instancia de la fuente
  $sourceInstance = AmORM::source($source);

  // Si no existe la BD se intenta crear
  if(!$sourceInstance->exists()){
    // Si se crear la BD entonces mostrar mensaje de error
    if($sourceInstance->create()){
      echo "\nDatabase '{$source}' created";
    }else{
      // No se pudo crear la BD
      echo "\nCan't create database '{$source}'";
      return;
    }
  }else{
    echo "\nDatabase '{$source}' already exists";
  }

  function echoResult($table, $created){
    echo "\n table '{$table}': ". (
      $created===0? "model not found" : (
      $created===1? "already exists" : (
      $created===true? "created" : (
      "error creating: " . $created
      ))));
  }
  // Si no se indico el modelo entonces se creará todas las tablas
  // del modelo
  if($model === null ||  empty($model)){

    // Obtener los nombres de la tabla en el archivo
    $results = $sourceInstance->createTables();

    foreach ($results as $tableName => $created)
      echoResult($tableName, $created);

  }else{

    // Crear solo la tabla del modelo indicado
    $result = $sourceInstance->createTableIfNotExists($model);
    // Mostrar el resultado
    echoResult($model, $result);

  }

}

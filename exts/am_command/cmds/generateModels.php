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
 
// General las clases para el modelo de la BD
function am_command_generateModels($target = null, $params = null, $config = null, $file = null, $argv = array()){

  echo "\n";

  $model = trim(array_shift($argv));
  $source = trim(array_shift($argv));

  // Si no se recibió el model se buscará el modelo por defecto
  if(!$source)
    $source = 'default';

  // Si no existe la configuración para la fuente
  if(null === AmORM::getSourceConf($source)){
    echo 'Fuente de datos inválida';
    return;
  }

  // Obtener instancia de la fuente
  $sourceInstance = AmORM::source($source);

  function echoResult($table, $result){
    echo
      "\n  {$table}:".
      "\n    folders              : " . ($result['folder']? 'createds' : '').
      "\n    configuration file   : " . ($result['conf']?   'created' : 'already exists').
      "\n    class for model      : " . ($result['model']?  'created' : 'already exists').
      "\n";
  }

  // Si no se indico el modelo entonces se genera
  // el ORM de toda la fuente
  if($model === null ||  empty($model)){

    // Generar todos los modelos
    $ret = $sourceInstance->createClassModels();

    // Mostrar el resultado de la creación de archivo
    // de configuracion de la fuente
    echo "\nsource {$source}:";
    echo "\n";
    echo "\n  configuration file     : " . ($ret['source']? 'created' : 'already exists');
    echo "\n";

    // Mostrar el resultado
    // El resultado esta agrupado por tabla
    foreach ($ret['tables'] as $table => $result) {
      echoResult('table '.$table, $result);
    }

  }else{

    // Obtener instancia de la tabla
    $tableInstance = $sourceInstance->describeTable($model);

    // Si no se encuentra la instancia de la tabla
    if(!$tableInstance){
      echo "No se encontró la tabla '{$source}'.'{$model}'";
      return;
    }

    // Mostrar el resultado
    echoResult($model, $tableInstance->createClassModels());

  }

}

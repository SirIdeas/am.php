<?php

/**
 * Clase para atender peticiones para comandos
 */

class AmCommand{

  // Ejecucion de un comando
  public static function exec($argv){

    // Imprimir comando
    echo "Amathista commands\n";

    // Obtener los targets del archivo de configuracion
    $targets = Am::getAttribute("commands");

    // 1er: origen de la peticion: HTTP/consola
    $file = array_shift($argv);

    // 2do: Comando a ejecutar
    $cmd = array_shift($argv);
    
    // El comando puede ser indicado con un target especifico: $cmd="comando:target"
    // Dividir para obtener target
    $params = explode(":", $cmd);    
    $cmd = array_shift($params);    // La primera parte es el comando real
    $target = array_shift($params); // El siguiente elemento es el target. Si no existe es null
                                    // $params queda con el resto de los parametros del argumento
                                    // $argv queda con el resto de los parametros recibidos
    // Determinar el nombre de la funcion que ejecuta el comando
    $funtionName = "am_command_{$cmd}";
    
    // Si la funcion no existe mostrar error
    function_exists($funtionName) or die("Am: command not found {$cmd}");

    // Imprimir el comando que se ejecutará
    echo "\n-- $cmd:";

    // Si el target esta indicado
    if(isset($target)){
      
      // Si el target esta definido pero no existe en la configuracion
      // mostrar error
      isset($targets[$cmd][$target]) or die("Am: target not found {$cmd}:{$target}");
      // Obtener la configuracion del target indicado
      $config = $targets[$cmd][$target];

      // Llamda de la funcion que atiende el comando
      // params: 1: target indicado
      //         2: parametros del argumento
      //         3: configuracion del target
      //         4: parametros recibidos
      $funtionName($target, $params, $config, $file, $argv);

    // Sino se definio el target, pero existen targets en la configuracion para el comando
    }elseif(isset($targets[$cmd])){

      // Ejecutar el comando con todos los targets en la configuracion
      foreach($targets[$cmd] as $target => $conf){
        $funtionName($target, $params, $conf, $file, $argv);
      }

    }

    echo "\n";

  }

  // Atender peticion por por terminar
  public static function asTerminal(){

    // se une los argumentos con "/"
    $arguments = implode("/", func_get_args());

    // Separar todos los argumentos
    $arguments = explode("/", $arguments);

    // Ejecutar comando
    self::exec($arguments);

  }

  // PENDIENTE DESARROLLAR
  public static function asRequest(){
    header("content-type: text/plain");
    call_user_func_array(array("AmCommand", "asTerminal"), func_get_args());
  }

}

// Agregar ruta para atender peticiones por consola
Am::setRoute(":arguments(am\.php/.*)", "AmCommand::asTerminal");

// Agregar ruta para atender petidicones HTTP
Am::setRoute("/:arguments(am-command/.*)", "AmCommand::asRequest");

// Concatenar: PENDIENTE ORGANIZAR
function am_command_concat($target, $params, $config, $file, $argv){

  foreach ($config as $fileName => $assets) {
    // REVISAR: No se deberia usar A,Asset
    $asset = new AmAsset($fileName, $assets);
    file_put_contents($fileName, $asset->getContent());
    echo "\nAm: Asset created $fileName";
  }

}

// Copiar archivos: PENDIENTE DESARROLLAR
function am_command_copy($target, $params, $config, $file, $argv){
  print_r($config["src"]);
}

function am_command_generateModels($target, $params, $config, $file, $argv){
  
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

  function echoResult($table, $result){
    echo "\n{$table}:";
    echo "\n  folders              : " . ($result["folders"]?    "createds" : "");
    echo "\n  configuration file   : " . ($result["conf"]?       "created" : "already exists");
    echo "\n  class base for table : " . ($result["tableBase"]?  "created" : "already exists");
    echo "\n  class for table      : " . ($result["table"]?      "created" : "already exists");
    echo "\n  class base for model : " . ($result["modelBase"]?  "created" : "already exists");
    echo "\n  class for model      : " . ($result["model"]?      "created" : "already exists");
    echo "\n";
  }

  // Obtener instancia de la fuente
  $sourceInstance = AmORM::source($source);

  // Si no se indico el modelo entonces se genera
  // el ORM de toda la fuente
  if($model ===null ||  empty($model)){

    // Generar todos los modelos
    $ret = $sourceInstance->createClassModels();

    // Mostrar el resultado
    // El resultado esta agrupado por tabla
    foreach ($ret as $table => $result) {
      echoResult($table, $result);
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
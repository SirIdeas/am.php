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
 * ORM Basico de Amathista
 */

final class AmORM{

  protected static
    $includedModels = array(),
    $sources = array();

  // Incluye un archivo dentro buscado dentro de la
  // carpeta de la libreria
  public static function requireFile($file, $onCurrentDir = true){
    $path = ($onCurrentDir? dirname(__FILE__)."/" : "") . "{$file}.php";
    if(!is_file($path))
      die("AmORM: file not found '{$path}'");
    require_once $path;
  }

  // Incluye un driver de BD
  public static function driver($driver){

    // Obtener el nombre de la clase
    $driverClassName = AmORM::camelCase($driver, true)."Source";

    // Se incluye satisfactoriamente el driver
    self::requireFile("drivers/{$driverClassName}.class");

    // Se retorna en nombre de la clase
    return $driverClassName;

  }

  // Incluye un validator y devuelve el nombre de la clases correspondiente
  public static function validator($validator){

    // Obtener el nombre de la clase
    $validatorClassName = AmORM::camelCase($validator, true)."Validator";

    // Si se incluye satisfactoriamente el validator
    self::requireFile("validators/{$validatorClassName}.class");

    // Se retorna en nombre de la clase
    return $validatorClassName;

  }

  // Devuelve la configuracion de una determinada fuente de datos
  public static function getSourceConf($sourceName = "default"){

    // Obtener configuraciones para las fuentes
    $sources = Am::getAttribute("sources", array());

    // Si no existe una configuración para el nombre de fuente
    if(!isset($sources[$sourceName]))
      return null;

    // Asignar valores por defecto
    return array_merge(
      array(
        "name"      => $sourceName,
        "database"  => $sourceName,
        "driver"    => null,
      ),
      $sources[$sourceName]
    );

  }

  // Devuelve una instancia de una fuente
  public static function source($name = "default"){

    // Obtener la instancia si ya existe
    if(isset(self::$sources[$name]))
      return self::$sources[$name];

    // Obtener la configuración de la fuente
    $sourceConf = self::getSourceConf($name);

    // Si no existe una configuración para el nombre de fuente
    // solicitado se retorna NULL
    if($sourceConf === null)
      die("Am: No se encontró la configuración para la fuente '{$name}'");

    // Obtener el driver de la fuente
    $driverClassName = AmORM::driver($sourceConf["driver"]);

    // Crear instancia de la fuente
    $source = new $driverClassName($sourceConf);
    $source->connect(); // Conectar la fuente

    return self::$sources[$name] = $source;

  }

  // Devuelve la instancia de una tabla en una fuente determinada
  public static function table($tableName, $source = "default"){

    // Obtener la instancia de la fuente
    $source = self::source($source);

    // Si ya ha sido instanciada la tabla
    // entonces se devuelve la instancia
    if($source->hasTableInstance($tableName))
      return $source->getTable($tableName);

    // Instancia la clase
    $table = new AmTable(array(
      "source" => $source,
      "tableName" => $tableName
    ));

    // Incluir modelo
    self::requireFile($table->getPathClassModelBase(), false);  // Clase base para el modelo

    // Asignar tabla
    $source->setTable($tableName, $table);

    return $table;

  }

  public static function model($model){

    // Si es un modelo nativo
    if(preg_match("/^:(.*)@(.*)$/", $model, $m) || preg_match("/^:(.*)$/", $model, $m)){

      // Si no se indica la fuente tomar la fuente por defecto
      if(empty($m[2]))
        $m[2] = "default";

      // Incluir modelo y  obtener la tabla
      $table = self::table($m[1], $m[2]);

      // Retornar el nombre de la clase del modelo correspondiente
      return $table->getClassNameModelBase();

    }

    // Obtener configuraciones de mails
    $models = Am::getAttribute("models");

    // Si se recibió un string asignar como nombre del modelo
    if(is_string($model))
      $model = array("name" => $model);

    // Si no se recibió el nombre del modelo retornar falso
    if(!isset($model["name"]))
      return false;

    // Configuración de valores po defecto
    $defaults = itemOr("defaults", $models, array());
    if(is_string($defaults))
      $defaults = array("root" => $defaults);

    // Configuración de valores del model
    $modelConf = itemOr($model["name"], $models, array());
    if(!is_string($defaults))
      $modelConf = array("root" => $modelConf);

    // Combinar opciones recibidas en el constructor con las
    // establecidas en el archivo de configuracion
    $model = array_merge($defaults, $modelConf, $model);

    // Si ya fue incluido el model salir
    if(in_array($model["name"], self::$includedModels))
      return $model["name"];
    else
      // Incluir como modelo de usuario
      // Guardar el nombre del modelo dentro de los modelos incluidos
      // para no generar bucles infinitos
      self::$includedModels[] = $model["name"];

    // Incluir de configuracion local del modelo
    if(is_file($modelConfFile = $model["root"] . ".model.php")){
      $modelConf = require_once($modelConfFile);
      $model = array_merge($model, $modelConf);
    }

    // Incluir modelos requeridos por el modelo actual
    foreach($model["models"] as $require)
      self::model($require);

    // Incluir archivo del modelo
    if(is_file($modelFile = $model["root"] . $model["name"] . ".model.php"))
      require_once($modelFile);

    // Retornar el nombre de la clase
    return $model["name"];

  }

  ///////////////////////////////////////////////////////////////////////////////////
  // UTILIDADES
  ///////////////////////////////////////////////////////////////////////////////////

  public static function isNameValid($string){
    return preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $string) != 0;
  }

  // Devuelve la cadena "s" convertida en formato under_score
  public static function underscor($s) {

    // Primer caracter en miniscula
    if(!empty($s)){
      $s[0] = strtolower($s[0]);
    }

    // Crear funcion para convertir en minuscula
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');

    // Operar
    return preg_replace_callback("/([A-Z])/", $func, str_replace(" ", "_", $s));

  }

  // Devuelve una cadena "s" en formato camelCase. Si "cfc == true" entonces
  // el primer caracter tambien es convertido en mayusculas
  public static function camelCase($s, $cfc = false){

    // Primer caracter en mayuscula o en miniscula
    if(!empty($s)){
      if($cfc){
        $s[0] = strtoupper($s[0]);
      }else{
        $s[0] = strtolower($s[0]);
      }
    }

    // Funcion para convertir cada caracter en miniscula
    $func = create_function('$c', 'return strtoupper($c[1]);');

    // Operar
    return preg_replace_callback("/_([a-z])/", $func, $s);

  }

}

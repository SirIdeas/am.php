<?php

/**
 * ORM Basico de Amathista
 */

final class AmORM{

  protected static
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

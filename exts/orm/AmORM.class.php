<?php

/**
 * ORM Basico de Amathista
 */

final class AmORM{

  protected static
    $sources = array();

  // Incluye un archivo dentro buscado dentro de la
  // carpeta de la libreria
  public static function requireFile($file){
    Am::requireFile(dirname(__FILE__). "/{$file}");
  }

  // Incluye un driver de BD
  public static function driver($driver){

    // Obtener el nombre de la clase
    $driverClassName = Am::camelCase($driver, true)."Source";

    // Se incluye satisfactoriamente el driver
    self::requireFile("drivers/{$driverClassName}.class");

    // Se retorna en nombre de la clase
    return $driverClassName;

  }

  // Incluye un validator y devuelve el nombre de la clases correspondiente
  public static function validator($validator){

    // Obtener el nombre de la clase
    $validatorClassName = Am::camelCase($validator, true)."Validator";
    
    // Si se incluye satisfactoriamente el validator
    self::requireFile("validators/{$validatorClassName}.class");

    // Se retorna en nombre de la clase
    return $validatorClassName;

  }

  // Devuelve la configuracion de una determinada fuente de datos
  public static function getSourceConf($sourceName = "default"){

    // Obtener configuraciones para las fuentes
    $sources = Am::getAttribute("sources");

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
  public static function table($table, $source = "default"){
    
    // Obtener la instancia de la fuente
    $source = self::source($source);

    // Si ya ha sido instanciada la tabla
    // entonces se devuelve la instancia
    if($source->hasTableInstance($table))
      return $source->getTable($table);

    // Incluir Modelo de la tabla
    Am::requireFile($source->getPathClassTableBase($table));  // Clase Base para la tabla
    Am::requireFile($source->getPathClassTable($table));      // Clase para la Tabla
    Am::requireFile($source->getPathClassModelBase($table));  // Clase base para el modelo
    Am::requireFile($source->getPathClassModel($table));      // Clase para el model

    // Obtener el nombre de la tabla
    $tableClassName = $source->getClassNameTable($table);

    // Instancia la clase
    $instance = new $tableClassName();

    $source->setTable($table, $instance);

    return $instance;

  }

}
    
// Incluir Nucleo del ORM
AmORM::requireFile("AmField.class");
AmORM::requireFile("AmTable.class");
AmORM::requireFile("AmModel.class");
AmORM::requireFile("AmRelation.class");
AmORM::requireFile("AmQuery.class");
AmORM::requireFile("AmSource.class");
AmORM::requireFile("AmValidator.class");
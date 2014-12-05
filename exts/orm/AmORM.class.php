<?php

/**
 * ORM Basico de Amathista
 */

final class AmORM{

  protected static
    $instances = array();

  // Incluye un archivo dentro buscado dentro de la
  // carpeta de la libreria
  public static function requireFile($file){
    return Am::requireFile(dirname(__FILE__). "/{$file}");
  }

  // Incluye un driver de BD
  public static function requireDriver($driver){

    // Obtener el nombre de la clase
    $driverClassName = Am::camelCase($driver)."Source";

    // Si se incluye satisfactoriamente el driver
    if(self::requireFile("drivers/{$driverClassName}.class"))
      // Se retorna en nombre de la clase
      return $driverClassName;

    // De lo contrario se retorna null
    return null;

  }

  // Incluye un validator y devuelve el nombre de la clases correspondiente
  public static function requireValidator($validator){

    // Obtener el nombre de la clase
    $validatorClassName = Am::camelCase($validator)."Validator";

    // Si se incluye satisfactoriamente el validator
    if(self::requireFile("validators/{$validatorClassName}.class"))
      // Se retorna en nombre de la clase
      return $validatorClassName;

    // De lo contrario se retorna null
    return null;

  }

  // Devuelve una instancia de una fuente
  public static function getSource($name = "default"){

    // Obtener la instancia si ya existe
    if(isset(self::$instances[$name]))
      return self::$instances[$name];

    // Obtener configuraciones para las fuentes
    $sources = Am::getAttribute("sources");

    // Si no existe una configuración para el nombre de fuente
    // solicitado se retorna NULL
    if(!isset($sources[$name]))
      die("Am: No se encontró la configuración para la fuente '{$name}'");

    // Asignar valores por defecto
    $sources[$name] = array_merge(
      array(
        "name"      => $name,
        "database"  => $name,
        "driver"    => null,
      ),
      $sources[$name]
    );

    // Obtener el driver de la fuente 
    $driverClassName = $sources[$name]["driver"]."Source";
    AmORM::requireDriver($sources[$name]["driver"]);

    // Crear instancia de la fuente
    $source = new $driverClassName($sources[$name]);
    $source->connect(); // Conectar la fuente

    return self::$instances[$name] = $source;

  }

  // Devuelve la instancia de de una tabla en una fuente determinada
  public static function getTable($table, $source = "default"){
    
    // Obtener la instancia de la fuente
    $source = self::getSource($source);

    

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

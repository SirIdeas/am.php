<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(
  
  /**
   * Directorios de clases.
   */
  'autoload' => array(
    'validators' => false,
    'drivers' => false,
    'relations' => false,
    'clauses' => false,
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(

    // Agregar directorios donde buscar clases
    'autoload' => array(
      'schemes' => true,
      'models' => true,
    ),

    // Configuraciones de los esquemas
    'schemes' => array(),

    // Directorios generales
    'dirs' => array(
      'schemes' => 'schemes',
    ),

    // Configuraciones de los validadores.
    'validators' => array(
      'messages' => array(
        'bit' => 'Formato de bit no coincide',
        'unique' => 'Ya existe un registro con [fieldname] == "[value]"'
      )
    ),

    // Formatos
    'formats' => array(
      'AMSCHEME_EMPTY_ALIAS' => 'AmScheme: Alias no puede ser vacío en el campo "%s"',
      'AMSCHEME_INVALID_ALIAS' => 'AmScheme: Alias de objeto inválido "%s"',
      'AMSCHEME_INVALID_FIELD' => 'AmScheme: Campo Inválido %s',
      'AMSCHEME_INVALID_NAME' => 'AmScheme: Nombre de objeto inválido "%s"',
      'AMSCHEME_QUERY_TYPE_UNKNOW' => 'AmScheme: Tipo de consulta indefinida "%s"',
      'AMSCHEME_SCHEMECONF_NOT_FOUND' => 'AmScheme: No se encontró la configuración para la fuente "%s"',
      'AMSCHEMA_TABLE_ALREADY_HAVE_A_FIELD_NAMED' => 'AmScheme: La tabla "%s" ya tiene un campo llamado "%s"',
      'AMSCHEME_MODEL_CALL_MARK_AS_UPDATED' => 'AmScheme: Model metodo markAsUpdated solo puede ser llamada por la tabla del modelo',
      'AMSCHEME_MODEL_DONT_HAVE_PK' => 'AmScheme: Model "%s" no tiene primary key',
      'AMSCHEME_MODEL_NOT_EXISTS' => 'AmScheme: Model "%s" no existe',
      'AMSCHEME_HAS_MANY_AND_BELONG_TO_FOREIGN_DIFFERENT_SCHEMES' => 'AmScheme: Relación hasManyAndBelontTo "%s" definida entre diferentes esquemas: "%s" pertenece al esquema "%s" y "%s" pertenece al esquema "%s"',
      'AMSCHEME_RELATION_NOT_EXISTS' => 'AmScheme: Relación %s@%s no existe',
      'AMSCHEME_RELATION_SET_MUST_RECIVED_AMMODEL' => 'AmScheme: Asignación a relación debe recibir una instancia de %s',
      'AMSCHEME_FIELD_INVALID' => 'AmScheme: Campo "%s" inválido en cláusula %s',
      'AMSCHEME_INT_INVALID' => 'AmScheme: Valor "%s" para cláusula %s debe ser un entero',
      'AMSCHEME_QUERY_REPEATED_NOT' => 'AmScheme: Operador NOT repetido',
      'AMSCHEME_QUERY_INVALID_IS' => 'AmScheme: Asignación de operador IS inválida',
      'AMSCHEME_QUERY_INVALID_IN' => 'AmScheme: Asignación de operador IN inválida',
      'AMSCHEME_QUERY_INVALID_IN_COLLECTION' => 'AmScheme: Colección para operador IN inválida: "%s"',
      'AMSCHEME_QUERY_UNKWON_OPERATOR' => 'AmScheme: Operador "%s" desconocido',
      'AMSCHEME_QUERY_INVALID_CONDITION' => 'AmScheme: Condición inválida: "%s"',
      'AMSCHEME_QUERY_INVALID_IN_ARGS_NUMBERS' => 'AmScheme: Número de argumentos de condición IN inválida: "%s"',
      'AMSCHEME_QUERY_IN_FIRST_PARAM_MUST_BE_STRING' => 'AmScheme: El primer parámetro de la condición IN debe ser una cadena de caracteres',
      'AMSCHEME_QUERY_IN_SECOND_PARAM_MUST_BE_COLLECION' => 'AmScheme: El segundo parámetro de la condición IN debe ser una coleción',
      'AMSCHEME_QUERY_BOOLEAN_OPERATOR_CONSECITIVE' => 'AmScheme: Operadores booleanos consecutivos'
      'AMSCHEME_RELATION_SET_MUST_RECIVED_AMMODEL' => 'AmScheme: Asignación a relación debe recibir una instancia de %s'
    )
  ),
  
  /**
   * Archivos de la extensión
   */
  'requires' => array(
    'exts/am_coder/',
  ),

  /**
   * Métodos de mezcla de configuación
   */
  'mergeFunctions' => array(
    'models' => 'array_merge_recursive',
    'schemes' => 'array_merge_recursive',
    'validators' => 'merge_if_both_are_array',
  )
  
);
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

    // Directorios de los modelos.
    'models' => array(
    ),

    // Configuraciones de los esquemas
    'schemes' => array(),

    // Directorios generales
    'dirs' => array(
      'models' => 'models',
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
      'AMSCHEME_EMPTY_ALIAS' => 'AmScheme: Alias no puede ser vacío "%s"',
      'AMSCHEME_INVALID_ALIAS' => 'AmScheme: Alias de objeto inválido "%s"',
      'AMSCHEME_INVALID_NAME' => 'AmScheme: Nombre de objeto inválido "%s"',
      'AMSCHEME_QUERY_TYPE_UNKNOW' => 'AmScheme: Tipo de consulta indefinida "%s"',
      'AMSCHEME_SCHEMECONF_NOT_FOUND' => 'AmScheme: No se encontró la configuración para la fuente "%s"',
      'AMSCHEMA_TABLE_ALREADY_HAVE_A_FIELD_NAMED' => 'AmScheme: La tabla "%s" ya tiene un campo llamado "%s"',
      'AMSCHEME_MODEL_CALL_MARK_AS_UPDATED' => 'AmScheme: Model metodo markAsUpdated solo puede ser llamada por la tabla del modelo',
      'AMSCHEME_MODEL_DONT_HAVE_PK' => 'AmScheme: Model "%s" no tiene primary key',
      'AMSCHEME_MODEL_NOT_EXISTS' => 'AmScheme: Model "%s" no existe',
      'AMSCHEME_FOREIGN_ALREADY_EXISTS' => 'AmScheme: Relación "%s@%s" ya existe existe',
      'AMSCHEME_HAS_MANY_AND_BELONG_TO_FOREIGN_DIFFERENT_SCHEMES' => 'AmScheme: Relación hasManyAndBelontTo "%s" definida entre diferentes esquemas: "%s" pertenece al esquema "%s" y "%s" pertenece al esquema "%s"',
      'AMSCHEME_HAS_MANY_AND_BELONG_TO_FOREIGN_SELECT_PARAM_MAY_BE_ARRAY' => 'AmScheme: Parámetro select de la relación hasManyAndBelontTo "%s"@"%s"debe ser un array',
      'AMSCHEME_RELATION_NOT_EXISTS' => 'AmScheme: Relación %s@%s no existe',
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
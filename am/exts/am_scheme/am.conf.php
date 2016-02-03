<?php

return array(
  
  /**
   * ---------------------------------------------------------------------------
   * Archivos de la extensión
   * ---------------------------------------------------------------------------
   */
  'files' => array(
    'AmField.class',
    'AmRelation.class',
    'AmScheme.class',
    'AmQuery.class',
    'AmTable.class',
    'AmModel.class',
    'AmValidator.class',
    'drivers/MysqlScheme.class',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuración a extender
   * ---------------------------------------------------------------------------
   */
  'extend' => array(
    'models' => array(
      '' => array(
        'models' => array()
      )
    ),

    // Formatos
    'formats' => array(
      'AMSCHEME_QUERY_TYPE_UNKNOW' => 'AmScheme: Tipo de consulta indefinida "%s"',
      'AMSCHEME_FILE_NOT_FOUND' => 'AmScheme: No se encontró el archivo "%s"',
      'AMSCHEME_SCHEMECONF_NOT_FOUND' => 'AmScheme: No se encontró la configuración para la fuente "%s"',
    )
  ),
  
  /**
   * ---------------------------------------------------------------------------
   * Archivos de la extensión
   * ---------------------------------------------------------------------------
   */
  'requires' => array(
    'exts/am_coder/',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Métodos de mezcla de configuación
   * ---------------------------------------------------------------------------
   */
  'mergeFunctions' => array(
    'models' => 'array_merge_recursive',
    'schemes' => 'array_merge_recursive',
    'validators' => 'array_merge_recursive',
  )
  
);
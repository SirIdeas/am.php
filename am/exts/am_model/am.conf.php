<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(
  
  /**
   * ---------------------------------------------------------------------------
   * Archivos de la extensión
   * ---------------------------------------------------------------------------
   */
  'files' => array(
    'AmORM.class',
    'AmField.class',
    'AmTable.class',
    'AmModel.class',
    'AmRelation.class',
    'AmQuery.class',
    'AmSource.class',
    'AmValidator.class',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuración a extender
   * ---------------------------------------------------------------------------
   */
  'extend' => array(
    'models' => array(
      'defaults' => array(
        'root' => 'model/',
        'models' => array()
      )
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
    'sources' => 'array_merge_recursive',
    'validators' => 'array_merge_recursive',
  )
);

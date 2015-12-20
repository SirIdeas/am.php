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
    'AmController.class'
  ),

  /**
   * ---------------------------------------------------------------------------
   * Métodos de mezcla de configuación
   * ---------------------------------------------------------------------------
   */
  'mergeFunctions' => array(
    'controllers' => 'array_merge_recursive',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Extensiones requeridas
   * ---------------------------------------------------------------------------
   */
  'requires' => array(
    'exts/am_response/',
    // 'exts/am_credentials/',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuración a extender
   * ---------------------------------------------------------------------------
   */
  'extend' => array(

    // Configuración inicial de los controladores
    'controllers' => array(
      'defaults' => array(
        'name' => 'Am',
        'views' => 'views',  // Carpeta por defecto para las vistas
        'paths' => array(    // Carpetas de vistas del controlador
          realPath('../am/exts')
        ),
      ),
    ),

    // Formatos
    'formats' => array(
      'AMCONTROLLER_ACTION_NOT_FOUND' => 'AmController: No se encontró la acción "%s@%s"',
      'AMCONTROLLER_ACTION_FORBIDDEN' => 'AmController: Acción prohibida "%s@%s"'
    )

  ),

);

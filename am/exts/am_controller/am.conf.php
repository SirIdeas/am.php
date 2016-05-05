<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
return array(

  /**
   * Métodos de mezcla de configuación
   */
  'mergeFunctions' => array(
    'controllers' => 'merge_if_both_are_array',
  ),

  /**
   * Extensiones requeridas
   */
  'requires' => array(
    'exts/am_response/',
    'exts/am_credentials/',
  ),

  /**
   * Eventos a enlazar
   */
  'bind' => array(
    // Atender las respuestas por controlador
    'response.controller' => 'AmController::response',
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(

    // Agregar directorios donde buscar clases
    'autoload' => array(
      'controllers' => true,
    ),
    
    // Formatos
    'formats' => array(
      'AMCONTROLLER_CLASS_IS_NOT_A_CONTROLLER' => 'AmController: "%s" no es un controlador',
      'AMCONTROLLER_ACTION_NOT_FOUND' => 'AmController: No se encontró la acción "%s@%s"',
      'AMCONTROLLER_ACTION_FORBIDDEN' => 'AmController: Acción prohibida "%s@%s por el método %s"'
    )

  ),

);

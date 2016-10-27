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
    'routing' => 'array_merge_recursive',
  ),

  /**
   * Extensiones requeridas
   */
  'requires' => array(
    'exts/am_response',
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(
    'routing' => array(),

    // Formatos
    'formats' => array(
      'AMROUTE_ACTION_NOT_FOUND' => 'AmRoute: no se encontró la acción %s',
      'AMROUTE_BAD_ROUTE' => 'AmRoute: Mal definición de rutas %s (%s)',
      'AMROUTE_NOT_MATCH' => 'AmRoute: No se encontró la ruta',
      'AMROUTE_NOT_FOUND_DISPATCHER' => 'AmRoute: No se encontró despachador %s : %s',
    )

  ),

);

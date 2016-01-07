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
    'controllers' => 'merge_if_both_are_array',
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
      
        // ---------------------------------------------------------------------
        // Para todo el controlador.
        // ---------------------------------------------------------------------

        // Nombre del controlador.
        'name' => 'Am',

        // Nombre del controlador padre.
        'parent' => null,
        
        // Carpeta raíz del controlador.
        'root' => 'controllers',

        // Directorio hijo donde se buscará las vistas.
        'views' => 'views',

        // Directorios alternativos donde se buscará las vistas.
        'paths' => array(),

        // Prefijos.
        'prefixs' => array(
          'filters' => 'filter_',
          'actions' => 'action_',
          'getActions' => 'get_',
          'postActions' => 'post_',
        ),

        // Acciones permitidas.
        'allows' => array(),

        // Tipo de respuesta para el servicio: json, txt.
        'serviceType' => 'json',

        // Filtros.
        'filters' => array(),

        // ---------------------------------------------------------------------
        // Solo para la petición actual
        // ---------------------------------------------------------------------
        
        // Acción a ejecutar.
        'action' => null,

        // Parémetros para ejecutar la acción.
        'params' => array(),

        // Nombre de la vista a renderizar.
        'view' => null,

      ),
    ),

    // Formatos
    'formats' => array(
      'AMCONTROLLER_ACTION_NOT_FOUND' => 'AmController: No se encontró la acción "%s@%s"',
      'AMCONTROLLER_ACTION_FORBIDDEN' => 'AmController: Acción prohibida "%s@%s"'
    )

  ),

);

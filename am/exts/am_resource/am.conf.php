<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(

  /**
   * Extensiones requeridas
   */
  'requires' => array(
    'exts/am_controller/',
    'exts/am_scheme/',
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(

    // Configuración inicial de los controladores
    'controllers' => array(
      'AmResource' => array(
      
        // Directorio hijo donde se buscará las vistas.
        'views' => 'views',

        // Prefijos.
        'prefixs' => array(
          'actions'     => 'action_',
          'getActions'  => 'get_',
          'postActions' => 'post_',
          'filters'     => 'filter_',
        ),

        // Acciones permitidas
        'allows' => array(
          'list'    => true,
          'new'     => true,
          'detail'  => true,
          'edit'    => true,
          'delete'  => true,
          'cou'     => true,
        ),

        // Filtros
        'filters' => array(
          'before' => array(
            'loadRecord' => array('detail', 'edit', 'delete')
          )
        ),

      ),
    ),

  ),

);

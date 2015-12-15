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
    'AmTpl.class'
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuración a extender
   * ---------------------------------------------------------------------------
   */
  'extend' => array(
    'formats' => array(
      'AMTPL_VIEW_NOT_FOUND' => 'AmTpl: Método no encontrado AmTpl::"%s"',
      'AMTPL_SET_BAD_ARGS_NUMBER' => 'AmTpl::set(): Número de argumentos inválido',
      'AMTPL_SUBVIEW_NOT_FOUND' => 'AmTpl: Sub vista "%s" en "%s" no encontrada',
      'AMTPL_UNOPENED_SECTION' => 'AmTpl: closing section unopened'
    ),
  ),

);

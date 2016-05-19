<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
return array(

  /**
   * Enlaces de eventos
   */
  'bind' => array(
    // Atender el llamado a renderiza vistas
    'render.template' => 'AmTpl::renderize',
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(
    'formats' => array(
      'AMTPL_VIEW_NOT_FOUND' => 'AmTpl: No se econtró la vista "%s"',
      'AMTPL_METHOD_NOT_FOUND' => 'AmTpl: Método no encontrado AmTpl::"%s"',
      'AMTPL_SET_BAD_ARGS_NUMBER' => 'AmTpl::set(): Número de argumentos inválido',
      'AMTPL_SUBVIEW_NOT_FOUND' => 'AmTpl: Subvista "%s" en "%s" no encontrada',
      'AMTPL_UNOPENED_SECTION' => 'AmTpl: No se ha abierto ninguna sección',
      'AMTPL_SECTION_CREATE_INTO_OTHER_SECTION' => 'AmTpl: No se pueden crear secciones dentro de otras secciones',
    ),
  ),
  
  'requires' => array(
    'exts/am_csrfguard/',
  ),


);

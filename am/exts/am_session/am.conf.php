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
    'session.get' => 'AmSession::get',
  ),

  /**
   * Configuración a extender
   */
  'extend' => array(

    /**
     * Extensión que manejará las sessiones
     */
    'session' => 'AmNormalSession',

    'aliases' => array(

      'AmNormalSession' => 'exts/am_normal_session'

    ),

  )
);

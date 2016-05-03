<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(
  
  'requires' => array(
    'exts/am_session/',
  ),

  /**
   * Eventos a enlazar
   */
  'bind' => array(
    // Despachar rutas.
    'credentials.get' => 'AmCredentialsHandler::get',

  ),

  'mergeFunctions' => array(
    'credentials' => 'merge_r_if_are_array_and_snd_first_not_false',
  ),

);

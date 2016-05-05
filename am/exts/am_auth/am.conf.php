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
    'exts/am_credentials/',
    'exts/am_token/',
  ),

);

// return array(

//   'extend' => array(
//     'control' => array(
//       'AmAuth' => dirname(__FILE__) . '/',
//     ),
//     'mails' => array(
//       'amAuth_recovery' => array(
//         'dir' => dirname(__FILE__) . '/mails/',
//         'charset' =>  'utf-8',
//         'subject' => 'Recuperar contraseña',
//       )
//     )
//   ),

//   'requires' => array(
//     'exts/am_flash/',
//     'exts/am_mailer/',
//   ),

// );

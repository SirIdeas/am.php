<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(
  
  'autoload' => array(
    'php_mailer/PHPMailerAutoload.php' => true,
  ),

  'mergeFunctions' => array(
    'smtp' => 'merge_r_if_are_array',
    'mails' => 'merge_if_both_are_array',
  ),

  'extend' => array(

    // Configuraciones de los esquemas
    'mails' => array(
      '' => array(
        'smtp' => false,
        'a' => 1,
      ),
    ),

    // Configuraciones de los esquemas
    'smtp' => array(
      '' => array(),
    ),

  ),

);
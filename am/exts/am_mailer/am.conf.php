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
    'mails' => 'array_merge_recursive',
  ),

  'extend' => array(

    // Configuraciones de los esquemas
    'mails' => array(
      '' => array(
        'smtp' => false,
      ),
    ),

    // Configuraciones de los esquemas
    'smtp' => array(
      '' => array(),
    ),

  ),

);
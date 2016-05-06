<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(

  'mergeFunctions' => array(
    'ssl' => 'merge_r_if_are_array',
  ),

  'extend' => array(

    // Configuraciones de los esquemas
    'ssl' => array(
      '' => array(
        'keyPassPhrase' => null,
        'keyPublic' => null,
        'keyPrivate' => null,
        'keyPublicFile' => null,
        'keyPrivateFile' => null,
      ),
    ),

  ),

);
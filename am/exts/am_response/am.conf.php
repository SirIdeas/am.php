<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(

  /**
   * Directorios de clases.
   */
  'autoload' => array(
    'types' => false,
  ),

  /**
   * Configuración a extender.
   */
  'extend' => array(

    // Formatos
    'formats' => array(
      'AMRESPONSE_TEMPLATE_NOT_FOUND' => 'AmResponse: No se encontró el template "%s"',
      'AMRESPONSE_FILE_NOT_FOUND' => 'AmResponse: No se encontró el archivo "%s"',
      'AMRESPONSE_CALLBACK_NOT_FOUND' => 'AmResponse: No se encontró el callback "%s"',
    )

  ),

);

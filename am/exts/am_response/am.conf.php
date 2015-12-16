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
    'AmResponse.class',
    'types/AmFileResponse.class',
    'types/AmCallResponse.class',
    'types/AmRedirectResponse.class',
    'types/AmTemplateResponse.class',
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuración a extender
   * ---------------------------------------------------------------------------
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

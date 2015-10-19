<?php
/**
 * Amathista - PHP Framework
 *
 * @author   Alex J. Rondón <arondn2@gmail.com>
 */

/**
 * -----------------------------------------------------------------------------
 * Inclusión de núcleo de Amathista.
 * -----------------------------------------------------------------------------
 * Inclusión del núcleo de Amathista. Esto incluye la clase principal Am,
 * helpers escenciales, manejo de errores personalizado.
 * 
 */
include '../am/start.php';

/**
 * -----------------------------------------------------------------------------
 * Inicialización de la aplicación.
 * -----------------------------------------------------------------------------
 * Para inicializar la aplicación debe indicar la carpeta donde esta ubicada
 * en el filesystem, ya sea relativa o absolutamente. Desde esta ruta se
 * ejecutará toda la aplicación.
 * 
 */
Am::app('../app');

/**
 * -----------------------------------------------------------------------------
 * Despachar la petición con la aplicación
 * -----------------------------------------------------------------------------
 * Si se esta ejecutando Amathista desde la línea de comandos, entonces se
 * entrará el interprete PHP con todas las dependecias de la aplicación
 * configurada, de lo contrario se despachará como una petición HTTP.
 * 
 */
Am::run();

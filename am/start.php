<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Semilla para números aleatorios.
 * -----------------------------------------------------------------------------
 * Se inicializa la semilla para números aleatorios.
 */
mt_srand((double)microtime() * 1000000);

/**
 * -----------------------------------------------------------------------------
 * Inicio de ejecución del framework
 * -----------------------------------------------------------------------------
 */
// Si está definido se debe generar un error.
if(defined('AM_START'))
  throw new Exception('Am: ya existe una constante "AM_START" definida');

// Definir constante
define('AM_START', date('YmdHis'));

/**
 * -----------------------------------------------------------------------------
 * Carpeta raíz de Amathista framework
 * -----------------------------------------------------------------------------
 * Define la versión del framework si no esta definida.
 */
// Si está definido se debe generar un error.
if(defined('AM_ROOT'))
  throw new Exception('Am: ya existe una constante "AM_ROOT" definida');

// Definir constante
define('AM_ROOT', dirname(__FILE__));

/**
 * -----------------------------------------------------------------------------
 * Obtener el bootfile de la aplicación
 * -----------------------------------------------------------------------------
 * Define el archivo que inicializa Amathista
 */
// Si está definido se debe generar un error.
if(defined('AM_BOOTFILE'))
  throw new Exception('Am: ya existe una constante "AM_BOOTFILE" definida');

// Definir constante
define('AM_BOOTFILE', realpath($_SERVER['SCRIPT_FILENAME']));

/**
 * -----------------------------------------------------------------------------
 * Versión del framework.
 * -----------------------------------------------------------------------------
 * Define la versión del framework si no esta definida.
 */
@define('AM_VERSION', '0.1.0');

/**
 * -----------------------------------------------------------------------------
 * Inclusión del núcleo.
 * -----------------------------------------------------------------------------
 * Se incluye los archivos que forman parte del núcleo del framework.
 * 
 */

require dirname(__FILE__) . '/core/am-helpers.php';
require dirname(__FILE__) . '/core/Am.class.php';
require dirname(__FILE__) . '/core/AmObject.class.php';

/**
 * -----------------------------------------------------------------------------
 * Ubicación del Framework.
 * -----------------------------------------------------------------------------
 * La primera ruta a agregar es la ruta donde se ubica el fuente del framework.
 * Esta es la última ruta donde se buscarán extensiones.
 * 
 */

Am::addDir(dirname(__FILE__) . '/');
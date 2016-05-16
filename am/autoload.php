<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Semilla para números aleatorios.
 * Se inicializa la semilla para números aleatorios.
 */
mt_srand((double)microtime() * 1000000);

/**
 * Inicio de ejecución del framework
 */
// Si está definido se debe generar un error.
if(defined('AM_START'))
  throw new Exception('Am: ya existe una constante "AM_START" definida');

// Definir constante
define('AM_START', date('YmdHis'));

/**
 * Carpeta raíz de Amathista framework
 * Define la versión del framework si no esta definida.
 */
// Si está definido se debe generar un error.
if(defined('AM_ROOT'))
  throw new Exception('Am: ya existe una constante "AM_ROOT" definida');

// Definir constante
define('AM_ROOT', dirname(__FILE__));

/**
 * Obtener el bootfile de la aplicación
 * Define el archivo que inicializa Amathista
 */
// Si está definido se debe generar un error.
if(defined('AM_BOOTFILE'))
  throw new Exception('Am: ya existe una constante "AM_BOOTFILE" definida');

// Definir constante
define('AM_BOOTFILE', realpath($_SERVER['SCRIPT_FILENAME']));

/**
 * Obtener el bootdir de la aplicación
 * Define el directorio raíz donde inicializa Amathista
 */
// Si está definido se debe generar un error.
if(defined('AM_BOOTDIR'))
  throw new Exception('Am: ya existe una constante "AM_BOOTDIR" definida');

// Definir constante
define('AM_BOOTDIR', dirname(AM_BOOTFILE));

// Define el directorio de la papelera
if(!defined('AM_STORAGE'))
  define('AM_STORAGE', '../storage');

// Definir directorio de la papelera
if(!defined('AM_TRASH'))
  define('AM_TRASH', AM_STORAGE . '/.trash');

/**
 * Versión del framework.
 * Define la versión del framework si no esta definida.
 */
define('AM_VERSION', '0.1.0');

/**
 * Inclusión del núcleo.
 * Se incluye los archivos que forman parte del núcleo del framework.
 */
require AM_ROOT . '/core/am-helpers.php';
require AM_ROOT . '/core/Am.class.php';

// Asignar función para cargar clases.
spl_autoload_register('Am::autoload');

/**
 * Inicializa Amathista.
 */
Am::start();

/**
 * Ubicación del Framework.
 * La primera ruta a agregar es la ruta donde se ubica el fuente del framework.
 * Esta es la última ruta donde se buscarán extensiones.
 * 
 */
Am::addDir(AM_ROOT . '/');
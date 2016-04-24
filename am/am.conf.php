<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Configuración principal
 */
return array(

  /**
   * Identificador único de la aplicación.
   */
  'id' => 'am',

  /**
   * Configuración del errorReporting.
   */
  'errorReporting' => E_ALL ^ E_DEPRECATED,

  /**
   * Hash con alias de extensiones
   */
  'consts' => array(),

  /**
   * Variables de entorno.
   */
  'env' => array(),

  /**
   * Array de las rutas o aliases de las extensiones a cargar.
   * Las rutas pueden ser relativas o absolutas. Puede indicar directamente el
   * archivo que se desea incluir (sin la extensión php) o la carpeta donde se
   * encuentra la extensión. Serán buscado en la raíz de la aplicación y de no
   * encontrarse se seguirá buscando en los directorios del entorno desde el
   * mas reciente hasta el más antiguo.
   */
  'requires' => array(),

  /**
   * Hash con alias de extensiones
   */
  'aliases' => array(),

  /**
   * Directorios para la auto carga.
   */
  'autoload' => array(),

  /**
   * Enlaces de eventos.
   */
  'bind' => array(),

  /**
   * Formatos
   */
  'formats' => array(
    'AM_NOT_FOUND' => 'Am: Not Found',
    'AM_CLASS_NOT_FOUND' => 'Am: Class "%s" Not Found',
    'AM_NOT_FOUND_EXT' => 'Am: No se encontró la extensión "%s"',
    'AM_NOT_FOUND_COMMAND' => 'Am: No se encontró el comando %s',
    'AM_NOT_FOUND_VIEW' => 'Am: No existe view "%s"',
    'AM_DIR_IS_FILE' => 'Am: Se espera que "%s" sea un directorio pero es un archivo',
    'AM_DIR_NOT_IS_WRITABLE' => 'Am: No se pude escribir en "%s"',
    'AMOBJECT_CANNOT_ACCESS_PROPERTY' => 'Am: No puede acceder al atributo protegido/privado %s::$%s',
  ),

  /**
   * Configuraciones de las tareas (Ver ./tasks.conf.php).
   */
  'tasks' => array(
    'copy' => array(
      'args' => array(
        'origin'   => '-o',
        'destiny'  => '-d',
        'files'    => '-f',
        'rewrite'  => '-r:bool:false',
        'vervose'  => '-r:bool:false'
      )
    )
  )

);
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
/**
 * -----------------------------------------------------------------------------
 * Configuración principal
 * -----------------------------------------------------------------------------
 */
return array(

  /**
   * ---------------------------------------------------------------------------
   * Configuración del errorReporting
   * ---------------------------------------------------------------------------
   */
  'errorReporting' => E_ALL ^ E_DEPRECATED,

  /**
   * ---------------------------------------------------------------------------
   * Array de las rutas o aliases de las extensiones a cargar.
   * ---------------------------------------------------------------------------
   * Las rutas pueden ser relativas o absolutas. Puede indicar directamente el
   * archivo que se desea incluir (sin la extensión php) o la carpeta donde se
   * encuentra la extensión. Serán buscado en la raíz de la aplicación y de no
   * encontrarse se seguirá buscando en los directorios del entorno desde el
   * mas reciente hasta el más antiguo.
   */
  'requires' => array(),

  /**
   * ---------------------------------------------------------------------------
   * Configuración de las rutas (Ver ./routes.conf.php)
   * ---------------------------------------------------------------------------
   */
  'routing' => array(
    'routes' => array()
  ),

  /**
   * ---------------------------------------------------------------------------
   * Configuraciones de las tareas (Ver ./tasks.conf.php)
   * ---------------------------------------------------------------------------
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
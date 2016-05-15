<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(

  // Directorio hijo donde se buscará las vistas.
  'views' => 'views',

  // Directorios alternativos donde se buscará las vistas.
  'paths' => array(),

  // Prefijos.
  'prefixs' => array(
    'filters' => 'filter_',
    'actions' => 'action_',
    'getActions' => 'get_',
    'postActions' => 'post_',
  ),

  // Acciones permitidas.
  'allows' => array(
    '' => true
  ),
    
  // Campos encriptados
  'encriptedFields' => array(),

  // Configuraciones de credenciales requeridas para las acciones
  'credentials' => false,

  // Tipo de respuesta para el servicio: json, txt.
  'servicesFormat' => 'json',

  // SSL configuration
  'ssl' => '',

  // Variables de entorno dentro del controlador
  'env' => array(),

  // Filtros.
  'filters' => array(),

  //////////////////// Solo para la petición actual
  
  // Acción a ejecutar.
  'action' => null,

  // Parémetros para ejecutar la acción.
  'params' => array(),

  // Nombre de la vista a renderizar.
  'view' => null,

);
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Configuración de los controladores
 */
return array(/*
  
  // Configuraciónes por controlador.

  // Configuración por defecto para todos los controladores
  '' => array(
    // 'nombrePropiedad' => valor
  ),

  // views: Directorio hijo donde se buscará las vistas
  // Por defecto: 'views'
  // El primer directorio donde se buscará las vistas correspondientes a las
  // acciones será {root}/{views}.
  
  'MyCtrl' => array(
    'views' => 'templates/'
  ),

  // paths: Directorios alternativos de vistas.
  // Por defecto: array()
  // Directorios alternativos donde se buscarán las vistas.
  
  'MyCtrl' => array(
    // ...
    
    'paths' => array(
      // ...
      'app/views/',
      'app/templates/',
      // ...
    )

    // ...
  ),

  // prefixs: Prefijos para los tipos de elementos.
  // Por defecto: array(
  //  'filters' => 'filter_',
  //  'actions' => 'action_',
  //  'getActions' => 'get_',
  //  'postActions' => 'post_',
  // )
  // Prefijos utilizados para determinar los nombres reales de los método de
  // clase dependiendo del tipo de elemento buscado:
  // - filters: Filtros de acciones
  // - actions: Acciones sin importar el método request por el que se pida.
  // - {method}Actions: Acciones por un determinado método request.
  
  'MyCtrl' => array(
    // ...
    'prefixs' => array(
      // {elementName} => {valor}
      // ...
      'filters' => 'f_',
      'getActions' => 'g_'
      // ...
    )
    // ...
  ),

  // allows: Acciones permitidas.
  // Por defecto: array(
  //  '' => true
  // )
  // Listado de acciones permitidas. Indica por cuales request methods están
  // permitidas cada accion del controlador.
  
  'MyCtrl' => array(
    // ..
    'allows' => array(
      // Indica que todas las acciones por defecto admite cualquier metodo o no
      '' => true/false,

      // Todos los request methods permitido para la acción index.
      'index' => true,
      'show' => true,

      // Ningún request method permitido para lacción edit.
      'edit' => false,

      // Solo el request method post permitido para la acción delete.
      'delete' => array(
        'get' => false,
        'post' => true,
      ),
      // ...
    )
    // ..
  ),

  // servicesFormat: Acciones permitidas.
  // Por defecto: 'json'
  // Indica como se responderá los servicios: json (json_encode),
  // txt(var_export) o cualquier otro.
  
  'MyCtrl' => array(
    // 'servicesFormat' => ('txt'|'json')
    // ...
    'servicesFormat' => 'json'
    // ...
  ),

  // filters: Definición de filtros
  // Por defecto: array()
  // Definición de filtros. Se define cuando, el nombre del filtro, para cuales
  // acciones se ejecutan y a que dirección rediriguir si no pasa el filtro.
  // 
  
  'MyCtrl' => array(
    // ...
    'filters' => array(
      // '{before|before_get|after}' => array(...)
      // ...
      'before' => array(
        // {filterName} => ...
        // ...
        'find' => 'all',

        // Es lo mismo que:
        'find' => array(
          'to' => 'all',
        ),

        // Filtro solo para las acciones ...
        'validate' => array(
          'to' => array('index')
        )

        // Filtro para todas las acciones excepto ...
        'validate' => array(
          'except' => array('index')
        ),

        // ...
      )
      // ...
    )
    // ...
  ),

*/);
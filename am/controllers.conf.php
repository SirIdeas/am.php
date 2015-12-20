<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
/**
 * -----------------------------------------------------------------------------
 * Configuración de los controladores
 * -----------------------------------------------------------------------------
 */
return array(/*
  
  // Configuraciónes por controlador.

  // Configuración por defecto para todos los controladores
  'default' => array(
    // 'nombrePropiedad' => valor
  ),

  // ---------------------------------------------------------------------------
  // Nombre del controlador.
  // ---------------------------------------------------------------------------
  // Por defecto: 'Am'
  // ---------------------------------------------------------------------------
  // El nombre del controlador es utilizado para determinar el nombre de la
  // clase del controlador y el archivo con el código fuente.
  // Nombre de la clase: {name}Controller
  // Código fuente:  {root}/{name}.controller.php
  
  'MyCtrl' => array(
    // ...
  ),

  // Igual que
  'MyCtrl' => array(
    // ...
    'name' => 'MyCtrl'
    // ...
  ),

  // ---------------------------------------------------------------------------
  // parentName: Nombre del controlador padre.
  // ---------------------------------------------------------------------------
  // Por defecto: null
  // El controlador hijo hereda todas las propiedades del padre. Por definición
  // Todos los controladores heredan de la configuración de default y heredan
  // de AmController. Antes de incluir el controlador hijo se incluye el
  // controlador padre.

  // Controlador padre
  'MyParentCtrl' => array(
    // ...
  ),

  // Controlador hijo
  'MyCtrl' => array(
    // ...
    'parent' => 'MyParentCtrl'
    // ...
  ),
  
  // ---------------------------------------------------------------------------
  // root: Carpeta raíz del controlador.
  // ---------------------------------------------------------------------------
  // Por defecto: 'controllers'
  // Carpeta donde se buscará el archivo con el código fuente del controlador,
  // y donde se buscará la carpeta de vistas.

  'MyCtrl' => 'controller/index',
  
  // Igual que:
  'MyCtrl' => array(
    'root' => 'controller/index'
  ),

  // ---------------------------------------------------------------------------
  // Directorio hijo donde se buscará las vistas
  // ---------------------------------------------------------------------------
  // Por defecto: 'views'
  // El primer directorio donde se buscará las vistas correspondientes a las
  // acciones será {root}/{views}.
  
  'MyCtrl' => array(
    'views' => 'templates/'
  ),

  // ---------------------------------------------------------------------------
  // Directorios alternativos de vistas.
  // ---------------------------------------------------------------------------
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

  // ---------------------------------------------------------------------------
  // Prefijos para los tipos de elementos.
  // ---------------------------------------------------------------------------
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

  // ---------------------------------------------------------------------------
  // Acciones permitidas.
  // ---------------------------------------------------------------------------
  // Por defecto: array()
  // Listado de acciones permitidas. Indica que acciones del controlador están
  // permitidas y cuales no. Por defecto asume que una acción está permitida.
  
  'MyCtrl' => array(
    // ..
    'allows' => array(
      // {actionName} => (true|false)
      // ...
      'index' => true,
      'show' => true,
      'edit' => false,
      'delete' => false,
      // ...
    )
    // ..
  ),

  // ---------------------------------------------------------------------------
  // Acciones permitidas.
  // ---------------------------------------------------------------------------
  // Por defecto: 'json'
  // Indica como se responderá los servicios: json (json_encode),
  // txt(var_export) o cualquier otro.
  
  'MyCtrl' => array(
    // 'serviceType' => ('txt'|'json'|any)
    // ...
    'serviceType' => 'txt'
    // ...
  ),

  // ---------------------------------------------------------------------------
  // Definición de filtros
  // ---------------------------------------------------------------------------
  // Por defecto: array()
  // Definición de filtros. Se define cuando, el nombre del filtro, para cuales
  // acciones se ejecutan y a que dirección rediriguir si no pasa el filtro.
  // 
  
  'MyCtrl' => array(
    // ...
    'filters' => array(
      // '{before|befoer_get|after}' => array(...)
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
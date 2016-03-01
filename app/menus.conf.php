<?php

return array(
  'sidebar' => array(
    '/' => array(
      'txt' => 'Inicio',
    ),
    '' => array(
      'txt' => 'Primeros pasos',
      'items' => array(
        '/introduction' => 'Introducción',
        '/get-started' => 'Comenzando',
        '/routing' => 'Rutas',
        '/views' => 'Vistas',
        '/controllers' => 'Controladores',
        '/models' => 'Modelos',
      )
    ),
    '/server' => array(
      'txt' => 'Servidor',
      'items' => array(
        '/server#apache' => 'Apache',
        '/server#nginx' => 'Nginx',
      )
    )
  ),
  'introduction' => array(
    '#terms' => array(
      'txt' => 'Términos',
    ),
    '#requirements' => array(
      'txt' => 'Requerimientos',
    ),
    '#download' => array(
      'txt' => 'Descarga',
    ),
    '#struct' => array(
      'txt' => 'Estructura',
    ),
    '#init-files' => array(
      'txt' => 'Archivos iniciales',
    ),
    '#test-site' => array(
      'txt' => 'Sitio de pruebas',
    ),
  ),

  'get-started' => array(
    '#how-works' =>array(
      'txt' => 'Cómo funciona',
    ),
    '#basics-events' =>array(
      'txt' => 'Eventos Básicos',
    ),
    '#main-conf-file' =>array(
      'txt' => 'Archivo principal de configuración',
      'items' => array(
        '#basics-properties' => 'Propiedades básicas'
      )
    ),
    '#main-init-file' =>array(
      'txt' => 'Archivo principal de inicio',
    ),
  ),

  'routes' => array(
    '#configuration' => array(
      'txt' => 'Configuración',
    ),
    '#routes-struct' => array(
      'txt' => 'Estructura de las rutas',
    ),
    '#shapes' => array(
      'txt' => 'Forma simple o Forma explícita de las rutas',
    ),
    '#route-types' => array(
      'txt' => 'Tipos de rutas',
      'items' => array(
        '#file' => 'Reponder con un archivo',
        '#download' => 'Responder con la descarga de un archivo',
        '#template' => 'Renderizar un template',
        '#redirect' => 'Redirigir a otra URL de la aplicación',
        '#goto' => 'Rediriguir a una URL externa',
        '#call' => 'Realizar la llamada de una función o método',
      ),
    ),
    '#nested-route' => array(
      'txt' => 'Rutas Anidadas',
    ),
    '#route-params' => array(
      'txt' => 'Parámetros de ruta',
      'items' => array(
        '#route-params-types' => 'Tipos de los parámetros de ruta',
      ),
    ),
    '#avanced-options' => array(
      'txt' => 'Opciones avanzadas',
      'items' => array(
        '#route-dispatcher' => 'Despachadores de rutas',
        '#route-pre-processor' => 'Pre-procesadores de ruta',
        '#flow-request' => 'Flujo de petición',
      )
    ),
  ),

  'views' => array(
    '#embedded-code' => array(
      'txt' => 'Código embebido',
    ),
    '#root-url' => array(
      'txt' => 'URL raíz de la aplicación',
    ),
    '#directives' => array(
      'txt' => 'Directivas',
      'items' => array(
        '#heritage' => 'Herencia',
        '#nested-views' => 'Vistas anidadas',
        '#sections' => 'Secciones',
        '#vars' => 'Variables',
      ),
    )
  ),

  'controllers' => array(
    '#routes' => array(
      'txt' => 'Rutas',
    ),
    '#configuration' => array(
      'txt' => 'Configuración',
    ),
    '#considerations' => array(
      'txt' => 'Consideraciones',
    ),
    '#properties' => array(
      'txt' => 'Propiedades',
      'items' => array(
        '#property-name' => 'Nombre del controlador',
        '#property-root' => 'Directorio raíz',
        '#property-parent' => 'Controlador padre',
        '#property-views' => 'Directorio principal de vistas',
        '#property-paths' => 'Directorios secundarios de vistas',
        '#property-prefixs' => 'Prefijos',
        '#property-allows' => 'Acciones permitidas',
        '#property-services' => 'Formato de respuesta de los web services',
        '#property-filters' => 'Filtros',
        '#property-headers' => 'Cabeceras de respuesta',
      ),
    ),
    '#route-params' => array(
      'txt' => 'Recibir parámetros de la ruta',
    ),
    '#rendering-views' => array(
      'txt' => 'Renderizado de vistas',
    ),
    '#response-types' => array(
      'txt' => 'Tipos de respuestas',
      'items' => array(
        '#response-template' => 'Responder con una vista',
        '#response-go' => 'Responder con una redirección',
        '#response-file' => 'Responder con un archivo',
        '#response-error' => 'Responder con un error',
        '#response-services' => 'Responder como un web services',
      ),
    ),
  ),

  'models' => array(
    '#configuration' => array(
      'txt' => 'Configuración',
    ),
    '#properties' => array(
      'txt' => 'Propiedades',
    ),
    '#model-definition' => array(
      'txt' => 'Definición de modelo',
      'items' => array(
        '#scheme-name' => 'Nombre de esquema',
        '#table-name' => 'Nombre de tabla',
        '#fields' => 'Campos',
        '#primary-key' => 'Clave primaria',
        '#created-at-and-updated-at' => 'Fecha de creación y modificación',
      ),
    ),
    '#basic-actions' => array(
      'txt' => 'Acciones básicas',
      'items' => array(
        '#insert' => 'Insertar',
        '#find' => 'Buscar',
        '#update' => 'Actualizar',
        '#delete' => 'Eliminar',
      ),
    ),
  ),

  'routes' => array(
    '#apache' => array(
      'txt' => 'Apache',
    ),
    '#nginx' => array(
      'txt' => 'Nginx',
    ),

  ),

);
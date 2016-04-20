<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Agregar algunos de los métodos que atenderán cierto tipo de rutas.
 */
Am::addRouteDispatcher('file', function($destiny, $env, $params){
  return Am::file($destiny);
});

Am::addRouteDispatcher('download', function($destiny, $env, $params){
  return Am::download($destiny);
});

Am::addRouteDispatcher('redirect', 'Am::redirect');
Am::addRouteDispatcher('go', 'Am::go');
Am::addRouteDispatcher('call', 'Am::call');
Am::addRouteDispatcher('template', 'Am::template');
Am::addRouteDispatcher('controller', 'Am::controller');
Am::addRouteDispatcher('assets', 'Am::assets');

// PENDIENTE Esto debe pasar a la extensión AmResource
Am::addRoutePreProcessor('resource', function($route){

  $route['control'] = $route['resource'];
  $route['routes'] = array_merge(
    itemOr('routes', $route, array()),
    array(
      ''            => 'control => @index',
      '/new'        => 'control => @new',
      '/data.json'  => 'control => @data',
      '/:id/detail' => 'control => @detail',
      '/:id/edit'   => 'control => @edit',
      '/:id/delete' => 'control => @delete',
      '/cou'        => 'control => @cou',
      '/search'     => 'control => @search',
    )
  );

  return $route;

});

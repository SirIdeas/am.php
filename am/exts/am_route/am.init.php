<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Configuración de eventos globales que atenderá la extensión.
 */
// Despachar rutas.
Am::on('route.evaluate', 'AmRoute::evaluate');
// Agregar precallbakcs.
Am::on('route.addPreProcessor', 'AmRoute::addPreProcessor');
// Agregar métodos de atención a tipos de rutas.
Am::on('route.addDispatcher', 'AmRoute::addDispatcher');

/**
 * Agregar algunos de los métodos que atenderán cierto tipo de rutas.
 */
Am::addRouteDispatcher('file',        'Am::file');
Am::addRouteDispatcher('download',    'Am::download');
Am::addRouteDispatcher('redirect',    'Am::redirect');
Am::addRouteDispatcher('go',          'Am::go');
Am::addRouteDispatcher('call',        'Am::call');
Am::addRouteDispatcher('template',    'Am::template');
Am::addRouteDispatcher('controller',  'Am::controller');

// PENDIENTE Esto debe pasar a la extensión AmResource
function resourcePrecall($route){

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

}

Am::addRoutePreProcessor('resource', 'resourcePrecall');

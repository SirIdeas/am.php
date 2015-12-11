<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Configuración de eventos globales que atenderá la extensión.
 * -----------------------------------------------------------------------------
 */
// Despachar rutas.
Am::on('route.evaluate', 'AmRoute::evaluate');
// Agregar precallbakcs.
Am::on('route.addPreProcessor', 'AmRoute::addPreProcessor');
// Agregar métodos de atención a tipos de rutas.
Am::on('route.addDispatcher', 'AmRoute::addDispatcher');

/**
 * -----------------------------------------------------------------------------
 * Agregar algunos de los métodos que atenderán cierto tipo de rutas.
 * -----------------------------------------------------------------------------
 */
Am::ring('route.addDispatcher', 'file',     'Am::file');
Am::ring('route.addDispatcher', 'download', 'Am::download');
Am::ring('route.addDispatcher', 'redirect', 'Am::redirect');
Am::ring('route.addDispatcher', 'go',       'Am::go');
Am::ring('route.addDispatcher', 'call',     'Am::call');
Am::ring('route.addDispatcher', 'template', 'Am::template');

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

Am::ring('route.addPreProcessor', 'resource', 'resourcePrecall');

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
Am::call('route.addDispatcher', 'file',     'Am::respondeFile');
Am::call('route.addDispatcher', 'download', 'Am::downloadFile');
Am::call('route.addDispatcher', 'redirect', 'Am::redirect');
Am::call('route.addDispatcher', 'goto',     'Am::gotoUrl');
Am::call('route.addDispatcher', 'call',     'Am::responseCall');
Am::call('route.addDispatcher', 'template', 'Am::renderTemplate');

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

Am::call('route.addPreProcessor', 'resource', 'resourcePrecall');

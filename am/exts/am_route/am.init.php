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
Am::on('route.addPreCallback', 'AmRoute::addPreCallback');
// Agregar métodos de atención a tipos de rutas.
Am::on('route.addAttendCallback', 'AmRoute::addAttendCallback');

/**
 * -----------------------------------------------------------------------------
 * Agregar algunos de los métodos que atenderán cierto tipo de rutas.
 * -----------------------------------------------------------------------------
 */
AmRoute::addAttendCallback('file',     'Am::respondeFile');
AmRoute::addAttendCallback('download', 'Am::downloadFile');
AmRoute::addAttendCallback('redirect', 'Am::redirect');
AmRoute::addAttendCallback('goto',     'Am::gotoUrl');
AmRoute::addAttendCallback('call',     'Am::responseCall');
AmRoute::addAttendCallback('template', 'Am::renderTemplate');

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

Am::call('route.addPreCallback', 'resource', 'resourcePrecall');

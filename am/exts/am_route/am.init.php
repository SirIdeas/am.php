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
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
AmRoute::addDispatcher('file', function($destiny, $env, $params){
  return Am::file($destiny);
});

AmRoute::addDispatcher('download', function($destiny, $env, $params){
  return Am::download($destiny);
});

AmRoute::addDispatcher('redirect', 'Am::redirect');
AmRoute::addDispatcher('go', 'Am::go');
AmRoute::addDispatcher('call', 'Am::call');
AmRoute::addDispatcher('template', 'Am::template');
AmRoute::addDispatcher('action', 'Am::action');
AmRoute::addDispatcher('assets', 'Am::assets');
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

Am::addRoutePreProcessor('resource', function($route){

  $route['controller'] = $route['resource'];
  $route['routes'] = array_merge(
    itemOr('routes', $route, array()),
    array(
      ''              => 'controller => @index',
      '/new'          => 'controller => @new',
      '/data.json'    => 'controller => @data',
      '/{id}/detail'  => 'controller => @detail',
      '/{id}/edit'    => 'controller => @edit',
      '/{id}/delete'  => 'controller => @delete',
      '/cou'          => 'controller => @cou',
      '/search'       => 'controller => @search',
    )
  );

  return $route;

});

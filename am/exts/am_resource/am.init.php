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
      '/{id}/edit'    => 'controller => @edit',
      '/cou'          => 'controller => @cou',
      '/{id}/delete'  => 'controller => @delete',
      '/{id}/detail'  => 'controller => @detail',
      '/data'         => 'controller => @data',
      // '/search'       => 'controller => @search',
    )
  );

  return $route;

});

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

Am::addRoutePreProcessor('auth', function($route){

  $route['controller'] = $route['auth'];
  $route['routes'] = array_merge(
    itemOr('routes', $route, array()),
    array(
      '/signup'  => 'controller => @signup',
      '/login'  => 'controller => @login',
      '/logout' => 'controller => @logout',
    )
  );

  return $route;

});

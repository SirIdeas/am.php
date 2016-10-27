<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

AmRoute::addPreProcessor('resource', function($controller, $route){

  return array(
    AmRoute::action($controller.'@index',   $route.''),
    AmRoute::action($controller.'@new',     $route.'/new'),
    AmRoute::action($controller.'@edit',    $route.'/{id}/edit'),
    AmRoute::action($controller.'@cou',     $route.'/cou'),
    AmRoute::action($controller.'@delete',  $route.'/{id}/delete'),
    AmRoute::action($controller.'@detail',  $route.'/{id}/detail'),
    AmRoute::action($controller.'@data',    $route.'/data'),
    AmRoute::action($controller.'@search',  $route.'/search'),
  );

  return $route;

});

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

AmRoute::addPreProcessor('auth', function($controller, $route){

  return array(
    AmRoute::action($controller.'@login', $route.'/login'),
    AmRoute::action($controller.'@logout', $route.'/logout'),
  );

});

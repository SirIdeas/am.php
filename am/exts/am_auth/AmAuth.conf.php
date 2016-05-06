<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(
  //////////////////////////////////////////////////////////////////////////////
  // Asegurar los valores para los siguientes metodos
  'prefixs' => array(
    'actions'     => 'action_',
    'getActions'  => 'get_',
    'postActions' => 'post_',
    'filters'     => 'filter_',
  ),

  //////////////////////////////////////////////////////////////////////////////
  // Acciones permitidas
  'allow' => array(
    ''          => false,
    'login'     => true,
    'logout'    => true,
  ),


  'env' => array(

    ////////////////////////////////////////////////////////////////////////////
    // Parametros del recursos
    'auth' => '',

    ////////////////////////////////////////////////////////////////////////////
    // Nombres de los formularios
    'formName' => 'login',
    
  ),

);

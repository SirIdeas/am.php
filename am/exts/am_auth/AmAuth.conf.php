<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

return array(

  //////////////////////////////////////////////////////////////////////////////
  // Acciones permitidas
  'allow' => array(
    'login'    => true,
    'signup'    => true,
    'logout'    => true,
    'recovery'  => true,
    'reset'     => true,
  ),

  //////////////////////////////////////////////////////////////////////////////
  // Asegurar los valores para los siguientes metodos
  'prefixs' => array(
    'actions'     => 'action_',
    'getActions'  => 'get_',
    'postActions' => 'post_',
    'filters'     => 'filter_',
  ),

  'env' => array(

    ////////////////////////////////////////////////////////////////////////////
    // Parametros del recursos
    'authClass' => null,

    ////////////////////////////////////////////////////////////////////////////
    // RSA Params
    'keyPrivate' => null,
    'keyPublic' => null,
    'keyPassPhrase' => null,

    ////////////////////////////////////////////////////////////////////////////
    // Nombres de los formularios
    'formSignupName' => 'signup',
    'formLoginName' => 'login',

    ////////////////////////////////////////////////////////////////////////////
    // Nombres de los formularios
    'encriptedFields' => array(
      // 'login' => array('username', 'password'),
    ),
    
  ),

);

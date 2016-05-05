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
    'signup'    => true,
    'logout'    => true,
  ),


  'env' => array(

    ////////////////////////////////////////////////////////////////////////////
    // Parametros del recursos
    'credentials' => '',

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

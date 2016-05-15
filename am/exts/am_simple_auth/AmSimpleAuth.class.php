<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Controlador para autenticación de usuarios simple.
 */
// PENDIENTE Documentar
class AmSimpleAuth extends AmAuth{
  
  public function action(){
    parent::action();

    $this->msgsKeys = array('success', 'danger');
    $this->allows = $this->get('allows');

  }

  protected function in(AmCredentials $user){

    Am::getCredentialsHandler($this->auth)->setAuthenticated($user);

  }

  // Bandeja de la administracion
  public function action_login(){

    $this->username = '';
    $this->password = '';

    $this->fields = array(
      'username' => array(
        'label' => 'Email',
        'type' => 'email',
        'required' => true,
        'encripted' => true,
      ),
      'password' => array(
        'label' => 'Contraseña',
        'type' => 'password',
        'required' => true,
        'encripted' => true,
      ),
    );

  }

  public function get_login(){}
  public function post_login(){
    $ret = parent::post_login();

    if($ret['success'] === true){

      // Usuario esta autenticado
      AmFlash::success('Bienvenido');

      return Am::go($this->urls['home']);  // Ir a index

    }

    // Usuario no autenticado
    AmFlash::danger('Falló la auntenticación');

  }

  public function action_logout(){

    Am::getCredentialsHandler($this->auth)->setAuthenticated(null);

    return Am::go($this->urls['login']);  // Ir a index

  }

  // public function action_signup(){

  //   $this->fields = array(
  //     'email' => array(
  //       'label' => 'Email',
  //       'type' => 'email',
  //       'required' => true,
  //     ),
  //     'name' => array(
  //       'label' => 'Nombre',
  //       'type' => 'text',
  //       'required' => true,
  //     ),
  //     'password' => array(
  //       'label' => 'Password',
  //       'type' => 'password',
  //       'required' => true,
  //     ),
  //     'confirm_password' => array(
  //       'label' => 'Confirme password',
  //       'type' => 'password',
  //       'required' => true,
  //     ),
  //     'conditions' => array(
  //       'label' => 'Acepta las condiciones de uso',
  //       'type' => 'checkbox',
  //       'required' => true,
  //     ),
  //   );
    
  // }

  // public function post_signup(){
  //   $ret = parent::post_signup();

  //   if(isset($ret['success'])){

  //     // Usuario esta autenticado
  //     AmFlash::success('Resgitro completado');

  //     return Am::go($this->urls['login']);  // Ir a index

  //   }

  //   // Usuario no autenticado
  //   AmFlash::danger('Falló el registro');

  // }

  // // Bandeja de la administracion
  // public function action_recovery(){

  //   $this->form = $this->recoveryFormName;

  //   $this->username = '';

  //   $this->fields = array(
  //     'username' => array(
  //       'label' => 'Email',
  //       'type' => 'email',
  //       'required' => true
  //     ),
  //   );

  // }

}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Controlador para autenticación de usuarios.
 */
// PENDIENTE: Documentar
class AmAuth extends AmController{

  public function action(){

    $this->authClass = Am::getCredentialsHandler($this->auth)->getCredentialsClass();

  }

  // Bandeja de la administracion
  public function post_login(){

    $params = Am::g('post');

    // Obtener el nombre de la clase
    $class = $this->authClass;

    $this->attrs = $this->decrypt($params[$this->formName], array('username', 'password'));

    $this->username = itemOr('username', $this->attrs);
    $this->password = itemOr('password', $this->attrs);

    // Busca el usuario por usernam y por password
    $user = $class::auth($this->username, $this->password);

    $ret = array(
      'success' => isset($user),
    );

    // Usuario esta autenticado
    if($ret['success']){
      $token = $this->in($user);
      if(!empty($token))
        $ret['token'] = $token;
    }

    return $ret;

  }

  public function action_logout(){

    return array(
      'success' => $this->out($this->request->token)
    );

  }

  protected function in(AmCredentials $user){

    $token = AmToken::create();
    $token->setContent(array(
      'id' => $user->getCredentialsId()
    ));
    $token->save();
    return $token->getID();

  }

  protected function out($token){
    
    $token = AmToken::load($token);
    if($token)
      return $token->delete();
    return true;
    
  }

}

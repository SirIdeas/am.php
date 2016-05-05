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

    $this->attrs = $this->decryptFields('login', $params[$this->formLoginName]);

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

  public function post_signup(){

    $params = Am::g('post');

    // Obtener el nombre de la clase
    $class = $this->authClass;
    
    $attrs = $this->decryptFields('signup', $params[$this->formSignupName]);

    // Instanciar usuario
    return $class::register($attrs);

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

  public function decryptFields($actionName, array $attrs){

    $fields = itemOr($actionName, $this->encriptedFields, array());

    foreach($fields as $field)
      $attrs[$key] = $this->decrypt(itemOr($field, $attrs, null));

    return $attrs;

  }

  public function decrypt($str){

    $str = base64_decode($str);
    if(!isset($this->keyPrivate) || !isset($this->keyPassPhrase))
      return $str;
    if(!openssl_private_decrypt($str, $str, openssl_pkey_get_private($this->keyPrivate, $this->keyPassPhrase)))
      return false;
    return $str;

  }

  public function encrypt($str){

    if(!isset($this->keyPublic))
      return $str;  
      // Validations
    openssl_public_encrypt($str, $encrypted, $this->keyPublic);
    $str = base64_encode($str);
    return $str;

  }

}

<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

// Interfaz para clases que puedan enviar y recibir correso

class AmAuthControl extends AmControl{

  public function post_signup(){

    // Obtener el nombre de la clase
    $class = $this->authClass;
    
    $attrs = array_merge(array(
      'username' => '',
      'password' => '',
      'confirm_password' => '',
    ), $this->request->signup);

    $attrs['username'] = $this->sslDecrypt($attrs['username']);
    $attrs['password'] = $this->sslDecrypt($attrs['password']);
    $attrs['confirm_password'] = $this->sslDecrypt($attrs['confirm_password']);

    // Intenta crear el usuario
    $ret = $class::register($attrs);

    return $ret;

  }

  // Bandeja de la administracion
  public function post_login(){

    // Obtener el nombre de la clase
    $class = $this->authClass;
    $username = itemOr('username', $this->request->login);
    $password = itemOr('password', $this->request->login);

    $username = $this->sslDecrypt($username);
    $password = $this->sslDecrypt($password);

    // Busca el usuario por usernam y por password
    $user = $class::auth($username, $password);

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

  public function newRecoveryCode(){
    return sprintf("%06d", mt_rand(0,1000000));
  }

  // Acción para solicitar las instrucciones para recuperar la sontraseña
  public function post_recovery(){
    $class = $this->authClass;
    $login = $this->sslDecrypt($this->request->username);
    $r = $class::getByLogin($login);

    $ret = array();

    if(!$r)
      $ret['error'] = 'userNotFound';

    else{

      // Generate code
      $code = $this->newRecoveryCode();

      // Create token
      $token = AmToken::create();
      $token->setContent(array(
        'id' => $r->getCredentialsId(),
        'code' => $code
      ));
      $token->save();

      // Prepare mailer`
      $mail = AmMailer::get('amAuth_recovery', array(
        'with' => array('r' => $r->toArray(), 'code' => $code),
        'address' => array($r->getCredentialsEmail())
      ));

      // Send code to user for email
      if($mail->send()){
        $ret['recoveryToken'] = $token->getID();
      }else{
        $ret['error'] = 'troublesSendingEmail';
        $ret['errorTxt'] = $mail->errorInfo();
      }

    }

    $ret['success'] = !!$r && !isset($ret['error']);
    
    return $ret;

  }

  // Acción para solicitar las instrucciones para recuperar la sontraseña
  public function post_checkCode(){
    $token = AmToken::load($this->request->token);
    $code = $this->sslDecrypt($this->request->code);
    $ret =array();
  
    // Validations
    if(!$token){
      $ret['error'] = 'tokenNotFound';
    }else{
      $data = $token->getContent();
      if($data['code'] != $code){
        $ret['error'] = 'invalidCode';
      }else{
        $class = $this->authClass;
        $this->r = $class::getCredentialsInstance($data['id']);
        if(!$this->r){
          $ret['error'] = 'userNotFound';
        }
      }
    }

    $ret['success'] = !isset($ret['error']);

    return $ret;

  }

  // Accion para restaurar la contraseña
  public function post_reset(){
    $ret = $this->post_checkCode();

    $pass = $this->sslDecrypt($this->request->password);
    $passConfirm = $this->sslDecrypt($this->request->confirm_password);
    
    // Validations
    if($ret['success']){
      if (!$this->isValidPassword($pass))
        $ret['error'] = 'passwordInvalid';
      else if($pass != $passConfirm)
        $ret['error'] = 'passwordDiff';

      // Password change
      else if(!$this->r->resetPasword($pass))
        $ret['error'] = 'troublesResetingPassword';

      $ret['success'] = !isset($ret['error']);
    }
    
    return $ret;

  }

  private function in(AmCredentials $user){

    $token = AmToken::create();
    $token->setContent(array(
      'id' => $user->getCredentialsId()
    ));
    $token->save();
    return $token->getID();

  }

  private function out($token){
    $token = AmToken::load($token);
    if($token)
      return $token->delete();
    return true;
  }

  public function sslDecrypt($str){
    $str = base64_decode($str);
    if(!isset($this->keyPrivate) || !isset($this->keyPassPhrase))
      return $str;
    if(!openssl_private_decrypt($str, $str, openssl_pkey_get_private($this->keyPrivate, $this->keyPassPhrase)))
      return false;
    return $str;
  }

  public function sslEncrypt($str){
    if(!isset($this->keyPublic))
      return $str;  
      // Validations
    openssl_public_encrypt($str, $encrypted, $this->keyPublic);
    $str = base64_encode($str);
    return $str;
  }

  public function isValidPassword($pass){

    if(strlen($pass)<4)
      return false;
    return true;

  }

}

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
      '$username' => $username,
      '$password' => $password,
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
    $this->out($this->request->token);
    return array(
      'success' => true
    );
  }

  // Acción para solicitar las instrucciones para recuperar la sontraseña
  public function post_recovery(){
    $class = $this->authClass;
    $login = $this->request->recovery['username'];
    $r = $class::getByLogin($login);

    $ret = array();

    if(!$r)
      $ret['error'] = 'userNotFound';

    else{

      // Enviar mensaje de registro de Ipn
      $this->mail = AmMailer::get('recovery', array(
        // Asignar variables
        'dir' => dirname(__FILE__). '/mails/',
        'subject' => 'Recuperar contraseña',
        'smtp' => false, 
        'with' => array(
          'url' => Am::serverUrl($r->getCredentialsResetUrl())
        ),
      ))

      ->addAddress($r->getCredentialsEmail());

      if(!$this->mail->send())
        $ret['error'] = 'troublesSendingEmail';

    }

    $ret['success'] = !!$r && !isset($ret['error']);
    
    return $ret;

  }

  // Accion para restaurar la contraseña
  public function post_reset($token){
    $class = $this->authClass;
    $r = $class::getByToken($token);
    $pass = $this->request->reset['password'];

    $ret = array();

    if(!$r)
      $ret['error'] = 'userNotFound';

    if (!$this->isValidPassword($pass))
      $ret['error'] = 'passwordInvalid';

    else if($pass != $this->request->reset['confirm_password'])
      $ret['error'] = 'passwordDiff';

    else if(!$r->resetPasword($pass))
      $ret['error'] = 'troublesResetingPassword';

    $ret['success'] = !!$r && !isset($ret['error']);
    
    return $ret;

  }

  protected function in(AmCredentials $user){

    $token = AmToken::create();
    $token->setContent($user->getCredentialsId());
    $token->save();
    return $token->getName();

  }

  protected function out($token){
    $token = AmToken::load($token);
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

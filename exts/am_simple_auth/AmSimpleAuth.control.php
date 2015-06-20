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

class AmSimpleAuthControl extends AmAuthControl{

  public function __construct($data = null){
    parent::__construct($data);
    
    if(!$this->authClass)
      $this->authClass = Am::getCredentialsHandler()->getCredentialsClass();

  }

  protected function in(AmCredentials $user){
    Am::getCredentialsHandler()->setAuthenticated($user);
  }

  protected function out(){
    Am::getCredentialsHandler()->setAuthenticated(null);
  }

  // Bandeja de la administracion
  public function action_login(){}
  public function get_login(){}
  public function post_login(){
    $ret = parent::post_login();

    if(isset($ret['success'])){

      // Usuario esta autenticado
      AmFlash::success($this->texts['welcome']);
      Am::gotoUrl($this->urls['index']);  // Ir a index

    }else{

      // Usuario no autenticado
      AmFlash::danger($this->texts['authFailed']);

    }

  }

  public function action_logout(){
    $ret = parent::action_logout();
    Am::gotoUrl($this->urls['index']);  // Ir a index
  }

  // Acción para solicitar las instrucciones para recuperar la sontraseña
  public function action_recovery(){}
  public function get_recovery(){}
  public function post_recovery(){
    $ret = parent::post_recovery();

    if(isset($ret['success']))
      AmFlash::success($this->texts['recoveryEmailSended']);

    elseif($ret['error'] == 'userNotFound')
      AmFlash::danger($this->texts['userNotFound']);

    else{
      AmFlash::danger($this->texts['troublesSendingEmail']);
      AmFlash::danger($this->mail->errorInfo());

      if($this->showMailContentIfFail){
        echo $this->mail->getContent();
        exit;
      }

    }
    
    Am::gotoUrl($this->urls['index'].'recovery');  // Ir a index

  }

  // Accion para restaurar la contraseña
  public function action_reset(){}
  public function get_reset(){}
  public function post_reset($token){
    $ret = parent::post_reset();

    if(isset($ret['success'])){
      AmFlash::success($this->texts['passwordResetSuccess']);
      Am::gotoUrl($this->urls['index']);  // Ir a index
    }else
      AmFlash::danger($this->texts[$ret['error']]);
      
  }

}

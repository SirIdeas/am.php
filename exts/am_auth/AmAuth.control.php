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
  public function action_login(){}
  public function get_login(){}
  public function post_login(){

    // Obtener el nombre de la clase
    $class = $this->authClass;

    // Busca el usuario por usernam y por password
    $user = $class::auth(
      $this->request->login["username"],
      $this->request->login["password"]
    );

    if(isset($user)){

      // Usuario esta autenticado
      Am::getCredentialsHandler()->setAuthenticated($user);
      AmFlash::success($this->texts["welcome"]);
      Am::gotoUrl($this->urls["index"]);  // Ir a index

    }else{

      // Usuario no autenticado
      AmFlash::danger($this->texts["authFailed"]);

    }

  }

  public function action_logout(){
    Am::getCredentialsHandler()->setAuthenticated($user);
    Am::gotoUrl($this->urls["index"]);  // Ir a index
  }

  // Acción para solicitar las instrucciones para recuperar la sontraseña
  public function action_recovery(){}
  public function get_recovery(){}
  public function post_recovery(){
    $class = $this->authClass;
    $login = $this->request->recovery["username"];
    $r = $class::getByLogin($login);

    if($r){

      // Enviar mensaje de registro de Ipn
      $mail = AmMailer::get("recovery", array(
        // Asignar variables
        "dir" => dirname(__FILE__). "/mails/",
        "subject" => "Recuperar contraseña",
        "smtp" => false, 
        "with" => array(
          "url" => Am::serverUrl($r->getCredentialsResetUrl())
        ),
      ))

      ->addAddress($r->getCredentialsEmail());

      if($mail->send())
        AmFlash::success($this->texts["recoveryEmailSended"]);

      else
        AmFlash::danger($this->texts["troublesSendingEmail"]);

      header("content-type: text/plain");
      echo $mail->getContent();
      // var_dump($mail);
      exit;

    }else
      AmFlash::danger($this->texts["userNotFound"]);
    
    Am::gotoUrl($this->urls["index"]."recovery");  // Ir a index

  }

  // Accion para restaurar la contraseña
  public function action_reset(){}
  public function get_reset(){}
  public function post_reset($token){
    $class = $this->authClass;
    $r = $class::getByToken($token);
    $pass = $this->request->reset["password"];

    if($r){

      if (strlen($pass)<4)
        AmFlash::danger($this->texts["passwordInvalid"]);

      else if($pass != $this->request->reset["confirm_password"])
        AmFlash::danger($this->texts["passwordDiff"]);

      else if(!$r->resetPasword($pass))
        AmFlash::danger($this->texts["troublesResetingPassword"]);

      else{
        AmFlash::success($this->texts["passwordResetSuccess"]);
        Am::gotoUrl($this->urls["index"]);  // Ir a index
      }
      
    }else
      AmFlash::danger($this->texts["userNotFound"]);
  }

}

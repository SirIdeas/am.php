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

  public function action_login(){

    $this->form = $this->formLoginName;

    $this->fields = array(
      'username' => array(
        'label' => 'Email',
        'type' => 'email',
        'required' => true
      ),
      'password' => array(
        'label' => 'Password',
        'type' => 'text',
        'required' => true
      ),
    );

  }

  // Bandeja de la administracion
  public function post_login(){

    $params = Am::g('post');

    // Obtener el nombre de la clase
    $class = $this->authClass;

    $attrs = $this->decryptFields('login', $params[$this->formLoginName]);

    $this->username = itemOr('username', $attrs);
    $this->password = itemOr('password', $attrs);

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

  public function action_signup(){

    $this->form = $this->formSignupName;

    $this->fields = array(
      'email' => array(
        'label' => 'Email',
        'type' => 'email',
        'required' => true,
      ),
      'name' => array(
        'label' => 'Nombre',
        'type' => 'text',
        'required' => true,
      ),
      'password' => array(
        'label' => 'Password',
        'type' => 'password',
        'required' => true,
      ),
      'confirm_password' => array(
        'label' => 'Confirme password',
        'type' => 'password',
        'required' => true,
      ),
      'conditions' => array(
        'label' => 'Acepta las condiciones de uso',
        'type' => 'checkbox',
        'required' => true,
      ),
    );
    
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

  // public function newRecoveryCode(){

  //   return sprintf("%06d", mt_rand(0,1000000));

  // }

  // // Acción para solicitar las instrucciones para recuperar la sontraseña
  // public function post_recovery(){

  //   $class = $this->authClass;
  //   $login = $this->decrypt($this->request->username);
  //   $r = $class::getByLogin($login);

  //   $ret = array();

  //   if(!$r)
  //     $ret['error'] = 'userNotFound';

  //   else{

  //     // Generate code
  //     $code = $this->newRecoveryCode();

  //     // Create token
  //     $token = AmToken::create();
  //     $token->setContent(array(
  //       'id' => $r->getCredentialsId(),
  //       'code' => $code
  //     ));
  //     $token->save();

  //     // Prepare mailer`
  //     $mail = AmMailer::get('amAuth_recovery', array(
  //       'with' => array('r' => $r->toArray(), 'code' => $code),
  //       'address' => array($r->getCredentialsEmail())
  //     ));

  //     // Send code to user for email
  //     if($mail->send()){
  //       $ret['recoveryToken'] = $token->getID();
  //     }else{
  //       $ret['error'] = 'troublesSendingEmail';
  //       $ret['errorTxt'] = $mail->errorInfo();
  //     }

  //   }

  //   $ret['success'] = !!$r && !isset($ret['error']);
    
  //   return $ret;

  // }

  // // Acción para solicitar las instrucciones para recuperar la sontraseña
  // public function post_checkCode(){

  //   $token = AmToken::load($this->request->token);
  //   $code = $this->decrypt($this->request->code);
  //   $ret =array();
  
  //   // Validations
  //   if(!$token){
  //     $ret['error'] = 'tokenNotFound';
  //   }else{
  //     $data = $token->getContent();
  //     if($data['code'] != $code){
  //       $ret['error'] = 'invalidCode';
  //     }else{
  //       $class = $this->authClass;
  //       $this->r = $class::getCredentialsInstance($data['id']);
  //       if(!$this->r){
  //         $ret['error'] = 'userNotFound';
  //       }
  //     }
  //   }

  //   $ret['success'] = !isset($ret['error']);

  //   return $ret;

  // }

  // // Accion para restaurar la contraseña
  // public function post_reset(){

  //   $ret = $this->post_checkCode();

  //   $pass = $this->decrypt($this->request->password);
  //   $passConfirm = $this->decrypt($this->request->confirm_password);
    
  //   // Validations
  //   if($ret['success']){
  //     if (!$this->isValidPassword($pass))
  //       $ret['error'] = 'passwordInvalid';
  //     else if($pass != $passConfirm)
  //       $ret['error'] = 'passwordDiff';

  //     // Password change
  //     else if(!$this->r->resetPasword($pass))
  //       $ret['error'] = 'troublesResetingPassword';

  //     $ret['success'] = !isset($ret['error']);
  //   }
    
  //   return $ret;

  // }

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

  public function isValidPassword($pass){

    if(strlen($pass)<4)
      return false;
    return true;

  }

}

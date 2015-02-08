<?php

/**
 * Clase que sirve de apoyo para el proceso de atutenticación
 * y aprobación de permisos con credenciales.
 */
final class AmCredentialsHandler{
  
  protected
    // Clase que servirá como clase de autenticación.
    // Esta clase deberá implementar la interfaz AmWithCredentials
    $credentialsClass = null,

    // Identificador del usuario logeado
    $credentialsId = null,

    // 
    $credentials = null,

    // Url donde se autentica el usuario.
    $authUrl = null;
  
  public function setCredentialsClassDefault() {
    
    $conf = Am::getConf('credentials', false, true);

    // Asignar la u
    $this->setAuthUrl(url($conf->authUrl));
    
    $this->setCredentialsClass($conf->class, AmSession::params('credentials_id'));

  }
  
  public function setCredentialsClass($credentialClass, $credentialsId) {
    
    $this->credentialsClass = $credentialClass;
    $this->credentialsId = $credentialsId;
    
    if(class_exists($credentialClass)){
      $this->credentials = $credentialClass::getCredentialsInstance($credentialsId);
    }
    
    if(!isset($this->credentials)){
      AmSession::destroyParam('credentials_id');
    }
    
  }
  
  public function getCredentials(){
    
    return $this->credentials;
    
  }
  
  public function setAuthUrl($authUrl){
    
    $this->authUrl = $authUrl;
    
  }
  
  public function getAuthUrl(){
    
    return $this->authUrl;
    
  }
  
  public function redirectToAuth(){
    
    AmControl::currentControl()->redirect($this->getAuthUrl());
    
  }
  
  public function isAuth(){
    
    return isset($this->credentials);
    
  }
  
  public function hasCredentials($credentials){
    
    if($this->isAuth()){
      
      $c = $this->credentials;
      
      if(is_array($credentials)){
        foreach($credentials as $credential){
          
          if(!is_array($credential)){
            $credential = array($credential);
          }
          
          $sw = false;
          foreach($credential as $credentialOr){
            if($c->hasCredential($credentialOr)){
              $sw = true;
              break;
            }
          }
          
          if(!$sw){
            return false;
          }
        }
        
        return true;
        
      }
      
      return $c->hasCredential($credentials);
      
    }
    
    return false;
    
  }
  
  public function setAuthenticated(AmWithCredentials $credentials = null){
    
    $this->credentials = $credentials;
    
    if($this->isAuth()){
      
      AmSession::params('credentials_id', $this->credentials->getCredentialsId());

    }else{
      
      AmSession::destroyParam('credentials_id');
      
    }
    
  }
  
  public function checkAuth(){
    
    if(!$this->isAuth()){
      
      $this->redirectToAuth();
      
    }
    
  }
  
  public function needCredentials($credential, $action){
    if(is_array($credential)){
      if(isset($credential['only'])){
        return in_array($action, $credential['only']);
      }
      if(isset($credential['except'])){
        return !in_array($action, $credential['except']);
      }
      return false;
    }
    return true;
  }
  
  public function checkCredentials($credentials, $action){
    if(is_array($credentials)){
      if(empty($credentials)){
        if($this->getCredentials() === null){
          $this->redirectToAuth();
        }
      }else{
        foreach($credentials as $credential){
          if($credential!==false && $this->needCredentials($credential, $action)){
            if(!is_array($credential)){
              $credential = array($credential);
            }elseif(AmArray::isAssocArray($credential)){
              $credential = itemOr($credential, 'roles', array());
            }
            if(!$this->hasCredentials($credential)){
              $this->redirectToAuth();
            }
          }
        }
      }
    }
  }

  public static function getInstance(){
    return new self;
  }
  
}

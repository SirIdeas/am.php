<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase que sirve de apoyo para el proceso de atutenticación
 * y aprobación de permisos con credenciales.
 */
// PENDIENTE Documentar
final class AmCredentialsHandler{

  protected static
    $instances = array();

  protected
    // Nombre de las credenciales que desea obtener
    $name = null,

    // Clase que servirá como clase de autenticación.
    // Esta clase deberá implementar la interfaz AmWithCredentials
    $credentialsClass = null,

    // Identificador del usuario logeado
    $credentialsId = null,

    // Instancia de las credenciales del usuario logeado
    $credentials = null,

    // Url donde se autentica el usuario.
    $authUrl = null;

  // Constructor de la clase
  public function __construct($name = ''){

    // Nombre del las credenciales que desea obtener
    $this->name = $name;

    $this->session = Am::session('credentials');

    // Obtener la configuracion
    $conf = itemOr($this->name, Am::getProperty('credentials', array()), array());

    // Inicializar parametros
    $this->authUrl = itemOr('authUrl', $conf);

    // Aignar clase que se utilizará para las credenciales
    $this->setCredentialsClass(
      itemOr('class', $conf),   // Clase
      $this->session[$this->name]     // Identificador
    );

  }

  // Retorna el nombre de la clase utilizada para las credenciales
  public function getCredentialsClass(){
    
    return $this->credentialsClass;
    
  }

  // Asignar la clases que se utilizara para las credenciales
  public function setCredentialsClass($credentialClass, $credentialsId) {

    // Asignar valores
    $this->credentialsClass = $credentialClass;
    $this->credentialsId = $credentialsId;

    // Si la clase no existe no se puede buscar las credenciales
    if(!class_exists($credentialClass))
      return;

    // Obtener instancia de las credenciales mediante el Id.
    $this->credentials = $credentialClass::getCredentialsInstance($credentialsId);

    // Sino se obtivieron credenciales se destruye el ID guardado
    if(!$this->isAuth()){
      unset($this->session[$this->name]);
      $this->credentialsId = null;
    }

  }

  // Indica si hay un usuario autenticado o no
  public function isAuth(){

    return isset($this->credentials);

  }

  // Redirigue al enlace para autenticar al usuario
  public function redirectToAuth(){

    return Am::go($this->authUrl);

  }

  // Devuelve la instancia del usuario logeado
  public function getCredentials(){

    return $this->credentials;

  }

  // Asignar una la autenticacion de un usuario.
  public function setAuthenticated(AmCredentials $credentials = null){

    // Asignar credenciales
    $this->credentials = $credentials;

    // Si son unas credenciales válidas
    // Guardar el ID de las credenciales
    if($this->isAuth())
      $this->session[$this->name] = $credentials->getCredentialsId();

    // De lo contrario borrar el ID de la session
    else
      unset($this->session[$this->name]);

  }

  // Indica si la session actual tiene las credenciales recibidas por parametros
  public function hasCredentials($credentials){

    // Si no hay usuario autenticado retornar falso
    if(!$this->isAuth())
      return false;

    // Si las credenciales solicitadas no son un arrauy
    // se puede preguntar directamente al usuario
    // autenteicado
    if(!is_array($credentials))
      return $this->credentials->hasCredential($credentials);

    // Veriricar cada credencial
    foreach($credentials as $credential){

      // Si no es una grupo de credenciales volverla un grupo de uno
      if(!is_array($credential))
        $credential = array($credential);

      // Verificar si el usuario tiene al menos una
      // de las credenciales del grupo.
      $hasOneCredential = false;
      foreach($credential as $credentialOr){
        if($this->credentials->hasCredential($credentialOr)){
          $hasOneCredential = true;
          break;
        }
      }

      // Si No tiene al menos una de las credenciales del grupo
      // no se le otorga permisos
      if(!$hasOneCredential){
        return false;
      }
    }

    // Tiene todas las credenciales
    return true;

  }

  // Devuelve una instancia para del manejador de credenciales.
  // Destinada a un callback.
  public static function get($name = ''){

    if(!isset(self::$instances[$name]))
      self::$instances[$name] = new self($name);

    return self::$instances[$name];

  }

}

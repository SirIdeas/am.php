<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmSSL{

  protected static
    $instances = array();

  protected
    $name = null,
    $keyPassPhrase = null,
    $keyPublic = null,
    $keyPrivate = null,
    $keyPublicFile = null,
    $keyPrivateFile = null;

  public function __construct($name, $options = array()){

    $this->name = $name;
    foreach ($options as $key => $value)
      $this->$key = $value;

    if(is_file($this->keyPublicFile))
      $this->keyPublic = file_get_contents($this->keyPublicFile);

    if(is_file($this->keyPrivateFile))
      $this->keyPrivate = file_get_contents($this->keyPrivateFile);

  }

  public function getKeyPassPhrase(){

    return $this->keyPassPhrase;

  }

  public function getKeyPublic(){

    return $this->keyPublic;

  }

  public function getKeyPrivate(){

    return $this->keyPrivate;

  }

  public function getKeyPublicFile(){

    return $this->keyPublicFile;

  }

  public function getKeyPrivateFile(){

    return $this->keyPrivateFile;

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

  public static function get($name = '', array $options = array()){

    if(!isset(self::$instances[$name])){

      // Obtener configuraciones de ssl
      $ssl = Am::getProperty('ssl');

      // Combinar opciones recibidas en el constructor con las
      // establecidas en el archivo de configuracion
      $options = array_merge(
        // Configuración de valores po defecto
        itemOr('', $ssl, array()),
        // Configuración de valores del mail
        itemOr($name, $ssl, array()),
        // Parametros locales
        $options
      );

      // Crear instancia
      self::$instances[$name] = new self($name, $options);

    }

    return self::$instances[$name];

  }

}
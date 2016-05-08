<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmToken extends AmObject{

  // Valore por defecto para el token
  const
    ROOT_FOLDER = 'tokens/',
    TIME_EXPIRATION_DEF = 90000,
    MAX_TIME_NO_USE_DEF = 3600;

  // Propiedades del token
  protected
    $id = null,
    $fileName = null,
    $createdAt = null,
    $updatedAt = null,
    $timeExpiration = null,
    $maxTimeNoUse = null,
    $content = null;

  public function __construct($id, $timeExpiration = null, $maxTimeNoUse = null){

    $this->id = $id;
    $this->fileName = AM_STORAGE.'/'.self::ROOT_FOLDER.$id;
    $this->timeExpiration = isset($timeExpiration)? $timeExpiration : self::TIME_EXPIRATION_DEF;
    $this->maxTimeNoUse = isset($maxTimeNoUse)? $maxTimeNoUse : self::MAX_TIME_NO_USE_DEF;

    if($this->exists()){
      $content = trim(file_get_contents($this->fileName));

      if(!empty($content)){
        $content = json_decode($content, true);
        parent::__construct($content);
      }

    }

  }

  public function isExpired(){
    return !isset($this->createdAt) ||
           !isset($this->updatedAt) ||
            time()>=($this->createdAt + $this->timeExpiration) ||
            time()>=($this->updatedAt + $this->maxTimeNoUse);
  }

  public function exists(){
    return is_file($this->fileName);
  }

  // PAra obtener ciertos atributos
  public function getID(){ return $this->id; }
  public function getFileName(){ return $this->fileName; }
  public function getCreatedFile(){ return $this->createdFile; }
  public function getUpdateFile(){ return $this->updatedFile; }
  public function getContent(){ return $this->content; }

  // Atributos seteables
  public function setMaxTimeNoUse($time){ $this->maxTimeNoUse = $time; return $this; }
  public function setTimeExpiration($time){ $this->timeExpiration = $time; return $this; }
  public function setContent($content){ $this->content = $content; return $this; }

  public function toArray(){
    return array(
      'createdAt' => $this->createdAt,
      'updatedAt' => $this->updatedAt,
      'timeExpiration' => $this->timeExpiration,
      'maxTimeNoUse' => $this->maxTimeNoUse,
      'content' => $this->content,
    );
  }

  public function save(){

    $this->updatedAt = time();

    if(!isset($this->createdAt))
      $this->createdAt = $this->updatedAt;

    // Determinar data a guardar
    $data = $this->toArray();

    // Crear el directorio si no existe
    $dir = dirname($this->fileName);
    if(!is_dir($dir))
      mkdir($dir, 0775, true);

    // Guardar datos en el archivo en formato json
    return file_put_contents($this->fileName, json_encode($data));

  }

  public static function getNewID(){
    return md5(mt_rand());
  }

  public static function load($id){
    $token = new self($id);
    if($token->isExpired())
      $token->delete();
    return $token->exists() ? $token : null;
  }

  public static function create(){

    do{
      $id = self::getNewID();
      $token = self::load($id);
    }while(isset($token));

    $token = new self($id);
    $token->save();

    return $token;

  }

  public function delete(){
    if($this->exists())
      return !!unlink($this->getFileName());
    return false;
  }

} 
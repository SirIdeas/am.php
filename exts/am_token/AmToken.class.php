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

class AmToken extends AmObject{

  // Valore por defecto para el token
  static protected
    $ROOT_FOLDER = 'tokens/',
    $TIME_EXPIRATION_DEF = 90000,
    $MAX_TIME_NO_USE_DEF = 3600;

  // Propiedades del token
  protected
    $name = null,
    $fileName = null,
    $createdAt = null,
    $updatedAt = null,
    $timeExpiration = null,
    $maxTimeNoUse = null,
    $content = null;

  public function __construct($name, $timeExpiration = null, $maxTimeNoUse = null){

    $this->name = $name;
    $this->fileName = self::$ROOT_FOLDER . $name;
    $this->timeExpiration = isset($timeExpiration)? $timeExpiration : self::$TIME_EXPIRATION_DEF;
    $this->maxTimeNoUse = isset($maxTimeNoUse)? $maxTimeNoUse : self::$MAX_TIME_NO_USE_DEF;

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
  public function getName(){ return $this->name; }
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

  public static function getNewName(){
    return md5(mt_rand()).md5(mt_rand());
  }

  public static function load($name){
    $token = new self($name);
    if($token->isExpired())
      $token->delete();
    return $token->exists() ? $token : null;
  }

  public static function create(){

    do{
      $name = self::getNewName();
      $token = self::load($name);
    }while(isset($token));

    $token = new self($name);
    $token->save();

    return $token;

  }

  public function delete(){
    if($this->exists())
      return unlink($this->getFileName());
    return false;
  }

} 
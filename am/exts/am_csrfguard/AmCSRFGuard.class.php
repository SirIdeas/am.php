<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// Tomado de https://www.owasp.org/index.php/PHP_CSRF_Guard
// PENDIENTE Documentar
class AmCSRFGuard{

  const PrefixName = 'CSRFGuard_';
  const FieldnameName = 'CSRFName';
  const FieldnameToken = 'CSRFToken';

  public function __construct(){

    $this->session = Am::session('csrfguard');

  }

  public function generate(){
    
    $this->remove();
    $this->name = self::PrefixName.mt_rand(0,mt_getrandmax());
    $this->token = self::generateToken();

    $this->session[$this->name] = $this->token;

  }

  public function get($name){

    return $this->session[$name];

  }

  public function remove(){

    if(isset($this->name))
      unset($this->session[$this->name]);

  }

  public static function create(){

    $csrf = new self;
    $csrf->generate();
    return $csrf;

  }

  public static function generateToken(){

    if(function_exists('hash_algos') and in_array('sha512',hash_algos())){
      $token = hash('sha512',mt_rand(0,mt_getrandmax()));
    }else{
      $token = ' ';
      for($i = 0; $i<128; ++$i){
        $r = mt_rand(0, 35);
        if($r<26){
          $c = chr(ord('a')+$r);
        }else{ 
          $c = chr(ord('0')+$r-26);
        }
        $token.=$c;
      }
    }
    return $token;
  }

  public static function validate(array $params){

    $csrf = new self;

    $name = itemOr(self::FieldnameName, $params);

    $trueToken = $csrf->get($name);
    $formToken = itemOr(self::FieldnameToken, $params);

    if(!$trueToken)
      return false;

    $result = $trueToken === $formToken;
    $csrf->remove($name);

    return $result;

  }

}
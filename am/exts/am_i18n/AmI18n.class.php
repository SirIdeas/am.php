<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar
class AmI18n extends DateTime{

  protected static

    // Idioma a mostrar
    $lang = null,

    // Archivos cargados
    $filesLoades = array(),

    // Textos cargados
    $textos = array();

  public static function setLang($lang) {
    
    self::$lang = strtolower($lang);

  }

  public static function getLang() {
    
    return self::$lang;

  }

  protected static function load($file){
    
    $ret = array();
    $lines = explode("\n", file_get_contents('i18n/'.$file));

    foreach($lines as $line){
      $line = trim($line);
      if (!empty($line)) {
        $line = explode(' ', $line);
        $key = array_shift($line);
        $text = trim(implode(' ', $line));
        $ret[$key] = $text;
      }
    }

    return $ret;

  }

  public static function get($key, $lang = null){

    if(!isset($lang)) $lang = self::$lang;

    $filePath = explode('.', $key);
    $key = array_pop($filePath);
    $filePath = implode('/', $filePath).'.'.$lang;

    if (!isset(self::$textos[$filePath])) {
      self::$textos[$filePath] = self::load($filePath);
    }

    return itemOr($key, itemOr($filePath, self::$textos, array()), null);
    
  }

}

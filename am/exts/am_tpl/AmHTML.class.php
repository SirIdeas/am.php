<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE Documentar
class AmHTML{

  protected static $inlineTags = array(
    'link', 'input', 'br', 'hr',
  );

  public static function attrs(array $originalAttrs){

    $attrs = $originalAttrs;
    array_walk($attrs, function(&$value, $key) {
      $value = htmlentities($key).'="'.htmlentities($value).'"';
    });
    return implode(' ', $attrs);

  }

  public static function tag($tag, array $attrs, $inline = false){

    $attrs = self::attrs($attrs);
    $close = ($inline || in_array($tag, self::$inlineTags))? '/' : '';
    return "<{$tag} {$attrs}{$close}>";

  }

  public static function endt($tag){

    return "</{$tag}>";

  }

  public final static function __callStatic($tag, $args = null){

    $attrs = isset($args[0])? $args[0] : array();
    $inline = isset($args[1])? $args[1] : array();
    return self::tag($tag, $attrs, $inline);

  }

  public static function icon($href, $attrs = array(), $absolute = false){

    if(is_bool($attrs)){
      $absolute = $attrs;
      $attrs = array();
    }

    if(!$absolute)
      $href = Am::url($href);

    $attrs = array_merge($attrs, array(
      'rel' => 'shortcut icon',
      'href' => $href,
    ));

    return self::link($attrs);

  }

  public static function css($href, $attrs = array(), $absolute = false){

    if(is_bool($attrs)){
      $absolute = $attrs;
      $attrs = array();
    }

    if(!$absolute)
      $href = Am::url($href);

    $attrs = array_merge($attrs, array(
      'rel' => 'stylesheet',
      'href' => $href,
    ));

    return self::link($attrs);

  }

  public static function js($src, $attrs = array(), $absolute = false){

    if(is_bool($attrs)){
      $absolute = $attrs;
      $attrs = array();
    }

    if(!$absolute)
      $src = Am::url($src);

    $attrs = array_merge($attrs, array(
      'rel' => 'stylesheet',
      'src' => $src,
    ));

    return self::script($attrs).self::endt('script');

  }

}
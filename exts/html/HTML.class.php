<?php

class HTML{

  protected static
    $singlesTags = array("br", "input");

  protected
    $tag = "div",
    $attrs = array(),
    $content = null;

  public function __construct($tag = "div", $content = null, $attrs = array()){
    $this->tag = $tag;
    $this->attrs = $attrs;

    // Crear tag hijos
    if(is_array($content))
      foreach($content as $i => $value)
        $content[$i] = call_user_func_array(array(__CLASS__, "t"), $value);

    $this->content = $content;

  }

  public function __toString(){
    $attrs = $this->attrs;
    $tag = $this->tag;

    if(is_array($attrs)){
      foreach($attrs as $attr => $val)
        $attrs[$attr] = $attr.'="'.$val.'"';
      $attrs = implode(" ", $attrs);
    }

    if(is_array($this->content))
      $content = implode("", $this->content);
    else
      $content = $this->content;

    if(in_array($tag, self::$singlesTags))
      return "<{$tag} $attrs></{$tag}>";

    return "<{$tag} $attrs>{$content}</{$tag}>";

  }

  public static function t($tag = "div", $content = null, $attrs = array()){
    return new self($tag, $content, $attrs);
  }

}

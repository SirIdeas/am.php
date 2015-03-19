<?php

class HTML{

  protected static
    $singlesTags = array("br", "input");

  protected
    $tag      = "div",
    $attrs    = array(),
    $content  = null;

  public function __construct($tag = "div", $content = null, $attrs = array()){
    $this->tag = $tag;
    $this->c($content);
    $this->e($attrs);
  }

  public function c($content = null){

    // Crear tag hijos
    if(is_array($content))
      foreach($content as $i => $value)
        if(is_array($value))
          $content[$i] = call_user_func_array(array(__CLASS__, "t"), $value);

    $this->content = $content;

  }

  public function e($attrs = array()){
    $this->attrs = array_merge($this->attrs, $attrs);

  }

  public function r($content = null){

    if(is_array($this->content))
      $this->content[] = $content;

    else
      $this->content = $content;

    return (string)$this;

  }

  public function __toString(){

    $attrs = $this->attrs;
    $tag = $this->tag;

    if(is_array($attrs)){
      foreach($attrs as $attr => $val){
        $attrs[$attr] = $attr.'="'.$val.'"';}
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

function formTag($options){

  // Set default values
  $options = array_merge(
    array(
      "record"  => array(),
      "action"  => null,
      "method"  => "post",
      "fields"  => array(),
      "name"    => "form",
      "hide"    => array(),
    ),
    $options
  );

  $options["default"] = array_merge(
    array(
      "id"      => null,
      "name"    => "",
      "type"    => "text",
      "wrapper" => null,
      "label"   => null
    ),
    $options["default"]
  );

  // Get Record
  $record = itemOr("record", $options, array());

  unset($options["record"]);

  // Create Fields tags
  $fieldsTags = array();
  foreach($options["fields"] as $k => $field){
    if(in_array($k, $options["hide"])) continue;

    $field = array_merge($options["default"], $field);

    // Get id of field
    $field["id"]  = $options["name"]
                    .(empty($field["name"])?"":"_")
                    .itemOr("id", $field, $field["name"]);

    // Create or get tag for label
    if(is_string($field["label"])){
      $field["label"] = HTML::t("label", $field["label"], array(
        "for" => $field["id"]
      ));
    }elseif(is_array($field["label"])){
      $field["label"]["for"] = itemOr("for", $field["label"], $field["id"]);
      $field["label"] = HTML::t("label", $field["name"], $field["label"]);
    }elseif($field["label"] instanceof HTML){
      $field["label"] = clone $field["label"];
      $field["label"]->c($field["name"]);
      $field["label"]->e(array(
        "for" => $field["id"]
      ));
    }
    $label = $field["label"];

    // Get tag for label
    $wrapper = clone itemOr("wrapper", $field);

    // Get value of model
    if($record instanceof AmModel)
      $field["value"] = $record->getFieldValue($field["name"]);
    else
      $field["value"] = itemOr($field["name"], $record);

    // Set default values
    $field["name"]  = $options["name"]."[{$field["name"]}]";

    // Get type of field
    $field["type"]  = itemOr($field["type"], array(
      "integer"   => "number",
      "decimal"   => "number",
      "bit"       => "text",
      "timestamp" => "datetime",
      "year"      => "number",
      "char"      => "text",
      "varchar"   => "text",
    ), $field["type"]);

    unset($field["label"]);
    unset($field["wrapper"]);

    // Create tag for input
    $input = HTML::t("input", null, $field);

    // Add field to fieldTags
    if($wrapper instanceof HTML){
      if(isset($label))
        $wrapper->c(array($label, $input));
      else
        $wrapper->c($input);
    }else
      $wrapper = $input;

    $fieldsTags[] = $wrapper;

  }
  // Unset used fields
  unset($options["fields"]);
  unset($options["default"]);
  unset($options["hide"]);

  // Return tag of form
  return HTML::t("form", $fieldsTags, $options);

}

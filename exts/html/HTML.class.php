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

class HTML extends AmObject{

  protected static
    $singlesTags = array("br", "input");

  protected
    $tag      = "div",
    $attrs    = array(),
    $content  = null;

  public function __construct($tag = "div", $content = null, $attrs = array()){
    $this->tag = $tag;
    $this->setContent($content);
    $this->addAttrs($attrs);
  }

  public function setContent($content = null){

    // Crear tag hijos
    if(is_array($content))
      foreach($content as $i => $value)
        if(is_array($value))
          $content[$i] = call_user_func_array(array(__CLASS__, "t"), $value);
        else
          $content[$i] = (string)$content[$i];

    $this->content = $content;

  }

  public function addAttrs($attrs = array(), $rw = true){
    if($rw)
      $this->attrs = array_merge($this->attrs, $attrs);
    else
      $this->attrs = array_merge($attrs, $this->attrs);
  }

  public function render($content = null){
    $attrs = $this->attrs;
    $tag = $this->tag;

    if(is_array($attrs)){
      foreach($attrs as $attr => $val){
        $attrs[$attr] = $attr.'="'.$val.'"';}
      $attrs = implode(" ", $attrs);
    }

    if(is_array($this->content)){
      $content = implode("", $this->content).$content;
    }else{
      $content = $this->content.$content;
    }

    if(in_array($tag, self::$singlesTags))
      return "<{$tag} $attrs></{$tag}>";

    return "<{$tag} $attrs>{$content}</{$tag}>";

  }

  public function __toString(){
    return $this->render();
  }

  public static function merge($f1, $f2){
    if($f1 instanceof self){
      if($f2 instanceof self)
        return $f2;
      elseif(is_array($f2)){
        $f1->addAttrs($f2);
        return $f1;
      }
    }elseif(is_array($f1)){
      if($f2 instanceof self){
        $f2->addAttrs($f1, false);
        return $f2;
      }elseif(is_array($f2)){
        return array_merge($f1, $f2);
      }
    }elseif(is_array($f2)){
      return $f2;
    }
    return $f1;

  }

  public static function t($tag = "div", $content = null, $attrs = array()){
    return new self($tag, $content, $attrs);
  }

}


class HTMLFormField extends HTML{

  protected
    $form       = null,
    $wrapper    = null,
    $label      = null,
    $fieldName  = null,
    $attrs = array(
      "id"    => null,
      "name"  => "",
      "type"  => "text"
    );

  public function __construct($tag, HTMLForm $form, $fieldName, $wrapper = null, $label = null, $attrs = array()){
    $this->form = $form;
    $this->wrapper = $wrapper;
    $this->label = $label;
    $this->fieldName = $fieldName;
    $attrs = array_merge(
      $this->attrs,
      $attrs
    );

    $attrs["type"] = itemOr($attrs["type"], array(
      "integer"   => "number",
      "decimal"   => "number",
      "bit"       => "text",
      "timestamp" => "datetime",
      "year"      => "number",
      "char"      => "text",
      "varchar"   => "text",
    ), $attrs["type"]);

    parent::__construct($tag, null, $attrs);

  }

  public function render($content = null){

    if(isset($this->form))
      $this->addAttrs(array(
        "value" => $this->form->getRecordValue($this->fieldName)
      ));

    $input = parent::render($content);

    // Add field to fieldTags
    if($this->wrapper instanceof HTML){
      if(isset($this->label))
        $this->wrapper->setContent(array($this->label, $input));
      else
        $this->wrapper->setContent($input);
      $ret = (string)$this->wrapper;
    }else
      $ret = $this->label.$input;

    return $ret;

  }

}

class HTMLForm extends HTML{

  protected
    $record   = array(),    // Objeto con los datos
    $fields   = array(),    // Campos del formulario
    $hides    = array(),    // Campos ocultos
    $head     = null,        // Cabecera del formulario
    $foot     = null,        // Footer del formulario
    $attrs    = array(
      "action"  => null,
      "method"  => "post",
      "name"    => "form",
    ),
    $defaults = array();    // Valores por defecto para los campos

  public function __construct($options, array $attrs = array()){

    // Asignar valores por defecto que no se pueden inicializar en el atributo
    $this->extend(array(
      "foot" => HTML::t("button", "Enviar", array(
        "type" => "submit"
      ))
    ));

    // If 1r param is a model take it table.
    if($options instanceof AmModel)
      $options = array(
        "record" => $options,
        "fields" => $options->getTable()
      );

    // If 1r param is a table take its fields
    if($options instanceof AmTable)
      $options = array(
        "fields" => (array)$options->getFields()
      );

    $options["record"] = itemOr("record", $options);
    $options["fields"] = itemOr("fields", $options);

    // If 1r param is a model take it table.
    if($options["record"] instanceof AmModel && !isset($options["fields"]))
      $options["fields"] = $options["record"]->getTable();

    // If 1r param is a model take it table.
    if($options["fields"] instanceof AmTable)
      $options["fields"] = (array)$options["fields"]->getFields();

    // Para mezcar los atributos enviatos por los options, con los
    // valores por defecto y los recibidos por paraemtro
    $options["attrs"] = array_merge(
      $this->attrs,
      itemOr("attrs", $options, array()),
      $attrs
    );

    // El tag siempre será un formulario
    $options["tag"] = "form";

    // Extender las opciones
    $this->extend($options);

  }

  // Agregar un campo a los campos ocultos
  public function hide($fieldName){
    $this->hide[] = $fieldName;
  }

  // Convierte un campo en un HTMLFormField
  public function parseField($field){

    // Si es oculto o si ya es una instancia del HTMLFormField ignorar
    if($field instanceof HTMLFormField)
      return $field;

    // Si es una instancia de un AmField convertir en array y tomar solo
    // el nombre, el label y el tipo
    if($field instanceof AmField)
      $field = array(
        "name" => $field->getName(),
        "label" => $field->getName(),
        "type" => $field->getType(),
      );

    if(!is_array($field))
      return $field;

    // Obtener el label
    $field["label"] = HTML::merge(
      itemOr("label", $this->defaults),
      itemOr("label", $field)
    );

    // Obtener el wrapper
    $field["wrapper"] = HTML::merge(
      itemOr("wrapper", $this->defaults),
      itemOr("wrapper", $field)
    );

    // Mezclar los datos con los valores por defecto de los campos
    $field = array_merge(
      $this->defaults,
      $field
    );

    // get any attrs
    $formName   = itemOr("name", $this->attrs);
    $fieldName  = itemOr("name", $field);
    $formId     = itemOr("id", $this->attrs, $formName);
    $fieldId    = itemOr("id", $field, $fieldName);

    // Set ID and name
    $field["id"]    = $labelFor = $formId.(empty($formId)?"":"_").$fieldId;
    $field["name"]  = "{$formName}[{$fieldName}]";

    $label = null;
    // Create or get tag for label
    if(is_string($field["label"])){
      $fieldName      = $field["label"];
      $label = HTML::t("label");

    }elseif(is_array($field["label"])){
      $labelFor       = itemOr("for", $field["label"], $field["id"]);
      $label = HTML::t("label", null, $field["label"]);

    }elseif($field["label"] instanceof HTML){
      $label = clone $field["label"];
    }

    // Set content and for of label
    if(isset($label)){
      $label->setContent($fieldName);
      $label->addAttrs(array("for" => $labelFor));
    }

    // Get tag for label
    $wrapper = itemOr("wrapper", $field);
    if($wrapper instanceof HTML)
      $wrapper = clone $wrapper;

    unset($field["label"]);
    unset($field["wrapper"]);

    return new HTMLFormField("input", $this, $fieldName, $wrapper, $label, $field);

  }

  // Renderizar el formulario
  public function parseFields(){

    $content = array();

    // Agregar la cabecera
    if(isset($this->head))
      $content[] = $this->head;

    // Recorrer los campos
    foreach($this->fields as $k => $field){
      $this->fields[$k] = $this->parseField($field);
      if(!in_array($k, $this->hides))
        $content[] = $this->fields[$k];
    }

    // Agregar el footer
    if(isset($this->foot))
      $content[] = $this->foot;

    $this->setContent($content);

  }

  // Get de value or record
  public function getRecordValue($fieldName){

    // Get value of model
    if($this->record instanceof AmModel)
      return $this->record->getFieldValue($fieldName);

    // Get value of class
    if($this->record instanceof stdClass)
      return isset($this->record->$fieldName)? $this->record->$fieldName : null;

    // Get value os array
    if(is_array($this->record))
      return itemOr($fieldName, $this->record);

    return null;

  }

  public function render($content = null){
    $this->parseFields();
    return parent::render($content);
  }

}

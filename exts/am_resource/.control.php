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

return array(

  //////////////////////////////////////////////////////////////////////////////
  // Parametros del recursos
  "model" => null,

  //////////////////////////////////////////////////////////////////////////////
  // Url del index del controlador
  "url" => null,

  //////////////////////////////////////////////////////////////////////////////
  // Campos para los formularios y para los detalles
  "fields" => array(),

  // ---------------------------------------------------------------------------
  "fieldNames" => array(),

  //////////////////////////////////////////////////////////////////////////////
  // Columnas dle lista del index
  "columns" => array(),

  //////////////////////////////////////////////////////////////////////////////
  // Campos a ocultar en el formulario
  "hides" => array(),

  //////////////////////////////////////////////////////////////////////////////
  // Configuracion del formulario
  "formConf" => array(
    "attrs" => array(
      "role" => "form",
    ),
    "wrapper" => HTML::t("div", null, array(
      "class" => "text-center"
    )),
    "defaults"  => array(
      "label"   => HTML::t("label"),
      "wrapper" => HTML::t("div"),
      "required" => ""
    ),
    "foot" => HTML::t("div",
      HTML::t("button", "Enviar",
        array("type" => "submit"),
        array()
      )
    )
  ),

  // Filtros
  "filters" => array(
    "before" => array(
      "loadRecord" => array(
        "scope" => "only",
        "to" => array("detail", "edit", "delete")
      )
    )
  ),

  //////////////////////////////////////////////////////////////////////////////
  // Acciones permitidas
  "allow" => array(
    "list"      => true,
    "new"       => true,
    "detail"    => true,
    "edit"      => true,
    "delete"    => true,
    "options"   => true,
  ),

  //////////////////////////////////////////////////////////////////////////////
  // Asegurar los valores para los siguientes metodos
  "views" => "views",
  "prefixs" => array(
    "actions"     => "action_",
    "getActions"  => "get_",
    "postActions" => "post_",
    "filters"     => "filter_",
  ),

);

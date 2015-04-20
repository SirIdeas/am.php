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

  // Entorno global
  "env" => array(
    "siteName" => "Sir Ideas | Gestión"
  ),

  "routes" => array(

    /////////////////////////////////////////////////////////////////////////////////
    // Paginas Simples
    /////////////////////////////////////////////////////////////////////////////////

    // Llamar a una funcion
    array(
      "route" => "/template",
      "template" => "control/views/index.view.php"
    ),

    "/template2" => "template => control/views/index.view.php",

    // Responder con archivo
    array(
      "route" => "/file",
      "file" => "control/views/index.view.php"
    ),

    // Responder con descarga de archivo
    array(
      "route" => "/download",
      "download" => "control/views/index.view.php"
    ),

    // Responder con archivo
    array(
      "route" => "/assets",
      "assets" => "styles/styles.css"
    ),

    // Rediriguir a algun lugar
    array(
      "route" => "/redirect",
      "redirect" => "/redirect2"
    ),

    // Rediriguir a algun lugar
    array(
      "route" => "/gotourl",
      "goto" => "http://google.com"
    ),
    array(
      "route" => "/function",
      "control" => "hola"
    ),

    // Llamar metodo estatico de una clase
    array(
      "route" => "/static_method",
      "control" => "A::hola"
    ),

    // Llamar controlador
    array(
      "route" => "/controlador",
      "control" => "Index@index2"
    ),

    // Buscar en rutas internas
    array(
      "route" => "/admin/",
      "app" => "control/admin/"
    ),

    // Ruta anidadas
    array(
      "route" => "/admin2/",
      "control" => "Index@",
      "routes" => array(
        array(
          "route" => "action1",
          "control" => "action1"
        ),
        array(
          "route" => "action2",
          "control" => "action2"
        )
      )
    ),

    // Carpeta publica
    array(
      "route" => "/:file(".implode("|", array(
        "sitemap\.xml",
        "favicon\.ico",
        "robots\.txt",
        "vendor/.*",
        "images/.*",
        "fonts/.*",
        "css/.*",
        "js/.*"
      )).")",
      "file" => "public/:file"
    ),

    // Assets
    array(
      "route" => "/:file(".implode("|", array(
        "css/.*",
        "js/.*"
      )).")",
      "assets" => ":file"
    ),


    /////////////////////////////////////////////////////////////////////////////////
    // Especiales
    /////////////////////////////////////////////////////////////////////////////////

  )

);

<?php

function hola(){
  echo "hola";
}

class A{

  static function hola(){
    echo "hola2";
  }
}

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

<?php

return array(

  // Entorno global
  "env" => array(
    "title" => "Amathista Template"
  ),

  "routes" => array(

    // Carpeta publica
    "/:file(".implode("|", array(
      "sitemap\.xml",
      "favicon\.ico",
      "robots\.txt",
      "vendor/.*",
      "images/.*",
      "fonts/.*",
      "css/.*",
      "js/.*"
    )).")"
    => "file => public/:file",

  )

);

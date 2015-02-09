<?php

return array(

  "init" => array(
    "control" => array(
      
      "defaults" => array(

        "root" => "control/", // Carpeta raiz del controlador
        "views" => "views/",  // Carpeta por defecto para las vistas
        "paths" => array(),   // Carpetas de vistas del controlador

        // Prefixs for each method types. Always is merge with parent configuration
        "prefixs" => array(
          "actions" => "action_",
          "getActions" => "get_",
          "postActions" => "post_",
          "filters" => "filter_",
        ),

      ),

    ),
  ),

  "files" => array(
    "AmControl.class"
  ),

  "mergeFunctions" => array(
    "control" => "array_merge_recursive",
  )
  
);
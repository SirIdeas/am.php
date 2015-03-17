<?php

$__DIR__ = dirname(__FILE__);

return array(
  // Rutas fisicas
  "concat" => array(
    // Target
    "public" => array(
      "public/vendor/vendor.css" => array(
        // Archivos fisicos
         $__DIR__."/../bower_components/bootstrap/dist/css/bootstrap.min.css"
      ),
      "public/vendor/ie-fixs.js" => array(
        "{$__DIR__}/../bower_components/es5-shim/es5-shim.js",
        "{$__DIR__}/../bower_components/json3/lib/json3.min.js"
      ),
      "public/vendor/vendor.js" => array(
        $__DIR__."/../bower_components/jquery/dist/jquery.min.js",
        $__DIR__."/../bower_components/bootstrap/dist/js/bootstrap.min.js"
      )
    )
  ),
);

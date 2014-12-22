<?php

/**
 * Configuracion de carga inicial
 */

return array(
  "control" => array(
    "path" => "control/",
    "views" => "views/",
  ),
  "requires" => array(
    "core/AmObject.class",
    "exts/AmDateTime.class",
    "exts/AmRoute.class",
    "exts/AmTemplate.class",
    "exts/AmAsset.class",
    "exts/AmCommand.class",
    "exts/AmControl.class",
  ),
);

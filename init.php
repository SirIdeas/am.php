<?php

define("AM_VERSION", "0.1.0-beta");
// Version de Amathista

// Incluir núcleo de framework
// require dirname(__FILE__) . "/core/errors.php";
require dirname(__FILE__) . "/core/helpers.php";
require dirname(__FILE__) . "/core/Am.class.php";
require dirname(__FILE__) . "/core/AmObject.class.php";

Am::addDir(dirname(__FILE__) . "/"); // Agregar ruta del nucle de amathista

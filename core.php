<?php

// Si la constante AM_FOLDER está definida entonces generar error
!defined("AM_FOLDER") or die("Am: Alreadys defined \"AM_FOLDER\"");

// Definir constante con la carpeta contenedora framework
define("AM_FOLDER", dirname(__FILE__) . "/");

// Incluir núcleo de framework
require AM_FOLDER . "/am.php";

amLoader(AM_FOLDER . "/exts/am/");


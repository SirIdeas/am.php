<?php

return array(

  "errorReporting" => E_ALL,    // Indicar que errores se mostrarán
  
  "sessionManager" => "normalSession", // MAnejador de session

  "requires" => array(
    "exts/am_route/",
    "exts/am_resource/",
    "exts/am_data_time/",
    "exts/am_template/",
    "exts/am_mailer/",
    "exts/am_flash/",
    "exts/am_credentials/",
  ),


);

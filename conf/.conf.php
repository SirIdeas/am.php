<?php

/**
 * Configuracion de carga inicial
 */

return array(

  "errorReporting" => E_ALL,      // Indicar que errores se mostrarán
  
  "requires" => array(
    "exts/am_data_time/",
    "exts/am_route/",
    "exts/am_asset/",
    "exts/am_template/",
    "exts/am_command/",
    "exts/am_control/",
    "exts/am_mailer/",
    "exts/am_flash/",
    "exts/am_orm/",
    // "exts/am_credentials/",
  ),

);

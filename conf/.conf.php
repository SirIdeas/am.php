<?php

/**
 * Configuracion de carga inicial
 */

return array(

  "errorReporting" => E_ALL,      // Indicar que errores se mostrarán

  "session" => null,              // ID para variables de sesion
  "smtp" => array(),              // Configuraciones SMTP
  "mails" => array(),             // Configuraciones de los mails
  "sources" => array(),           // Configuraciones de las fuentes de datos
  "validators" => array(),        // Configuraciones de las validaciones

  "requires" => array(
    "exts/am_data_time/",
    "exts/am_route/",
    "exts/am_template/",
    "exts/am_asset/",
    "exts/am_command/",
    "exts/am_control/",
  ),

);

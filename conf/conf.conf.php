<?php

/**
 * Configuracion de carga inicial
 */

return array(

  "errorReporting" => E_ALL,      // Indicar que errores se mostrarán

  "conf" => array(),              // Define las cargas iniciales. Archivo conf/routes.conf.php
  "requires" => array(),          // Archivos a incluir en el arranque
  "routes" => array(              // Rutas
    "env" => array(),
    "routes" => array()
  ),
  "assets" => array(),            // Archivos de recursos
  "timezone" => null,             // Zona horario
  "session" => null,              // ID para variables de sesion
  "commands" => array(),          // Target para los comandos
  "control" => array(),           // Definiciones de controladores
  "smtp" => array(),              // Configuraciones SMTP
  "mails" => array(),             // Configuraciones de los mails
  "sources" => array(),           // Configuraciones de las fuentes de datos
  "validators" => array(),        // Configuraciones de las validaciones

  "requires" => array(
    "core/AmObject.class",
    "exts/am_data_time/AmDateTime.class",
    "exts/am_route/AmRoute.class",
    "exts/am_template/AmTemplate.class",
    "exts/am_asset/",
    "exts/am_command/AmCommand.class",
    "exts/am_control/AmControl.class",
  ),

);

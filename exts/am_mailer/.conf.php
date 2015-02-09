<?php

return array(
  
  "init" => array(
    "smtp" => false,
    "mails" => array(
      // Configuración por defecto par ael envio de correo
      "defaults" => array(
        "dir" => "mails/",
      )
    ),
  ),

  "files" => array(
    "php_mailer/PHPMailerAutoload",
    "AmMailer.class",
  ),

  "mergeFunctions" => array(
    "smtp" => "merge_r_if_are_array",
    "mails" => "array_merge_recursive",
  )

);
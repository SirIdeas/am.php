<?php

return array(
  
  "mails" => array(
    // Configuración por defecto par ael envio de correo
    "default" => array(
      "dir" => "mails/",
    )
  ),

  "files" => array(
    "php_mailer/PHPMailerAutoload",
    "AmMailer.class",
  ),

  "mergeFunctions" => array(
    "smtp" => "array_merge_recursive",
    "mails" => "array_merge_recursive",
  )

);
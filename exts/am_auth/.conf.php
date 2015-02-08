<?php

return array(
  
  "files" => array(
    "AmCredentials.class"
    "AmCredentialsHandler.class"
  ),

  "requires" => array(
    "exts/am_session/",
  ),

  "mergeFunctions" => array(
    "credentials" => "array_merge_recursive",
  )

);
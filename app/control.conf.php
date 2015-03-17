<?php

return array(

  "defaults" => array(
    "prefixs" => array(
      "actions"     => "action_",
      "getActions"  => "get_",
      "postActions" => "post_",
      "filters"     => "filter_",
    ),
    "paths" => array(     // Carpetas de vistas del controlador
      dirname(__FILE__) . "/views/",
    ),
  ),

);

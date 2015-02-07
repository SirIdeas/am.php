<?php

return array(

  "control" => array(
    "defaults" => array(
      "path" => "control/",
      "views" => "views/",
      
      // Prefixs for each method types. Always is merge with parent configuration
      'prefixs' => array(
        'actions' => 'action_',
        'getActions' => 'get_',
        'postActions' => 'post_',
        'filters' => 'filter_',
      ),

    ),
    

  ),

  "files" => array(
    "AmControl.class"
  ),

  "mergeFunctions" => array(
    "control" => "array_merge_recursive",
  )
  
);
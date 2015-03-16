<?php

return array(

  "extend" => array(
    "routes" => array(
      "route" => "/vendor/dinamictable/:file",
      "file" => dirname(__FILE__) . "/dist/:file",
    )
  ),


  "files" => array(
    "dinamicTableServer"
  ),

  "requires" => array(
    "exts/am_orm/",
  ),

);

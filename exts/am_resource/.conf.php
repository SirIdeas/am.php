<?php

return array(

  "extend" => array(
    "control" => array(
      "AmResource" => dirname(__FILE__) . "/",
    ),
  ),

  "requires" => array(
    "exts/am_control/",
    "exts/am_orm/",
    "exts/dinamic_table/",
  ),

);

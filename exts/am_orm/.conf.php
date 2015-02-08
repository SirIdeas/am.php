<?php

return array(

  "init" => array(
    "sources" => array(),
    "validators" => array(),
  ),
  
  "files" => array(
    "AmORM.class",
    "AmField.class",
    "AmTable.class",
    "AmModel.class",
    "AmRelation.class",
    "AmQuery.class",
    "AmSource.class",
    "AmValidator.class",
  ),
  
  "requires" => array(
    "exts/am_coder/",
  ),

  "mergeFunctions" => array(
    "sources" => "array_merge_recursive",
    "validators" => "array_merge_recursive",
  )
);

<?php

return array(

  "extend" => array(
    "models" => array(
      "defaults" => array(
        "root" => "model/",
        "models" => array()
      )
    )
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

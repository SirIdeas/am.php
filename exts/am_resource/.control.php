<?php

return array(

  //////////////////////////////////////////////////////////////////////////////
  // Parametros del recursos
  "model" => null,

  // ---------------------------------------------------------------------------
  "columns" => array(),
  "fields" => array(),
  "fieldNames" => array(),

  // Filtros
  "filters" => array(
    "before" => array(
      "loadRecord" => array(
        "scope" => "only",
        "to" => array("detail", "edit", "remove")
      )
    )
  ),

  // Acciones permitidas
  "allow" => array(
    "list"      => true,
    "new"       => true,
    "detail"    => true,
    "edit"      => true,
    "remove"    => true,
    "options"   => true,
  ),

  //////////////////////////////////////////////////////////////////////////////
  // Asegurar los valores para los siguientes metodos
  "views" => "views",
  "prefixs" => array(
    "actions"     => "action_",
    "getActions"  => "get_",
    "postActions" => "post_",
    "filters"     => "filter_",
  ),

);

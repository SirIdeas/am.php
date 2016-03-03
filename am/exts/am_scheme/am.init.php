<?php

// Crea las tablas si no existen
Am::on('core.loadedClass', function($className){

  // Si es un modelo
  if(is_subclass_of($className, 'AmModel')){

    // PENDIENTE: la variable $autoMigrate no puede ser static
    // Si se asignó la variable $autoMigrate en true
    if($className::$autoMigrate){

      // Por los momentos se creará la tabla si no existe
      $className::me()->create();

    }

  }

});
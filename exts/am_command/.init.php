<?php

// Agregar ruta para atender peticiones por consola
Am::setRoute(":arguments(am\.php/.*)", "AmCommand::asTerminal");

// Agregar ruta para atender petidicones HTTP
Am::setRoute("/:arguments(am-command/.*)", "AmCommand::asRequest");
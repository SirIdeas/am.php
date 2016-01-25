<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

 /**
 * -----------------------------------------------------------------------------
 * Configuración de eventos globales que atenderá la extensión.
 * -----------------------------------------------------------------------------
 */

// Atender las respuestas por controlador
Am::on('response.controller', 'AmController::response');

// Agregar el preprocesador de rutas a las que no tienen tipo
Am::addRoutePreProcessor('', 'AmController::routePreProcessor');
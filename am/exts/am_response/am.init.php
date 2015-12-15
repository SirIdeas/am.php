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
Am::on('response.file',     'AmResponse::file');
Am::on('response.call',     'AmResponse::call');
Am::on('response.go',       'AmResponse::go');
Am::on('response.template', 'AmResponse::template');
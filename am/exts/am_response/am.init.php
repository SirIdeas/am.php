<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Configuración de eventos globales que atenderá la extensión.
 */
Am::on('response.file',     'AmResponse::file');
Am::on('response.call',     'AmResponse::call');
Am::on('response.template', 'AmResponse::template');
Am::on('response.go',       'AmResponse::go');
Am::on('response.assets',   'AmResponse::assets');
Am::on('response.e404',     'AmResponse::e404');
Am::on('response.e403',     'AmResponse::e403');
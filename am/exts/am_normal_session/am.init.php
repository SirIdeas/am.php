<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// Se debe iniciar la sesion
session_start();

// Asignar callbacks
Am::on('session.all', 'AmNormalSession::all');
Am::on('session.get', 'AmNormalSession::get');
Am::on('session.set', 'AmNormalSession::set');
Am::on('session.has', 'AmNormalSession::has');
Am::on('session.delete', 'AmNormalSession::delete');
Am::on('session.id', 'AmNormalSession::id');

// Asignar id de la sesion
Am::emit('session.id', Am::getProperty('id'));


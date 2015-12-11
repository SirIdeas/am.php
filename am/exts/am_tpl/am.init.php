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
// Atender el llamado a renderizaa vistas
Am::on('render.template', 'AmTpl::renderize');

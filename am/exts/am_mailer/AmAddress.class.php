<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar

// Interfaz para clases que puedan enviar y recibir correso
interface AmAddress{
  public function getMail();
  public function getName();
}
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */
 
/**
 * Interfaz para clases que serviran para la verificacion de
 * La autenticación
 */
// PENDIENTE Documentar
interface AmCredentials {

  // Pregunta si el usuario autenteicado tiene
  // una determinada credencial.
  public function hasCredential($credential);

  // Devuelve el identidicador único del usuario
  public function getCredentialsId();

  // Devuelve la instancia de un usuario apartir de su
  // identificador unico.
  public static function getCredentialsInstance($crendentialId);

  // Autentica un usuario por nombre y passsword
  public static function auth($nick, $password);

}

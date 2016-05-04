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

  // Devuelve el login único del usuario
  public function getCredentialsEmail();

  // Resetea la contraseña
  public function resetPasword($pass);

  // Devuelve la instancia de un usuario apartir de su
  // identificador unico.
  public static function getCredentialsInstance($crendentialId);

  // Devuelve un registro a partir de su login
  public static function getByLogin($login);

  // Autentica un usuario por nombre y passsword
  public static function auth($nick, $password);

  // Registra un usuario
  public static function register(AmCredentials $user, array $attrs);
  
}

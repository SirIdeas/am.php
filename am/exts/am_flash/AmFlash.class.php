<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar
/**
 * Clase para el manejo de mensajes mensajes flash.
 * Los mensajes Flash son mesajes que guardados en 
 * session para mantenerse entre una peticion y otra.
 * Los mensajes son guardados por tipos
 */
final class AmFlash{

  protected static $session = null;

  protected final static function session($varname = null, $def = null){
    
    // Obtener la sessión del sistema
    if(!isset(self::$session))
      self::$session = Am::session('flash');

    // retornar la sessión
    if(!isset($varname))
      return self::$session;

    // Se pidió una variable la sessión
    return itemOr($varname, self::$session[$varname], $def);

  }
  
  // Obtener todos los mensajes
  public final static function all(){
    
    $session = self::session();
    $ret = $session['flash'];

    // Eliminar los mensajes de session
    unset($session['flash']);

    // Retornar los mensajes
    return $ret? $ret : array();
    
  }

  // Devuelve los mensajes flash de un tipo especifico
  public final static function get($index){
    
    // Obtener la sesión
    $session = self::session();

    // Obtener todos los mensajes flash
    $flash = $session['flash'];
    
    // Si esta definida la lista de mensajes solicitada
    if(isset($flash[$index])){
      
      //Obtener la lista de mensajes
      $ret = $flash[$index];

      // Eliminarlos se sesion
      unset($flash[$index]);

      $session['flash'] = $flash;

      // Retornar los obtenido
      return $ret;

    }
    
    return array();

  }

  // Agrega un mensaje a los Flash
  public final static function add($index, $message){
    
    // Obtener la sesión
    $session = self::session();

    // Obtener todos los mensajes flash
    $flash = $session['flash'];
    
    // Si no ha sido iniciada se inicializa
    if(!$flash)
      $flash = array();

    // Si no ha sido inicializada la lista de mensajes
    // para el tipo $index, entonces se inicializa
    if(!isset($flash[$index]))
      $flash[$index] = array();

    // Se agrega el mensaje
    $flash[$index][] = $message;

    // Guardar arra modificado en en sesion 
    $session['flash'] = $flash;

  }

  // Funcion comun para obtener/agregar cada tipo de mensaje
  public final static function __callStatic($name, $arguments = null){

    if(!empty($arguments))
      return self::add($name, $arguments[0]);
    return self::get($name);

  }

}

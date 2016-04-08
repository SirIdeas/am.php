<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

// PENDIENTE documentar
final class AmSession{
  
  // Devuelve un array con todas las variables de sesion
  public final static function all(){

    return Am::emit('session.all');
    
  }

  // Devuelve el contenido de una variable de sesion
  public final static function get($index){

    return Am::emit('session.get', $index);
    
  }

  // Indica si existe o no una variable de sesion
  public final static function has($index){

    return Am::emit('session.has', $index);
    
  }

  public final static function set($index, $value){

    return Am::emit('session.set', $index, $value);
    
  }
  
  // Elimina una variable de la sesion
  public final static function delete($index){

    return Am::emit('session.delete', $index);
    
  }
  
  // Asigna una ID de sesion
  public final static function id($id){

    return Am::emit('session.id', $id);
    
  }

}

<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

/**
 * Clase principal de Amathista
 */

final class AmNormalSession{

  // Asigna una ID de sesion
  public final static function id($sessionId){

    self::$sessionId = $sessionId;

    // Crear contendor de la sesion
    if(!isset($_SESSION[$sessionId]))
      $_SESSION[$sessionId] = array();

  }

  protected static
    $sessionId; // ID de la sesion

  // Devuelve un array con todas las variables de sesion
  public final static function all(){
    return $_SESSION[self::$sessionId];
  }

  // Devuelve el contenido de una variable de sesion
  public final static function get($index){
    return self::has($index) ? unserialize($_SESSION[self::$sessionId][$index]) : null;
  }

  // Indica si existe o no una variable de sesion
  public final static function has($index){
    return isset($_SESSION[self::$sessionId][$index]);
  }

  public final static function set($index, $value){
    $_SESSION[self::$sessionId][$index] = serialize($value);
  }

  // Elimina una variable de la sesion
  public final static function delete($index){
    unset($_SESSION[self::$sessionId][$index]);
  }

}

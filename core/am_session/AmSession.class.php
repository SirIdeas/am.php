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

final class AmSession{

  // Devuelve un array con todas las variables de sesion
  public final static function all(){
    return Am::call("session.all");
  }

  // Devuelve el contenido de una variable de sesion
  public final static function get($index){
    return Am::call("session.get", $index);
  }

  // Indica si existe o no una variable de sesion
  public final static function has($index){
    return Am::call("session.has", $index);
  }

  public final static function set($index, $value){
    return Am::call("session.set", $index, $value);
  }

  // Elimina una variable de la sesion
  public final static function delete($index){
    return Am::call("session.delete", $index);
  }

  // Asigna una ID de sesion
  public final static function id($id){
    return Am::call("session.id", $id);
  }

}

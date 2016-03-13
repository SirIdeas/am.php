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
 * Clase para atender peticiones de archivos virtuales/compuestos
 */

class AmAsset{

  protected
    $file   = null, // Nombre virtual del archivo compuesto
    $assets = null; // Lista de archivos que lo componen

  // Constructor. Recibe el nombre del archivo y la lista de archivos que lo componen
  public function __construct($file, array $assets){
    $this->file = $file;
    $this->assets = $assets;
  }

  // Obtener le contenido del archivo virtual
  public function getContent(){

    // Concatenar el contenido de los archivos configurados
    $content = '';
    foreach($this->assets as $asset){

      // Si el archivo existe entonces concatenar
      if(is_file($asset)){
        $content .= "\n".file_get_contents($asset);
      // Mostrar error
      }else{}

    }
    // Retornar todo lo contatenado
    return $content;
  }

  // Devuelve el mime-type del archivo basandose en el nombre virtual
  public function getMimeType(){
    return Am::mimeType($this->file);
  }

  // Renderiza el archivo virtual imprimiendo el contenido
  public function render(){
    header("content-type: {$this->getMimeType()}");
    echo $this->getContent();
  }

  // Funcion para atender la llamada de archivos virtuales compuestos
  public static function response($file){

    // Obtener los recursos configurados
    $assets = Am::getProperty('assets', array());

    // Si no exite un recurso con el nombre del solicitado retornar falso
    if(!isset($assets[$file]))
      return false;

    // Instanciar archivo
    $asset = new self($file, $assets[$file]);

    // Responder
    $asset->render();

    return true;

  }

}

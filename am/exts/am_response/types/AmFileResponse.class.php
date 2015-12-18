<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para crear respuestas con archivos
 * -----------------------------------------------------------------------------
 */

class AmFileResponse extends AmResponse{

  protected

    /**
     * -------------------------------------------------------------------------
     * Propiedades de la petición.
     * -------------------------------------------------------------------------
     */
    $__p = array(

      // Cabeceras iniciales para responder con archivo
      'headers' => array(
        'Content-Transfer-Encoding' => 'Content-Transfer-Encoding: binary',
        'Expires' => 'Expires: 0',
        'Cache-Control' => 'Cache-Control: must-revalidate',
        'Pragma' => 'Pragma: public',
      ),

      // Ruta del archivo que se devolverá.
      'filename' => null,

      // Tipo MIME de la respuesta.
      // Si no se asigna s determina el tipo MIME del archivo a devolver.
      'mimeType' => null,

      // Nombre del archivo que se responderá.
      // Si no se asigna se toma el basename del archivo a devolver.
      'name' => null,

      // Si el archivo es adjunto o no.
      // Determina si el archivo se descarga o se intenva ver desde el
      //  explorador.
      'attachment' => false,

    );

  /**
   * ---------------------------------------------------------------------------
   * Asignar el archivo a descargar
   * ---------------------------------------------------------------------------
   * @param  string   $filename   Ruta del archivo a devolver.
   * @return this
   */
  public function filename($filename){
    $this->__p->filename = $filename;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Asignar si se descarga o no el archivo.
   * ---------------------------------------------------------------------------
   * @param  bool   $attachment   Si se descarga o no el archivo.
   * @return this
   */
  public function attachment($attachment = true){
    $this->__p->attachment = $attachment;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Asignar tipo MIME
   * ---------------------------------------------------------------------------
   * @param  bool   $mimeType   Tipo MIME a asignar
   * @return this
   */
  public function mimeType($mimeType){
    $this->__p->mimeType = $mimeType;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si la petición se puede resolver o no.
   * ---------------------------------------------------------------------------
   * Se sobreescribe el método para saber si el archivo existe o no.
   * @return  boolean   Indica si la petición se puede resolver o no.
   */
  public function isResolved(){
    return parent::isResolved() && is_file($this->__p->ilename);
  }

  /**
   * ---------------------------------------------------------------------------
   * Acción de la respuesta: Leer el archivo
   * ---------------------------------------------------------------------------
   * @return  AmResponse  Si el archivo que se intenta devolver no existe 
   *                      se devuelve una respuesta 404. De lo contario retorna
   *                      null
   */
  public function make(){

    // Si el archivo no existe retornar error 404
    if(!$this->isResolved())
      return Am::e404(Am::t('AMRESPONSE_FILE_NOT_FOUND', $this->__p->filename));

    // Determinar el tipo MIME
    if(isset($this->__p->mimeType))
      $mimeType = $this->__p->mimeType;
    else
      $mimeType = Am::mimeType($this->__p->filename);

    // Determinar si se descarga o no el archivo
    if(isset($this->__p->name))
      $name = $this->__p->name;
    else
      $name = basename($this->__p->filename);

    $attachment = $this->__p->attachment ? ' attachment;' : '';

    // Agregar cabeceras
    $this->addHeader("Content-Disposition:{$attachment} filename=\"{$name}\"");
    $this->addHeader('Content-Length: ' . filesize($this->__p->filename));
    $this->addHeader("Content-Type: {$mimeType}");

    parent::make();

    // Leer archivo
    readfile($this->__p->filename);

  }

}
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
     * Cabeceras iniciales para responder con archivo
     * -------------------------------------------------------------------------
     */
    $headers = array(
      'Content-Transfer-Encoding: binary',
      'Expires: 0',
      'Cache-Control: must-revalidate',
      'Pragma: public',
    ),

    /**
     * -------------------------------------------------------------------------
     * Ruta del archivo que se devolverá.
     * -------------------------------------------------------------------------
     */
    $filename = null,

    /**
     * -------------------------------------------------------------------------
     * Tipo MIME de la respuesta.
     * -------------------------------------------------------------------------
     * Si no se asigna s determina el tipo MIME del archivo a devolver.
     */
    $mimeType = null,

    /**
     * -------------------------------------------------------------------------
     * Nombre del archivo que se responderá.
     * -------------------------------------------------------------------------
     * Si no se asigna se toma el basename del archivo a devolver.
     */
    $name = null,

    /**
     * -------------------------------------------------------------------------
     * Si el archivo es adjunto o no.
     * -------------------------------------------------------------------------
     * Determina si el archivo se descarga o se intenva ver desde el explorador.
     */
    $attachment = false;

  /**
   * ---------------------------------------------------------------------------
   * Asignar el archivo a descargar
   * ---------------------------------------------------------------------------
   * @param  string   $filename   Ruta del archivo a devolver.
   * @return this
   */
  public function filename($filename){
    $this->filename = $filename;
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
    $this->attachment = $attachment;
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
    $this->mimeType = $mimeType;
    return $this;
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
    if(!is_file($this->filename))
      return AmResponse::e404();

    // Determinar el tipo MIME
    if(isset($this->mimeType))
      $mimeType = $this->mimeType;
    else
      $mimeType = Am::mimeType($this->filename);

    // Determinar si se descarga o no el archivo
    if(isset($this->name))
      $name = $this->name;
    else
      $name = basename($this->filename);

    $attachment = $this->attachment ? ' attachment;' : '';

    // Agregar cabeceras
    $this->addHeader("Content-Disposition:{$attachment} filename=\"{$name}\"");
    $this->addHeader('Content-Length: ' . filesize($this->filename));
    $this->addHeader("Content-Type: {$mimeType}");

    parent::make();

    // Leer archivo
    readfile($this->filename);

  }

}
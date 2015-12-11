<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para crear respuestas
 * -----------------------------------------------------------------------------
 */
class AmResponse{

  protected
    /**
     * -------------------------------------------------------------------------
     * Listado de headers a agregar para la petición
     * -------------------------------------------------------------------------
     */
    $headers = array(),

    /**
     * -------------------------------------------------------------------------
     * Indica si se resolvío o no la petición
     * -------------------------------------------------------------------------
     */
    $resolved = true;

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una instancia de una respuesta con un archivo
   * ---------------------------------------------------------------------------
   * @return AmFileResponse   Instancia creada
   */
  public static function file(){
    return new AmFileResponse;
  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una instancia de una respuesta con un archivo
   * ---------------------------------------------------------------------------
   * @return AmFileResponse   Instancia creada
   */
  public static function redirect(){
    return new AmRedirectResponse;
  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una instancia de una respuesta con el llamado de un callback
   * ---------------------------------------------------------------------------
   * @return AmCallResponse   Instancia creada
   */
  public static function call(){
    return new AmCallResponse;
  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve una instancia de una respuesta con el render de un template
   * ---------------------------------------------------------------------------
   * @return AmTemplateResponse   Instancia creada
   */
  public static function template(){
    return new AmTemplateResponse;
  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega las cabeceras para indicar un error 404 a la respuesta.
   * ---------------------------------------------------------------------------
   * @param   string  $msg  $mensaje para el error a 404.
   */
  public static function e404($msg = null){

    if(!$msg)
      $msg = Am::t('NOT_FOUND');

    return (new AmResponse())
      ->resolved(false)
      ->addHeader("HTTP/1.0 404 {$msg}")
      ->addHeader("Status: 404 {$msg}");

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve si la petición fue resuelta o no.
   * ---------------------------------------------------------------------------
   * @return boolean  Devuelve si la petición fué resuelta o no
   */
  public function isResolved(){
    return $this->resolved;
  }

  /**
   * ---------------------------------------------------------------------------
   * Asignar Si la petición se resolvió o no.
   * ---------------------------------------------------------------------------
   * @param  bool   $resolved   
   * @return this
   */
  public function resolved($resolved = true){
    $this->resolved = $resolved;
    return $this;
  }

  /**
   * -------------------------------------------------------------------------
   * Agrega un header al listado de headers de la respuesta
   * -------------------------------------------------------------------------
   * @param string $header Header a agregar
   */
  public function addHeader($header, $key = null){
    if(isset($key))
      $this->headers[$key] = $header;
    else
      $this->headers[] = $header;
    return $this;
  }

  /**
   * -------------------------------------------------------------------------
   * Método para ejecutar la respuesta
   * -------------------------------------------------------------------------
   */
  public function make(){

    foreach ($this->headers as $header) {
      header($header);
    }
    
  }

}
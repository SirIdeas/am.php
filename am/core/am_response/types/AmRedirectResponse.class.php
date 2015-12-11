<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para respuestas de redirección a otra URL.
 * -----------------------------------------------------------------------------
 */

class AmRedirectResponse extends AmResponse{

  protected

    /**
     * -------------------------------------------------------------------------
     * URL a la que se redirigirá
     * -------------------------------------------------------------------------
     */
    $url = null;

  /**
   * ---------------------------------------------------------------------------
   * Asignar URL
   * ---------------------------------------------------------------------------
   * @param  bool   $url   URL a la que se redirigirá
   * @return this
   */
  public function url($url){
    $this->url = $url;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Acción de la respuesta: Redirigir a la URL
   * ---------------------------------------------------------------------------
   */
  public function make(){
    $this->addHeader("location: {$this->url}");
    parent::make();
  }

}
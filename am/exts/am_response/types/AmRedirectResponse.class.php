<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para respuestas de redirección a otra URL.
 */
class AmRedirectResponse extends AmResponse{

  /**
   * Constructor de la Clase.
   */
  public function __construct($data = null){
    parent::__construct();

    // Inicializar propiedades
    $this->__p->extend(array(
      // URL a la que se redirigirá
      'url' => null
    ));

    // Asignar propiedades recibicas por parámetros
    $this->__p->extend($data);

  }

  /**
   * Asignar URL.
   * @param  bool  $url URL a la que se redirigirá.
   * @return $this
   */
  public function url($url){
    $this->__p->url = $url;
    return $this;
  }

  /**
   * Acción de la respuesta: Redirigir a la URL
   */
  public function make(){
    parent::make();
    $this->addHeader("location: {$this->__p->url}", 'location');

  }

}
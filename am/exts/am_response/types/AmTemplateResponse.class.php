<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para crear respuestas con renderizados de templates
 * -----------------------------------------------------------------------------
 */
class AmTemplateResponse extends AmResponse{

  protected

    /**
     * -------------------------------------------------------------------------
     * Propiedades de la petición.
     * -------------------------------------------------------------------------
     */
    $__p = array(

      // String de la ruta del la vista a renderizar.
      'tpl' => null,

      // Array todos los parámetros de la llamada.
      'params' => array(),

    );

  /**
   * -------------------------------------------------------------------------
   * Asignar callback
   * --------------------------------------------------------------------------
   * @param  array/string   $callback   Callback a ser llamado
   * @return this
   */
  public function tpl($tpl){
    $this->__p->tpl = $tpl;
    return $this;
  }

  /**
   * -------------------------------------------------------------------------
   * Asignar parámetros de la llamada
   * --------------------------------------------------------------------------
   * @param  array   $args   Parámetros de la llamada
   * @return this
   */
  public function params(array $params){
    $this->__p->params = $params;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si la petición se puede resolver o no.
   * ---------------------------------------------------------------------------
   * Se sobreescribe el método para saber si el template existe o no.
   * @return  boolean   Indica si la petición se puede resolver o no.
   */
  public function isResolved(){
    return parent::isResolved() && is_file($this->__p->tpl);
  }

  /**
   * ---------------------------------------------------------------------------
   * Acción de la respuesta: Realizar llamado del callback
   * ---------------------------------------------------------------------------
   * @return  AmResponse  Si el callback a ejecutar no existe se devuelve una
   *                      respuesta 404. De lo contario retorna null
   */
  public function make(){

    // Si no existe el archivo responder con error 404
    if(!$this->isResolved())
      return Am::e404(Am::t('AMRESPONSE_TEMPLATE_NOT_FOUND', $this->__p->tpl));
      
    parent::make();
    Am::ring('render.template', $this->__p->tpl, $this->__p->params);

  }

}
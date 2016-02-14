<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para crear respuestas con renderizados de templates.
 */
class AmTemplateResponse extends AmResponse{

  /**
   * Constructor de la Clase.
   */
  public function __construct($data = null){
    parent::__construct();

    // Inicializar propiedades
    $this->__p->extend(array(

      // Ruta del archivo que se devolverá.
      'tpl' => null,

      // Array todos los parámetros de la llamada.
      'vars' => array(),

      // Opciones para la vista.
      'options' => array(),

      // Indica si se tiene que verificar la existencia de la vista
      'checkView' => true

    ));

    // Asignar propiedades recibicas por parámetros
    $this->__p->extend($data);

  }

  /**
   * Asignar callback.
   * @param  array/string   $callback   Callback a ser llamado
   * @return this
   */
  public function tpl($tpl){
    $this->__p->tpl = $tpl;
    return $this;
  }

  /**
   * Indicar si se tiene que verificar o no la existencia de la vista.
   * @param  array/string   $callback   Callback a ser llamado
   * @return this
   */
  public function checkView($checkView){
    $this->__p->checkView = $checkView;
    return $this;
  }

  /**
   * Agregar variables de la llamada.
   * @param  array  $vars   Parámetros de la llamada.
   * @param  bool   $rw     Indica si las variables nuevas sobreescriben las
   *                        anteriores.
   * @return this
   */
  public function vars(array $vars, $rw = false){
    if($rw)
      $this->__p->vars = $vars;
    else
      $this->__p->vars = array_merge($this->__p->vars, $vars);
    return $this;
  }

  /**
   * Agrega una variable.
   * @param   string    $varName  Nombre de la variable a agregar.
   * @param   strning   $value    Valor de la varible.
   * @return  this
   */
  public function with($varName, $value){
    $this->__p->vars[$varName] = $value;
    return $this;
  }

  /**
   * Agregar opciones para la vista.
   * @param  array  $options  Opciones a agregar.
   * @param  bool   $rw       Indica si las opciones nuevas sobreescriben las
   *                          anteriores.
   * @return this
   */
  public function options(array $options, $rw = false){
    if($rw)
      $this->__p->options = $options;
    else
      $this->__p->options = array_merge($this->__p->options, $options);
    return $this;
  }

  /**
   * Indica si la petición se puede resolver o no.
   * Se sobreescribe el método para saber si el template existe o no.
   * @return  boolean   Indica si la petición se puede resolver o no.
   */
  public function isResolved(){
    return parent::isResolved() && is_file($this->__p->tpl);
  }

  /**
   * Acción de la respuesta: Realizar llamado del callback
   * @return  AmResponse  Si el callback a ejecutar no existe se devuelve una
   *                      respuesta 404. De lo contario retorna null
   */
  public function make(){
    parent::make();
    
    $ret = Am::ring('render.template',
      $this->__p->tpl, $this->__p->vars, $this->__p->options);

    // Si no existe el archivo responder con error 404
    if($this->__p->checkView && !$ret && !$this->isResolved())
      return Am::e404(Am::t('AMRESPONSE_TEMPLATE_NOT_FOUND', $this->__p->tpl));

  }

}
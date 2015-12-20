<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para crear respuestas con callbacks
 * -----------------------------------------------------------------------------
 */
class AmCallResponse extends AmResponse{

  /**
   * ---------------------------------------------------------------------------
   * Constructor de la Clase.
   * ---------------------------------------------------------------------------
   */
  public function __construct($data = null){
    parent::__construct();

    // Inicializar propiedades
    $this->__p->extend(array(
      
      // String o array Callback a llamar.
      'callback' => null,

      // Callback obtenido apartir el $callback.
      'realCallback' => null,

      // Array con las variables de entorno.
      'env' => array(),

      // Array todos los parámetros de la llamada.
      'params' => array(),

      // Indica si el callback es válido.
      'isValidCallback' => false,

    ));

    // Asignar propiedades recibicas por parámetros
    $this->__p->extend($data);

  }

  /**
   * -------------------------------------------------------------------------
   * Asignar callback
   * --------------------------------------------------------------------------
   * @param  array/string   $callback   Callback a ser llamado
   * @return this
   */
  public function callback($callback){
    $this->__p->callback = $callback;
    
    // Obtener el callback real
    $this->__p->realCallback = $this->getCallback();

    return $this;
  }

  /**
   * -------------------------------------------------------------------------
   * Asignar variables de entorno
   * --------------------------------------------------------------------------
   * @param  array   $env   Variables de entorno
   * @return this
   */
  public function env(array $env){
    $this->__p->env = $env;
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

  private function getCallback(){

    $c = $this->__p->callback;

    // Responder como una función como controlador
    if (is_array($c) && call_user_func_array('method_exists', $c))
      
      return $c;

    if(function_exists($c))

      return $c;

    // Responder con un método estático como controlador
    if(preg_match('/^(.*)::(.*)$/', $c, $a)){
      array_shift($a);

      if(call_user_func_array('method_exists', $a))
        return $a;

    }

    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si la petición se puede resolver o no.
   * ---------------------------------------------------------------------------
   * Se sobreescribe el método para saber si el callback existe o no.
   * @return  boolean   Indica si la petición se puede resolver o no.
   */
  public function isResolved(){
    return parent::isResolved() && $this->__p->realCallback !== false;
  }

  /**
   * ---------------------------------------------------------------------------
   * Acción de la respuesta: Realizar llamado del callback
   * ---------------------------------------------------------------------------
   * @return  AmResponse  Si el callback a ejecutar no existe se devuelve una
   *                      respuesta 404. De lo contario retorna null
   */
  public function make(){
    parent::make();

    // Obtener las propiedades
    $params = $this->__p->params;
    $env = $this->__p->env;

    // Agegar entorno como último parámetro de la llamada.
    $params[] = $env;

    // Si $this->__p->realCallback es un array entonces tiene un callback valido
    if($this->__p->isResolved()){

      return call_user_func_array($this->__p->realCallback, $params);

    }

    // Responder con un error 404
    return Am::e404(Am::t('AMRESPONSE_CALLBACK_NOT_FOUND',
      var_export($this->__p->callback, true)));

  }

}
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

  protected

    /**
     * -------------------------------------------------------------------------
     * String o array Callback a llamar.
     * -------------------------------------------------------------------------
     * Puede ser el nombre de una función, un método estático en formato de
     * string ('Clase::metodo') o formato array (array('Clase', 'metodo')), un
     * método de un objeto (array($obj, 'método')) o una llamada a un
     * controlador ('NombreControlador@accion').
     */
    $callback = null,

    /**
     * -------------------------------------------------------------------------
     * Array con las variables de entorno.
     * -------------------------------------------------------------------------
     * Es agregado como ultimo argumento de la llamada.
     */
    $env = array(),

    /**
     * -------------------------------------------------------------------------
     * Array todos los parámetros de la llamada.
     * -------------------------------------------------------------------------
     * Contiene los parámetros obtenido de la ruta.
     */
    $params = array();

  /**
   * -------------------------------------------------------------------------
   * Asignar callback
   * --------------------------------------------------------------------------
   * @param  array/string   $callback   Callback a ser llamado
   * @return this
   */
  public function callback($callback){
    $this->callback = $callback;
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
    $this->env = $env;
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
    $this->params = $params;
    return $this;
  }

  /**
   * ---------------------------------------------------------------------------
   * Acción de la respuesta: Realizar llamado del callback
   * ---------------------------------------------------------------------------
   * @return  AmResponse  Si el callback a ejecutar no existe se devuelve una
   *                      respuesta 404. De lo contario retorna null
   */
  public function make(){

    // Obtener las propiedades
    $c = $this->callback;
    $params = $this->params;
    $env = $this->env;

    // Agegar entorno como último parámetro de la llamada.
    $params[] = $env;

    // Responder como una función como controlador
    if (is_array($c) && call_user_func_array('method_exists', $c)){
      
      parent::make();
      return call_user_func_array($c, $params);

    }else if(function_exists($c)){

      // Llamar funcion
      parent::make();
      return call_user_func_array($c, $params);

    // Responder con un método estático como controlador
    }elseif(preg_match('/^(.*)::(.*)$/', $c, $a)){
      array_shift($a);

      if(call_user_func_array('method_exists', $a)){
        parent::make();
        return call_user_func_array($a, $params);
      }

    }elseif(preg_match('/^(.*)@(.*)$/', $c, $a)){
      array_shift($a);

      // Despachar con controlador
      parent::make();
      return Am::ring('response.control', $a[0], $a[1], $params, $env);

    }

    // Responder con un error 404
    return AmResponse::e404();

  }

}
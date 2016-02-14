<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para crear respuestas
 */
class AmResponse extends AmObject{

  protected
    /**
     * Propiedades Iniciales de la petición.
     */
    $__p = array();

  /**
   * Constructor de la Clase.
   * Inicializa la propiedad __p con una instancia de AmObject. Las
   * propiedades son las indicadas en $this->__p mas la recibidas por
   * parámetro $data
   */
  public function __construct($data = null){
    // Instancia el objeto de las propiedades
    $this->__p = new AmObject;

    // Inicializar propiedades
    $this->__p->extend(array(
      
      // Listado de headers a agregar para la petición
      'headers' => array(),

      // Indica si se resolvío o no la petición
      'resolved' => true,

      // Cuerpo de la respuesta
      'content' => null,

    ));

    // Asignar propiedades recibicas por parámetros
    $this->__p->extend($data);

  }

  /**
   * Responde con un archivo indicado por parámetro.
   * @param   string  $filename     Ruta del archivo con el que se responderá.
   * @param   bool    $attachment   Si la ruta se descarga o no.
   * @param   string    $name       Nombre con el que se entregará el archivo.
   * @param   mimeType  $mimeType   Tipo mime para la descarga.
   * @return  any                   Respuesta de manejador configurado.
   */
  public static function file($filename, $attachment = false, $name = null,
    $mimeType = null){
    return (new AmFileResponse)
      ->filename($filename)
      ->attachment($attachment)
      ->name($name)
      ->mimeType($mimeType);
  }

  /**
   * Busca una llamada como función, método estático de una clase o llamada
   * a controlador.
   * @param   string $callback  String que identifica el controlador a buscar.
   * @param   array  $env      Variables de entorno.
   * @param   array  $params   Argumentos obtenidos de la ruta.
   * @return  any    Respuesta de manejador configurado.
   */
  public static function call($callback, array $env = array(),
                              array $params = array()){
    return (new AmCallResponse)
      ->callback($callback)
      ->env($env)
      ->params($params);
  }

  /**
   * Busca un template y lo renderiza.
   * @param   string  $tpl        Template a renderizar.
   * @param   array   $vars       Variables de la vista.
   * @param   array   $options    Opciones para la vista.
   * @param   array   $checkView  Indica si se desea o no chequear si la vista
   *                              existe.
   * @return  any                 Respuesta de manejador configurado.
   */
  public static function template($tpl, array $vars = array(),
                                  array $options = array(), $checkView = true){
    return (new AmTemplateResponse)
      ->tpl($tpl)
      ->vars($vars)
      ->options($options)
      ->checkView($checkView);
  }

  /**
   * Redirigir a una URL.
   * @param   string $url   URL que se desea ir.
   */
  public static function go($url){

    return (new AmRedirectResponse)
      ->url($url);

  }

  /**
   * Agrega las cabeceras para indicar un error 404 a la respuesta.
   * @param   string  $msg  $mensaje para el error a 404.
   */
  public static function e404($msg = null){

    if(!$msg)
      $msg = Am::t('AM_NOT_FOUND');

    return (new AmResponse())
      ->resolved(false)
      ->addHeader("HTTP/1.0 404 {$msg}")
      ->addHeader("Status: 404 {$msg}")
      ->content($msg);

  }

  /**
   * Agrega las cabeceras para indicar un error 403 a la respuesta.
   * @param   string  $msg  $mensaje para el error a 403.
   */
  public static function e403($msg = null){

    if(!$msg)
      $msg = Am::t('AM_NOT_FOUND');

    return (new AmResponse())
      ->resolved(false)
      ->addHeader("HTTP/1.0 403 {$msg}")
      ->addHeader("Status: 403 {$msg}")
      ->content($msg);

  }

  /**
   * Devuelve el valor de una propiedad.
   */
  public function get($propertyName){
    return $this->__p->$propertyName;
  }

  /**
   * @param   string  $propertyName   Nombre de la propiedad que se desea
   *                                  asignar.
   * @param   any     $value          Valor a asignar.
   * @return  $this
   */
  public function set($propertyName, $value){
    $this->__p->$propertyName = $value;
    return $this;
  }

  /**
   * Devuelve si la petición fue resuelta o no.
   * @return  boolean  Devuelve si la petición fué resuelta o no
   */
  public function isResolved(){
    return $this->get('resolved');
  }

  /**
   * Asignar Si la petición se resolvió o no.
   * @param   bool  $resolved   
   * @return  this
   */
  public function resolved($resolved = true){
    $this->set('resolved', $resolved);
    return $this;
  }

  /**
   * Asignar el cuerpo.
   * @param   string  $content  Cuerpo de la respuesta.   
   * @return  this
   */
  public function content($content){
    $this->set('content', $content);
    return $this;
  }

  /**
   * Agrega contenido al final del cuerpo de la respuesta.
   * @param   string  $content  Cuerpo de la respuesta.   
   * @return  this
   */
  public function addContent($content){

    $this->content($this->get('content').$content);
    return $this;

  }

  /**
   * Agrega contenido al inicio del cuerpo de la respuesta.
   * @param   string  $content  Cuerpo de la respuesta.
   * @return  this
   */
  public function addContentToBegin($content){

    $this->content($content.$this->get('content'));
    return $this;
    
  }

  /**
   * Agrega un header al listado de headers de la respuesta.
   * @param   string  $header   Header a agregar.
   * @param   string  $key      Posición donde se quiere agregar la cabecera.
   * @return          this
   */
  public function addHeader($header, $key = null){
    if(isset($key))
      $this->__p->headers[$key] = $header;
    else
      $this->__p->headers[] = $header;
    return $this;
  }

  /**
   * Agrega un header al listado de headers de la respuesta.
   * @param   string  $key  Posicion de la cabecera que se desea eliminar.
   * @return          this
   */
  public function remoteHeader($key){
    unset($this->__p->headers[$key]);
    return $this;
  }

  /**
   * Método para ejecutar la respuesta.
   * Si tiene un cuerpo asignado lo imprime.
   */
  public function make(){

    $content = $this->get('content');
    if(isset($content))
      echo $content;
    
  }

  /**
   * Ejecutar una respuesta.
   * @param   self    $response   Respuesta que se desea despachar
   */
  public static function response($response){

    $headers = array();

    // Para captar todo lo que se imprima en la salida standar
    ob_start();

    while($response instanceof AmResponse){

      // Llamar respuesta
      $ret = $response->make();

      // Obtener cabecera
      $headers = array_merge(
        $headers,
        $response->get('headers')
      );
      
      $response = $ret;

    }
    
    // Para obtener la salida
    $buffer = ob_get_clean();

    // Incluir las cabeceras
    foreach ($headers as $header)
      header($header);

    // Imprimir la salida de la respuestas
    echo $buffer;
      

  }

}
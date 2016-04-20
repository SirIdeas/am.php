<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para controlador estandar. 
 */
class AmController extends AmResponse{

  private static

    /**
     * Callbacks para mezclar atributos.
     */
    $mergeFunctions = array(
      'paths'   => 'merge_unique',
      'prefixs' => 'array_merge',
      'allows'  => 'merge_if_both_are_array',
      'headers' => 'merge_unique',
      'filters' => 'merge_r_if_snd_first_not_false',
    );

  public function __construct($data = null){

    $data = AmObject::parse($data);
    
    $this->extend(itemOr('env', $data));
    unset($data['env']);

    parent::__construct($data);

  }

  /**
   * Asigna el nombre de la vista a setView.
   * Es un Alias de la funcion setRender que agrega .php al final del valor
   * recibido.
   * @param  String $view Nombre de la vista que se desea asignar.
   * @return $this
   */
  final protected function setView($view){
    // Las vista de las acciones son de extencion .php
    return $this->setRender(self::getViewName($view));
  }

  /**
   * Indica si una accion esta permitida o no para cierto request method.
   * Si las acciones permitidas no tiene el item correspondiente a la acción
   * solicitada entonces se asume que esta permitida la acción.
   * @param  string  $action Nombre de la acción que se desea consultar.
   * @param  string  $method Método para el que se quiere consultar.
   * @return bool            Si la acción está o no permitida.
   */
  final public function isActionAllow($action, $method){

    $method = strtolower($method);

    // Get allows actions configuration
    $allows = $this->get('allows');

    // Obtener permisos correspondientes a la accion
    $allow = itemOr($action, $allows, itemOr('', $allows));

    if(is_bool($allow))
      return $allow;

    if(isset($allow[$method]))
      return !!$allow[$method];

    if(isset($allow['']))
      return !!$allow[''];

    return true;

  }

  /**
   * Revisa si una accion esta permitida para cierto request method.
   * Si la acción no está permitida devuelve un error 403.
   * @param  string          $action Nombre de la acción que se desea consultar.
   * @param  string          $method Metodo para el que se quiere consultar.
   * @return AmResponse/null         Si está permitida devuelve null, de lo
   *                                 contrario Devuelve una respuesta de no 
   *                                 autorizadod.
   */
  final public function checkIsActionAllow($action, $method){
    if(!$this->isActionAllow($action, $method))
      return Am::e403(Am::t('AMCONTROLLER_ACTION_FORBIDDEN',
        get_class($this), $action, $method));
  }


  /**
   * Devuelve el prefijo para determinado elemento
   * @param  string $key Nombre del elemento del que se quiere obtener el
   *                     prefijo.
   * @return string      Prefijo de elemento.
   */
  final protected function getPrefix($key){

    return itemOr($key, $this->get('prefixs'), '');

  }

  /**
   * Agregar un filtro.
   * @param string $name     Nombre del filtro a agregar,
   * @param string $when     Cuando se ejecutará el filtro: before, after,
   *                         before_get, after_get, ...
   * @param string $to       Para que acciones se ejecutará el filtro. Puede
   *                         ser 'all' que indica que el filtro se ejecuta para
   *                         todas las acciones o un array de string que indica
   *                         las acciones para las que se ejecutará el filtro.
   * @param array  $except   Array de acciones para las cuales no se ejecutará
   *                         el filtro.
   * @param string $redirect A donde se redirigirá si el filtro no pasa.
   */
  final protected function addFilter($name, $when, $to = 'all',
                                     $except = array(), $redirect = null){

    // Obtener los filtros
    $filters = $this->get('filters');

    // Filtro 'only' para ciertos métodos
    if(is_array($to)){
      $redirect = $except;
      $except = array();
    }

    // Si no se ha creado el contenedor del filtro, se crea
    if(!isset($filters[$when][$name])){

      // Crear array vacío en el when si no existe.
      if(!isset($filters[$when]))
        $filters[$when] = array();

      // Agregar filtro vacío
      $filters[$when][$name] = array(
        // A quienes se aplicara el filtro
        'to' => $to,
        // A quienes no se aplicará el filtro en caso de que to=='all'
        'except' => array(),
        // Si la peticion no pasa el filtro rediriguir a la siguiente URL
        'redirect' => $redirect
      );

    }

    // Mezclar los métodos a los que se aplicará el filtro con los que
    // ya habian sido agregados y obtener los valores unicos
    if(is_array($to) && is_array($filters[$when][$name]['to']))
      $filters[$when][$name]['to'] = array_unique(array_merge(
        $filters[$when][$name]['to'],
        $to
      ));

    // Mezclar los métodos a los que se aplicará el filtro con los que
    // ya habian sido agregados y obtener los valores unicos
    $filters[$when][$name]['except'] = array_unique(array_merge(
      $filters[$when][$name]['except'],
      $except
    ));

    // Asignar el filtro
    $this->set('filters', $filters);

  }

  /**
   * Ejecuta los filtros correspondiente para un método.
   * @param  string          $when   Indica el estado que se ejecutara: before,
   *                                 before_get, bofore_post, after, after_get,
   *                                 after_post.
   * @param  string          $action Nombre del metodo del que se desea ejecutar
   *                                 los filtros.
   * @param  array           $params Parámetros extras para los filtros.
   * @return bool/AmResponse         Si el llamado de la cción paso o no el
   *                                 filtro.
   */
  final protected function executeFilters($when, $action,
                                          array $params = array()){

    // Obtener los filtros.
    $filters = $this->get('filters');

    // Si no hay filtro a ejecutar para dicha peticion salir
    if(!isset($filters[$when]))
      return true;

    // Recorrer los filtros del peditoestado
    foreach($filters[$when] as $filterName => $filter){

      // Si filter es un string se asume que es el scope
      if(is_string($filter) || (is_array($filter) && isHash($filter)))
        $filter = array('to' => $filter);

      // Valores por defecto del filtro
      $filter = array_merge(array(
        'to' => 'all',
        'except' => array(),
        'redirect' => null,
      ), $filter);

      // Determinar si el filtro se ejecura para todos las acciones
      $all = $filter['to'] === 'all';

      if($all)
        $filter['to'] = array();

      // Si el filtro no se aplica a todos y si el metodo solicitado no esta
      // dentro de los métodos a los que se aplicará el filtro actual continuar
      // con el siguiente filtro.
      if(!$all && !in_array($action, $filter['to']))
        continue;

      // Si el método esta dentro de las excepciones del filtro
      // continuar con el siguiente filtro
      if(in_array($action, $filter['except']))
        continue;

      // Obtener le nombre real del filtro
      $filterRealName = $this->getPrefix('filters') . $filterName;

      // Llamar el filtro
      $ret = call_user_func_array(array(&$this, $filterRealName), $params   );

      // Si la accion pasa el filtro o no se trata de un filtro before se debe
      // continuar con el siguiente filtro
      if($ret !== false || $when != 'before')
        continue;

      // El retornna una respuesta entonces devolver dicha respuesta.
      if($ret instanceof parent)
        return $ret;

      // Si se indica una ruta de redirección se lleva a esa ruta
      if(isset($filter['redirect']))
        return Am::go($filter['redirect']);

      // Si no retornar false para indicar que no se pasó el filtro.
      return false;

    }

    // Si todos los filtros pasaron retornar verdadero.
    return true;

  }

  /**
   * Ejecuta una acción, con un método y unos parámetros.
   * @param  string     $action Nombre de la acción a ejecutar.
   * @param  string     $method Nombre del método como se ejecuta.
   * @param  array      $params Parámetros para ejecutar la acción.
   * @return AmResponse         Instancia de la respuesta generada.
   */
  final protected function executeAction($action, $method, array $params){

    // Chequear si esta permitida o no la acción.
    // Si devuelve una respuesta devolver dicha respuesta.
    $ret = $this->checkIsActionAllow($action, $method);
    if($ret instanceof parent)
      return $ret;

    // PENDIENTE
    // // Verificar las credenciales
    // Am::getCredentialsHandler()
    //   ->checkCredentials($action, $this->credentials);
    
    $return = null;
    // Para guardar métodos ejecutados.
    $executed = array();
    
    // Ejecutar el método action
    if(method_exists($this, $actionMethod = 'action')){

      // Agregar a ejecutados
      $executed[] = $actionMethod;

      call_user_func_array(array($this, $actionMethod), $params);

    }

    $methodsToExec = array(
      'before' => $this->getPrefix('actions') . $action,
      "before_{$method}" => $this->getPrefix("{$method}Actions") . $action
    );

    foreach ($methodsToExec as $when => $actionMethod) {

      // Ejecutar filtros before
      $ret = $this->executeFilters($when, $action, $params);

      // Si retorno falso o un respuesta devolverlo
      if($ret === false || $ret instanceof parent) return $ret;

      // Ejecutar el método si existe y no se ha ejecutado
      if($return === null && !in_array($actionMethod, $executed) &&
        method_exists($this, $actionMethod)){

        // Agregar a ejecutados
        $executed[] = $actionMethod;

        $ret = call_user_func_array(array($this, $actionMethod), $params);

        // Convertir en respuesta
        if(is_array($ret) || is_object($ret)){
          if(!$ret instanceof parent)
            $ret = $this->responseService($ret);
          $return = $ret;
        }

      }

    }

    // Ejecutar filtros before para el request method
    $ret = $this->executeFilters("before_{$method}", $action, $params);

    // Si retorno falso o un respuesta devolverlo
    if($ret === false || $ret instanceof parent) return $ret;

    // Ejecutar la acción para el request method
    
    // Obtener le nombre del método
    $actionMethod = $this->getPrefix("{$method}Actions") . $action;

    // Ejecutar el método si existe y no se ha ejecutado
    if($return !== null && !in_array($actionMethod, $executed) &&
      method_exists($this, $actionMethod)){

      // Agregar a ejecutados
      $executed[] = $actionMethod;

      $ret = call_user_func_array(array($this, $actionMethod), $params);

      // Convertir en respuesta
      if(is_array($ret) || is_object($ret)){
        if(!$ret instanceof parent)
          $ret = $this->responseService($ret);
        $return = $ret;
      }
        
    }

    // Ejecutar filtros after
    foreach (array("after_{$method}", 'after') as $when) {
      
      $ret = $this->executeFilters($when, $action, $params);

      if($return !== null && (is_array($ret) || is_object($ret))){
        if(!$ret instanceof parent)
          $ret = $this->responseService($ret);
        $return = $ret;
      }

    }

    return $return;

  }

  /**
   * Devuelve un array de los paths de ámbito del controlador.
   * @return array Listado de los directorios donde se buscará la vista.
   */
  final protected function getPaths(){

    // Tomar valores de paths validos
    $ret = array_filter($this->get('paths'));

    // Obtener la directorio raíz del controlador
    // y el directorio de vistas.
    $root = $this->get('root');
    $views = $this->get('views');

    // // Agregar carpeta raiz del controlador si existe si existe
    // $path = realPath($root);
    // if(isset($root) && $path)
    //   array_unshift($ret, $path);

    // Agregar carpeta raiz del controlador para vistas
    // si existe si existe
    $path = realPath($root . '/' . $views);
    if(isset($root) && isset($views) && $path)
      array_unshift($ret, $path);

    return array_unique($ret);

  }

  /**
   * Obtener el nombre de la vista actual.
   * @return string Nombre de la vista actual.
   */
  final protected function getView(){

    return $this->get('view');

  }

  /**
   * Asignar la vista actual.
   * @param  string  $view   Nombre de la vista.
   * @return $this
   */
  final protected function setRender($view){

    return $this->set('view', $view);

  }

  /**
   * Renderizar la vista.
   * @param  string     $view Vista a renderizar. Si no se indica se tomará la
   *                          asignada en la propiedad 'view'.
   * @return AmResponse       Respuesta para renderizar la vista.
   */
  final protected function view($view = null){

    // Si no indicó la vista obtener la actual.
    if(!isset($view))
      $view = $this->getView();

    // Renderizar vista mediante un callback
    return self::template(

      // Vista a renderizar
      $view,

      // Variables de la vista
      $this->toArray(),

      // Parámetros para el renderizado de la vista.
      array('paths' => $this->getPaths()),

      false

    );

  }

  /**
   * Responder como servicio.
   * @param  array/object $content Contenido de la respuesta.
   * @return AmResponse            Respuesta
   */
  final private function responseService($content, $as = null){

    // Si no se indica como se desea responder entonces se busca la propiedad
    // servicesFormat
    if(!isset($as))
      $as = $this->get('servicesFormat');

    $mimeType = Am::mimeType(".{$as}");

    // Convertir a array
    if(isset($content) && is_object($content))
      $content = (array)$content;

    // Codificar el contenido.
    switch ($as){
      case 'json':
        $content = json_encode($content);
        break;
      case 'txt':
        $content = var_export($content, true);
        break;
      default:
        $content = print_r($content, true);
        break;
    }

    return (new parent)
      ->addHeader("Content-Type: {$mimeType}", 'contentType')
      ->content($content);

  }

  /**
   * Para despachar la petición.
   */
  final public function make(){

    $ret = null;

    ob_start();

    // Ejecutar accion con sus respectivos filtros.
    $ret = $this->executeAction(
      $this->get('action'),
      Am::getMethod(),
      $this->get('params')
    );
    
    // Para obtener la salida
    $buffer = ob_get_clean();

    // Crear respuesta de vista si lo devuelto no es una respuesta
    if(!$ret instanceof parent){

      $ret = $this->view();

    }

    // Agregar el buffer al principio de la respuesta
    return $ret->addContentToBegin($buffer);

  }
  
  /**
   * Devuelve el nombre de la vista a renderizar para una acción.
   * @param   $action   string  Nombre de la acción de la que se desea obtener
   *                            la vista.
   * @return  string            Nombre de la vista
   */
  final protected static function getViewName($action){

    return "{$action}.php";

  }

  /**
   * Método para mezclar dos configuraciones.
   * La mezcla se basa en los parámetros de 
   * @param  array $confToRewrite Configuración a sobreescribir.
   * @param  array $conf          Configuración a agregar.
   * @return array                Configuraciones mezcladas.
   */
  final private static function mergeConf(array $confToRewrite, array $conf){

    // Agregar items de
    foreach ($confToRewrite as $key => $value)

      // Si no existe en la configuraicon hija se asigna.
      if(!isset($conf[$key]))
        $conf[$key] = $confToRewrite[$key];

      // De lo contrario mezclar los datos
      else if(isset(self::$mergeFunctions[$key]))

        $conf[$key] = call_user_func_array(self::$mergeFunctions[$key],
          array(
            $confToRewrite[$key],
            $conf[$key]
          )
        );

    return $conf;

  }

  /**
   * Incluye un controlador.
   * @param  string $control Nombre del controlador a incluir.
   * @return array           Configuración del controlador
   */
  final public static function includeController($controller){

    // Obtener la carpeta donde esta ubicada el controlador
    $root = realPath(dirname(Am::whereIs($controller)));

    // Obtener el controlador parent
    $parentController = get_parent_class($controller);

    // Obtener configuraciones del controlador
    $confs = Am::getProperty('controllers');

    // Obtener valores por defecto
    $confDef = itemOr('', $confs, array());

    // Si no existe configuracion para el controlador
    $conf = itemOr($controller, $confs, array());

    // Obtener la configuracion del padre
    if($parentController === get_class()){

      // Mezclar con la configuración por defecto
      $conf = self::mergeConf($confDef, $conf);
      
    }else{
      
      // Obtener la configuracion del padre
      $confParent = self::includeController($parentController);

      // Agregar carpeta de vistas por defecto del padre.
      $confParent['paths'][] = $confParent['root'] . '/' . $confParent['views'];
      // $confParent['paths'][] = $confParent['root'];
      
      // Mezclar con la configuracion del padre
      $conf = self::mergeConf($confParent, $conf);

    }

    // Mezclar con el archivo de configuracion en la raiz del controlador.
    if(is_file($realFile = realPath("{$root}/am.conf.php")))

      $conf = self::mergeConf($conf, require($realFile));

    // Incluir como extension
    Am::load($root . '/');

    // Carpeta raíz del controlador
    $conf['root'] = $root;

    // Retornar la configuracion obtenida
    return $conf;

  }

  /**
   * Obtiene el controlador y la accion de un cadena de caracteres.
   * @param  string      $actionStr String con la accion en formato
   *                                'controlador@accion'
   * @return false/array            Hash con el controlador y la acción. Si no
   *                                coincide con el formato devuelve false.
   */
  final private static function getControllerAndAction($actionStr){

    if(preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)@([a-zA-Z_\{\}][a-zA-Z0-9_\{\}]*)$/',
          $actionStr, $m))
      return array(
        'controller' => $m[1],
        'action' => $m[2]
      );
    return false;

  }

  /**
   * Pre procesador de rutas.
   * Verifica y transforma la ruta del formato 'controlador@acction'.
   * @param  array $route Ruta a evaluar.
   * @return array        Ruta transformada.
   */
  final public static function routePreProcessor($route){

    // Obtener acción
    $action = self::getControllerAndAction($route['']);

    // Si es una acción válida se asigna
    if(false !== $action)
      $route['controller'] = $action;

    // Si no es una acción válida se se verifica ahora en el indice controller
    elseif(isset($route['controller'])){
      // Obtener la acción
      $action = self::getControllerAndAction($route['controller']);

      // Si es una acción válida se asigna
      if(false !== $action)
        $route['controller'] = $action;
      
    }
    
    return $route;

  }

  /**
   * Manejador para el evento response.controller.
   * Funcion para atender las respuestas por controlador. Recive , un array con
   * el entorno y un array con los parámetros obtenidos de la ruta.
   * @param  string  $action La acción a ejecutar en formato del controlador
   *                         (controlador@accion).
   * @param  array   $env    Variables de entorno.
   * @param  array   $params Argumentos obtenidos de la ruta.
   */
  final public static function response($action, array $env = array(),
                                  array $params = array()){

    // Obtener el controlador y la accion
    if(is_string($action))
      $action = self::getControllerAndAction($action);

    $controller = $action['controller'];
    $action = $action['action'];

    // Valores por defecto
    $conf = array_merge(
      // Incluye el controlador y devuelve la configuracion para el mismo.
      self::includeController($controller),

      // Asignar vista que se mostrará
      array(
        'view' => self::getViewName($action),
        'action' => $action,
        'params' => $params,
      )
    );

    // Si no se puede instanciar el controlador retornar error.
    if(!class_exists($controller))
      return Am::e404(Am::t('AMCONTROLLER_ACTION_NOT_FOUND',
        $controller, $action));
    
    // Obtener la instancia del controlador.
    $controller = Am::getInstance($controller, $conf);

    // Asignación de propiedades como propiedades del controlador.
    foreach ($env as $propertyName => $value)
      $controller->$propertyName = $value;

    // Devolver controlador
    return $controller;

  }

}

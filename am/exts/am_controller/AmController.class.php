<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para controlador estandar. 
 * -----------------------------------------------------------------------------
 */

class AmController extends AmResponse{

  /**
   * ---------------------------------------------------------------------------
   * Ubicación por defecto del controlador.
   * ---------------------------------------------------------------------------
   */
  const DEFAULT_CONTROLLER_FOLDER = 'controllers/';

  private static

    /**
     * -------------------------------------------------------------------------
     * Callbacks para mezclar atributos.
     * -------------------------------------------------------------------------
     */
    $mergeFunctions = array(
      'paths'   => 'array_merge',
      'prefix'  => 'array_merge',
      'allows'  => 'array_merge',
      'headers' => 'merge_unique',
      'filters' => 'merge_r_if_snd_first_not_false',
    );

  /**
   * ---------------------------------------------------------------------------
   * Constructor de la Clase.
   * ---------------------------------------------------------------------------
   */
  public function __construct($data = null){
    parent::__construct();

    $this->__p->extend(array(
      
      // -----------------------------------------------------------------------
      // Para todo el controlador.
      // -----------------------------------------------------------------------
      
      // Carpeta raíz del controlador.
      'root' => null,

      // Nombre del controlador.
      'name' => null,

      // Directorios donde se buscará las vistas.
      'paths' => array(),

      // Filtros.
      'filters' => array(),

      // Prefijos.
      'prefixs' => array(
        'filters' => 'filter_',
        'actions' => 'action_',
        'getActions' => 'get_',
        'getPost' => 'post_',
      ),

      // Acciones permitidas.
      'allows' => array(),

      // Tipo de respuesta para el servicio: json, txt.
      'serviceMimeType' => 'json',

      // -----------------------------------------------------------------------
      // Solo para la petición actual
      // -----------------------------------------------------------------------
      
      // Acción a ejecutar.
      'action' => null,

      // Parémetros para ejecutar la acción.
      'params' => array(),

      // Nombre de la vista a renderizar.
      'view' => null,

    ));

    // Asignar propiedades recibicas por parámetros
    $this->__p->extend($data);

  }

//     $credentials = false,     // Credenciales para el controlador

  /**
   * ---------------------------------------------------------------------------
   * Asigna el nombre de la vista a renderizar.
   * ---------------------------------------------------------------------------
   * Es un Alias de la funcion setView que agrega .view.php al final del valor
   * recibido.
   * @param   String  $view   Nombre de la vista que se desea asignar.
   * @return  $this
   */
  final protected function render($view){
    // Las vista de las acciones son de extencion .view.php
    return $this->setView(self::getViewName($view));
  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si una accion esta permitida o no.
   * ---------------------------------------------------------------------------
   * Si las acciones permitidas no tiene el item correspondiente a la acción
   * solicitada entonces se asume que esta permitida la acción.
   * @param   string    $action   Nombre de la acción que se desea consultar.
   * @return  boolean             Si la acción está o no permitida.
   */
  final public function isActionAllow($action){
    // Get allows actions configuration
    $allows = $this->get('allows');

    return isset($allows[$action])? $allows[$action] : true;

  }

  /**
   * ---------------------------------------------------------------------------
   * Revisa si una accion esta permitida.
   * ---------------------------------------------------------------------------
   * Si la acción no está permitida devuelve un error 403.
   * @param  string   $action   Nombre de la acción que se desea consultar.
   * @return AmResponse/null    Si está permitida devuelve null, de lo contrario
   *                            Devuelve una respuesta de no autorizadod.
   */
  final public function checkIsActionAllow($action){
    if(!$this->isActionAllow($action))
      return Am::e403(Am::t('AMCONTROLLER_ACTION_FORBIDDEN',
        $this->get('name'), $action));
  }


  /**
   * ---------------------------------------------------------------------------
   * Devuelve el prefijo para determinado elemento
   * ---------------------------------------------------------------------------
   * @param   string  $key  Nombre del elemento del que se quiere obtener el
   *                        prefijo.
   * @return  string        Prefijo de elemento.
   */
  final protected function getPrefix($key){

    return itemOr($key, $this->get('prefixs'), '');

  }

  /**
   * ---------------------------------------------------------------------------
   * Agregar un filtro.
   * ---------------------------------------------------------------------------
   * @param   string  $name       Nombre del filtro a agregar,
   * @param   string  $when       Cuando se ejecutará el filtro: before, after,
   *                              before_get, after_get, ...
   * @param   string  $to         Para que acciones se ejecutará el filtro.
   *                              Puede 'all' que indica que el filtro se
   *                              ejecuta para todas las acciones, 'only', que
   *                              indica que le filtro se ejecuta para ciertas
   *                              acciones, 'except' que indica que se
   *                              ejecutara el filtro para todas las acciones
   *                              con algunas excepciones, o un array de string
   *                              que indica las acciones para las que se
   *                              ejecutará el filtro.
   * @param   array   $except     Array de acciones para las cuales no se
   *                              ejecutará el filtro.
   * @param   [type]  $redirect   A donde se redirigirá si el filtro no pasa.
   */
  final protected function addFilter($name, $when, $to = 'all',
                                     $except = array(), $redirect = null){

    // Obtener los filtros
    $filters = $this->get('filters');

    // Filtro 'only' para ciertos métodos
    if(is_array($to)){
      $scope = 'only';
      $redirect = $except;
      $except = array();

    // Filtro para 'all' métodos o para 'except'
    }else{
      $scope = $to;
      $to = array();
    }

    // Si no se ha creado el contenedor del filtro, se crea
    if(!isset($filters[$when][$name])){

      // Crear array vacío en el when si no existe.
      if(!isset($filters[$when]))
        $filters[$when] = array();

      // Agregar filtro vacío
      $filters[$when][$name] = array(
        // A que metodo se aplicara el filtro: 'all', 'only' o 'except'
        'scope' => $scope,
        // A quienes se aplicara el filtro en caso de que scope=='only'
        'to' => array(),
        // A quienes no se aplicará el filtro en caso de que scope=='except'
        'except' => array(),
        // Si la peticion no pasa el filtro rediriguir a la siguiente URL
        'redirect' => $redirect
      );

    }

    // Mezclar los métodos a los que se aplicará el filtro con los que
    // ya habian sido agregados y obtener los valores unicos
    $filters[$state][$name]['to'] = array_unique(array_merge(
      $filters[$state][$name]['to'],
      $to
    ));

    // Mezclar los métodos a los que se aplicará el filtro con los que
    // ya habian sido agregados y obtener los valores unicos
    $filters[$state][$name]['except'] = array_unique(array_merge(
      $filters[$state][$name]['except'],
      $except
    ));

    // Asignar el filtro
    $this->set('filters', $filters);

  }

  /*
  
    // Agregar un filtro antes de la ejecucion de metodos
    final protected function addBeforeFilter(
      $name, $to = 'all', $except = array(), $redirect = null){
      $this->addFilter($name, 'before', $to, $except, $redirect);
    }

    // Agregaun filtro antes de la ejecucion de métodos GET
    final protected function addBeforeGetFilter(
      $name, $to = 'all', $except = array(), $redirect = null){
      $this->addFilter($name, 'before_get', $to, $except, $redirect);
    }

    // Agregaun filtro antes de la ejecucion de métodos POST
    final protected function addBeforePostFilter(
      $name, $to = 'all', $except = array(), $redirect = null){
      $this->addFilter($name, 'before_post', $to, $except, $redirect);
    }

    // Agregaun filtro despues de la ejecucion de métodos
    final protected function addAfterFilter(
      $name, $to = 'all', $except = array()){
      $this->addFilter($name, 'after', $to, $except);
    }

    // Agregaun filtro despues de la ejecucion de métodos GET
    final protected function addAfterGetFilter(
      $name, $to = 'all', $except = array()){
      $this->addFilter($name, 'after_get', $to, $except);
    }

    // Agregaun filtro despues de la ejecucion de métodos POST
    final protected function addAfterPostFilter(
      $name, $to = 'all', $except = array()){
      $this->addFilter($name, 'after_post', $to, $except);
    }

  */

  /**
   * ---------------------------------------------------------------------------
   * Ejecuta los filtros correspondiente para un método.
   * ---------------------------------------------------------------------------
   * @param   string  $when     Indica el estado que se ejecutara: before,
   *                            before_get, bofore_post, after, after_get,
   *                            after_post.
   * @param   string  $action   Nombre del metodo del que se desea ejecutar
   *                            los filtros.
   * @param   array  $params    Parámetros extras para los filtros.
   * @return  bool/AmResponse   Si el llamado de lacción paso o no el filtro.
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

      // Si el filtro no se aplica a todos y si el metodo solicitado no esta
      // dentro de los métodos a los que se aplicará el filtro actual continuar
      // con el siguiente filtro.
      if($filter['scope'] != 'all' && !in_array($action, $filter['to']))
        continue;

      // Si el método esta dentro de las excepciones del filtro
      // continuar con el siguiente filtro
      if(isset($filter['except']) && in_array($action, $filter['except']))
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
   * ---------------------------------------------------------------------------
   * Ejecuta una acción, con un método y unos parámetros.
   * ---------------------------------------------------------------------------
   * @param   string  $action   Nombre de la acción a ejecutar.
   * @param   string  $method   Nombre del método como se ejecuta.
   * @param   array   $params   Parámetros para ejecutar la acción.
   * @return  [type]            
   */
  final protected function executeAction($action, $method, array $params){

    // Chequear si esta permitida o no la acción.
    // Si devuelve una respuesta devolver dicha respuesta.
    $ret = $this->checkIsActionAllow($action);
    if($ret instanceof parent)
      return $ret;

    // PENDIENTE
    // // Verificar las credenciales
    // Am::getCredentialsHandler()
    //   ->checkCredentials($action, $this->credentials);
      
    // Guarda el valor a retornar.
    $return = null;

    // Para guardar métodos ejecutados.
    $executed = array();

    // Obtener los nombre de los métodos a ejecutar.
    $methodsToExec = array(
      'action',
      // Acción normal
      'before' => $this->getPrefix('actions') . $action,
      // Acción correspondiente al método
      "before_{$method}" => $this->getPrefix("{$method}Actions") . $action,
      // Despues de acción con el método
      "after_{$method}" => null,
      // Despues de la acción
      'after' => null
    );

    foreach ($methodsToExec as $when => $actionMethod) {

      $ret = null;

      // Si el método a ejecutar no es 'action' ni null
      if($actionMethod !== 'action'){

        // Ejecutar filtros befores
        $ret = $this->executeFilters($when, $action, $params);

        // Si retorno falso o un respuesta devolverlo
        if($actionMethod!==null && ($ret === false || $ret instanceof parent))
          return $ret;

      }

      // Ejecutar el método si existe y no se ha ejecutado
      if(!in_array($actionMethod, $executed) &&
        method_exists($this, $actionMethod)){

        $ret = call_user_func_array(array($this, $actionMethod), $params);

        // Agregar a ejecutados
        $executed[] = $actionMethod;

      }

      // Si retorna una array, respuesta o objeto salir.
      if(!$return && (is_array($ret) || is_object($ret))){

        // Convertir en respuesta
        if(!$ret instanceof parent)
          $ret = $this->responseService($ret);

        $return = $ret;

        // Si el métod ejecutado es accion retorna.
        if('action' === $actionMethod)
          return $return;

        // Si no es el filtro after
        if(null !== $actionMethod)
          break;

      }

    }

    return $return;

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve un array de los paths de ámbito del controlador
   * ---------------------------------------------------------------------------
   * @return  array   Listado de los directorios donde se buscará la vista.
   */
  final protected function getPaths(){

    // PENDIENTE: Revisar mas adelante como llegan los paths aqui.
    // Se puede presentar un problema pues se invertir el orden de recorrido
    // de findFileIn
    $ret = array_filter($this->get('paths'));  // Tomar valores validos
    $ret = array_unique($ret);          // Valor unicos
    $ret = array_reverse($ret);         // Invertir array

    // Obtener la directorio raíz del controlador
    // y el directorio de vistas.
    $root = $this->get('root');
    $views = $this->get('views');

    // Agregar carpeta raiz del controlador si existe si existe
    $path = realPath($root);
    if(isset($root) && $path)
      array_unshift($ret, $path);

    // Agregar carpeta raiz del controlador para vistas
    // si existe si existe
    $path = realPath($root . '/' . $views);
    if(isset($views) && $path)
      array_unshift($ret, $path);

    // Invertir array,
    return $ret;

  }

  /**
   * ---------------------------------------------------------------------------
   * Obtener el nombre de la vista actual.
   * ---------------------------------------------------------------------------
   * @return string         Nombre de la vista actual.
   */
  final protected function getView(){

    return $this->get('view');

  }

  /**
   * ---------------------------------------------------------------------------
   * Asignar la vista actual.
   * ---------------------------------------------------------------------------
   * @param   string  $view   Nombre de la vista
   * @return  this
   */
  final protected function setView($view){

    return $this->set('view', $view);

  }

  /**
   * ---------------------------------------------------------------------------
   * Renderizar la vista.
   * ---------------------------------------------------------------------------
   * @param   string      $view   Vista a renderizar. Si no se indica se tomará
   *                              La asignada en la propiedad 'view'.
   * @return  AmResponse          Respuesta para renderizar la vista.
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
      array('paths' => $this->getPaths())

    );

  }

  /**
   * ---------------------------------------------------------------------------
   * Responder como servicio.
   * ---------------------------------------------------------------------------
   * @param   array/object  $content  Contenido de la respuesta.
   * @return  AmResponse              Respuesta
   */
  final private function responseService($content){

    $type = $this->get('serviceMimeType');
    $mimeType = Am::mimeType(".{$type}");

    // Convertir a array
    if(isset($content) && is_object($content))
      $content = (array)$content;

    // Codificar el contenido.
    switch ($type){
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
      ->addHeader("Content-Type: {$mimeType}")
      ->content($content);

  }

  /**
   * ---------------------------------------------------------------------------
   * Para despachar la petición.
   * ---------------------------------------------------------------------------
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

      $ret = $this->view($this->getView());

    }

    // Agregar el buffer al principio de la respuesta
    return $ret->addContentToBegin($buffer);

  }
  
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de la vista a renderizar para una acción.
   * ---------------------------------------------------------------------------
   * @param   $action   string  Nombre de la acción de la que se desea obtener
   *                            la vista.
   * @return  string            Nombre de la vista
   */
  final protected static function getViewName($action){

    return "{$action}.view.php";

  }

  /**
   * ---------------------------------------------------------------------------
   * Método para mezclar dos configuraciones.
   * ---------------------------------------------------------------------------
   * La mezcla se basa en los parámetros de 
   * @param   array  $confToRewrite   Configuración a sobreescribir.
   * @param   array  $conf            Configuración a agregar.
   * @return  array                   Configuraciones mezcladas.
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
   * ---------------------------------------------------------------------------
   * Incluye un controlador.
   * ---------------------------------------------------------------------------
   * @param   string   $control   Nombre del controlador a incluir.
   * @return  array               Configuración del controlador
   */
  final public static function includeController($controller){

    // Obtener configuraciones del controlador
    $confs = Am::getProperty('controllers');

    // Obtener valores por defecto
    $defaults = itemOr('defaults', $confs, array());

    // Si no existe configuracion para el controlador
    $conf = itemOr($controller, $confs, array());

    // Si no es un array, entonces el valor indica el path del controlador
    if(is_string($conf))
      $conf = array('root' => $conf);

    var_dump($conf);

    $conf['root'] = realPath(itemOr('root', $conf, self::DEFAULT_CONTROLLER_FOLDER));

    if(is_file($realFile = "{$conf['root']}/am.init.php"))
      require_once $realFile;

    // Mezclar con el archivo de configuracion en la raiz del
    // controlador.
    if(is_file($realFile = "{$conf['root']}/am.conf.php"))
      $conf = self::mergeConf($conf, require($realFile));

    // Asignar el nombre del controlador si no lo tiene.
    $conf['name'] = itemOr('name', $conf, $controller);

    // Si tiene no tiene padre o si el padre esta vacío
    // y se mezcla con la configuracion por defecto
    if(!isset($conf['parent']) || empty($conf['parent'])){

      // Mezclar con valores por defecto
      $conf = self::mergeConf($defaults, $conf);

      // Obtener el nombre del padre
      $parentControllerName = itemOr('name', $defaults, null);

    // Mezclar con configuracion del padre
    }else{

      // Obtener la configuracion del padre
      $confParent = self::includeController($conf['parent']);

      // Agregar carpeta de vistas por defecto del padre.
      $confParent['paths'][] = $confParent['root'];
      $confParent['paths'][] = $confParent['root'] . '/' . $confParent['views'];

      // Obtener el nombre del padre
      $parentControllerName = itemOr('name', $confParent, null);

      // Mezclar con la configuracion del padre
      $conf = self::mergeConf($confParent, $conf);

    }

    // Obtener la ruta del controlador
    // Incluir controlador si existe el archivo
    if(is_file($file = "{$conf['root']}/{$conf['name']}.controller.php")){
      require_once $file;
    }else{
      // Si no tiene un archivo que incluir se asigna como nombre de
      // controlador el nombre del controlador padre.
      $conf['name'] = $parentControllerName;
    }

    // Incluir como extension
    Am::load($conf['root'] . '/');

    // Retornar la configuracion obtenida
    return $conf;

  }

  /**
   * ---------------------------------------------------------------------------
   * Obtiene el controlador y la accion de un cadena de caracteres.
   * ---------------------------------------------------------------------------
   * @param  string       $actionStr  String con la accion en formato
   *                                  'controlador@accion'
   * @return false/array              Array asociativo con el controlador y
   *                                  la acción. Si no coincide con el formato
   *                                  devuelve false.
   */
  final private static function getControllerAndAction($actionStr){

    if(preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)@([a-zA-Z_][a-zA-Z0-9_]*)$/',
          $actionStr, $m))
      return array(
        'controller' => $m[1],
        'action' => $m[2]
      );
    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Pre procesador de rutas.
   * ---------------------------------------------------------------------------
   * Verifica y transforma la ruta del formato 'controlador@acction'.
   * @param  array  $route  Ruta a evaluar.
   * @return array          Ruta transformada.
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
   * ---------------------------------------------------------------------------
   * Manejador para el evento response.controller.
   * ---------------------------------------------------------------------------
   * Funcion para atender las respuestas por controlador. Recive , un array con
   * el entorno y un array con los parámetros obtenidos de la ruta.
   * @param   string  $action   La acción a ejecutar en formato del controlador
   *                            (controlador@accion).
   * @param   array  $env       Variables de entorno.
   * @param   array  $params    Argumentos obtenidos de la ruta.
   */
  final public static function response($action, array $env = array(),
                                  array $params = array()){

    $controller = $action['controller'];
    $action = $action['action'];

    // Valores por defecto
    $conf = array_merge(
      // Incluye el controlador y devuelve la configuracion para el mismo
      self::includeController($controller),

      // Asignar vista que se mostrará,
      array(
        'view' => self::getViewName($action),
        'action' => $action,
        'params' => $params,
      )
    );

    // Obtener la instancia del controlador
    $controller = Am::getInstance("{$conf['name']}Controller", $conf);

    // Si no se puede instanciar el controlador retornar false.
    
    if(null === $controller)
      return Am::e404(Am::t('AMCONTROLLER_ACTION_NOT_FOUND',
        $controller, $action));

    // Asignación de propiedades como propiedades del controlador.
    foreach ($env as $propertyName => $value)
      $controller->$propertyName = $value;

    // Devolver controlador
    return $controller;

  }

}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para renderizar Templates
 */
// PENDIENTE Documentar
class AmTpl extends AmObject{

  protected

    /**
     * Nombre de la vista a renderizar.
     */
    $file = null,

    /**
     * Variables de entorno.
     */
    $env = array(),

    /**
     * Vista padre.
     */
    $parent = null,

    /**
     * Listado de nonmbres de secciones abiertas.
     */
    $openSectionName = null,

    /**
     * Contenido de las secciones.
     */
    $sections = array(),

    /**
     * Contenido de la vista hija.
     */
    $child = null,

    // /**
    //  * Indica si se imprimó el contenido de la vista hija
    //  */
    // $printedChild = false,

    // /**
    //  * Lista de vista de las que depende la vista actual: Padre y anidadas.
    //  */
    // $dependences = array(),

    /**
     * Lista de directorios donde se buscará la vista.
     */
    $paths = array(),

    /**
     * Bandera que indica si se ignoran o no las vistas inexistentes.
     */
    $ignore = true,

    /**
     * Errores generados.
     */
    $errors = array(),

    /**
     * Parámetros con los que se inicializó la vista.
     */
    $options = array();

  /**
   * Constructor de la vista
   * @param string $file    Nombre de la vista a buscar.
   * @param array  $options Opciones de la vista.
   */
  public function __construct($file, array $options = array()){
    parent::__construct($options);

    // setear paths
    if(!is_array($this->paths))
      $this->paths[] = $this->paths;

    // Asignar atributos
    $this->options = $options;
    $this->file = $file;

  }

  /**
   * Determina la ruta de un archivo.
   * Busca un archivo en los directorios de la propiedad $this->paths y de
   * vuelve la primera aparición.
   * @param  string $file Nombre del archivo a buscar
   * @return string       Devuelve la ruta del primera aparición del $file
   *                      dentro de los directorios de $this->paths
   */
  public function findView($file){

    // Si no existe la vista mostrar error
    if(false === ($fileRet = findFileIn($file, $this->paths, false))){

      // Instanciar el error
      $error = Am::t('AMTPL_VIEW_NOT_FOUND', $file);
      $this->errors[] = $error;

      // Ignorar el error
      if(!$this->ignore)
        throw $error;

    }

    return $fileRet;

  }

  public function parent($view){

    $this->parent = $view;

  }

  public function section($name){

    if(isset($this->openSectionName))
      throw Am::e('AMTPL_SECTION_CREATE_INTO_OTHER_SECTION');

    $this->openSectionName = $name;
    ob_start();

  }

  /**
   * Cerrar sección
   */
  public function endSection(){

    // Si no existen secciones abiertas entonces mostrar error
    if(!isset($this->openSectionName))
      throw Am::e('AMTPL_UNOPENED_SECTION');

    // Obtener el nombre de la ultima seccion abierta
    $name = $this->openSectionName;
    $this->openSectionName = null;

    $content = ob_get_clean();

    // Agregar seccion si no existe
    // Obtener directivas del nombre de la seccion
    preg_match('/^([+]?)(.*[^+])([+]?)$/', $name, $m);
    array_shift($m);
    list($start, $name, $end) = $m;

    // Crear seccion si no existe
    if(!isset($this->sections[$name]))
      $this->sections[$name] = '';

    // Agregar al principio
    if($start === '+')
      $this->sections[$name] = $content . $this->sections[$name];

    // Agregar al final
    elseif($end === '+')
      $this->sections[$name] = $this->sections[$name] . $content;

    else
      $this->sections[$name] = $content;

  }

  public function deleteSection($name){

    unset($this->sections[$name]);

  }

  public function place($view, $env = array()){

    $file = $this->findView($view);
    if($file)
      return $this->r(file_get_contents($file), $env);

  }

  public function insert($view, $env = array()){

    echo $this->place($view, $env);

  }

  public function put($sectionName, $env = array()){

    echo $this->r(itemOr($sectionName, $this->sections), $env);

  }

  public function child(){

    echo $this->child;
    $this->child = null;

  }

  public function a(array $originalAttrs){
    $attrs = $originalAttrs;
    array_walk($attrs, function(&$a, $b) { $a = $b.'="'.$a.'"'; });
    return implode(' ', $attrs);
  }

  public function t($tag, array $attrs){
    $attrs = $this->a($attrs);
    return "<{$tag} {$attrs}>";
  }

  public function endt($tag){
    return "</{$tag}>";
  }

  public function tag($tag, array $attrs){
    echo $this->t($tag, $attrs);
  }

  public function endTag($tag){
    echo $this->endt($tag);
  }

  public function form(array $attrs){
    $csrf = AmCSRFGuard::create();

    echo $this->t('form', $attrs);
    echo $this->t('input', array(
      'type' => 'hidden',
      'name' => AmCSRFGuard::FieldnameName,
      'value' => $csrf->name,
    ));
    echo $this->t('input', array(
      'type' => 'hidden',
      'name' => AmCSRFGuard::FieldnameToken,
      'value' => $csrf->token,
    ));

  }

  public function endForm(){

    echo $this->endt('form');

  }

  private function r($content, array $env = array()){

    $content = explode('(:/:)', $content);
    $content = implode('(:= Am::url() :)', $content);

    $content = explode('(:=', $content);
    $content = implode('(: echo ', $content);

    $content = explode('(:', $content);

    $__ = array_merge($this->env, $env);
    $this->isOpenSection = false;
    $prevParent = $this->parent;
    $this->parent = null;

    ob_start();
    echo array_shift($content);
    $this->_r($content);
    $viewCode = ob_get_clean();

    $before = array_keys($__);
    $call = function($_) use (&$__){
      unset($__['_']);
      unset($__['__']);
      extract($__);
      ob_start();
      eval("?>{$_}");
      $__ = get_defined_vars();
      return ob_get_clean();
    };

    $child = $call($viewCode);

    $this->env = $__;
    if(isset($this->parent)){
      $this->child = $child;
      $child = $this->place($this->parent, $this->env);
    }

    $this->parent = $prevParent;
    return $child;

  }

  private function _r(array $content){

    if(empty($content)) return;

    $line = array_shift($content);

    $glue = ':)';
    $part = explode($glue, $line);
    if(count($part)===1){
      $glue = "\n";
      $part = explode($glue, $line);
    }

    $code = trim(array_shift($part));
    $str = implode($glue, $part);
    $method = false;

    $reg = '/^(parent|insert|section|endSection|deleteSection|put|child|tag|endTag|form|endForm)\:?(.*)/';

    if(preg_match_all($reg, $code, $m)){
      $method = $m[1][0];
      $params = $m[2][0];

      if(in_array($method, array('put', 'insert')))
        $code = "\$this->$method($params, get_defined_vars())";
      else
        $code = "\$this->$method($params)";

    }

    if($method === 'endSection'){
      if($this->isOpenSection === true)
        $this->isOpenSection = false;
      else
        throw Am::e('AMTPL_UNOPENED_SECTION');
    }

    if($this->isOpenSection === true)
      echo "(:$line";
    else
      echo "<?php {$code} ?>{$str}";

    if($method === 'section'){

      if($this->isOpenSection === true)
        throw Am::e('AMTPL_SECTION_CREATE_INTO_OTHER_SECTION');
      else
        $this->isOpenSection = true;

    } 

    $this->_r($content);

  }

  /**
   * Renderiza compila la vista e imprime el contenido.
   */
  public function render(){

    // Determinar ruta real de la vista
    return $this->place($this->file);

  }

  /**
   * Indica si se generó un error durante el compilado de la vista.
   * @return bool Si tiene o no errores
   */
  public function hasError(){

    return count($this->errors)>0;

  }

  /**
   * Manejador para el evento render.template.
   * @param  string $tpl     Nombre de la vista a renderizar
   * @param  array  $vars    Variables para el renderizado.
   * @param  array  $options Opciones para la vista.
   * @return bool            Si se generó o no errores en el renderizado.
   */
  public static function renderize($tpl, array $vars = array(),
                                   array $options = array()){

    // header('content-type:text/plain');

    // Determinar las variables de entorno
    // Mazclar las que viene enlas opciones con las recibidas por parámetro
    $options['env'] = array_merge(itemOr('env', $vars, array()), $vars);

    // Instancia vista
    $view = new self($tpl, $options);

    // Compilar y guardar
    echo $view->render();

    return !$view->hasError();

  }

}

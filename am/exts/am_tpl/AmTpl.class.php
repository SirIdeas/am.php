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
final class AmTpl extends AmObject{

  protected

    /**
     * Nombre de la vista a renderizar.
     */
    $file = null,

    /**
     * Ruta real de la vista a renderizar.
     */
    $realFile = null,

    /**
     * Contenido del archivo.
     */
    $content = '',

    /**
     * Contenido del archivo.
     */
    $buffer = '',

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
    $openSections = array(),

    /**
     * Contenido de las secciones.
     */
    $sections = array(),

    /**
     * Contenido de la sección abierta
     */
    $currentSection = '',

    // /**
    //  * Contenido de la vista hija.
    //  */
    // $child = null,

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

    // Determinar ruta real de la vista
    $this->realFile = $this->findView($file);

    // Leer archivo
    if($this->realFile !== false)
      $this->content = file_get_contents($this->realFile);

    // // Obtener padre
    // preg_match_all('/\(::[ ]*parent:(.*):\)/', $this->content, $parents);
    // $this->parent = trim(array_pop($parents[1]));

    // // Quitar sentencias de padres
    // $this->content = implode('',
    //   preg_split('/\(::[ ]*parent:(.*):\)/',
    //   $this->content)
    // );

    // // Obtener lista de hijos en comandos place
    // preg_match_all('/\(::[ ]*place:(.*):\)/',
    //   $this->content, $dependences1);

    // // Obtener lista de hijos en comandos put
    // $dependences = $dependences1[1];

    // // Instanciar padre dentro de las dependencias
    // if(!empty($this->parent))
    //   array_unshift($dependences, $this->parent);

    // $this->dependences = array();

    // // Inicializar las dependencias'
    // foreach ($dependences as $views){
    //   $views = trim($views);
    //   if(!empty($views))
    //     $this->dependences[$views] = false;
    // }

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

  // /**
  //  * Compilar la vista.
  //  * @param  string $child    Contenido de la vista hija
  //  * @param  array  $sections Array de secciones heredadas
  //  * @return array            Se retorna en un array el resultado de la
  //  *                          renderización de la vista, las secciones
  //  *                          generadas, el entorno de la aplicación y los
  //  *                          errores generados.
  //  */
  // public function compile($child = null, array $sections = array()){

  //   // Asignar secciones recibidas
  //   $this->sections = $sections;
  //   $this->child = $child;  // Contenido de un vista hija

  //   // Dividir por comandos
  //   $parts = preg_split('/\(::(.*):\)/', $this->content);

  //   // Obtener comando
  //   preg_match_all('/\(::(.*):\)/', $this->content, $cmds);
  //   $cmds = $cmds[1];

  //   ob_start(); // Para optener todo lo que se imprima durante el compilad

  //   // Recorrer las partes entre los comando
  //   foreach($parts as $i => $part){
  //     echo $part; // Imprimir la parte actual
  //     if(isset($cmds[$i])){ // Si existe un comando en la misma posicion

  //       // Obtener parametros del comando
  //       $cmds[$i] = explode(':', trim($cmds[$i]));
  //       $method = array_shift($cmds[$i]);
  //       $params = $cmds[$i];

  //       // Si no existe un metodo con el mismo nombre del comando mostrar error
  //       if(!method_exists($this, $method))
  //         throw Am::e('AMTPL_METHOD_NOT_FOUND', $method);

  //       // Llamado el metodo
  //       call_user_func_array(array($this, $method), $params);

  //     }

  //   }

  //   // Obtener el contenido
  //   $content = ob_get_clean();

  //   $content = preg_replace('/\(:\/:\)/', '<?php echo Am::url() ? >', $content);
  //   $content = preg_replace('/\(:=[ ]*(.*):\)/', '<?php echo ${1} ? >', $content);
  //   $content = preg_replace('/\(:(.*):\)/', '<?php ${1} ? >', $content);

  //   // Si la vista tiene un padre
  //   if(!empty($this->parent)){
  //     // Obtener instancia de vista del padre
  //     $parentView = $this->getSubView($this->parent)
  //       // Compilar padre
  //       ->compile($content, $this->sections);

  //     // Mezclar generadas en el padre con las definidas en la vista acutal
  //     $this->env = $parentView['env'] = array_merge(
  //       $parentView['env'], $this->env);
  //     $this->errors = array_merge($this->errors, $parentView['errors']);
  //     return $parentView;
  //   }

  //   // Si no se imprimió el resultado del hijo se agrega al final de la vista.
  //   if(!$this->printedChild)
  //     $content .= $this->child;

  //   return array(
  //     'content'   => $content,        // Todo lo impreso
  //     'sections'  => $this->sections, // Devolver secciones definidas
  //     'env'       => $this->getEnv(), // Variables definidas
  //     'errors'    => $this->errors    // Indica si se generó un error
  //   );

  // }

  // /**
  //  * Devuelve la instancia de una vista pasando como opciones la vista actual.
  //  * @param  string $name Nombre del archivo a buscar
  //  * @return AmTpl        Instancia de AmTpl con la nueva vista
  //  */
  // public function getSubView($name){

  //   // Si no esta definida la dependiencia mostrar error
  //   if(!isset($this->dependences[$name]))
  //     throw Am::e('AMTPL_SUBVIEW_NOT_FOUND', $name, $this->realFile);

  //   // Si la dependencia no es una instancia de AmView
  //   if(!$this->dependences[$name] instanceof self){
  //     // Se instancia la vista
  //     $this->dependences[$name] = new self($name, $this->options);
  //   }

  //   // Devolver instancia de la vista
  //   return $this->dependences[$name];

  // }

  // /**
  //  * Imprimir una vista.
  //  * Crea la instancia de una subvista y e imprime su contenido.
  //  * @param string $view Nombre de la vista a renderizar.
  //  */
  // public function place($name){
  //   // Instancia la subvista.
  //   $view = $this->getSubView($name)->compile('', $this->sections);

  //   // Imprimir su contenido
  //   echo $view['content'];

  //   // Mezclar las seciones definidas en la subvista con las de la vista actual
  //   $this->sections = array_merge($view['sections'], $this->sections);

  //   // Mezclar tamvien el entorno y los errores
  //   $this->env = array_merge($view['env'], $this->env);
  //   $this->errors  = array_merge($this->errors, $view['errors']);

  // }

  // /**
  //  * Imprimir una sección.
  //  * @param string $name Nombre de la sección a imprimir.
  //  */
  // public function put($name){
  //   // Obtener la sección si existe e imprimirla
  //   $section = isset($this->sections[$name])? $this->sections[$name] : '';
  //   echo $section;
  // }

  // /**
  //  * Abrir una sección.
  //  * @param string $name Nombre que se le dará a la nueva sección.
  //  */
  // public function section($name){
  //   $this->openSections[] = $name;
  //   ob_start();
  // }

  // /**
  //  * Imprime el contenido de la vista hija
  //  */
  // public function child(){
  //   $this->printedChild = true;
  //   echo $this->child;
  // }

  // /**
  //  * Agrega una variable de entorno.
  //  * Puede recibir un string con el formato varname=valor, o dos string donde el
  //  * primero el el varname y el segundo el valor.
  //  */
  // public function set(){
  //   $args = func_get_args();

  //   if(count($args)!=2)
  //     $args = explode('=', implode(':', $args));

  //   if(count($args)!=2)
  //     throw Am::e('AMTPL_SET_BAD_ARGS_NUMBER');
    
  //   $this->_set($args[0], $args[1]);
    
  // }

  // /**
  //  * Callback de la asignación de la variable de entorno
  //  */
  // private function _set(){
  //   extract($this->getEnv());
  //   eval('$this->env[\''.
  //     trim(func_get_arg(0)).'\'] = '.
  //     trim(func_get_arg(1)).';');
  // }

  // /**
  //  * Devuelve las variables de entorno.
  //  * @return hash Hash de las variables de entorno.
  //  */
  // public function getEnv(){
  //   return $this->env;
  // }

  // /**
  //  * Vistas de las que depende.
  //  * @return array Lista de todos las subvista que se incluyen en la vista.
  //  */
  // public function dependences(){

  //   // La primera dependencia es el archivo pripio de la vista
  //   $dependences = array($this->realFile);

  //   // Sedebe agregar las dependencias de las vistas relacionadas (padre hijas, y anidadas)
  //   foreach ($this->dependences as $key => $value) {
  //     $dependences = array_merge($dependences, $this->getSubView($key)->dependences());
  //   }

  //   return $dependences;

  // }

  private function e($str){

    if(empty($this->openSections)){

      $this->buffer .= $str;

    }else{

      $this->currentSection .= $str;      

    }

  }

  public function parent($view){

    $this->parent = $view;

  }

  public function place($view){

    // $file = $this->findView($view);
    // if($file)
    //   $this->_render(file_get_contents($file));

  }

  public function section($name){

    $this->openSections[] = $name;
    ob_start();

  }

  /**
   * Cerrar sección
   */
  public function endSection(){

    // Si no existen secciones abiertas entonces mostrar error
    if(empty($this->openSections))
      throw Am::e('AMTPL_UNOPENED_SECTION');

    // Obtener el nombre de la ultima seccion abierta
    $name = array_pop($this->openSections);

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

  public function put($view){

  }

  public function child(){

  }

  /**
   * Renderiza compila la vista e imprime el contenido.
   */
  public function render(){

    ob_start();
    $this->_render($this->content);
    $viewCode = ob_get_clean();
    // echo $viewCode;
    eval("?>{$viewCode}");
    var_dump($this->sections);

  }

  public function _render($content){

    $reg = '/(parent|place|section|endSection|put|child)\:?(.*)/';
    $parts = explode('(:', $content);
    echo array_shift($parts);
    
    foreach($parts as $line){
      $part = explode(':)', $line);
      if(count($part)===1){
        $part = explode("\n", $part[0]);
        if(count($part)===2){
          $part[1] = "\n".$part[1];
        }
      }
      $code = trim($part[0]);
      $str = itemOr(1, $part, '');

      if(preg_match_all($reg, $code, $m)){
        $method = $m[1][0];
        $params = $m[2][0];
        $code = "\$this->$method($params)";
      }

      echo "<?php {$code} ?>{$str}";

    }

    // // Obtener contenido compilado de la vista
    // $this->result = $this->compile($this->child);

    // // Guardar vista minificada
    // $this->result['content'] = trim($this->result['content']);
    // if(!empty($this->result['content'])){
    //   extract($this->getEnv());  // Crear variables
    //   eval("? > {$this->result['content']}");
    // }

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

    header('content-type:text/plain');

    // Determinar las variables de entorno
    // Mazclar las que viene enlas opciones con las recibidas por parámetro
    $options['env'] = array_merge(itemOr('env', $vars, array()), $vars);

    // Instancia vista
    $view = new self($tpl, $options);

    // Compilar y guardar
    $view->render();

    return !$view->hasError();

  }

}

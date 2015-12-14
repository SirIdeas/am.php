<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para renderizar Templates
 * -----------------------------------------------------------------------------
 */

final class AmTpl extends AmObject{

  protected
    $file = null,             // Vista a buscar
    $content = '',            // Contenido del archivo
    $env = array(),           // Entorno
    $parent = null,           // Vista padre
    $openSections = array(),  // Lista de secciones abiertas
    $sections = array(),      // Lista de secciones y su contenido
    $child = null,            // Contenido de la vista hija
    $dependences = array(),   // Lista de vistas de las que depende (padre, hijas y anidadas)
    $paths = array(),         // Lista de directorios donde se buscará la vista
    $ignore = true,           // Bandera que indica si se ignoran las vistas inexistentes sin generar error
    $errors = array(),        // Indica si se generó o no un error durante el renderizado
    $options = array();       // Guarda los parametros con los que se inicializó la vista

  public function __construct($file, $options = array()){
    parent::__construct($options);

    // setear paths
    if(!is_array($this->paths))
      $this->paths[] = $this->paths;

    // Asignar atributos
    $this->options = $options;
    $this->file = $file;
    $this->realFile = $this->findView($file);

    // Leer archivo
    if($this->realFile !== false)
      $this->content = file_get_contents($this->realFile);

    // Obtener padre
    preg_match_all('/\(::[ ]*parent:(.*):\)/', $this->content, $parents);
    $this->parent = trim(array_pop($parents[1]));

    // Quitar sentencias de padres
    $this->content = implode('',
      preg_split('/\(::[ ]*parent:(.*):\)/',
      $this->content)
    );

    // Obtener lista de hijos en comandos place
    // PENDIENTE: 'put =' no esta funcionando
    preg_match_all('/\(::[ ]*(place:(.*)|put:.*=(.*)):\)/',
      $this->content, $dependences1);

    // Obtener lista de hijos en comandos put
    $dependences = array_merge($dependences1[2], $dependences1[3]);

    // Instanciar padre dentro de las dependencias
    if(!empty($this->parent))
      array_unshift($dependences, $this->parent);

    $this->dependences = array();

    foreach ($dependences as $views){
      $views = trim($views);
      if(!empty($views))
        $this->dependences[$views] = false;
    }

    // if(!empty($this->dependences))
    //   $this->dependences = array_keys(array_filter(
    //     array_combine($this->dependences, $this->dependences)
    //   ));

    // Convertir el array de dependencias a un array asociativo
    // donde todos los valores sean false
    // if(0<count($this->dependences)){
    //   $this->dependences = array_combine(
    //     $this->dependences,
    //     array_fill(0, count($this->dependences), false)
    //   );
    // }

  }

  // Busca una vista en los paths definidos
  public function findView($file){
    // Si no existe la vista mostrar error
    if(false === ($fileRet = findFileIn($file, $this->paths))){
      $this->errors[] = "Am: No existe view '{$file}.'";
      $this->ignore or die(implode(',', $this->errors));
    }
    return $fileRet;
  }

  // Compilar la vista
  public function compile($child = null, array $sections = array()){

    // Asignar secciones recibidas
    $this->sections = $sections;
    $this->child = $child;  // Contenido de un vista hija

    // Dividir por comandos
    $parts = preg_split('/\(::(.*):\)/', $this->content);

    // Obtener comando
    preg_match_all('/\(::(.*):\)/', $this->content, $cmds);
    $cmds = $cmds[1];

    ob_start(); // Para optener todo lo que se imprima durante el compilad

    // Recorrer las partes entre los comando
    foreach($parts as $i => $part){
      echo $part; // Imprimir la parte actual
      if(isset($cmds[$i])){ // Si existe un comando en la misma posicion

        // Obtener parametros del comando
        $cmds[$i] = explode(':', trim($cmds[$i]));
        $method = array_shift($cmds[$i]);
        $params = $cmds[$i];

        // Si no existe un metodo con el mismo nombre del comando mostrar error
        method_exists($this, $method) or die("Am: unknow method ".get_class($this)."->{$method}");

        // Si el metodo es set
        if($method == 'set')
          // Se divide el argumento en dos parametros
          $params = explode('=', implode(':', $params));

        // Llamado el metodo
        call_user_func_array(array($this, $method), $params);

      }

    }

    // Obtener el contenido
    $content = ob_get_clean();

    $content = preg_replace('/\(:\/:\)/', '<?php echo Am::url() ?>', $content);
    $content = preg_replace('/\(:=[ ]*(.*):\)/', '<?php echo ${1} ?>', $content);
    $content = preg_replace('/\(:(.*):\)/', '<?php ${1} ?>', $content);

    // Si la vista tiene un padre
    if(!empty($this->parent)){
      // Obtener instancia de vista del padre
      $parentView = $this->getSubView($this->parent)
        // Compilar padre
        ->compile($content, $this->sections);

      // Mezclar generadas en el padre con las definidas en la vista acutal
      $this->env = $parentView['env'] = array_merge($parentView['env'], $this->env);
      $this->errors = array_merge($this->errors, $parentView['errors']);
      return $parentView;
    }

    return array(
      'content'   => $content,        // Todo lo impreso
      'sections'  => $this->sections, // Devolver secciones definidas
      'env'       => $this->getEnv(), // Variables definidas
      'errors'    => $this->errors    // Indica si se generó un error
    );

  }

  // Obtiene una vista con el mismo entorno de la vista actual
  public function getSubView($name){
    // Si no esta definida la dependiencia mostrar error
    if(!isset($this->dependences[$name])){
      throw new Exception("Am: not found subview \"{$name}\" in \"{$this->realFile}\"");
    }
    // Si la dependencia no es una instancia de AmView
    if(!$this->dependences[$name] instanceof self){
      // Se instancia la vista
      $this->dependences[$name] = new self($name, $this->options);
    }
    // Devolver instancia de la vista
    return $this->dependences[$name];
  }

  // Inserta una vista anidada
  public function place($view){
    $view = $this->getSubView($view)->compile('', $this->sections);
    echo $view['content'];
    $this->sections = array_merge($view['sections'], $this->sections);
    $this->env = array_merge($view['env'], $this->env);
    $this->errors  = array_merge($this->errors, $view['errors']);
  }

  // Imprimir una seccion
  public function put($name){
    // // Si tiene una vista por defecto se carga
    // PENDIENTE: PAra hacer funcionar 'put ='
    // if(preg_match('/(.*)=(.*)/', $name, $m)){

    //   array_shift($m);
    //   list($name, $path) = $m;
    //   $name = trim($name);
    //   $path = trim($path);
    //   $this->place($path);

    // }

    $section = isset($this->sections[$name])? $this->sections[$name] : '';
    echo $section;

  }

  // Abrir una seccion
  public function section($name){
    $this->openSections[] = $name;
    ob_start();
  }

  // Cerrar seccion
  public function endSection(){
    // Si no existen secciones abiertas entonces mostrar error
    !empty($this->openSections) or die('Am: closing section unopened');

    // Obtener lo impreso hasta hora
    $content = ob_get_clean();

    // Obtener el nombre de la ultima seccion abierta
    $name = array_pop($this->openSections);

    // Agregar seccion si no existe
    // Obtener directivas del nombre de la seccion
    preg_match('/^([+]?)(.*[^+])([+]?)$/', $name, $m);
    array_shift($m);
    list($start, $name, $end) = $m;

    // Crear seccion si no existe
    if(!isset($this->sections[$name]))
      $this->sections[$name] = '';

    // No se recibió comandos
    if(empty($start) && empty($end))
      $this->sections[$name] = $content;

    // Agregar al principio
    if($start === '+')
      $this->sections[$name] = $content . $this->sections[$name];

    // Agregar al final
    if($end === '+')
      $this->sections[$name] = $this->sections[$name] . $content;

  }

  // Imprimir el contenido de la vista hija
  public function child(){
    echo $this->child;
  }

  // Agregar variable
  public function set(){
    extract($this->getEnv());
    eval('$this->env[\''.
      trim(func_get_arg(0)).'\'] = '.
      trim(func_get_arg(1)).';');
  }

  // Obtener variables de la vista. Cinluye el entorno + las variables definidas en la vista
  public function getEnv(){
    return $this->env;
  }

  // Obtener lista de dependencas
  public function dependences(){

    // La primera dependencia es el archivo pripio de la vista
    $dependences = array($this->realFile);

    // Sedebe agregar las dependencias de las vistas relacionadas (padre hijas, y anidadas)
    foreach ($this->dependences as $key => $value) {
      $dependences = array_merge($dependences, $this->getSubView($key)->dependences());
    }

    return $dependences;

  }

  // Generar vista
  public function render(){

    // Obtener contenido compilado de la vista
    $this->result = $this->compile($this->child);

    // Guardar vista minificada
    $this->result['content'] = trim($this->result['content']);
    if(!empty($this->result['content'])){
      extract($this->getEnv());  // Crear variables
      // ob_start();
      eval("?> {$this->result['content']}");
      // $ret = trim(ob_get_clean());
      // $end = substr($ret, strlen($ret)-2);
      // $ret = substr($ret, 0, strlen($ret)-2);
      // echo $ret;
    }

  }

  // Método que indica si se generó algun error al renderizar la vista
  public function hasError(){
    return count($this->errors)>0;
  }

  // Funcion para atender el llamado de render.tempalte
  public static function renderize($tpl, array $params = array()){

    // Instancia vista
    $view = new self($tpl, array('env' => $params));

    // Compilar y guardar
    $view->render();

    return !$view->hasError();

  }

}

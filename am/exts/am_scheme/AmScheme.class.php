<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para representar una conexión a una base de datos.
 */
abstract class AmScheme extends AmObject{

  protected static

    /**
     * Instancias de los esquemas cargados.
     */
    $schemes = array();

  protected

    /**
     * String con el nombre clave de la conexión. Se asume es único.
     */
    $name = null,

    /**
     * String con el prefijo para las clases nacidas de esta fuente.
     */
    $prefix = null,

    /**
     * String con el driver utilizado en la fuente.
     */
    $driver = null,

    /**
     * String con el nombre de la base de datos a conectarse.
     */
    $database = null,

    /**
     * String con la dirección o nombre del servidor.
     */
    $server = null,

    /**
     * int/string con número del puerto para la conexión.
     */
    $port = null,

    /**
     * String con el nombre de usuario para la conexion.
     */
    $user = null,

    /**
     * String con el password para la conexion.
     */
    $pass = null,

    /**
     * String con el set de charset.
     */
    $charset = null,

    /**
     * String con el set de reglas para de caracteres.
     */
    $collation = null,

    /**
     * Hash con los las instancias por modelos de la conexión.
     */
    $tables = array(),

    /**
     * Equivalencias entre los tipos de datos del DBMS y PHP
     */
    $types = array(),

    /**
     * Hash de tamaños de subtipos
     */
    $lenSubTypes = array(),

    /**
     * Tipos por defecto de cada subtipo
     */
    $defaultsBytes = array();

  /**
   * Constructor. Se conecta en el contructor.
   */
  public function __construct($params = array()){
    parent::__construct($params);

    $this->connect(); // Conectar la fuente

  }

  /**
   * El destructor del objeto cierra la conexión
   */
  public function __destruct() {

    $this->close();

  }
    
  /**
   * Devuelve el nombre del esquema a la que referencia.
   * @return string Nombre del esquema.
   */
  public function getName(){
    
    return $this->name;

  }
    
  /**
   * Devuelve el prefijo para los modelos del esquema.
   * @return string Prefijo para clases.
   */
  
  public function getPrefix(){
    
    return $this->prefix;

  }
    
  /**
   * Devuelve el nombre driver de conexión.
   * @return string Nombre del driver.
   */
  public function getDriver(){
    
    return $this->driver;

  }
    
  /**
   * Devuelve el nombre de la base de datos.
   * @return string Nombre de la base de datos.
   */
  public function getDatabase(){
    
    return $this->database;

  }
    
  /**
   * Devuelve el nombre o dirección del servidor.
   * @return string Nombre o dirección del servidor.
   */
  public function getServer(){
    
    return $this->server;

  }
    
  /**
   * Devuelve el Número del puerto para la conexión.
   * @return int/string Número de puerto para la conexión.
   */
  public function getPort(){
    
    return $this->port;

  }
    
  /**
   * Devuelve el nombre de usuario para la conexión.
   * @return string Nombre de usuario para la conexión.
   */
  public function getUser(){
    
    return $this->user;

  }
    
  /**
   * Devuelve el password para la conexión.
   * @return string Password para la conexión.
   */
  public function getPass(){
    
    return $this->pass;

  }
    
  /**
   * Devuelve el charset.
   * @return string Charset.
   */
  public function getCharset(){
    
    return $this->charset;

  }
    
  /**
   * Devuelve el reglas de caracteres.
   * @return string Coleción de reglas para los caracteres.
   */
  public function getCollation(){
    
    return $this->collation;

  }

  /**
   * Obtiene un hash de los subtipos de un tipo de datos en el DBSM.
   * @param  $string $type  Nombre del tipo de datos.
   * @return hash           Hash con la colección de subtipos.
   */
  public function getLenSubTypes($type){

    return itemOr($type, $this->lenSubTypes);
    
  }

  /**
   * Devuelve un tipo de datos para el DBMS dependiendo de un tipo de datos
   * de lenguaje y la longuitud del mismo.
   * @param  string $type Tipo de datos en el lenguaje.
   * @param  int    $len  Longuitud del tipo de datos.
   * @return string       Tipo de datos en el DBMS.
   */
  protected function getTypeByLen($type, $len){
    $types = $this->getLenSubTypes($type);
    $index = array_search($len, $types);
    if($index)
      return $index;
    return itemOr($type, $this->defaultsBytes);
  }

  /**
   * Devuelve el nombre de un objeto de la BD pasado por realScapeString y por
   * nameWrapper.
   * @param  string $name Nombre que se desea escapar y colocar entre comillas.
   * @return string       Resultado de la operación.
   */
  public function nameWrapperAndRealScape($name){

    return $this->nameWrapper($this->realScapeString($name));

  }

  /**
   * Prepara el nombre complete de un objeto. Primero los separa por partes en
   * puntos luego los pasa por la función <code>nameWrapperAndRealScape</code>.
   * @param  string $name Nombre relativo del objeto.
   * @return string       Nombre procesado.
   */
  public function nameWrapperAndRealScapeComplete($name){

    // Dividir en puntos
    $nameArr = explode('.', (string)$name);

    // Preparar el nombre de cada parte del campo
    foreach ($nameArr as $key => $value) {
      if(!isNameValid($value))
        throw Am::e('AMSCHEME_INVALID_NAME', $name);
        
      $nameArr[$key] = $this->nameWrapperAndRealScape($value);
    }

    // Pegar campos
    return implode('.', $nameArr);

  }

  /**
   * Devuelve una cadena espacada y entre comillas.
   * @param  string $string Cadena que se desea escapar y colocar entre
   *                        comillas.
   * @return string         Resultado de la operación.
   */
  public function stringWrapperAndRealScape($string){

    return $this->stringWrapper($this->realScapeString($string));
    
  }

  /**
   * Agrega una tabla a la conexión.
   * @param  AmTable $table Tabla a agregar.
   * @return $this
   */
  public function addTable(AmTable $table){
    
    // Se agrega la tabla en la posición del modelo.
    $this->tables[$table->getModel()] = $table;
    return $this;

  }

  /**
   * Devuelve la instancia de una tabla correspondiente a un modelo. Si no
   * existe la instancia para dicho modelo devuelve null.
   * @param  string  $model Nombre del modelo.
   * @return AmTable        Devuelve la instancia de la tabla si existe.
   */
  public function getTableInstance($model = null){

    // Devuelve la instancia si existe
    return itemOr($model, $this->tables);

  }

  /**
   * Devuelve si la instancia para un modelo está cargada.
   * @param  string $model  Nombre del modelo a consultar.
   * @return bool           Indica si existe la instancia de la tabla para el
   *                        modelo.
   */
  public function hasTableInstance($model){

    // Inidica si existe la instancia
    return isset($this->tables[$model]);

  }

  /**
   * Devuelve los modelos del esquema generados en la aplicación
   * @return array Array de strings con los nombres de los modelos.
   */
  public function getGeneratedModels(){

    $ret = array();

    // Buscar los modelo dentro de la carpeta del esquema
    $ret = amGlob($this->getDir(), array(

      // Obtener solo los archivos de configuración
      'include' => '/.*[\/\\\\](.*)\.conf\.php/',

      // No buscar recursivamente
      'recursive' => false,

      // Indica que sección de la regez devolverá.
      'return' => 1

    ));

    return $ret;

  }

  /**
   * Devuelve el directorio de modelos del schema actual.
   * @return string Directorio de modelos de esquema.
   */
  public function getDir(){

    // Obtener el nombre del esquema
    $name = $this->getName();

    // Obtener el nombre del esquema por defecto su directorio raíz es el mismo
    // directorio de esquemas.
    return Am::getDir('schemes') . (!empty($name)? "/{$name}" : '');

  }

  /**
   * Devuelve la nombre del modelo para identificarlo dentro de todos los
   * esquemas de la aplicación. El formato es: :<schemeName>@<tableName>
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return string        Nombre del modelo.
   */
  public function getSchemeModelName($model){

    return ':'.$this->name.'@'.underscore($model);

  }

  /**
   * Devuelve el nombre de la clase del modelo.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return string        Nombre de la clase del modelo.
   */
  public function getBaseModelClassName($model){

    return $this->getPrefix() . camelCase($model, true).'Base';

  }

  /**
   * Devuelve la dirección del archivo de configuración del modelo.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return string        Dirección del archivo de configuración.
   */
  public function getBaseModelConfFilename($model){

    return $this->getDir() .'/'. underscore($model) .'.conf.php';

  }

  /**
   * Devuelve la dirección de la clase base del model.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return string        Dirección de la clase base del modelo.
   */
  public function getBaseModelClassFilename($model){

    return $this->getDir() .'/'. $this->getBaseModelClassName($model) .'.class.php';

  }

  /**
   * Indica si existe la clase base de un modelo y su configuración.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return bool          Si existe o no el modelo.
   */
  public function existsBaseModel($model){

    // Preguntar si existen el archivo del modelo base y el del controlador.
    return is_file($this->getBaseModelConfFilename($model))

        && is_file($this->getBaseModelClassFilename($model));
  }

  /**
   * Devuelve la configuración de un modelo base leída desde su archivo de
   * copnfiguración.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return hash          Hash de propiedades del modelo.
   */
  public function getBaseModelConf($model){
  
    // Si el archivo existe retornar la configuración    
    if(is_file($confFilePath = $this->getBaseModelConfFilename($model)))
      return require $confFilePath;

    // Si no existe retornar falso
    return false;

  }

  // /**
  //  * Crea el archivo con la clase del modelo base basando en una tabla.
  //  * @param  AmTable $table Tabla en el que se basará el modelo a generar.
  //  * @return bool           Si se generó o no el modelo.
  //  */
  // public function generateBaseModelFile(AmTable $table){

  //   // Obtener el nombre del archivo destino
  //   $path = $this->getBaseModelClassFilename($table->getTableName());
    
  //   // Crear directorio donde se ubicará el archivo si no existe
  //   Am::mkdir(dirname($path));

  //   // Generar el archivo
  //   return !!file_put_contents($path, "<?php\n\n" .
  //     AmGenerator::classBaseModel($this, $table));
    
  // }

  // /**
  //  * Genera la clase base del modelo y su archivo de configuración.
  //  * @param  AmTable $table Tabla en el que se basará el modelo a generar.
  //  * @return Hash           Resultado de la generación del archivo de
  //  *                        configuración y el modelo.
  //  */
  // public function generateBaseModel(AmTable $table){
    
  //   // Obtener la ruta del archivo
  //   $file = $this->getBaseModelConfFilename($table->getTableName());

  //   // Crear directorio donde se ubicará el archivo
  //   Am::mkdir(dirname($file));

  //   // Crear archivo de configuración
  //   $writed = file_put_contents($file, AmCoder::encode($table->toArray()));

  //   return array(

  //     // Si el archivo fue creado o no
  //     'conf' => $writed,

  //     // Crear clase
  //     'model' => $this->generateBaseModelFile($table)

  //   );
    
  // }

  // /**
  //  * Genera todos los modelos bases correspondientes a las tablas de un esquema.
  //  * @return hash Resultado de la generación de la configuración y el modelo.
  //  */
  // public function generateScheme(){

  //    // Para retorno
  //   $ret = array(
  //     'tables' => array(),
  //   );

  //   // Obtener listado de nombres de tablas
  //   $tables = $this->queryGetTables()->col('tableName');

  //   foreach ($tables as $tableName)

  //     // Obtener instancia de la tabla
  //     $ret['tables'][$tableName] = $this->generateBaseModel(
  //       $this->getTableFromScheme($tableName)
  //     );

  //   return $ret;

  // }

  /**
   * Devuelve el nombre de la BD para ser reconocida en el DBSM.
   * @return string Nombre de la BD para ser utilizada dentro del DBSM.
   */
  public function getParseDatabaseName(){

    return $this->getParseName($this->getDatabase());

  }

  /**
   * Devuelve el nombre de una tabla para ser reconocida en el DBSM
   * @param  string/AmTable/AmQuey $table Tabla de la que se desea obtener le
   *                                      nombre. Puede ser un string y una
   *                                      instancia de AmTable.
   * @param  bool                  $only  Si se devuelve el nombre de la tabla
   *                                      relativo al nombre de la base de
   *                                      datos.
   * @return string                       Nombre de tabla obtenido.
   */
  public function getParseObjectDatabaseName($table, $only = false){

    // Si es una instancia de AmTable se debe obtener el nombre
    if($table instanceof AmQuery)
      $table = $table->getTable();

    // Si es una instancia de AmTable se debe obtener el nombre
    if($table instanceof AmTable)
      $table = $table->getTableName();

    // Si es un nombre válido
    if(isNameValid($table)){

      $table = $this->getParseName($table);

      // Si se desea obtener solo el nombre
      if($only)
        return $table;

      // Retornar el nombre de la tabla con la BD
      return $this->getParseDatabaseName().'.'.$table;

    }

  }
  
  /**
   * Devuelve la cadena con la dirección del servidor y el puerto.
   * @return string Dirección del servidor con el puertos
   */
  public function getServerString(){

    // Obtener el puerto
    $port = $this->getPort();

    if(empty($port))
      $port = $this->getDefaultPort();

    return "{$this->getServer()}:{$port}";

  }

  /**
   * Realiza la conexión.
   * @return Resource Handle de conexión establecida o FALSE si falló.
   */
  public function connect(){

    $ret = $this->start();

    // Cambiar la condificacion con la que se trabajará
    if($ret){

      // Asignar variables
      $this->setServerVar('character_set_server', $this->getCharset());
      $this->setServerVar('collation_server', $this->getCollation());
      // PENDIENTE: Revisar
      $this->execute("set names {$this->getCharset()}");

    }

    return $ret;

  }

  /**
   * Función para reconectar. Desconecta y vuelve a conectar la DB.
   * @return Resource Recurso generado por la nueva conexión.
   */
  public function reconnect(){

    $this->close();           // Desconectar
    return $this->connect();  // Volver a conectar

  }

  /**
   * Seleciona la BD.
   * @return bool Si se pudo selecionar la BD.
   */
  public function select(){

    // Ejecuta el SQL de seleción de de BD.
    return $this->query($this->sqlSelectDatabase());

  }

  /**
   * Indica si la BD existe.
   * @return bool Si la BD existe.
   */
  public function exists(){

    // Intenta selecionar. Si logra selecionar la BD existe.
    return !!$this->select();

  }

  /**
   * Crea una instancia de un AmQuery para la actual BD.
   * @param  string/AmQuery $from  From principal de la consulta.
   * @param  string         $alias Alias del from recibido.
   * @return AmQuery               Instancia de l query creado.
   */
  public function q($from = null, $alias = null){

    // Crear instancia
    $q = new AmQuery(array('scheme' => $this));

    // Asignar el modelo
    if($from instanceof AmQuery || $from instanceof AmTable){
      $q->setModel($from->getModel());
    }elseif(is_string($from) && is_subclass_of($from, 'AmModel')){
      $q->setModel($from); 
    }
    
    // Asignar el from de la consulta
    if(!empty($from))
      $q->fromAs($from, $alias);

    return $q;

  }

  /**
   * Ejecutar una consulta SQL desde el ámbito de la BD actual
   * @param  string/AmQuery $q SQL a ejecutar o instancia de AmQuery a ejecutar.
   * @return bool/int          Devuelve el resultado de la ejecución.
   *                           Puede ser un valor booleano que indica si se
   *                           ejecuto la consulta satisfactoriamente, o un
   *                           int en el caso de haberse ejecutatado un insert.
   */
  public function execute($q){

    // Obtener SQL si es una instancia de AmQuery
    if($q instanceof AmQuery)
      $q = $q->sql();

    // Selecionar la BD actual
    $this->select();

    // Ejecutar la consulta
    return $this->query($q);

  }

  // Ejecuta un conjunto de consultas
  public function executeGroup(array $queries){
    $sqls = array();
    foreach ($queries as $key => $q)
      $sqls[] = (string)$q;

    return $this->execute(implode(';', $sqls));

  }

  /**
   * Setea el valor de una variable en el DBSM.
   * @param  string $varName Nombre de la variable.
   * @param  mixed  $value   Valor a asignar.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return bool            Resultado de la operación
   */
  public function setServerVar($varName, $value, $scope = ''){

    return !!$this->execute($this->sqlSetServerVar($varName, $value, $scope));

  }

  /**
   * Crea la BD.
   * @param  bool $ifNotExists Indica si se agrega la clausula IF NOT EXISTS.
   * @return bool              Si se creó la BD. Si la BD ya existe y el
   *                           parámetro $ifNotExists == true, retornará true.
   */
  public function create($ifNotExists = true){

    return !!$this->execute($this->sqlCreate($ifNotExists));

  }

  /**
   * Elimina la BD.
   * @param  bool $ifExists Si se agrega la clausula IF EXISTS.
   * @return bool           Si se eliminó la BD. Si la BD no existe y el
   *                        parémetro $ifExists==true entonces retorna
   *                        true.
   */
  public function drop($ifExists = true){

    return !!$this->execute($this->sqlDrop($ifExists));

  }

  /**
   * Obtener la información de la BD.
   * @return hash Hash con las propiedades de laBD
   */
  public function getInfo(){

    return $this->queryGetInfo()->row();

  }

  /**
   * Crear tabla en la BD.
   * @param  AmTable $t           Tabla a crear
   * @param  bool    $ifNotExists Se agrega el parémtro IS NOT EXISTS.
   * @return bool                 Si se creó la tabla. Si la tabla existe y el
   *                              parámetro $ifNotExists == true, retornará
   *                              true.
   *                                  
   */
  public function createTable(AmTable $t, $ifNotExists = true){

    return !!$this->execute($this->sqlCreateTable($t, $ifNotExists));

  }

  /**
   * Crea todas las tablas de la BD basandose en los modelos bases generados.
   * @param  bool $ifNotExists Si la se creanran las tablas si no existe
   * @return hash              Hash con un valor por cada tabla que indica si
   *                           se creó.
   */
  public function createTables($ifNotExists = true){

    $ret = array(); // Para el retorno

    // Obtener los nombres de la tabla en el archivo
    $tablesNames = $this->getGeneratedModels();

    // Recorrer cada tabla generar crear la tabla
    foreach ($tablesNames as $table)
      // Crear la tabla
      $ret[$table] = $this->createTable($table, $ifNotExists);

    return $ret;

  }

  /**
   * Elimina una tabla.
   * @param  string/AmTable $table    Nombre o instancia de la tabla a eliminar.
   * @param  bool           $ifExists Si se agrega la clausula IF EXISTS.
   * @return bool                     Si se eliminó la Tabla. Si la Tabla no
   *                                  existe y el parémetro $ifExists==true
   *                                  entonces retorna true.
   */
  public function dropTable($table, $ifExists = true){

    return !!$this->execute($this->sqlDropTable($table, $ifExists));

  }

  /**
   * Eliminar todos los registros de una tabla y reinicia los campos
   * autoincrementables.
   * @param  string/AmTable $table    Nombre o instancia de la tabla.
   * @param  bool           $ignoreFk Si se ingorará los Foreing Keys.
   * @return bool                     Si se vació la tabla satisfactoriamente.
   */
  public function truncate($table, $ignoreFk = true){

    return !!$this->execute($this->sqlTruncate($table, $ignoreFk));

  }

  /**
   * Indica si la tabla existe.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return bool                  Si la tabla existe.
   */
  public function existsTable($table){

    // Intenta obtener la descripcion de la tabla para saber si existe.
    return !!$this->getTableDescription($table);

  }

  /**
   * Devuelve un array con el listado de tablas de la BD y su descripción.
   * @return array Array de hash con las descripción de las tablas.
   */
  public function getTables(){

    return $this->queryGetTables()->get();

  }

  /**
   * Obtiene la descripción de una tabla en el BD.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return hash                  Hash con la descripcion de la tabla.
   */
  public function getTableDescription($table){
    
    // Obtener nombre de la tabla
    $table = $table instanceof AmTable? $table->getTableName() : $table;

    return $this->q($this->queryGetTables())
                ->where("tableName = '{$table}'")
                ->row();

  }

  /**
   * Obtener un listado de las columnas de una tabla.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return hash                  Array de hash con la descripcion de los
   *                               campos.
   */
  public function getTableColumns($table){

    return $this->queryGetTableColumns($table)
                ->get(null, array($this, 'sanitize'));
                
  }

  /**
   * Obtener un listado de las claves foráneas de una tabla.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return array                 Array de hash conla descripción de las
   *                               claves foráneas.
   */
  public function getTableForeignKeys($table){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $schemeName = $this->getName();

    // Obtener los ForeignKeys
    $fks = $this->queryGetTableForeignKeys($table)
                ->get();

    foreach($fks as $fk){

      // Dividir el nombre del FK
      $name = explode('.', $fk['name']);

      // Obtener el ultimo elemento
      $name = array_pop($name);

      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          'scheme' => $schemeName,
          'table' => $fk['toTable'],
          'cols' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['cols'][$fk['columnName']] = $fk['toColumn'];

    }

    return $ret;

  }

  /**
   * Obtener el listado de referencias a una tabla.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return array                 Array de hash con la descripción de las
   *                               referencias.
   */
  public function getTableReferences($table){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $schemeName = $this->getName();

    // Obtener las referencias a una tabla
    $fks = $this->queryGetTableReferences($table)
                ->get();

    // Recorrer los FKs
    foreach($fks as $fk){

      // Dividir el nombre del FK
      $name = explode('.', $fk['name']);

      // Obtener el ultimo elemento
      $name = array_shift($name);

      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          'scheme' => $schemeName,
          'table' => $fk['fromTable'],
          'cols' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['cols'][$fk['toColumn']] = $fk['columnName'];

    }

    return $ret;

  }

  /**
   * Obtener un listado de las claves restricciones únicas de una tabla.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return array                 Array de hash con la descripción de
   *                               claves únicas.
   */
  public function getTableUniques($table){

    $uniques = $this->queryGetTableUniques($table)
                    ->get();

    // Group fields of unique indices for name.
    $realUniques = array();

    foreach ($uniques as $value) {
      $realUniques[$value['name']] = itemOr($value['name'],
        $realUniques, array());
      $realUniques[$value['name']][] = $value['columnName'];
    }

    return $realUniques;

  }

  /**
   * Devuelve la descripción completa de una tabla incluyendo los campos.
   * @param  string  $tableName Nombre de la tabla.
   * @return AmTable            Instancia de la tabla.
   */
  public function getTableFromScheme($tableName){

    // Obtener la descripcion basica
    $table = $this->getTableDescription($tableName);

    // Si no se encontró la tabla retornar falso
    if($table === false)
      return false;

    // Crear instancia anonima de la tabla
    $table = new AmTable(array_merge($table, array(

      // Asignar fuente
      'schemeName'    => $this->getName(),

      // Detalle de la tabla
      'fields'        => $this->getTableColumns($tableName),
      // 'referencesTo'  => $this->getTableForeignKeys($tableName),
      // 'referencesBy'  => $this->getTableReferences($tableName),
      // 'uniques'       => $this->getTableUniques($tableName),

    )));

    // Retornar tabla
    return $table;

  }

  /**
   * Crea una vista.
   * @param  AmQuery $q        Instancia de la consulta a crear.
   * @param  bool    $replace  Si se debe agregar la clausula OR REPLACE.
   * @return bool              Si se creó la vista.
   */
  public function createView(AmQuery $q, $replace = true){

    return !!$this->execute($this->sqlCreateView($q, $replace));

  }

  /**
   * Eliminar una vista.
   * @param  string/AmQuery $q        Nombre o instancia de la consulta a
   *                                  eliminar.
   * @param  bool           $ifExists Si se debe agregar la clausula IF EXISTS.
   * @return bool                     Si se eliminó la vista
   */
  public function dropView($q, $ifExists = true){

    return !!$this->execute($this->sqlDropView($q, $ifExists));

  }

  /**
   * Funcion para preparar la ejeción de un insert.
   * @param  array/AmQuery  $values Array hash de valores, array
   *                                de instancias de AmModels, array de
   *                                AmObjects o AmQuery con consulta select
   *                                a insertar.
   * @param  string/AmTable $model  Nombre del modelo o instancia de la
   *                                tabla donde se insertará los valores.
   * @param  array          $fields Campos que recibirán con los valores que
   *                                se insertarán.
   * @return hash                   Devuelve un hash que contiene el string
   *                                con el nombre de la tabla, string de
   *                                valores y el string de campos.
   */
  protected function prepareInsert($values, $model, array $fields = array()){

    $table = $model;

    // Obtener la instancia de la tabla
    if(!$table instanceof AmTable)
      $table = $this->getTableInstance($table);

    if($table){

      // Agregar fechas de creacion y modificacion si existen en la tabla
      $table->setAutoCreatedAt($values);
      $table->setAutoUpdatedAt($values);

    }else{

      $table = $model;

    }

    if($values instanceof AmQuery){

      // Si los campos recibidos estan vacíos se tomará
      // como campos los de la consulta
      if(count($fields) == 0){
        $fields = array_keys($values->getSelects());
      }

    // Si los valores es un array con al menos un registro
    }elseif(is_array($values) && count($values)>0){

      // Indica si
      $mergeWithFields = count($fields) == 0;

      $rawValues = array();

      // Recorrer cada registro en $values par obtener los valores a insertar
      foreach($values as $i => $v){

        if($v instanceof AmModel){
          // Si el registro es AmModel obtener sus valores como array
          // asociativo o simple
          $values[$i] = $v->getTable()->dataToArray($v, !$mergeWithFields);
          $rawValues[$i] = $v->getRawValues();

        }elseif($v instanceof AmObject)
          // Si es una instancia de AmObjet se obtiene como array asociativo
          $values[$i] = $v->toArray();

        // Si no se recibieron campos, entonces se mezclaran con los
        // indices obtenidos
        if($mergeWithFields)
          $fields = merge_unique($fields, array_keys($values[$i]));

      }

      // Preparar registros para crear SQL
      $resultValues = array();
      foreach($values as $i => $v){

        // Asignar array vacío
        $resultValues[$i] = array();

        // Agregar un valor por cada campo de la consulta
        foreach($fields as $f){
          $val = isset($v[$f])? $v[$f] : null;
          // Obtener el valor del registro actual en el campo actual
          if(isset($rawValues[$i][$f]) && $rawValues[$i][$f] === true){
            $resultValues[$i][] = $val;
          }else{
            $resultValues[$i][] = $this->stringWrapperAndRealScape($val);
          }
        }

      }

      // Asignar nuevos valores
      $values = $resultValues;

    }

    // Si es una consulta
    if($values instanceof AmQuery){

      // Los valores a insertar son el SQL de la consulta
      $values = $values->sql();

    }

    // Obtener el listado de campos
    foreach ($fields as $key => $field)
      $fields[$key] = $this->getParseName($field);

    return array(
      'table' => $this->getParseObjectDatabaseName($table),
      'values' => $this->sqlInsertValues($values),
      'fields' => $this->sqlInsertFields($fields),
    );

  }

  /**
   * Inserta registros en una tabla.
   * @param  array/AmQuery  $values Array hash de valores, array de instancias
   *                                de AmModels, array de AmObjects o AmQuery
   *                                con consulta select a insertar.
   * @param  string/AmTable $model  Nombre del modelo o instancia de la tabla
   *                                donde se insertará los valores.
   * @param  array          $fields Campos que recibirán con los valores que se
   *                                insertarán.
   * @return bool/int               Boolean se se logró insertar los registros,
   *                                o el id del registro insertado en el caso
   *                                de corresponda.
   */
  public function insertInto($values, $model, array $fields = array()){

    // Si los valores es una instancia de AmModel entonces convierte en un array
    // que contenga solo dicha instancia.
    if($values instanceof AmModel)
      $values = array($values);

    // Obtener el SQL para saber si es valido
    $sql = $this->sqlInsert($values, $model, $fields);

    // Si el SQL está vacío o si se genera un error en la inserción devuelve
    // falso
    if(trim($sql) == '' || $this->execute($sql) === false)
      return false;
    
    // De lo contrario retornar verdadero.
    return true;

  }

  /**
   * Prepara una columna para ser creada en una tabla de la BD.
   * @param  array  $column Datos de una columna.
   * @return string
   */
  public function sanitize(array $column){
    // Si no se encuentra el tipo se retorna el tipo recibido

    $nativeType = $column['type'];
    $column['type'] = itemOr($column['type'], $this->types, $column['type']);

    // Parse bool values
    $column['pk'] = parseBool($column['pk']);
    $column['allowNull']  = parseBool($column['allowNull']);

    // Get len of field
    // if is a bit, char or varchar take len
    if(in_array($nativeType, array('char', 'varchar')))
      $column['len'] = itemOr('len', $column);

    elseif($nativeType == 'bit')
      $column['len'] = itemOr('precision', $column);

    // else look len into bytes used for native byte
    else
      $column['len']  = itemOr($nativeType, array_merge(
        $this->getLenSubTypes('int'),
        $this->getLenSubTypes('float'),
        $this->getLenSubTypes('text')
      ));

    if(in_array($column['type'], array('int', 'float'))){

      $column['unsigned'] = preg_match('/unsigned/',
        $column['columnType']) != 0;

      $column['zerofill'] = preg_match('/unsigned zerofill/',
        $column['columnType']) != 0;

      $column['autoIncrement'] = preg_match('/auto_increment/',
        $column['extra']) != 0;

    }

    // Unset scale is not is a float
    if($column['type'] != 'float')
      unset($column['precision'], $column['scale']);

    else
      $column['scale'] = itemOr('scale', $column, 0);

    // Unset columnType an prescicion
    unset($column['columnType']);

    // Drop auto_increment of extra param
    $column['extra'] = trim(str_replace('auto_increment', '', $column['extra']));

    // Eliminar campos vacios
    foreach(array(
      'defaultValue',
      'collation',
      'charset',
      'len',
      'extra'
    ) as $attr)
      if(!isset($column[$attr]) || trim($column[$attr])==='')
        unset($column[$attr]);

    return $column;
    
  }

  /**
   * Obtener el SQL para una condicion IN.
   * @param  string               $field     Nombre del campo.
   * @param  string/AmQuery/array $collation Instancia de un query select, SQL
   *                                         array de valores o string a
   *                                         insertar.
   * @return string                          SQL correspondiente.
   */
  public function in($field, $collation){

    // Si es un array se debe preparar la condició
    if(is_array($collation)){

        // Filtrar elementos repetidos
        $collation = array_filter($collation);

        // Si no esta vacía la colecion
        if(!empty($collation)){

          // Agregar cadenas dentro de los comillas simple
          foreach ($collation as $i => $value){
            $value = $this->stringWrapperAndRealScape($value);
            $collation[$i] = is_numeric($value) ? $value : "\'{$value}\'";
          }

          // Unir colecion por comas
          $collation = implode($collation, ',');

        }else{
          // Si es una colecion vacía
          $collation = null;
        }

    }elseif($collation instanceof AmQuery){

      // Si es una consulta entonces se obtiene el SQL
      $collation = $this->sqlSelectQuery($collation);

    }

    // Agregar el comando IN
    return isset($collation) ? "$field IN($collation)" : 'false';

  }

  /**
   * Helper para obtener el SQL de la clausula WHERE.
   * @param  string/array $condition Condición o array de condiciones.
   * @param  string       $prefix    Si la condición tiene un prefijo.
   * @param  bool         $isIn      Si la condición es un IN.
   * @return string                  SQL correspondiente.
   */
  private function parseWhere($condition, $prefix = null, $isIn = false){

    if($isIn){

      // Es una condicion IN
      $condition = $this->in($condition[0], $condition[1]);

    }elseif(is_array($condition)){

      $str = '';
      $lastUnion = '';

      // Recorrer condiciones
      foreach($condition as $c){

        // Obtener siguiente condicion
        $next = $this->parseWhere($c['condition'], $c['prefix'], $c['isIn']);

        // Es la primera condicion
        if(empty($str)){
          $str = $next;
        }else{

          // Si el operador de union es igual al anterior o no hay una anterior
          if($c['union'] == $lastUnion || empty($lastUnion)){
            $str = "{$str} {$c['union']} {$next}";
          }else{
            // Cuando cambia el operador de union se debe agregar la condicion anterior
            // entre parentesis
            $str = "({$str}) {$c['union']} {$next}";
          }

          // guardar para la siguiente condicion
          $lastUnion = $c['union'];

        }

      }

      // Agregar parentesis a la condicion
      $condition = empty($str) ? '' : "({$str})";

    }

    // Eliminar espacios al principio y al final
    $condition = trim($condition);

    // Agregar el prefix (NOT) si existe
    return empty($condition) ? '' : trim($prefix.' '.$condition);

  }

  //////////////////////////////////////////////////////////////////////////////
  // Metodos para obtener los SQL a ejecutar.
  //////////////////////////////////////////////////////////////////////////////

  /**
   * SQL para setear un valor a una variable de servidor.
   * @param  string $varName Nombre de la variable.
   * @param  string $value   Valor a asignar a la variable.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return string          SQL correspondiente.
   */
  public function sqlSetServerVar($varName, $value, $scope = ''){

    $varName = $this->realScapeString($varName);
    $value = $this->stringWrapperAndRealScape($value);

    $scope = $scope === true? 'GLOBAL ' : $scope === false? 'SESSION ' : '';

    return "SET {$scope}{$varName}={$value}";

  }

  /**
   * Set de caracteres en un query SQL.
   * @param  string $charset Set de caracteres.
   * @return string          SQL correspondiente.
   */
  public function sqlCharset($charset = null){

    // Si no recibió argumentos obtener el charset de la BD
    if(!count(func_get_args())>0)
      $charset = $this->getCharset();

    // El el argumento esta vacío retornar cadena vacia
    if(empty($charset))
      return '';

    $charset = empty($charset) ? '' : " CHARACTER SET {$charset}";

    return $charset;

  }

  /**
   * Coleccion de caracteres en un query SQL.
   * @param  string $collatin Colección de caracteres.
   * @return string           SQL correspondiente.
   */
  public function sqlCollation($collation = null){

    // Si no recibió argumentos obtener el college de la BD
    if(!count(func_get_args())>0)
      $collation = $this->getCollation();

    // El el argumento esta vacío retornar cadena vacia
    if(empty($collation))
      return '';

    $collation = empty($collation) ? '' : " COLLATE {$collation}";

    return $collation;

  }

  /**
   * SQL Para crear la BD.
   * @param  boolean $ifNotExists Si se agrega la cláusula IF NOT EXISTS.
   * @return string               SQL correspondiente.
   */
  public function sqlCreate($ifNotExists = true){

    $database = $this->getParseDatabaseName();
    $charset = $this->sqlCharset();
    $collation = $this->sqlCollation();
    $ifNotExists = $ifNotExists? 'IF NOT EXISTS ' : '';
    $sql = "CREATE DATABASE {$ifNotExists}{$database}{$charset}{$collation}";
    return $sql;

  }

  /**
   * SQL para seleccionar la BD.
   * @return string SQL correspondiente.
   */
  public function sqlSelectDatabase(){

    $database = $this->getParseDatabaseName();
    return "USE {$database}";
    
  }

  /**
   * SQL para eliminar la BD.
   * @param  boolean $ifExists Si se agrega la cláusula IF EXISTS.
   * @return string            SQL correspondiente.
   */
  public function sqlDrop($ifExists = true){

    $database = $this->getParseDatabaseName();
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP DATABASE {$ifExists}{$database}";

  }

  /**
   * Obtener el SQL para eliminar una tabla.
   * @param  AmTable/string $table     Instancia o nombre de la tabla.
   * @param  bool           $orReplace Si se agrega la cláusula OR REPLACE.
   * @return string                    SQL correspondiente.
   */
  public function sqlCreateView(AmQuery $q, $orReplace = true){

    $queryName = $this->getParseObjectDatabaseName($q->getName());
    $orReplace = $orReplace? 'OR REPLACE ' : '';

    return "CREATE {$replace}VIEW {$queryName} AS {$q->sql()}";

  }

  /**
   * Obtener el SQL para eliminar una vista.
   * @param  AmQuery/string $q        Instancia o SQL del query.
   * @param  bool           $ifExists Si se debe agregar la cláusula IF EXISTS.
   * @return string                   SQL correspondiente.
   */
  public function sqlDropView($q, $ifExists = true){
    
    if($q instanceof AmQuery)
      $q = $q->getName();

    $queryName = $this->getParseObjectDatabaseName($q);
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP VIEW {$ifExists}{$queryName}";

  }

  /**
   * Obtener el SQL para un campo de una tabla al momento de crear la tabla.
   * @param  AmField $field Instancia del campo.
   * @return string         SQL correspondiente.
   */
  public function sqlField(AmField $field){

    // Preparar las propiedades
    $name = $this->getParseName($field->getName());
    $type = $field->getType();
    $len = $field->getLen();
    $charset = $this->sqlCharset($field->getCharset());
    $collation = $this->sqlCollation($field->getCollation());
    $default = $field->getDefaultValue();
    $extra = $field->getExtra();

    if(isset($default)){

      $default = $field->parseValue($default);

      if(in_array($type, array('text', 'char', 'varchar', 'bit')) ||
        (in_array($type, array('date', 'datetime', 'timestamp', 'time')) &&
          $default != 'CURRENT_TIMESTAMP'
        )
      )
        $default = "'{$default}'";

    }

    $attrs = array();

    if($field->isUnsigned())      $attrs[] = 'unsigned';
    if($field->isZerofill())      $attrs[] = 'zerofill';
    if(!empty($charset))          $attrs[] = $charset;
    if(!empty($collation))        $attrs[] = $collation;
    if(!$field->allowNull())      $attrs[] = 'NOT NULL';
    if($field->isAutoIncrement()) $attrs[] = 'AUTO_INCREMENT';
    if(isset($default))           $attrs[] = "DEFAULT {$default}";
    if(!empty($extra))            $attrs[] = $extra;

    $attrs = implode(' ', $attrs);

    // Get type
    // As int
    if($type === 'int')
      $type = self::getTypeByLen($type, $len);

    // As text
    elseif($type === 'text')
      $type = self::getTypeByLen($type, $len);

    // as float precision
    elseif($type == 'float'){

      $type = self::getTypeByLen($type, $len);

      $precision = $field->getPrecision();
      $scale = $field->getScale();

      if($precision && $precision)
        $type = "{$type}({$precision}, {$scale})";

    // with var len
    }elseif(in_array($type, array('bit', 'char', 'varchar'))){
      
      $type = "{$type}({$len})";

    }

    return "{$name} {$type} {$attrs}";

  }

  /**
   * Obtener el SQL para crear una tabla.
   * @param  AmTable $table       Instancia de la tabla a acrear
   * @param  bool    $ifNotExists Se se debe agregar la cláusula IF NOT EXISTS.
   * @return string  SQL del query.
   */
  public function sqlCreateTable(AmTable $table, $ifNotExists = true){

    // Obtener nombre de la tabla
    $tableName = $this->getParseObjectDatabaseName($table);

    // Lista de campos
    $fields = array();
    $realFields = $table->getFields();

    // Obtener el SQL para cada camppo
    foreach($realFields as $field)
      $fields[] = $this->sqlField($field);

    // Obtener los nombres de los primary keys
    $pks = $table->getPks();
    foreach($pks as $offset => $pk)
      $pks[$offset] = $this->getParseName($table->getField($pk)->getName());

    // Preparar otras propiedades
    $engine = $table->getEngine();
    $engine = empty($engine) ? '' : "ENGINE={$table->getEngine()} ";
    $charset = $this->sqlCharset($table->getCharset());
    $collation = $this->sqlCollation($table->getCollation());

    // Agregar los primaris key al final de los campos
    $fields[] = empty($pks) ? '' : 'PRIMARY KEY (' . implode(', ', $pks). ')';

    // Unir los campos
    $fields = "\n".implode(",\n", $fields);

    $ifNotExists = $ifNotExists? 'IF NOT EXISTS ' : '';

    // Preparar el SQL final
    return "CREATE TABLE {$ifNotExists}{$tableName}($fields){$engine}{$charset}{$collation};";

  }

  /**
   * Devuelve el SQL para truncar un tabla.
   * @param  AmTable/string $table    Instancia o nombre de la tabla.
   * @param  bool           $ignoreFk Si se debe ignorar las claves foráneas.
   * @return string         SQL de la acción.
   */
  public function sqlTruncate($table, $ignoreFk = true){

    // Obtener nombre de la tabla
    $tableName = $this->getParseObjectDatabaseName($table);

    $sql = "TRUNCATE {$tableName}";

    if($ignoreFk)
      $sql = $this->sqlSetServerVar('FOREIGN_KEY_CHECKS', 0).';'.
              $sql.';'.
              $this->sqlSetServerVar('FOREIGN_KEY_CHECKS', 1);

    return $sql;

  }

  /**
   * Obtener el SQL para eliminar una tabla.
   * @param  AmTable/string $table    Instancia o nombre de la tabla.
   * @param  bool           $ifExists Si se debe agregar la cláusula IF EXISTS.
   * @return string                   SQL correspondiente.
   */
  public function sqlDropTable($table, $ifExists = true){

    // Obtener nombre de la tabla
    $tableName = $this->getParseObjectDatabaseName($table);
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP TABLE {$ifExists}{$tableName}";

  }

  /**
   * Devuelve el SQL de un query SELECT
   * @param  AmQuery $q Query.
   * @return string     SQL del query.
   */
  public function sqlSelectQuery(AmQuery $q){

    return
      trim(implode(' ', array(
      trim($this->sqlClauseSelect($q)),
      trim($this->sqlClauseFrom($q)),
      trim($this->sqlJoins($q)),
      trim($this->sqlWhere($q)),
      trim($this->sqlGroups($q)),
      trim($this->sqlOrders($q)),
      trim($this->sqlLimit($q)),
      trim($this->sqlOffSet($q))
    )));

  }

  /**
   * Devuelve el SQL de la sección VALUES para un query INSERT.
   * @param  array/string $values Array de hash con los valores a insertar o SQL
   *                              ya preparado.
   * @return string               SQL correspondiente.
   */
  protected function sqlInsertValues($values){

    if(empty($values))
      return '';

    if(is_array($values) && count($values)>0){

      // Preparar registros para crear SQL
      foreach($values as $i => $v)
        // Unir todos los valores con una c
        $values[$i] = '(' . implode(',', $v) . ')';

      // Unir todos los registros
      $values = implode(',', $values);

      // Obtener Str para los valores
      $values = "VALUES {$values}";

    }


    return $values;

  }

  /**
   * Devuelve el SQL de la sección FIELDS para un query INSERT.
   * @param  array  $fields Campos que se desea preparar.
   * @return string         SQL correspondiente.
   */
  protected function sqlInsertFields(array $fields){

    // Unir campos
    if(!empty($fields))
      return '(' . implode(',', $fields) . ')';

    return '';

  }

  /**
   * Devuelve el SQL de un query INSERT.
   * @param  array/AmQuery  $values Array hash de valores, array
   *                                de instancias de AmModels, array de
   *                                AmObjects o AmQuery con consulta select
   *                                a insertar.
   * @param  string/AmTable $model  Nombre del modelo o instancia de la
   *                                tabla donde se insertará los valores.
   * @param  array          $fields Campos que recibirán con los valores que
   *                                se insertarán.
   * @return string                 SQL del query.
   */
  public function sqlInsert($values, $model, array $fields = array()){

    $q = $this->prepareInsert(
      $values, $model, $fields
    );

    if(empty($q['values']))
      return '';

    // Generar SQL
    return implode(' ', array(
      'INSERT INTO',
      $q['table'].$q['fields'],
      $q['values'],
    ));

  }

  /**
   * Obtener el SQL para una consulta UPDATE.
   * @param  AmQuery $q Query.
   * @return string     SQL del query.
   */
  public function sqlUpdateQuery(AmQuery $q){

    return implode(' ', array(
      'UPDATE',
      trim($this->getParseObjectDatabaseName($q)),
      trim($this->sqlJoins($q)),
      trim($this->sqlSets($q)),
      trim($this->sqlWhere($q))
    ));

  }

  /**
   * Obtener el SQL para una consulta DELETE.
   * @param  AmQuery $q Query.
   * @return string     SQL del query.
   */
  public function sqlDeleteQuery(AmQuery $q){

    return implode(' ', array(
      'DELETE FROM',
      trim($this->getParseObjectDatabaseName($q)),
      trim($this->sqlWhere($q))
    ));

  }

  /**
   * SQL Para la cláusula SELECT.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlClauseSelect(AmQuery $q){

    $selects = $q->getSelects();  // Obtener argmuentos en la clausula SELECT
    $distinct = $q->getDistinct();

    // Unir campos
    // SQLSQLSQL
    $selects = trim(implode(', ', $selects));

    // Si no se seleccionó ningun campo entonces se tomaran todos
    // SQLSQLSQL
    $selects = empty($selects) ? '*' : $selects;

    // Agregar SELECT
    // SQLSQLSQL
    return 'SELECT '.trim(($distinct ? 'DISTINCT ' : '').$selects);

  }

  /**
   * Obtener el SQL para la clausula FROM.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlClauseFrom(AmQuery $q){

    $froms = $q->getFroms();   // Listado de argumentos de la clausula FROM

    // Unir argumentos procesados
    // SQLSQLSQL
    $froms = trim(implode(', ', $froms));

    // Agregar FROM
    // SQLSQLSQL
    return empty($froms) ? '' : trim('FROM '.$froms);

  }

  /**
   * Obtener el SQL para la clausula JOIN.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlJoins(AmQuery $q){

    // Resultado
    $joins = $q->getJoins();

    // Unir argumentos procesados
    // SQLSQLSQL
    $joins = trim(implode(' ', $joins));

    // Agregar FROM
    // SQLSQLSQL
    return empty($joins) ? '' : ' '.$joins;

    // $joinsResult = array();

    // // Recorrer cada join
    // foreach($joins as $join){

    //     // Declarar posiciones del array como variables
    //     // Define $on, $as y $table
    //     extract($join);

    //     // Eliminar espacios iniciales y finales
    //     $on = trim($on);
    //     $as = trim($as);

    //     if($table instanceof AmQuery){
    //       // Si es una consulta insertar SQL dentro de parenteris
    //       $table = "({$table->sql()})";
    //       if(!isset($as)){
    //         $as = $table->getModel();
    //         if(is_subclass_of($as, 'AmModel'))
    //           $as = $as::me()->getTableName();
    //       }
    //     }elseif($table instanceof AmTable){
    //       // Si es una tabla obtener el nombre
    //       $table = $table->getTableName();
    //       if(!isset($as))
    //         $as = $table;
    //     }

    //     // Si los parametros quedan vacios
    //     if(!empty($on)) $on = " ON {$on}";
    //     if(!empty($as)) $as = " AS {$as}";

    //     // Agrgar parte de join
    //     $joinsResult[] = " $type JOIN {$table}{$as}{$on}";

    //     // Liberar variables
    //     unset($table, $as, $on);

    // }

    // // Unir todas las partes
    // return trim(implode(' ', $joinsResult));

  }

  /**
   * Obtener el SQL para la clausula WHERE.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlWhere(AmQuery $q){

    $where = trim($this->parseWhere($q->getWheres()));

    return (empty($where) ? '' : "WHERE {$where}");

  }

  /**
   * Obtener el SQL para la clausula ORDER BY.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlOrders(AmQuery $q){

    $ordersOri = $q->getOrders(); // Obtener orders agregados
    $orders = array();  // Orders para retorno

    // Recorrer lista de campos para ordenar
    foreach($ordersOri as $order => $dir){
      $orders[] = "{$order} {$dir}";
    }

    // Unir resultado
    $orders = trim(implode(', ', $orders));

    // Agregar ORDER BY
    return (empty($orders) ? '' : "ORDER BY {$orders}");

  }

  /**
   * Obtener el SQL para la clausula GROUP BY.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlGroups(AmQuery $q){

    // Unir grupos
    $groups = trim(implode(', ', $q->getGroups()));

    // Agregar GROUP BY
    return (empty($groups) ? '' : "GROUP BY {$groups}");

  }

  /**
   * Obtener el SQL para la clausula LIMIT.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlLimit(AmQuery $q){

    // Obtener limite
    $limit = trim($q->getLimit());

    // Agregar LIMIT
    return (empty($limit) ? '' : "LIMIT {$limit}");

  }

  /**
   * Obtener el SQL para la clausula OFFSET.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlOffset(AmQuery $q){

    // Obtener punto de partida
    $offset = $q->getOffset();
    $limit = $q->getLimit();

    // Agregar OFFSET
    return (!isset($offset) || !isset($limit) ? '' : "OFFSET {$offset}");

  }

  /**
   * Obtener el SQL para la clausula SET de un query UPDATE.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlSets(AmQuery $q){

    // Obtener sets
    $setsOri = $q->getSets();
    $sets = array(); // Lista para retorno

    // Recorrer los sets
    foreach($setsOri as $set){

      $value = $set['value'];

      // Acrear asignacion
      if($value === null){
        $sets[] = "{$set['field']} = NULL";
      }elseif($set['const'] === true){
        $sets[] = "{$set['field']} = " . $this->stringWrapperAndRealScape($value);
      }elseif($set['const'] === false){
        $sets[] = "{$set['field']} = {$value}";
      }

    }

    // Unir resultado
    $sets = implode(',', $sets);

    // Agregar SET
    return "SET {$sets}";

  }

  /**
   * Obtiene el SQL de una Query dependiendo de su tipo.
   * @param  AmQuery $q Instancia de query.
   * @return string     SQL obtenido.
   */
  public function sqlOf(AmQuery $q){
    $type = $q->getType();

    // Consulta de seleción
    if($type == 'select')
      return $this->sqlSelectQuery($q);

    // Consulta de inserción
    if($type == 'insert')
      return $this->sqlInsert($q, $q->getInsertTable(), $q->getInsertFields());

    // Consulta de actualización
    if($type == 'update')
      return $this->sqlUpdateQuery($q);

    // Consulta de eliminación
    if($type == 'delete')
      return $this->sqlDeleteQuery($q);

    throw Am::e('AMSCHEME_QUERY_TYPE_UNKNOW', var_export($q, true));

  }

  //////////////////////////////////////////////////////////////////////////////
  // Metodos estáticos
  //////////////////////////////////////////////////////////////////////////////
  
  /**
   * Devuelve un alias no existente en una colección. Si en la colección existe
   * algún key igual al alias se le irá agregando contador al final hasta
   * obtener uno que no exista.
   * @param  string $alias      Alias base.
   * @param  array  $collection Colección donde se buscará si el alias existe.
   * @return string             Alias generados
   */
  public function alias($alias, array $collection){

    if(!isNameValid($alias))
      throw Am::e('AMSCHEME_INVALID_ALIAS', $alias);

    $i = 0;
    $finalAlias = $alias;
    while(isset($collection[$finalAlias]))
      $finalAlias = $alias . $i++;

    return $finalAlias;

  }

  /**
   * Devuelve la configuración de un determinado esquema.
   * @param  string $name Nombre del esquema buscado.
   * @return hash         Configuración del esquema.
   */
  public static function getConf($name = ''){

    // Obtener configuraciones para las fuentes
    $schemes = Am::getProperty('schemes', array());

    // Si no existe una configuración para el nombre de fuente
    if(!isset($schemes[$name]))
      return null;

    // Asignar valores por defecto
    $schemes[$name] = array_merge(
      array(
        'database'  => $name,
        'driver'    => null,
      ),
      $schemes[$name]
    );

    $schemes[$name]['name'] = $name;

    return $schemes[$name];

  }

  /**
   * Devuelve una instancia de una fuente.
   * @param  string   $name Nombre del esquema buscado.
   * @return AmScheme       Instancia del esquema.
   */
  public static function get($name = ''){

    // Obtener la instancia si ya existe
    if(isset(self::$schemes[$name]))
      return self::$schemes[$name];

    // Obtener la configuración de la fuente
    $schemeConf = self::getConf($name);

    // Si no existe una configuración para el nombre de fuente solicitado se
    // retorna NULL
    if($schemeConf === null)
      throw Am::e('AMSCHEME_SCHEMECONF_NOT_FOUND', $name);

    // Obtener el driver de la fuente
    $driverClassName = self::driver($schemeConf['driver']);

    // Crear instancia de la fuente
    $schemes = new $driverClassName($schemeConf);

    return self::$schemes[$name] = $schemes;

  }

  /**
   * Incluye un driver de BD.
   * @param  string $driver Nombre del driver a incluir.
   * @return string         Nombre de la clase del driver a incluir.
   */
  public static function driver($driver){

    // Obtener el nombre de la clase
    $driverClassName = camelCase($driver, true).'Scheme';

    // Se retorna en nombre de la clase
    return $driverClassName;

  }

  /**
   * Incluye un modelo.
   * @param  string      $model Nombre del modelo a insertar. Puede ser un
   *                            modelo base :<modelName>@<schemeName> o el
   *                            nombre del modelo dado por el usuario.
   * @return string/bool        Si al final de la inclusión existe la clase
   *                            correspondiente devuelve el nombre de la clase,
   *                            de lo contrario devuelv falso.
   */
  public static function model($model){

    // Si es un modelo nativo
    if(preg_match('/^:(.*)@(.*)$/', $model, $m) ||
      preg_match('/^:(.*)$/', $model, $m)){

      // Si no se indica la fuente tomar la fuente por defecto
      if(empty($m[2]))
        $m[2] = '';
      
      $scheme = self::get($m[2]);

      $model = $scheme->getBaseModelClassName($m[1]);

    }

    // Retornar el nombre de la clase del modelo correspondiente
    return class_exists($model)? $model : false;

  }

  /**
   * Incluye un validador y devuelve el nombre de la clases correspondiente
   * @param  string $validator Nombre del validador a insertar.
   * @return string            Nombre de la clase del validador.
   */
  public static function validator($validator){

    // Obtener el nombre de la clase
    $validatorClassName = camelCase($validator, true).'Validator';

    // Se retorna en nombre de la clase
    return $validatorClassName;

  }

  //////////////////////////////////////////////////////////////////////////////
  // Metodos abstractos que deben ser definidos en las implementaciones
  //////////////////////////////////////////////////////////////////////////////

  /**
   * Metodo para obtener el puerto por defecto para una conexión.
   * @return string/int Devuelve el nro del purto por defecto.
   */
  abstract public function getDefaultPort();

  /**
   * Metodo para crear una conexion.
   * @return Resource Manejador para la conexión 
   */
  abstract protected function start();

  /**
   * Metodo para cerrar una conexión.
   * @return bool Resultado de la operación
   */
  abstract public function close();

  /**
   * Obtener el número del último error generado en la conexión.
   * @return int Nro de error.
   */
  abstract public function getErrNo();

  /**
   * Obtener la descripcion del último error generado en la conexión
   * @return string Descripción del error.
   */
  abstract public function getError();

  /**
   * Obtiene una cadena con un valor seguro para el manejador de DBSM.
   * @param  mixed  $value Valor que se desea procesar.
   * @return string        Valor procesado.
   */
  abstract public function realScapeString($value);

  /**
   * Realizar una consulta SQL.
   * @param  string   $sql SQL a ejecutar.
   * @return bool/int      Resultado de la operación.
   */
  abstract protected function query($sql);

  /**
   * Obtener el siguiente registro de un resultado
   * @param  Resourse $result Manejador del resultado.
   * @return hash             Hash de valores del registro.
   */
  abstract public function getFetchAssoc($result);

  /**
   * Obtener el ID del ultimo registro insertado. En el caso que el último
   * query ejecutado sea un insert de un solo elemento en una tabla con un solo
   * campo autonumérico.
   * @return null/int Null o valor autonumérico insertado.
   */
  abstract public function getLastInsertedId();

  /**
   * Ingresa el nombre de un objeto de la BD dentro de las comillas
   * correspondientes.
   * @param  string $name Nombre que se desea entre comillas.
   * @return string       Nombre entre comillas.
   */
  abstract public function nameWrapper($name);

  /**
   * Devuelve una cadena de caracteres entre comillas.
   * @param  string $string Cadena que se desea entre comillas.
   * @return string         Cadena entre comillas.
   */
  abstract public function stringWrapper($string);

  /**
   * Devuelve un nombre de un objeto de BD entendible para el DBSM.
   * @param   string  $name   Nombre que se desea obtener.
   * @return  string          Identificador válido.
   */
  abstract public function getParseName($name);
  
  /**
   * Query para obtener la información de una BD.
   * @return string SQL para la operación.
   */
  abstract public function queryGetInfo();

  /**
   * Query para obtener la descripción de las tablas del esquema.
   * @return string SQL para la operación.
   */
  abstract public function queryGetTables();
  
  /**
   * SQL para obtener la descripción de las columnas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function queryGetTableColumns($table);
  
  /**
   * SQL para obtener la descripción de las claves unicas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function queryGetTableUniques($table);
  
  /**
   * SQL para obtener la descripción de las claves foráneas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function queryGetTableForeignKeys($table);
  
  /**
   * SQL para obtener la descripción de las referencias a una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function queryGetTableReferences($table);

}
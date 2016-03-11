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
    $schemes = array(),

    /**
     * Directorio por defecto de los modelos base.
     */
    $schemesDir = 'schemes',

    /**
     * Directorio por defecto de los modelos.
     */
    $modelsDir = 'models';

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
    $tables = array();

  /**
   * Constructor. Se conecta en el contructor.
   */
  public function __construct($params = array()){
    parent::__construct($params);

    $this->connect(); // Conectar la fuente

  }

  /**
   * El destructor del objecto cierra la conexión
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
   * Carga la instancia de una tabla.
   * @param  string/AmTable $table Nombre o instancia de la tabla. 
   * @return AmTable               Instancia de la tabla solicitada.
   */
  public function loadTable($table){
    
    // Instanciar la tabla si el parámetro es un string.
    if(is_string($table))
      $table = self::table($table, $this->getName());

    // Agregar tabla
    $this->addTable($table);

    // Retornar tabla
    return $table;

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
    $ret = amGlobFiles($this->getDir(), array(

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
    return self::getSchemesDir() . (!empty($name)?'/' . $name : '');

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

    return $this->getDir() . '/'. underscore($model) .'.conf.php';

  }

  /**
   * Devuelve la dirección de la clase base del model.
   * @param  string $model Nombre del modelo que se desea consultar.
   * @return string        Dirección de la clase base del modelo.
   */
  public function getBaseModelClassFilename($model){

    return "{$this->getDir()}/{$this->getBaseModelClassName($model)}.class.php";

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
      return AmCoder::decode($confFilePath);

    // Si no existe retornar falso
    return false;

  }

  /**
   * Crea el archivo con la clase del modelo base basando en una tabla.
   * @param  AmTable $table Tabla en el que se basará el modelo a generar.
   * @return bool           Si se generó o no el modelo.
   */
  public function generateBaseModelFile(AmTable $table){

    // Obtener el nombre del archivo destino
    $path = $this->getBaseModelClassFilename($table->getTableName());
    
    // Crear directorio donde se ubicará el archivo si no existe
    @mkdir(dirname($path), 755, true);

    // Generar el archivo
    return !!@file_put_contents($path, "<?php\n\n" .
      AmGenerator::classBaseModel($this, $table));
    
  }

  /**
   * Genera la clase base del modelo y su archivo de configuración.
   * @param  AmTable $table Tabla en el que se basará el modelo a generar.
   * @return Hash           Resultado de la generación del archivo de
   *                        configuración y el modelo.
   */
  public function generateBaseModel(AmTable $table){
    return array(

      // Crear archivo de configuración
      'conf' => AmCoder::generate(
        $this->getBaseModelConfFilename($table->getTableName()),
        $table->toArray()
      ),

      // Crear clase
      'model' => $this->generateBaseModelFile($table)

    );
  }

  /**
   * Genera todos los modelos bases correspondientes a las tablas de un esquema.
   * @return hash Resultado de la generación de la configuración y el modelo.
   */
  public function generateScheme(){

     // Para retorno
    $ret = array(
      'tables' => array(),
    );

    // Obtener listado de nombres de tablas
    $tables = $this->q($this->sqlGetTables())->col('tableName');

    foreach ($tables as $tableName)

      // Obtener instancia de la tabla
      $ret['tables'][$tableName] = $this->generateBaseModel(
        $this->getTableFromScheme($tableName)
      );

    return $ret;

  }

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

      // Obtener charset y collation
      $charset = $this->realScapeString($this->getCharset());
      $collaction = $this->realScapeString($this->getCollation());

      // Asignar variables
      $this->setServerVar('character_set_server', $charset);
      $this->setServerVar('collation_server', $collaction);
      // PENDIENTE: Revisar
      $this->setServerVar('names', $charset);

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
  public function q($from = null, $alias = 'q'){

    // Crear instancia
    $q = new AmQuery(array('scheme' => $this));
    
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
  public function setServerVar($varName, $value, $scope = false){

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

    return $this->q($this->sqlGetInfo())->row();

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
   * Indica si la tabla existe.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return bool                  Si la tabla existe.
   */
  public function existsTable($table){

    // Intenta obtener la descripcion de la tabla para saber si existe.
    return !!$this->getTableDescription($table);

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
   * Devuelve un array con el listado de tablas de la BD y su descripción.
   * @return array Array de hash con las descripción de las tablas.
   */
  public function getTables(){

    return $this->q($this->sqlGetTables())
                ->get();

  }

  /**
   * Obtiene la descripción de una tabla en el BD.
   * @param  string/AmTable $table Nombre o instancia de la tabla.
   * @return hash                  Hash con la descripcion de la tabla.
   */
  public function getTableDescription($table){
    
    // Obtener nombre de la tabla
    $table = $table instanceof AmTable? $table->getTableName() : $table;

    return $this->q($this->sqlGetTables())
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

    return $this->q($this->sqlGetTableColumns($table))
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
    $fks = $this->q($this->sqlGetTableForeignKeys($table))
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
    $fks = $this->q($this->sqlGetTableReferences($table))
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

    $uniques = $this->q($this->sqlGetTableUniques($table))
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
      if(count($fields) == 0)
        $fields = array_keys($values->getSelects());

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
            $resultValues[$i][] = $this->realScapeString($val);
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

  public static function getLenByType(){

  }

  /**
   * Devuelve la carpeta destino para los modelos base de los esquemas.
   * @return string Directorio de modelos base.
   */
  public static function getSchemesDir(){

    return self::$schemesDir;

  }

  /**
   * Devuelve la carpeta destino para los modelos definidos.
   * @return string Directorio de modelos.
   */
  public static function getModelsDir(){

    return self::$modelsDir;

  }
  
  /**
   * Devuelve la configuración de un determinado esquema.
   * @param  string $name Nombre del esquema buscado.
   * @return hash         Configuración del esquema.
   */
  public static function getSchemeConf($name = ''){

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
    $schemeConf = self::getSchemeConf($name);

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
   * Devuelve la instancia de una tabla en una fuente determinada
   * @param  string  $tableName  Nombre de la tabla que se desea.
   * @param  string  $schemeName Nombre del esquema al que pertenece la
   *                             tabla.
   * @param  string  $model      Nombre del modelo de la tabla.
   * @return AmTable             Instancia de la tabla.
   */
  public static function table($tableName, $schemeName = '', $model = null){

    // Obtener la instancia de la fuente
    $scheme = self::get($schemeName);

    if(!isset($model))
      $model = $scheme->getSchemeModelName($tableName);

    // Si ya ha sido instanciada la tabla
    // entonces se devuelve la instancia
    if($scheme->hasTableInstance($model))
      return $scheme->getTableInstance($model);

    // Instancia la clase
    $table = new AmTable(array(
      'schemeName' => $schemeName,
      'tableName' => $tableName,
      'model' => $model
    ));

    return $table;

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

      // Incluir modelo y  obtener la tabla
      self::table($m[1], $m[2]);
      
      $scheme = self::get($m[2]);

      $model = $scheme->getBaseModelClassName($m[1]);

    }else{

      // Obtener el hash de directorios de modelos.
      $models = Am::getProperty('models');

      // Obtener el directorio del modelo actual.
      $modelDir = realPath(
        itemOr($model, $models, itemOr('', $models, self::getModelsDir()))
      );

      // Cargar los paths de clases en dicho directorio.
      Am::loadPathClases($modelDir);

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
  // Metodos Abstractos que deben ser definidos en las implementaciones
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
   * Obtiene una cade con un valor seguro para el manejador de DBSM.
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
   * Procesa un hash de propiedades de una columna de una tabla para
   * convertirlo en una forma estandar.
   * @param  hash $column Hash de atributos de la columna sin procesar.
   * @return hash         Hash de atributos de la columna procesado.
   */
  abstract public function sanitize(array $column);

  //////////////////////////////////////////////////////////////////////////////
  // Metodo para obtener los SQL a ejecutar.
  //////////////////////////////////////////////////////////////////////////////

  /**
   * Devuelve un nombre de un objeto de BD entendible para el DBSM.
   * @param   string  $name   Nombre que se desea obtener.
   * @return  string          Identificador válido.
   */
  abstract public function getParseName($name);

  /**
   * Obener el SQL de una query.
   * @param  AmQuery $q Query.
   * @return string     SQL de query.
   */
  abstract public function sqlSelectQuery(AmQuery $q);

  /**
   * SQL de valores para ejecutar un query insert.
   * @param  array/AmQuery $values Array de hash de valores, object y/o
   *                               instancias de AmModels o implementaciones.
   *                               También puede puede ser un consulta select.
   * @return string                SQL para los valores.
   */
  abstract protected function sqlInsertValues($values);

  /**
   * SQL para campos para ejecutar un query insert.
   * @param  array  $fields Array con los nombres de los campos.
   * @return string         SQL con los nombres de los campos.
   */
  abstract protected function sqlInsertFields(array $fields);

  /**
   * SQL para una query insert.
   * @param  array/AmQuery  $values Array de hash de valores, object y/o
   *                                instancias de AmModels o implementaciones.
   *                                También puede puede ser un consulta select.
   * @param  string/AmTable $table  Nombre o instancia de la tabla donde, o
   *                                nombre del modelo donde se desea insertar
   *                                los valores.
   * @param  array          $fields Listado de campos.
   * @return string                 SQL de la query insert.
   */
  abstract public function sqlInsert($values, $table, array $fields = array());

  /**
   * SQL para query update.
   * @param  AmQuery $q Query de la que se desea obtener el SQL.
   * @return string     SQL del query.
   */
  abstract public function sqlUpdateQuery(AmQuery $q);

  /**
   * SQL para un query delete.
   * @param  AmQuery $q [description]
   * @return string     [description]
   */
  abstract public function sqlDeleteQuery(AmQuery $q);

  /**
   * SQL para setear una variable del DBSM
   * @param  string $varName Nombre de la variable a setear.
   * @param  string $value   Valor a asignar.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return string          SQL para la operación.
   */
  abstract public function sqlSetServerVar($varName, $value, $scope = false);

  /**
   * SQL para seleccionar la BD
   * @return string SQL para la operación.
   */
  abstract public function sqlSelectDatabase();

  /**
   * SQL para la clausula SELECT de una query de selección.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlSelect(AmQuery $q);
  
  /**
   * SQL para la clausula FROM de una query de selección o actualización.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlFrom(AmQuery $q);
  
  /**
   * SQL para la clausulas JOINS de una query de selección o actualización.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlJoins(AmQuery $q);
  
  /**
   * SQL para la clausula WHERE de una query de selección, actualización o
   * eliminación.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlWhere(AmQuery $q);
  
  /**
   * SQL para la clausula GROUP BY de una query de selección.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlGroups(AmQuery $q);
  
  /**
   * SQL para la clausula ORDER BY de una query de selección.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlOrders(AmQuery $q);
  
  /**
   * SQL para la clausula LIMIT de una query de selección.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlLimit(AmQuery $q);
  
  /**
   * SQL para la clausula OFFSET de una query de selección.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlOffSet(AmQuery $q);
  
  /**
   * SQL para la clausula SET de una query de actualización.
   * @param  AmQuery $q Query del que se desea obtener la clausula.
   * @return string     SQL de la clausula.
   */
  abstract public function sqlSets(AmQuery $q);

  /**
   * SQL para crear una BD.
   * @param  bool   $ifNotExists Si se agregará o no la clausula IF NOT EXISTS.
   * @return string              SQL para la operación.
   */
  abstract public function sqlCreate($ifNotExists = true);
  
  /**
   * SQL para remover una BD.
   * @return string SQL para la operación.
   */
  abstract public function sqlDrop();
  
  /**
   * SQL para obtener la información de una BD.
   * @return string SQL para la operación.
   */
  abstract public function sqlGetInfo();

  /**
   * SQL para obtener la descripción de las tablas del esquema.
   * @return string SQL para la operación.
   */
  abstract public function sqlGetTables();
  
  /**
   * SQL para obtener la descripción de las columnas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function sqlGetTableColumns($table);
  
  /**
   * SQL para obtener la descripción de las claves unicas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function sqlGetTableUniques($table);
  
  /**
   * SQL para obtener la descripción de las claves foráneas de una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function sqlGetTableForeignKeys($table);
  
  /**
   * SQL para obtener la descripción de las referencias a una tabla.
   * @param  string $table Nombre de la tabla.
   * @return string        SQL para la operación.
   */
  abstract public function sqlGetTableReferences($table);

  /**
   * SQL para indicar el set de caracteres.
   * @return string SQL de la clausula.
   */
  abstract public function sqlCharset();

  /**
   * SQL para indicar la coleción de caracteres.
   * @return string SQL de la clausula.
   */
  abstract public function sqlCollation();

  /**
   * SQL para un campo para un query create table.
   * @param  AmField $field Campo del que se desea obtener el SQL.
   * @return string         SQL correspondiente.
   */
  abstract public function sqlField(AmField $field);

  /**
   * SQL para crear una tabla.
   * @param  AmTable $t           Instancia de la tabla que se desea crear.
   * @param  bool    $ifNotExists Si se agregará la clausula IF NOT EXISTS.
   * @return string               SQL para la operación.
   */
  abstract public function sqlCreateTable(AmTable $t, $ifNotExists = true);

  /**
   * SQL para eliminar una tabla.
   * @param  string $table    Nombre de la tabla a eliminar.
   * @param  bool   $ifExists Si se agregará la clausula IF EXISTS
   * @return string           SQL para la operación.
   */
  abstract public function sqlDropTable($table, $ifExists = true);

  /**
   * SQL para truncar(vaciar) una tabla.
   * @param  string $table    Nombre de la tabla que se desea truncar.
   * @param  bool   $ignoreFk Si se deben ignorar los foreign keys.
   * @return string           SQL para la operación.
   */
  abstract public function sqlTruncate($table, $ignoreFk = true);

  /**
   * SQL para crear una vista.
   * @param  AmQuery $t       Instancia del query del cual se desea crear la
   *                          vista.
   * @param  bool    $replace Si se agregará la clausula OR REPLACE.
   * @return string           SQL para la operación.
   */
  abstract public function sqlCreateView(AmQuery $q, $replace = true);

  /**
   * SQL para eliminar una vista.
   * @param  string $q        Nombre de la vista a eliminar.
   * @param  bool   $ifExists Si se agregará la clausula IF EXISTS
   * @return string           SQL para la operación.
   */
  abstract public function sqlDropView($q, $ifExists = true);

}
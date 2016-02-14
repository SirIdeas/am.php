<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * -----------------------------------------------------------------------------
 * Clase para representar una conexión a una base de datos.
 * -----------------------------------------------------------------------------
 */

abstract class AmScheme extends AmObject{

  protected static
    $defaultsLen = array(
      'bit' => 8,
      'char' => 64,
      'varchar' => 128,
    ),
    $includedModels = array(),
    $schemes = array(),
    $schemesDir = 'models';

  protected

    /**
     * -------------------------------------------------------------------------
     * String con el nombre clave de la conexión. Se asume es único.
     * -------------------------------------------------------------------------
     */
    $name = null,

    /**
     * -------------------------------------------------------------------------
     * String con el prefijo para las clases nacidas de esta fuente.
     * -------------------------------------------------------------------------
     */
    $prefix = null,

    /**
     * -------------------------------------------------------------------------
     * String con el driver utilizado en la fuente.
     * -------------------------------------------------------------------------
     */
    $driver = null,

    /**
     * -------------------------------------------------------------------------
     * String con el nombre de la base de datos a conectarse.
     * -------------------------------------------------------------------------
     */
    $database = null,

    /**
     * -------------------------------------------------------------------------
     * String con la dirección o nombre del servidor.
     * -------------------------------------------------------------------------
     */
    $server = null,

    /**
     * -------------------------------------------------------------------------
     * Integer/string con número del puerto para la conexión.
     * -------------------------------------------------------------------------
     */
    $port = null,

    /**
     * -------------------------------------------------------------------------
     * String con el nombre de usuario para la conexion.
     * -------------------------------------------------------------------------
     */
    $user = null,

    /**
     * -------------------------------------------------------------------------
     * String con el password para la conexion.
     * -------------------------------------------------------------------------
     */
    $pass = null,

    /**
     * -------------------------------------------------------------------------
     * String con el charset.
     * -------------------------------------------------------------------------
     */
    $charset = null,

    /**
     * -------------------------------------------------------------------------
     * String con el collage.
     * -------------------------------------------------------------------------
     */
    $collage = null,

    /**
     * -------------------------------------------------------------------------
     * Hash con los las instancias por modelos de la conexión.
     * -------------------------------------------------------------------------
     */
    $tables = array();

  /**
   * ---------------------------------------------------------------------------
   * El destructor del objecto cierra la conexión
   * ---------------------------------------------------------------------------
   */
  public function __destruct() {

    $this->close();

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre del esquema a la que referencia.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre del esquema.
   */
  public function getName(){
    
    return $this->name;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el prefijo para los modelos del esquema.
   * ---------------------------------------------------------------------------
   * @return  string  Prefijo para clases.
   */
  
  public function getPrefix(){
    
    return $this->prefix;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre driver de conexión.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre del driver.
   */
  public function getDriver(){
    
    return $this->driver;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de la base de datos.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre de la base de datos.
   */
  public function getDatabase(){
    
    return $this->database;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre o dirección del servidor.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre o dirección del servidor.
   */
  public function getServer(){
    
    return $this->server;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el Número del puerto para la conexión.
   * ---------------------------------------------------------------------------
   * @return  integer/string  Número de puerto para la conexión.
   */
  public function getPort(){
    
    return $this->port;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de usuario para la conexión.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre de usuario para la conexión.
   */
  public function getUser(){
    
    return $this->user;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el password para la conexión.
   * ---------------------------------------------------------------------------
   * @return  string  Passwor para la conexión.
   */
  public function getPass(){
    
    return $this->pass;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el charset.
   * ---------------------------------------------------------------------------
   * @return  string  Charset.
   */
  public function getCharset(){
    
    return $this->charset;

  }
    
  /**
   * ---------------------------------------------------------------------------
   * Devuelve el Collage.
   * ---------------------------------------------------------------------------
   * @return  string  Collage.
   */
  public function getCollage(){
    
    return $this->collage;

  }

  /**
   * ---------------------------------------------------------------------------
   * Agrega una tabla a la conexión.
   * ---------------------------------------------------------------------------
   * @param   AmTable   $table  Tabla a agregar.
   * @return  $this
   */
  public function addTable(AmTable $table){
    
    // Se agrega la tabla en la posición del modelo.
    $this->tables[$table->getModel()] = $table;
    return $this;

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la instancia de una tabla correspondiente a un modelo. Si no
   * existe la instancia para dicho modelo devuelve null.
   * ---------------------------------------------------------------------------
   * @param   string    $model  Nombre del modelo.
   * @return  AmTable           Devuelve la instancia de la tabla si existe.
   */
  public function getTableInstance($model = null){

    // Devuelve la instancia si existe
    return itemOr($model, $this->tables);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve si la instancia para un modelo está cargada.
   * ---------------------------------------------------------------------------
   * @param   string    $model  Nombre del modelo a consultar.
   * @return  boolean           Indica si existe la instancia de la tabla para
   *                            el modelo.
   */
  public function hasTableInstance($model){

    // Inidica si existe la instancia
    return isset($this->tables[$model]);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve los modelos del esquema generados en la aplicación
   * ---------------------------------------------------------------------------
   * @return  array   array de strings con los nombres de los modelos.
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
   * ---------------------------------------------------------------------------
   * Devuelve el directorio de modelos del schema actual.
   * ---------------------------------------------------------------------------
   * @return  string  Directorio de modelos de esquema.
   */
  public function getDir(){

    // Obtener el nombre del esquema
    $name = $this->getName();

    // Obtener el nombre del esquema por defecto su directorio raíz es el mismo
    // directorio de esquemas.
    return self::getSchemesDir() . (!empty($name)?'/' . $name : '');

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la nombre del modelo para identificarlo dentro de todos los
   * esquemas de la aplicación. El formato es: :<schemeName>@<tableName>
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  string          Nombre del modelo.
   */
  public function getSchemeModelName($model){

    return ':'.$this->name.'@'.underscore($model);

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de la clase del modelo.
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  string          Nombre de la clase del modelo.
   */
  public function getBaseModelClassName($model){

    return $this->getPrefix() . camelCase($model, true).'Base';

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la dirección del archivo de configuración del modelo.
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  string          Dirección del archivo de configuración.
   */
  public function getBaseModelConfFilename($model){

    return $this->getDir() . '/'. underscore($model) .'.conf.php';

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la dirección de la clase base del model.
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  string          Dirección de la clase base del modelo.
   */
  public function getBaseModelClassFilename($model){

    return $this->getDir() . '/'. $this->getBaseModelClassName($model) .'.php';

  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si existe la clase base de un modelo y su configuración.
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  bool            Si existe o no el modelo.
   */
  public function existsBaseModel($model){

    // Preguntar si existen el archivo del modelo base y el del controlador.
    return is_file($this->getBaseModelConfFilename($model))

        && is_file($this->getBaseModelClassFilename($model));
  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la configuración de un modelo base leída desde su archivo de
   * copnfiguración.
   * ---------------------------------------------------------------------------
   * @param   string  $model  Nombre del modelo que se desea consultar.
   * @return  hash            Hash de propiedades del modelo.
   */
  public function getBaseModelConf($model){
  
    // Si el archivo existe retornar la configuración    
    if(is_file($confFilePath = $this->getBaseModelConfFilename($model)))
      return AmCoder::decode($confFilePath);

    // Si no existe retornar falso
    return false;

  }

  /**
   * ---------------------------------------------------------------------------
   * Crea el archivo con la clase del modelo base basando en una tabla.
   * ---------------------------------------------------------------------------
   * @param   AmTable   $table  Tabla en el que se basará el modelo a generar.
   * @return  bool              Si se generó o no el modelo.
   */
  public function generateBaseModelFile(AmTable $table){

    // Incluir la clase para generar
    AmScheme::requireFile('AmGenerator.class.php');

    // Obtener el nombre del archivo destino
    $path = $this->getBaseModelClassFilename($table->getTableName());
    
    // Crear directorio donde se ubicará el archivo si no existe
    @mkdir(dirname($path), 755, true);

    // Generar el archivo
    return !!@file_put_contents($path, "<?php\n\n" .
      AmGenerator::classBaseModel($this, $table));
    
  }

  /**
   * ---------------------------------------------------------------------------
   * Genera la clase base del modelo y su archivo de configuración.
   * ---------------------------------------------------------------------------
   * @param   AmTable   $table  Tabla en el que se basará el modelo a generar.
   * @return  Hash              Resultado de la generación del archivo de
   *                            configuración y el modelo.
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
   * ---------------------------------------------------------------------------
   * Genera todos los modelos bases correspondientes a las tablas de un esquema.
   * ---------------------------------------------------------------------------
   * @return  Hash  Resultado de la generación de la configuración y el modelo.
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
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de la BD para ser reconocida en el DBSM.
   * ---------------------------------------------------------------------------
   * @return  string  Nombre de la BD para ser utilizada dentro del DBSM.
   */
  public function getParseDatabaseName(){

    return $this->getParseName($this->getDatabase());

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve el nombre de una tabla para ser reconocida en el DBSM
   * ---------------------------------------------------------------------------
   * @param   string/AmTable  $table  Tabla de la que se desea obtener le
   *                                  nombre. Puede ser un string y una
   *                                  instancia de AmTable.
   * @param   boolean         $only   Si se devuelve el nombre de la tabla
   *                                  relativo al nombre de la base de datos.
   * @return  string                  Nombre de tabla obtenido.
   */
  public function getParseObjectDatabaseName($table, $only = false){

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
   * ---------------------------------------------------------------------------
   * Obtiene la primera tabla de un Query y obtiene su nombre para ser
   * reconocido en el DBSM.
   * ---------------------------------------------------------------------------
   * @param   AmQuery   $q      Consulta
   * @param   boolean   $only   Si el nombre de la tabla se devolverá con el
   *                            nombre de la BD.
   * @return  string            Nombre de tabla obtenido.
   */
  public function getParseTableNameOfView(AmQuery $q, $only = false){

    // Obtener los froms de la consulta
    $froms = $q->getFroms();

    foreach ($froms as $from) {

      // Si es una tabla obtener el nombre
      if($from instanceof AmTable)
        $from = $from->getTableName();

      // Si tiene un nombre válido retornar el nombre de la tabla
      if(isNameValid($from))
        return $this->getParseObjectDatabaseName($from, $only);
      
    }

  }

  /**
   * ---------------------------------------------------------------------------
   * Devuelve la cadena con la dirección del servidor y el puerto.
   * ---------------------------------------------------------------------------
   * @return  string  Dirección del servidor con el puertos
   */
  public function getServerString(){

    // Obtener el puerto
    $port = $this->getPort();

    if(empty($port))
      $port = $this->getDefaultPort();

    return "{$this->getServer()}:{$port}";

  }

  /**
   * ---------------------------------------------------------------------------
   * Realiza la conexión.
   * ---------------------------------------------------------------------------
   * @return  Resource  Handle de conexión establecida o FALSE si falló.
   */
  public function connect(){

    $ret = $this->start();

    // Cambiar la condificacion con la que se trabajará
    if($ret){
      $this->setServerVar('character_set_server',
        $this->realScapeString($this->getCharset()));

      // PENDIENTE: Revisar
      $this->execute('set names \'utf8\'');
    }

    return $ret;

  }

  /**
   * ---------------------------------------------------------------------------
   * Función para reconectar. Desconecta y vuelve a conectar la DB.
   * ---------------------------------------------------------------------------
   * @return [type] [description]
   */
  public function reconnect(){

    $this->close();           // Desconectar
    return $this->connect();  // Volver a conectar

  }

  /**
   * ---------------------------------------------------------------------------
   * Seleciona la BD
   * ---------------------------------------------------------------------------
   * @return [type] [description]
   */
  public function select(){

    // Ejecuta el SQL de seleción de de BD.
    return $this->query($this->sqlSelectDatabase());

  }

  /**
   * ---------------------------------------------------------------------------
   * Indica si la BD existe
   * ---------------------------------------------------------------------------
   * @return [type] [description]
   */
  public function exists(){

    // Intenta selecionar. Si logra selecionar la BD existe.
    return !!$this->select();

  }

  /**
   * ---------------------------------------------------------------------------
   * Crea una instancia de un AmQuery para la actual BD.
   * ---------------------------------------------------------------------------
   * @param   string/AmQuery  $from   From principal de la consulta.
   * @param   string          $alias  Alias del from recibido.
   * @return  AmQuery                 Instancia de l query creado.
   */
  public function q($from = null, $alias = 'q'){

    // Crear instancia
    $q = new AmQuery;
    
    // Asignar fuente
    $q->setScheme($this);

    // Asignar el from de la consulta
    if(!empty($from))
      $q->fromAs($from, $alias);

    return $q;

  }

  /**
   * ---------------------------------------------------------------------------
   * Ejecutar una consulta SQL desde el ámbito de la BD actual
   * ---------------------------------------------------------------------------
   * @param   string(SQL)/AmQuery   $q  SQL a ejecutar o instancia de AmQuery a
   *                                    ejecutar.
   * @return  boolean/int               Devuelve el resultado de la ejecución.
   *                                    Puede ser un valor booleano que indica
   *                                    si se ejecuto la consulta
   *                                    satisfactoriamente, o un integer en el
   *                                    caso de haberse ejecutatado un insert.
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
   * ---------------------------------------------------------------------------
   * Setea el valor de una variable en el DBSM
   * ---------------------------------------------------------------------------
   * @param   string  $varName  Nombre de la variable
   * @param   any     $value    Valor a asignar
   */
  public function setServerVar($varName, $value){

    return !!$this->execute($this->sqlSetServerVar($varName, $value));

  }

  /**
   * ---------------------------------------------------------------------------
   * Crea la BD
   * ---------------------------------------------------------------------------
   * @param   boolean   $ifNotExists  Indica si la BD se ejecuta con el
   *                                  parmámetro existe.
   * @return  boolean                 Si se creó la BD. Si la BD ya existe y
   *                                  el parámetro $ifNotExists == true,
   *                                  retornará true.
   */
  public function create($ifNotExists = true){

    return !!$this->execute($this->sqlCreate($ifNotExists));

  }

  // Elimina la BD
  /**
   * [drop description]
   * @param  boolean $ifExists [description]
   * @return [type]            [description]
   */
  public function drop($ifExists = true){

    return !!$this->execute($this->sqlDrop($ifExists));

  }

  // Obtener la información de la BD
  /**
   * [getInfo description]
   * @return [type] [description]
   */
  public function getInfo(){

    return $this->q($this->sqlGetInfo())->row();

  }

  // Crear tabla
  /**
   * [createTable description]
   * @param  AmTable $t           [description]
   * @param  boolean $ifNotExists [description]
   * @return [type]               [description]
   */
  public function createTable(AmTable $t, $ifNotExists = true){

    return !!$this->execute($this->sqlCreateTable($t, $ifNotExists));

  }

  // Crea todas las tablas de la BD
  /**
   * [createTables description]
   * @return [type] [description]
   */
  public function createTables(){

    $ret = array(); // Para el retorno

    // Obtener los nombres de la tabla en el archivo
    $tablesNames = $this->getGeneratedModels();

    // Recorrer cada tabla generar crear la tabla
    foreach ($tablesNames as $table)
      // Crear la tabla
      $ret[$table] = $this->createTable($table);

    return $ret;

  }

  /**
   * [dropTable description]
   * @param  [type]  $table    [description]
   * @param  boolean $ifExists [description]
   * @return [type]            [description]
   */
  public function dropTable($table, $ifExists = true){

    return !!$this->execute($this->sqlDropTable($table, $ifExists));

  }

  // Indica si la tabla existe
  /**
   * [existsTable description]
   * @param  [type] $table [description]
   * @return [type]        [description]
   */
  public function existsTable($table){

    return !!$this->getTableDescription($table);

  }

  /**
   * [truncate description]
   * @param  [type]  $table    [description]
   * @param  boolean $ignoreFk [description]
   * @return [type]            [description]
   */
  public function truncate($table, $ignoreFk = true){

    return !!$this->execute($this->sqlTruncate($table, $ignoreFk));

  }

  // Devuelve un array con el listado de tablas de la BD
  /**
   * [getTables description]
   * @return [type] [description]
   */
  public function getTables(){

    return $this->q($this->sqlGetTables())
                ->get();

  }

  // Devuelve un array con el listado de tablas
  /**
   * [getTableDescription description]
   * @param  [type] $table [description]
   * @return [type]        [description]
   */
  public function getTableDescription($table){
    
    // Obtener nombre de la tabla
    $table = $table instanceof AmTable? $table->getTableName() : $table;

    return $this->q($this->sqlGetTables())
                ->where("tableName = '{$table}'")
                ->row();

  }

  // Obtener un listado de las columnas de una tabla
  /**
   * [getTableColumns description]
   * @param  [type] $table [description]
   * @return [type]        [description]
   */
  public function getTableColumns($table){

    return $this->q($this->sqlGetTableColumns($table))
                ->get(null, array($this, 'sanitize'));
                
  }

  // Obtener un listado de las columnas de una tabla
  /**
   * [getTableForeignKeys description]
   * @param  [type] $table [description]
   * @return [type]        [description]
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
          'columns' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['columns'][$fk['columnName']] = $fk['toColumn'];

    }

    return $ret;

  }

  // Obtener el listado de referencias a una tabla
  /**
   * [getTableReferences description]
   * @param  [type] $table [description]
   * @return [type]        [description]
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
          'columns' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['columns'][$fk['toColumn']] = $fk['columnName'];

    }

    return $ret;

  }

  // Obtener un listado de las columnas de una tabla
  /**
   * [getTableUniques description]
   * @param  [type] $table [description]
   * @return [type]        [description]
   */
  public function getTableUniques($table){

    $uniques = $this->q($this->sqlGetTableUniques($table))
                    ->get();

    // Group fields of unique indices for name.
    $realUniques = array();
    foreach ($uniques as $value) {
      $realUniques[$value['name']] = itemOr($value['name'], $realUniques,
        array());
      $realUniques[$value['name']][] = $value['columnName'];
    }

    return $realUniques;

  }


  // Devuelve la descripcion completa de una tabla
  // incluyendo los campos
  /**
   * [getTableFromScheme description]
   * @param  [type] $tableName [description]
   * @return [type]            [description]
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
      'referencesTo'  => $this->getTableForeignKeys($tableName),
      'referencesBy'  => $this->getTableReferences($tableName),
      'uniques'       => $this->getTableUniques($tableName),

    )));

    // Retornar tabla
    return $table;

  }

  /**
   * [createView description]
   * @param  AmQuery $q       [description]
   * @param  boolean $replace [description]
   * @return [type]           [description]
   */
  public function createView(AmQuery $q, $replace = true){

    return !!$this->execute($this->sqlCreateView($q, $replace));

  }

  /**
   * [dropView description]
   * @param  AmQuery $q        [description]
   * @param  boolean $ifExists [description]
   * @return [type]            [description]
   */
  public function dropView(AmQuery $q, $ifExists = true){

    return !!$this->execute($this->sqlDropView($q, $ifExists));

  }

  /**
   * [sqlOf description]
   * @param  AmQuery $q [description]
   * @return [type]     [description]
   */
  public function sqlOf(AmQuery $q){
    $type = $q->getType();

    if($type == 'select')
      return $this->sqlSelectQuery($q);

    if($type == 'insert')
      return $this->sqlInsertQuery($q,
        $q->getInsertTable(), $q->getInsertFields());

    if($type == 'update'){
      return $this->sqlUpdateQuery($q);
    }

    if($type == 'delete')
      return $this->sqlDelete($q);

    throw Am::e('AMSCHEME_QUERY_TYPE_UNKNOW', var_export($q, true));

  }

  // Ejecuta una consulta de insercion para los
  /**
   * [insertInto description]
   * @param  [type] $values [description]
   * @param  [type] $model  [description]
   * @param  array  $fields [description]
   * @return [type]         [description]
   */
  public function insertInto($values, $model, array $fields = array()){

    $table = $model;

    // Obtener la instancia de la tabla
    if(!$table instanceof AmTable)
      $table = $this->getTableInstance($table);

    if(!$table)
      throw Am::e('AMSCHEME_MODEL_WITHOUT_TABLE', $model);

    // Agregar fechas de creacion y modificacion si existen en la tabla
    $table->setAutoCreatedAt($values);
    $table->setAutoUpdatedAt($values);

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
          $values[$i] = $v->dataToArray(!$mergeWithFields);
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

    // Obtener el SQL para saber si es valido
    $sql = $this->sqlInsertQuery($values, $table, $fields);

    // Si el SQL está vacío o si se genera un error en la insercion
    // se devuelve falso
    if(trim($sql) == '' || $this->execute($sql) === false)
      return false;

    // Obtener el ultimo ID insertado
    $id = $this->getLastInsertedId();

    // Se retorna el el último id insertado o true en
    // el caso de que se hayan insertado varios registros
    return $id === 0 ? true : $id;

  }

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Devuelve la carpeta destino para los orm
  /**
   * [getSchemesDir description]
   * @return [type] [description]
   */
  public static function getSchemesDir(){

    return self::$schemesDir;

  }

  // Incluye un archivo dentro buscado dentro de la
  // carpeta de la libreria
  /**
   * [requireFile description]
   * @param  [type]  $file         [description]
   * @param  boolean $onCurrentDir [description]
   * @return [type]                [description]
   */
  public static function requireFile($file, $onCurrentDir = true){

    $path = ($onCurrentDir? dirname(__FILE__).'/' : '') . $file;

    if(!is_file($path))
      throw Am::e('AMSCHEME_FILE_NOT_FOUND', $path);

    require_once $path;

  }

  // Devuelve la configuracion de una determinada fuente de datos
  /**
   * [getSchemeConf description]
   * @param  string $schemeName [description]
   * @return [type]             [description]
   */
  public static function getSchemeConf($schemeName = ''){

    // Obtener configuraciones para las fuentes
    $schemes = Am::getProperty('schemes', array());

    // Si no existe una configuración para el nombre de fuente
    if(!isset($schemes[$schemeName]))
      return null;

    // Asignar valores por defecto
    $schemes[$schemeName] = array_merge(
      array(
        'database'  => $schemeName,
        'driver'    => null,
      ),
      $schemes[$schemeName]
    );

    $schemes[$schemeName]['name'] = $schemeName;

    return $schemes[$schemeName];

  }

  // Devuelve una instancia de una fuente
  /**
   * [get description]
   * @param  string $name [description]
   * @return [type]       [description]
   */
  public static function get($name = ''){

    // Obtener la instancia si ya existe
    if(isset(self::$schemes[$name]))
      return self::$schemes[$name];

    // Obtener la configuración de la fuente
    $schemeConf = self::getSchemeConf($name);

    // Si no existe una configuración para el nombre de fuente
    // solicitado se retorna NULL
    if($schemeConf === null)
      throw Am::e('AMSCHEME_SCHEMECONF_NOT_FOUND', $name);

    // Obtener el driver de la fuente
    $driverClassName = self::driver($schemeConf['driver']);

    // Crear instancia de la fuente
    $schemes = new $driverClassName($schemeConf);
    $schemes->connect(); // Conectar la fuente

    return self::$schemes[$name] = $schemes;

  }

  // Incluye un driver de BD
  /**
   * [driver description]
   * @param  [type] $driver [description]
   * @return [type]         [description]
   */
  public static function driver($driver){

    // Obtener el nombre de la clase
    $driverClassName = camelCase($driver, true).'Scheme';

    // Se incluye satisfactoriamente el driver
    self::requireFile("drivers/{$driverClassName}.class.php");

    // Se retorna en nombre de la clase
    return $driverClassName;

  }

  // Devuelve la instancia de una tabla en una fuente determinada
  /**
   * [table description]
   * @param  [type] $tableName  [description]
   * @param  string $schemeName [description]
   * @param  [type] $model      [description]
   * @return [type]             [description]
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

    // Incluir modelo
    $modelPath = realpath($scheme->getBaseModelClassFilename($tableName));
    
    if(is_file($modelPath))
      require_once $modelPath;

    return $table;

  }

  /**
   * [model description]
   * @param  [type] $model [description]
   * @return [type]        [description]
   */
  public static function model($model){

    // Si es un modelo nativo
    if(preg_match('/^:(.*)@(.*)$/', $model, $m) || preg_match('/^:(.*)$/', $model, $m)){

      // Si no se indica la fuente tomar la fuente por defecto
      if(empty($m[2]))
        $m[2] = '';

      // Incluir modelo y  obtener la tabla
      self::table($m[1], $m[2]);
      
      $scheme = self::get($m[2]);

      $model = $scheme->getBaseModelClassName($m[1]);

      // Retornar el nombre de la clase del modelo correspondiente
      return class_exists($model)? $model : false;

    }

    // Obtener configuraciones de mails
    $models = Am::getProperty('models');

    // Si se recibió un string asignar como nombre del modelo
    if(is_string($model))
      $model = array('name' => $model);

    // Si no se recibió el nombre del modelo retornar falso
    if(!isset($model['name']))
      return false;

    // Si ya fue incluido el model salir
    if(in_array($model['name'], self::$includedModels))
      return $model['name'];

    // Configuración de valores po defecto
    $defaults = itemOr('', $models, array());

    if(is_string($defaults))
      $defaults = array('root' => $defaults);

    // Asignar directorio raíz de los modelos si no existe
    $defaults['root'] = itemOr('root', $defaults, self::getSchemesDir());

    // Configuración de valores del model
    $modelConf = itemOr($model['name'], $models, array());
    if(is_string($modelConf))
      $modelConf = array('root' => $modelConf);

    // Combinar opciones recibidas en el constructor con las
    // establecidas en el archivo de configuracion
    $model = array_merge($defaults, $modelConf, $model);

    // Incluir como modelo de usuario
    // Guardar el nombre del modelo dentro de los modelos incluidos
    // para no generar bucles infinitos
    self::$includedModels[] = $model['name'];

    // Incluir de configuracion local del modelo
    if(is_file($modelConfFile = $model['root'] . '.model.php')){
      $modelConf = require_once($modelConfFile);
      $model = array_merge($model, $modelConf);
    }

    // Incluir modelos requeridos por el modelo actual
    foreach($model['models'] as $require)
      self::model($require);

    // Incluir archivo del modelo
    if(is_file($modelFile = $model['root'] . $model['name'] . '.php'))
      require_once($modelFile);

    // Retornar el nombre de la clase si existe
    return class_exists($model['name'])? $model['name'] : false;

  }

  // Incluye un validator y devuelve el nombre de la clases correspondiente
  /**
   * [validator description]
   * @param  [type] $validator [description]
   * @return [type]            [description]
   */
  public static function validator($validator){

    // Obtener el nombre de la clase
    $validatorClassName = camelCase($validator, true).'Validator';

    // Si se incluye satisfactoriamente el validator
    self::requireFile("validators/{$validatorClassName}.class.php");

    // Se retorna en nombre de la clase
    return $validatorClassName;

  }

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Metodo para obtener el puerto por defecto para una conexión
  /**
   * [getDefaultPort description]
   * @return [type] [description]
   */
  abstract public function getDefaultPort();

  // Metodo para crear una conexion
  abstract protected function start();

  // Metodo para cerrar una conexión
  abstract public function close();

  // Obtener el número del último error generado en la conexión
  abstract public function getErrNo();

  // Obtener la descripcion del último error generado en la conexión
  abstract public function getError();

  // Devuelve una cadena con un valor valido en el gesto de BD
  abstract public function realScapeString($value);

  // Realizar una consulta SQL
  abstract protected function query($sql);

  // Obtener el siguiente registro de un resultado
  abstract public function getFetchAssoc($result);

  // Obtener el ID del ultimo registro insertado
  abstract public function getLastInsertedId();

  // Devuelve un tipo de datos en el gestor de BD
  abstract public function sanitize(array $column);

  //---------------------------------------------------------------------------
  // Metodo para obtener los SQL a ejecutar
  //---------------------------------------------------------------------------

  // Devuelve un nombre entre comillas simples entendibles por el gesto
  abstract public function getParseName($identifier);

  // Consulta select
  abstract public function sqlSelectQuery(AmQuery $q);

  // Consulta insert
  abstract public function sqlInsertQuery($values, $table, array $fields = array());

  // Consulta update
  abstract public function sqlUpdateQuery(AmQuery $q);

  // Consulta delete
  abstract public function sqlDeleteQuery(AmQuery $q);

  // Setear un valor a una variable de servidor
  abstract public function sqlSetServerVar($varName, $value);

  // SQL para seleccionar la BD
  abstract public function sqlSelectDatabase();

  abstract public function sqlSelect(AmQuery $q, $with = true);
  abstract public function sqlFrom(AmQuery $q, $with = true);
  abstract public function sqlJoins(AmQuery $q);
  abstract public function sqlWhere(AmQuery $q, $with = true);
  abstract public function sqlGroups(AmQuery $q, $with = true);
  abstract public function sqlOrders(AmQuery $q, $with = true);
  abstract public function sqlLimit(AmQuery $q, $with = true);
  abstract public function sqlOffSet(AmQuery $q, $with = true);
  abstract public function sqlSets(AmQuery $q, $with = true);

  // Devuelve un String con el SQL para crear la base de datos
  abstract public function sqlCreate($ifNotExists = true);
  abstract public function sqlDrop();
  abstract public function sqlGetInfo();

  // SQL para obtener el listado de tablas
  abstract public function sqlGetTables();

  abstract public function sqlGetTableColumns($table);
  abstract public function sqlGetTableUniques($table);
  abstract public function sqlGetTableForeignKeys($table);
  abstract public function sqlGetTableReferences($table);

  // Set de Caracteres
  abstract public function sqlCharset();

  // Colecion de caracteres
  abstract public function sqlCollage();

  abstract public function sqlField(AmField $field);
  abstract public function sqlCreateTable(AmTable $t, $ifNotExists = true);
  abstract public function sqlDropTable($table, $ifExists = true);
  abstract public function sqlTruncate($table, $ignoreFk = true);

  abstract public function sqlCreateView(AmQuery $q, $replace = true);
  abstract public function sqlDropView($q, $ifExists = true);

}
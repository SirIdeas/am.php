<?php

abstract class AmScheme extends AmObject{

  protected static
    $includedModels = array(),
    $schemes = array(),
    $schemesDir = 'models';

  // Propiedades
  protected
    $name       = null,     // Nombre clave para la fuente. Se asume es único.
    $prefix     = null,     // Prefijo para las clases nacidas de esta fuente.
    $driver     = null,     // Driver utilizado en la fuente.
    $database   = null,     // Nombre de la base de datos para conectarse.
    $server     = null,     // Nombre del servidor.
    $port       = null,     // Puerto de conexion.
    $user       = null,     // Usuario para la conexion.
    $pass       = null,     // Password para la conexion.
    $charset    = null,     // Codificacion de caracteres.
    $collage    = null,     // Colexion de caracteres.
    $tables     = array();  // Listado de instancias de tablas.

  // El destructor del objeto deve cerrar la conexion
  public function __destruct() {

    $this->close();

  }

  // Métodos get para las propiedades principales
  public function getName(){
    
    return $this->name;

  }

  public function getPrefix(){
    
    return $this->prefix;

  }

  public function getDriver(){
    
    return $this->driver;

  }

  public function getDatabase(){
    
    return $this->database;

  }

  public function getServer(){
    
    return $this->server;

  }

  public function getPort(){
    
    return $this->port;

  }

  public function getUser(){
    
    return $this->user;

  }

  public function getPass(){
    
    return $this->pass;

  }

  public function getCharset(){
    
    return $this->charset;

  }

  public function getCollage(){
    
    return $this->collage;

  }

  // Setear la instancia de una tabla
  // 
  public function setTable($offset, AmTable $t){
    
    $this->tables[$offset] = $t;
    return $this;

  }

  public function getTableInstances(){
    
    return $this->tables;

  }

  // Obtener la instancia de una tabla
  public function getTableInstance($table){

    // Si es una instancia de una tabla
    if($table instanceof AmTable)
      return $table;

    // Si ya existe la instancia de la tabla
    if($this->hasTableInstance($table))
      return $this->tables[$table];

    // Sino instanciar la tabla
    return AmScheme::table($table, $this->getName());

  }

  // Indica si ya está cargada una instancia de las tablas
  public function hasTableInstance($table){

    return isset($this->tables[$table]);

  }

  public function getTableNames(){

    $ret = array();

    $ret = amGlobFiles($this->getDir(), array(
      'include' => '/(.*[\/\\\\](.*))\.conf\.php/',
      'return' => 1
    ));

    foreach ($ret as $i => $file) {
      if(dirname(dirname($file)) != realpath($this->getDir())){
        $ret[$i] = false;
      }else{
        $ret[$i] = basename($file);
      }
    }

    return array_values($ret);

  }

  // Obtener la ruta de la carpeta para las clases del ORM de la BD actual
  public function getDir(){

    return self::getSchemesDir() . '/' . $this->getName();

  }

  // Retorna donde se guarda la configuración de la fuente
  public function getConfFilename(){

    return $this->getDir() . '/' . underscore($this->getName()) . '.conf.php';

  }

  // Devuelve la configuracion particular de la fuente
  public function getConf(){

    $path = $this->getConfFilename();
    return AmCoder::read($path);

  }

  // Obtener la carpeta de archivos bases para un tabla
  public function getBaseModelDir($model){

    return $this->getDir();

  }

  // // Nombre de las clases relacionadas a una tabla
  public function getBaseModelClassname($model){

    return $this->getPrefix() . camelCase($model, true).'Base';

  }

  // Devuelve la direccion del archivo de configuracion
  public function getBaseModelConfFilename($model){

    return $this->getBaseModelDir($model) . '/'. underscore($model) .'.conf.php';

  }

  // Devuelve la dirección de la clase del model Base
  public function getBaseModelClassFilename($model){

    return $this->getBaseModelDir($model) . '/'. $this->getBaseModelClassname($model) .'.class.php';

  }

  // Inidic si todas las clases y archivos de un model existes
  public function existsBaseModel($model){

    return is_file($this->getBaseModelConfFilename($model))

        && is_file($this->getBaseModelClassFilename($model));
  }

  // Obtener la configuracion del archivo de configuracion propio de un model
  public function getBaseModelConf($model){

    return AmCoder::decode($this->getBaseModelConfFilename($model));

  }

  // Crea el archivo de configuracion para una fuente
  public function generateConfFile($path = null, $conf = null, $rw = true){

    // Obtener de el nombre del archivo destino
    if(!isset($path))
      $path = $this->getConfFilename();

    if(!isset($conf))
      $conf = $this->toArray();

    if(!is_file($path) || $rw){
      AmCoder::write($path, $conf);
      return true;
    }
    return false;
  }

  // Crea el archivo que contiene clase para la tabla
  public function generateBaseModelFile($model, AmTable $table){

    // Incluir la clase para generar
    AmScheme::requireFile('AmGenerator.class.php');

    // Obtener el nombre del archivo destino
    $path = $this->getBaseModelClassFilename($model);
    
    // Crear directorio donde se ubicará el archivo si no existe
    @mkdir(dirname($path), 755, true);

    // Generar el archivo
    return !!@file_put_contents($path, "<?php\n\n" .
      AmGenerator::classBaseModel($this, $table));
    
  }

  public function generateBaseModel($model, $table){
    return array(

      // Crear archivo de configuración
      'conf' => $this->generateConfFile(
        $this->getBaseModelConfFilename($model),
        $table->toArray()
      ),

      // Crear clase
      'model' => $this->generateBaseModelFile($model, $table)

    );
  }

  // Metodo para crear todos los modelos de la BD
  public function generateScheme(){

     // Para retorno
    $ret = array(
      // 'scheme' => $this->generateConfFile(),
      'tables' => array(),
    );

    // Obtener listado de nombres de tablas
    $tables = $this->q($this->sqlGetTables())->getCol('tableName');

    foreach ($tables as $model){

      // Obtener instancia de la tabla
      $table = $this->describeTable($model);

      $ret['tables'][$model] = $this->generateBaseModel($model, $table);

    }

    return $ret;

  }

  // Devuelve el nombre de la BD para ser reconocida en el gestor de BD
  public function getParseNameDatabase(){

    return $this->getParseName($this->getDatabase());

  }

  // Devuelve el nombre de una tabla para ser reconocida en el gestor de BD
  public function getParseNameTable($table, $only = false){

    // Obtenerl solo el nombre de la tabla
    $table = $table instanceof AmTable? $table->getTableName() : $table;
    $table = $this->getParseName($table);

    // Si se desea obtener solo el nombre
    if($only)
      return $table;

    // Retornar el nombre de la tabla con la BD
    return $this->getParseNameDatabase().'.'.$table;

  }


  // Devuelve la cadena de conexión del servidor
  public function getServerString(){

    $port = $this->getPort();
    $defPort = $this->getDefaultPort();

    return $this->getServer() . ':' . (!empty($port) ? $port : $defPort);

  }

  // Realiza la conexión
  public function connect(){

    $ret = $this->start();

    // Cambiar la condificacion con la que se trabajará
    if($ret){
      $this->setServerVar('character_set_server',
        $this->realScapeString($this->getCharset()));
      // REVISAR
      $this->execute('set names \'utf8\'');
    }

    return $ret;

  }

  // Función para reconectar
  public function reconnect(){

    $this->close();           // Desconectar
    return $this->connect();  // Volver a conectar

  }

  // Seleccionar la base de datos
  public function select(){

    return $this->query($this->sqlSelectDatabase());

  }

  // Indica si la BD existe
  public function exists(){

    return !!$this->select();

  }


  // Crea una instancia de un query
  public function q($from = null, $as = 'q'){
    $q = new AmQuery(); // Crear instancia
    $q->setScheme($this);  // Asignar fuente
    if(!empty($from)) $q->fromAs($from, $as);  // Asignar el from de la consulta
    return $q;

  }

  // Ejecutar una consulta SQL desde el ámbito de la BD actual
  public function execute($q){

    if($q instanceof AmQuery)
      $q = $q->sql();

    $this->select();
    return $this->query($q);

  }

  // Ejecuta un conjunto de consultas
  public function executeGroup(array $queries){
    $sqls = array();
    foreach ($queries as $key => $q)
      $sqls[] = (string)$q;

    return $this->execute(implode(';', $sqls));

  }

  // Setea el valor de una variable en el gestor
  public function setServerVar($varName, $value){

    return !!$this->execute($this->sqlSetServerVar($varName, $value));

  }

  // Crea la BD
  public function create(){

    return !!$this->execute($this->sqlCreate());

  }

  // Elimina la BD
  public function drop(){

    return !!$this->execute($this->sqlCreate());

  }

  // Obtener la información de la BD
  public function getInfo(){

    return $this->q($this->sqlGetInfo())->getRow('array');

  }

  // Crear tabla
  public function createTable(AmTable $t){

    return !!$this->execute($this->sqlCreateTable($t));

  }

  // Devuelve un array con el listado de tablas de la BD
  public function getTables(){
    return $this->q($this->sqlGetTables())
                ->getResult('array');
  }

  // Devuelve un array con el listado de tablas
  public function getTableDescription($table){
    return $this->q($this->sqlGetTables())
                ->where("tableName = '{$table}'")
                ->getRow('array');
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableColumns($table){
    return $this->q($this->sqlGetTableColumns($table))
                ->getResult('array', array($this, 'sanitize'));
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableForeignKeys($table){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $schemeName = $this->getName();

    // Obtener los ForeignKeys
    $fks = $this->q($this->sqlGetTableForeignKeys($table))
                ->getResult('array');

    foreach($fks as $fk){

      // Dividir el nombre del FK
      $name = explode('.', $fk['name']);

      // Obtener el ultimo elemento
      $name = array_pop($name);

      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          'scheme' => $schemeName,
          'tableName' => $fk['toTable'],
          'columns' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['columns'][$fk['columnName']] = $fk['toColumn'];

    }

    return $ret;

  }

  // Obtener el listado de referencias a una tabla
  public function getTableReferences($table){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $schemeName = $this->getName();

    // Obtener las referencias a una tabla
    $fks = $this->q($this->sqlGetTableReferences($table))
                ->getResult('array');

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
          'tableName' => $fk['fromTable'],
          'columns' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['columns'][$fk['toColumn']] = $fk['columnName'];

    }

    return $ret;

  }

  // Obtener un listado de las columnas de una tabla
  public function getTableUniques($table){

    $uniques = $this->q($this->sqlGetTableUniques($table))
      ->getResult('array');

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
  public function describeTable($tableName){

    // Obtener la descripcion basica
    $table = $this->getTableDescription($tableName);

    // Si no se encontró la tabla retornar falso
    if($table === false)
      return false;

    // Asignar fuente
    $table['scheme'] = $this;

    // Crear instancia anonima de la tabla
    $table = new AmTable($table);

    // Buscar la descripcion de sus campos y relaciones
    $table->describeTable(
      $this->getTableColumns($tableName),
      $this->getTableForeignKeys($tableName),
      $this->getTableReferences($tableName),
      $this->getTableUniques($tableName)
    );

    // Retornar tabla
    return $table;

  }

  public function sqlOf(AmQuery $q){
    $type = $q->getType();

    if($type == 'select')
      return $this->sqlSelectQuery($q);

    if($type == 'insert')
      return $this->sqlInsert($q, $q->getInsertTable(), $q->getInsertFields());

    if($type == 'update')
      return $this->sqlUpdate($q);

    if($type == 'delete')
      return $this->sqlDelete($q);

    throw Am::e('AMSCHEME_QUERY_TYPE_UNKNOW', var_export($q, true));

  }

  // Ejecuta una consulta de insercion para los
  public function insertInto($values, $table, array $fields = array()){

    // Obtener la instancia de la tabla
    $table = $this->getTableInstance($table);

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
          $fields = array_unique(array_merge($fields, array_keys($values[$i])));

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
  public static function getSchemesDir(){

    return self::$schemesDir;

  }

  // Incluye un archivo dentro buscado dentro de la
  // carpeta de la libreria
  public static function requireFile($file, $onCurrentDir = true){

    $path = ($onCurrentDir? dirname(__FILE__).'/' : '') . $file;

    if(!is_file($path))
      throw Am::e('AMSCHEME_FILE_NOT_FOUND', $path);

    require_once $path;

  }

  // Devuelve la configuracion de una determinada fuente de datos
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

  // Devuelve la instancia de una tabla en una fuente determinada
  public static function table($tableName, $scheme = ''){

    // Obtener la instancia de la fuente
    $scheme = self::get($scheme);

    // Si ya ha sido instanciada la tabla
    // entonces se devuelve la instancia
    if($scheme->hasTableInstance($tableName))
      return $scheme->getTable($tableName);

    // Instancia la clase
    $table = new AmTable(array(
      'scheme' => $scheme,
      'tableName' => $tableName
    ));

    // Incluir modelo
    self::requireFile($scheme->getBaseModelClassFilename($tableName), false);  // Clase base para el modelo

    // Asignar tabla
    $scheme->setTable($tableName, $table);

    return $table;

  }

  // Incluye un driver de BD
  public static function driver($driver){

    // Obtener el nombre de la clase
    $driverClassName = camelCase($driver, true).'Scheme';

    // Se incluye satisfactoriamente el driver
    self::requireFile("drivers/{$driverClassName}.class.php");

    // Se retorna en nombre de la clase
    return $driverClassName;

  }

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Metodo para obtener el puerto por defecto para una conexión
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
  abstract public function sqlCreate();
  abstract public function sqlDrop();
  abstract public function sqlGetInfo();

  // SQL para obtener el listado de tablas
  abstract public function sqlGetTables();

  abstract public function sqlGetTablePrimaryKeys($table);
  abstract public function sqlGetTableColumns($table);
  abstract public function sqlGetTableUniques($table);
  abstract public function sqlGetTableForeignKeys($table);
  abstract public function sqlGetTableReferences($table);

  // Set de Caracteres
  abstract public function sqlCharset();

  // Colecion de caracteres
  abstract public function sqlCollage();

  abstract public function sqlField(AmField $field);
  abstract public function sqlCreateTable(AmTable $t);
  abstract public function sqlTruncate(AmTable $t);
  abstract public function sqlDropTable(AmTable $t);

}
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

abstract class AmSource extends AmObject{

  protected static
    $ORM_FOLDER = 'models';

  // Propiedades
  protected
    $name       = null,     // Nombre clave para la fuente. Se Asumo es unico
    $prefix     = null,     // Prefijo para las clases nacidas de esta fuente
    $driver     = null,     // Driver utilizado en la fuente
    $database   = null,     // Nombre de la base de datos para conectarse
    $server     = null,     // Nombre del servidor
    $port       = null,     // Puerto de conexion
    $user       = null,     // Usuario para la conexion
    $pass       = null,     // Password para la conexion
    $charset    = null,     // Codificacion de caracteres
    $collage    = null,     // Colexion de caracteres
    $tables     = array(),  // Listado de instancias de tablas
    $tableNames = null;     // Listado de los nombres de la tabla de la BD

  // Reescribir constructor para leer la configuracion particular
  // de la fuente
  public function __construct($params = array()) {

    // Parchar los parametros
    $params = AmObject::parse($params);

    // Asignar solo el nombre
    parent::__construct(array(
      'name' => $params['name']
    ));

    // Mezclar con los valores particulares de la fuente
    $params = array_merge($this->getConf(), $params);

    // Eliminar el nombre porque ya se asignó
    unset($params['name']);

    // Llamar al constructor con los nuevos argumentos
    parent::__construct($params);

  }

  // El destructor del objeto deve cerrar la conexion
  public function __destruct() {
    $this->close();
  }

  // Métodos get para las propiedades principales
  public function getName(){ return $this->name; }
  public function getPrefix(){ return $this->prefix; }
  public function getDriver(){ return $this->driver; }
  public function getDatabase(){ return $this->database; }
  public function getServer(){ return $this->server; }
  public function getPort(){ return $this->port; }
  public function getUser(){ return $this->user; }
  public function getPass(){ return $this->pass; }
  public function getCharset(){ return $this->charset; }
  public function getCollage(){ return $this->collage; }
  public function getTables(){ return $this->tables; }
  public function getTableNames(){ return $this->tableNames; }

  // Obtener la instancia de una tabla
  public function getTable($table){

    // Si es una instancia de una tabla
    if($table instanceof AmTable)
      return $table;

    // Si ya existe la instancia de la tabla
    if($this->hasTableInstance($table))
      return $this->tables[$table];

    // Sino instanciar la tabla
    return AmORM::table($table, $this->getName());
  }

  // Indica si ya está cargada una instancia de las tablas
  public function hasTableInstance($table){
    return isset($this->tables[$table]);
  }

  // Obtener la ruta de la carpeta para las clases del ORM de la BD actual
  public function getDir(){
    return self::getFolderOrm() . '/' . $this->getName();
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
    return $this->getDir() . '/' . underscore($model);
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
    AmORM::requireFile('AmGenerator.class');

    // Obtener el nombre del archivo destino
    $path = $this->getBaseModelClassFilename($model);
    
    // Crear directorio donde se ubicará el archivo si no existe
    @mkdir(dirname($path), 755, true);

    // Generar el archivo
    file_put_contents($path, "<?php\n\n" .
      AmGenerator::classBaseModel($this, $table));
    
    return true;

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
  public function generateClassModels(){

     // Para retorno
    $ret = array(
      'source' => $this->generateConfFile(),
      'tables' => array(),
    );

    // Obtener listado de nombres de tablas
    $tables = $this->newQuery($this->sqlGetTables())->getCol('tableName');

    foreach ($tables as $model){

      // Obtener instancia de la tabla
      $table = $this->describeTable($model);

      $ret['tables'][$model] = $this->generateBaseModel($model, $table);

    }

    return $ret;

  }

  // Crea todas las tablas de la BD
  public function createTables(){

    $ret = array(); // Para el retorno

    // Obtener los nombres de la tabla en el archivo
    $tablesNames = $this->getTableNames();

    // Recorrer cada tabla generar crear la tabla
    foreach ($tablesNames as $tableName)
      // Crear la tabla
      $ret[$tableName] = $this->createTableIfNotExists($tableName);

    return $ret;

  }

  // Setear la instancia de una tabla
  public function setTable($offset, AmTable $t){
    $this->tables[$offset] = $t;
    return $this;
  }

  // Realiza la conexión
  public function connect(){
    $ret = $this->initConnect();

    // Cambiar la condificacion con la que se trabajará
    if($ret){
      $this->setServerVar('character_set_server', $this->realScapeString($this->getCharset()));
      // REVISAR
      $this->execute('set names \'utf8\'');
    }

    return $ret;
  }

  // Función para reconectar
  public function reconnect(){
    $this->close();      // Desconectar
    return $this->connect();  // Volver a conectar
  }

  // Devuelve la cadena de conexión del servidor
  public function getServerString(){

    $port = $this->getPort();
    $defPort = $this->getDefaultPort();

    return $this->getServer() . ':' . (!empty($port) ? $port : $defPort);

  }

  // Seleccionar la base de datos
  public function select(){
    return $this->query($this->sqlSelectDatabase());
  }

  // Indica si la BD existe
  public function exists(){
    return $this->select();
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

  // Ejecutar una consulta SQL desde el ámbito de la BD actual
  public function execute($sql){
    $this->select();
    return $this->query($sql);
  }

  // Crea una instancia de un query
  public function newQuery($from = null, $as = 'q'){
    $q = new AmQuery(); // Crear instancia
    $q->setSource($this);  // Asignar fuente
    if(!empty($from)) $q->fromAs($from, $as);  // Asignar el from de la consulta
    return $q;

  }

  // Ejecuta un conjunto de consultas
  public function executeGroup(array $queries){
    $sqls = array();
    foreach ($queries as $key => $q)
      // Si es un query obtener el SQL
      if($q instanceof AmQuery)
        $sqls[] = $q->sql();
      else
        // Si no convetir a string
        $sqls[] = (string)$q;

    return $this->execute(implode(';', $sqls));

  }

  // Devuelve un array con el listado de tablas de la BD
  public function getTablesFromSchema(){
    return $this->newQuery($this->sqlGetTables())
                ->getResult('array');
  }

  // Devuelve un array con el listado de tablas
  public function getTableDescription($table){
    return $this->newQuery($this->sqlGetTables())
                ->where("tableName = '{$table}'")
                ->getRow('array');
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
    $table['source'] = $this;

    // Crear instancia anonima de la tabla
    $table = new AmTable($table);
    // Buscar la descripcion de sus campos y relaciones
    $table->describeTable();

    // Retornar tgabla
    return $table;

  }

  // Obtener un listado de los campos primarios de una tabla
  public function getTablePrimaryKey(AmTable $t){
    return $this->newQuery($this->sqlGetTablePrimaryKeys($t))
      ->getCol('name');
  }

  // Obtener un listado de las columnas de una tabla
  public function getNativeTableColumns(AmTable $t){
    return $this->newQuery($this->sqlGetTableColumns($t))
      ->getResult('array');
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableColumns(AmTable $t){
    return $this->newQuery($this->sqlGetTableColumns($t))
      ->getResult('array', array($this, 'sanitize'));
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableUniques(AmTable $t){
    $uniques = $this->newQuery($this->sqlGetTableUniques($t))
      ->getResult('array');

    // Group fields of unique indices for name.
    $realUniques = array();
    foreach ($uniques as $value) {
      $realUniques[$value['name']] = itemOr($value['name'], $realUniques, array());
      $realUniques[$value['name']][] = $value['columnName'];
    }
    return $realUniques;

  }

  // Obtener un listado de las columnas de una tabla
  public function getTableForeignKeys(AmTable $t){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $sourceName = $this->getName();

    // Obtener los ForeignKeys
    $fks = $this->newQuery($this->sqlGetTableForeignKeys($t))->getResult('array');

    foreach($fks as $fk){

      // Dividir el nombre del FK
      $name = explode('.', $fk['name']);

      // Obtener el ultimo elemento
      $name = array_pop($name);

      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          'source' => $sourceName,
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
  public function getTableReferences(AmTable $t){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $sourceName = $this->getName();

    // Obtener las referencias a una tabla
    $fks = $this->newQuery($this->sqlGetTableReferences($t))->getResult('array');

    // Recorrer los FKs
    foreach($fks as $fk){

      // Dividir el nombre del FK
      $name = explode('.', $fk['name']);

      // Obtener el ultimo elemento
      $name = array_shift($name);

      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          'source' => $sourceName,
          'table' => $fk['fromTable'],
          'columns' => array()
        );
      }

      // Agregar la columna a la lista de columnas
      $ret[$name]['columns'][$fk['toColumn']] = $fk['columnName'];

    }

    return $ret;

  }

  // Setea el valor de una variable en el gestor
  public function setServerVar($varName, $value){
    return false !== $this->execute($this->sqlSetServerVar($varName, $value));
  }

  // Crea la BD
  public function create(){
    return false !== $this->execute($this->sqlCreate());
  }

  // Elimina la BD
  public function drop(){
    return false !== $this->execute($this->sqlCreate());
  }

  // Obtener la información de la BD
  public function getInfo(){
    return $this->newQuery($this->sqlGetInfo())->getRow('array');
  }

  // Crear tabla
  public function createTable(AmTable $t){
    return false !== $this->execute($this->sqlCreateTable($t));
  }

  // Crea un tabla en la BD
  public function createTableIfNotExists($model){
    // Si el model existe
    if($this->existsBaseModel($model)){

      // Obtener la instancia de la tabla
      $table = $this->getTable($model);
      // Obtener la instancia de la BD y
      // retornar si se pudo crear o no la tabla en la BD
      if(!$table->exists()){
        // Intentar crear la tabla
        if($table->create())
          return true;

        // Retornar error de MSYL
        return $this->getErrNo() . ': ' . $this->getError();

      }

      // La tabla ya existe en la BD
      return 1;

    }
    return 0;
  }

  // Elimina la Base de datos
  public function dropTable(AmTable $t){
    return false !== $this->execute($this->sqlDropTable($t));
  }

  // Vaciar tabla
  public function truncate(AmTable $t, $ignoreFK = false){
    $sql = '';
    if($ignoreFK === true)
      $sql .= $this->setServerVar('FOREIGN_KEY_CHECKS', 0);
    $ret = $this->execute($this->sqlTruncate($t));
    if($ignoreFK === true)
      $sql .= $this->setServerVar('FOREIGN_KEY_CHECKS', 1);
    return false !== $ret;
  }

  // Indica si la tabla existe
  public function existsTable(AmTable $t){
    return false !== $this->getTableDescription($t->getTableName());
  }

  // Ejecuta una consulta de insercion para los
  public function insertInto($table, $values, array $fields = array()){

    // Obtener la instancia de la tabla
    $table = $this->getTable($table);

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
    $sql = $this->sqlInsertInto($table, $values, $fields);

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

  // Devuelve la diferencia entre dos valores.
  // Si los elementos on array se compara recursivamente
  // Devuelve true si no hay diferencia. De lo contrario
  // devuelve un valores que son diferentes.
  private static function _diff($v1, $v2){
    if(is_array($v1) && is_array($v2)){
      $ret = array();
      $ks = array_unique(array_merge(array_keys($v1), array_keys($v2)));
      foreach($ks as $k){
        $vk1 = isset($v1[$k])?$v1[$k]:null;
        $vk2 = isset($v2[$k])?$v2[$k]:null;
        if(true !== ($diff = diff($vk1, $vk2)))
          $ret[$k] = $diff;
      }
      return count($ret)>0? $ret : true;
    }else if($v1 === $v2){
      return true;
    }
    return array($v1, $v2);

  }

  // Devuelve las diferencias entre el esquema del ORM local
  // y el esquema real de la BD
  public function diff(){

    // Obtener atributos de la BD locales
    $conf = $this->getConf();
    $conf = array(
      'charset' => itemOr('charset', $conf),
      'collage' => itemOr('collage', $conf),
    );

    // Obtener atributos del esquema en la BD
    $info = $this->getInfo();

    // Obtener diferencias.
    $diff = self::_diff($conf, $info);

    $tablesDiff = array();

    // Obtener diferencias entre las tablas
    $tables = array_unique(array_merge(
      $this->getTableNames(),
      $this->newQuery($this->sqlGetTables())
            ->getCol('tableName')
    ));

    foreach($tables as $t)
      if(true !== ($tableDiff = $this->diffLocalAndSchemaTables($t)))
        $tablesDiff[$t] = $tableDiff;

    if(count($tablesDiff)>0)
      if($diff === true)
        $diff = array('tables' => $tablesDiff);
      else
        $diff['tables'] = $tablesDiff;

    print_r($diff);

  }

  // Devuelve la diferencia de la tabla del modelo y la tabla
  // en la base de datos
  public function diffLocalAndSchemaTables($tableName){

    // Obtener configuracion local
    $localConf = $this->getBaseModelConf($tableName);
    $localConf = empty($localConf)? null : $localConf;

    // Obtener descripcion de la tabla desde la BD
    $schemaConf = $this->describeTable($tableName);
    $schemaConf = $schemaConf? $schemaConf->toArray() : null;

    // Obtener los codigo de chequeo para saber si
    // la estructura de la tabla cambio
    $localMd5 = md5(json_encode($localConf));
    $schemaMd5 = md5(json_encode($schemaConf));

    // Si son iguales los codigo entonces no ha cambiado la tabla
    if(md5(json_encode($localConf)) == md5(json_encode($schemaConf)))
      return true;

    // Si no son iguales los codigo de verificacion
    // se devuelve la diferencia entre las tablas
    return diff($localConf, $schemaConf);

  }

  // Converite el objeto en un array
  public function toArray(){

    // Obtener los nombres de las ta blas
    $tablesNames = array();
    $tables = $this->getTablesFromSchema();
    foreach ($tables as $table) {
      $tablesNames[] = $table['tableName'];
    }

    // Obtener la informacion de la BD en los esquemas
    $info = $this->getInfo();

    // Mezclar el Charset y el Collage
    $info['charset'] = ($charset = $this->getCharset())===null? $info['charset'] : $charset;
    $info['collage'] = ($collage = $this->getCollage())===null? $info['collage'] : $collage;

    return array(
      'name' => $this->getName(),
      'prefix' => $this->getPrefix(),
      'driver' => $this->getDriver(),
      'database' => $this->getDatabase(),
      'server' => $this->getServer(),
      'port' => $this->getPort(),
      'user' => $this->getUser(),
      'pass' => $this->getPass(),
      'charset' => $info['charset'],
      'collage' => $info['collage'],
      'tableNames' => $tablesNames,
    );
  }

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Metodo para obtener el puerto por defecto para una conexión
  abstract public function getDefaultPort();

  // Metodo para crear una conexion
  abstract protected function initConnect();

  // Metodo para cerrar una conexión
  abstract public function close();

  // Obtener el número del último error generado en la conexión
  abstract public function getErrNo();

  // Obtener la descripcion del último error generado en la conexión
  abstract public function getError();

  // Devuelve un tipo de datos en el gestor de BD
  abstract public function sanitize(array $column);

  // Obtener el siguiente registro de un resultado
  abstract public function getFetchAssoc($result);

  // Obtener el ID del ultimo registro insertado
  abstract public function getLastInsertedId();

  // Realizar una consulta SQL
  abstract protected function query($sql);

  // Devuelve una cadena con un valor valido en el gesto de BD
  abstract public function realScapeString($value);

  //---------------------------------------------------------------------------
  // Metodo para obtener los SQL a ejecutar
  //---------------------------------------------------------------------------

  // Devuelve un nombre entre comillas simples entendibles por el gesto
  abstract public function getParseName($identifier);

  // Set de Caracteres
  abstract public function sqlCharset();

  // Colecion de caracteres
  abstract public function sqlCollage();

  // Setear un valor a una variable de servidor
  abstract public function sqlSetServerVar($varName, $value);

  abstract public function sqlGetInfo();

  // Devuelve un String con el SQL para crear la base de datos
  abstract public function sqlCreate();

  // SQL para seleccionar la BD
  abstract public function sqlSelectDatabase();

  // SQL para obtener el listado de tablas
  abstract public function sqlGetTables();

  abstract public function sqlCreateTable(AmTable $t);
  abstract public function sqlDropTable(AmTable $t);
  abstract public function sqlTruncate(AmTable $t);

  abstract public function sqlGetTablePrimaryKeys(AmTable $t);
  abstract public function sqlGetTableColumns(AmTable $t);
  abstract public function sqlGetTableUniques(AmTable $t);
  abstract public function sqlGetTableForeignKeys(AmTable $t);
  abstract public function sqlGetTableReferences(AmTable $t);

  abstract public function sqlInsertInto($table, $values, array $fields = array());

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Devuelve la carpeta destino para los orm
  public static function getFolderOrm(){
    return self::$ORM_FOLDER;
  }

}

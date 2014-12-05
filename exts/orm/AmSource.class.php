<?php

/**
 * Abstraccion para las conexiones a las base de datos
 */

abstract class AmSource extends AmObject{

  protected static
    $ORM_FOLDER = "/model";

  // Propiedades
  protected
    $name     = null,     // Nombre clave para la fuente. Se Asumo es unico
    $prefix   = null,     // Prefijo para las clases nacidas de esta fuente
    $driver   = null,     // Driver utilizado en la fuente
    $database = null,     // Nombre de la base de datos para conectarse
    $server   = null,     // Nombre del servidor
    $port     = null,     // Puerto de conexion
    $user     = null,     // Usuario para la conexion
    $pass     = null,     // Password para la conexion
    $charset  = null,     // Codificacion de caracteres
    $collate  = null,     // Colexion de caracteres
    $tables   = array();  // Listado de instancias de tablas 

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

  // Obtener la instancia de una tabla
  public function getTable($offset){
    if(isset($this->tables[$offset]))
      return $this->tables[$offset];
    return $this->tables[$offset] = AmORM::getTable($offset, $this->getName());
  }

  // Nombre de las clases relacionadas a una tabla
  public function getClassNameTableBase($model){  return $this->getClassNameTable($model)."Base"; }
  public function getClassNameTable($model){      return $this->getClassNameModel($model)."Table"; }  
  public function getClassNameModelBase($model){  return $this->getClassNameModel($model)."Base"; }
  public function getClassNameModel($model){      return $this->getPrefix() . Am::camelCase($model, true); }

  // Setear la instancia de una tabla
  public function setTable($offset, AmTable $t){
    $this->tables[$offset] = $t;
    return $this;
  }

  // Función para reconectar
  public function reconnect(){
    $this->disconnect();      // Desconectar
    return $this->connect();  // Volver a conectar
  }

  // Devuelve la cadena de conexión del servidor
  public function getServerString(){
    
    $port = $this->getPort();
    $defPort = $this->getDefaultPort();

    return $this->getServer() . ":" . (!empty($port) ? $port : $defPort);

  }

  // Seleccionar la base de datos
  public function select(){
    return $this->query($this->sqlSelectDatabase());
  }

  // Indica si la BD existe
  public function exists(){
    return $this->select();
  }

  // Ejecutar una consulta SQL desde el ámbito de la BD actual
  public function execute($sql){
    $this->select();
    return $this->query($sql);
  }

  // Crea una instancia de un query
  public function newQuery($from = null, $as = "q"){
    $q = new AmQuery(); // Crear instancia
    $q->setSource($this);  // Asignar fuente
    if(!empty($from)) $q->fromAs($from, $as);  // Asignar el from de la consulta
    return $q;

  }

  // Devuelve un array con el listado de tablas de la BD
  public function getTablesSchema($type = null){
    return $this->newQuery($this->sqlGetTables())
                ->getResult($type);
  }

  // Devuelve un array con el listado de tablas
  public function getTableDescription($table, $type = null){
    return $this->newQuery($this->sqlGetTables())
                ->where("tableName = '$table'")
                ->getRow($type);
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
    $table->source = $this;

    // Crear instancia anonima de la tabla
    $table = new AmTable($table);
    // Buscar la descripcion de sus campos y relaciones
    $table->describeTable();
    
    // Retornar tgabla
    return $table;
      
  }

  // Obtener un listado de los campos primarios de una tabla
  public function getTablePrimaryKey(AmTable $t){
        
    $ret = array(); // Valor de retorno

    // Obtener los campos primarios de la tabla
    $pks = $this->newQuery($this->sqlGetTablePrimaryKeys($t))->getResult();
    
    // Agregar campos al retorn
    foreach($pks as $pk)
      $ret[] = $pk->name;
    
    return $ret;
      
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableColumns(AmTable $t){
    return $this->newQuery($this->sqlGetTableColumns($t))->getResult("array");
  }

  // Obtener un listado de las columnas de una tabla
  public function getTableForeignKeys(AmTable $t){

    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $sourceName = $this->getName();

    // Obtener los ForeignKeys
    $fks = $this->newQuery($this->sqlGetTableForeignKeys($t))->getResult();
        
    foreach($fks as $fk){
      
      // Dividir el nombre del FK
      $name = explode(".", $fk->name);

      // Obtener el ultimo elemento
      $name = array_pop($name);
      
      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          "source" => $sourceName,
          "table" => $fk->toTable,
          "columns" => array()
        );
      }
      
      // Agregar la columna a la lista de columnas
      $ret[$name]["columns"][$fk->columnName] = $fk->toColumn;

    }
    
    return $ret;

  }

  // Obtener el listado de referencias a una tabla
  public function getTableReferences(AmTable $t){
    
    $ret = array(); // Para el retorno

    // Obtener el nombre de la fuente
    $sourceName = $this->getName();

    // Obtener las referencias a una tabla
    $fks = $this->newQuery($this->sqlGetTableReferences($t))->getResult();
    
    // Recorrer los FKs
    foreach($fks as $fk){
      
      // Dividir el nombre del FK
      $name = explode(".", $fk->name);

      // Obtener el ultimo elemento
      $name = array_shift($name);
      
      // Si no existe el elmento en el array se crea
      if(!isset($ret[$name])){
        $ret[$name] = array(
          "source" => $sourceName,
          "table" => $fk->fromTable,
          "columns" => array()
        );
      }
      
      // Agregar la columna a la lista de columnas
      $ret[$name]["columns"][$fk->toColumn] = $fk->columnName;

    }
    
    return $ret;
      
  }

  // Crear tabla
  public function createTable(AmTable $t){
    return $this->getSource()->execute($this->sqlCreateTable($t)) !== false;
  }

  // Elimina la Base de datos
  public function dropTable(AmTable $t){
    return $this->getSource()->execute($this->sqlDropTable($t)) !== false;
  }

  // Vaciar tabla
  public function truncate(AmTable $t){
    return $this->getSource()->execute($this->sqlTruncate($t)) !== false;
  }

  // Indica si la tabla existe
  public function existsTable(AmTable $t){
    return $this->getTableDescription($this->tableName()) !== false;
  }

  // Ejecuta una consulta de insercion para los
  public function insertInto($table, $values, array $fields = array()){
    
    // Obtener el SQL para saber si es valido
    $sql = $this->sqlInsertInto($table, $values, $fields);
    
    // Si el SQL está vacío o si se genera un error en la insercion
    // se devuelve falso
    if(trim($sql) == "" || $this->execute($sql) === false)
      return false;

    // Obtener el ultimo ID insertado
    $id = $this->getLastInsertedId();

    // Se retorna el el último id insertado o true en
    // el caso de que se hayan insertado varios registros
    return $id === 0 ? true : $id;
    
  }

  // Obtener la ruta de la carpeta para las clases del ORM de la BD actual
  public function getFolder(){
    return self::getFolderOrm() . "/" . $this->getName();
  }

  // Obtener la carpeta para un tabla
  public function getFolderModel($model){
    return $this->getFolder() . "/" . Am::underscor($model);
  }

  // Obtener la carpeta de archivos bases para un tabla
  public function getFolderModelBase($model){
    return $this->getFolderModel($model) . "/base";
  }

  // Devuelve la direccion del archivo de configuracion
  public function getPathConf($model){
    return $this->getFolderModelBase($model) . "/". Am::underscor($model) .".conf";
  }

  // Devuelve la dirección de la clase de la tabla Base
  public function getPathClassTableBase($model){
    return $this->getFolderModelBase($model) . "/". $this->getClassNameTableBase($model) .".class";
  }

  // Devuelve la dirección de la clase de la tabla
  public function getPathClassTable($model){
    return $this->getFolderModel($model) . "/". $this->getClassNameTable($model) .".class";
  }

  // Devuelve la dirección de la clase del model Base
  public function getPathClassModelBase($model){
    return $this->getFolderModelBase($model) . "/". $this->getClassNameModelBase($model) .".class";
  }

  // Devuelve la dirección de la clase del model
  public function getPathClassModel($model){
    return $this->getFolderModel($model) . "/". $this->getClassNameModel($model) .".class";
  }

  // Crear carpetas de todas las tablas de la BD
  public function mkdirModel(){
    
    $ret = array(); // Para retorno

    $tables = $this->newQuery($this->sqlGetTables())
                   ->getCol("tableName");

    foreach ($tables as $t){
      // Obtener instancia de la tabla
      $table = $this->describeTable($t);
      // Crear modelo
      $ret[$t] = $table->mkdirModel();
    }

    return $ret;

  }

  // Metodo para crear todos los modelos de la BD
  public function createClassModels(){

    $ret = array(); // Para retorno

    $tables = $this->newQuery($this->sqlGetTables())
                   ->getCol("tableName");

    foreach ($tables as $t){
      // Obtener instancia de la tabla
      $table = $this->describeTable($t);
      // Crear modelo
      $ret[$t] = $table->createClassModels();
    }

    return $ret;

  }

  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////

  // Metodo para obtener el puerto por defecto para una conexión
  abstract public function getDefaultPort();

  // Metodo para crear una conexion
  abstract public function connect();

  // Metodo para cerrar una conexión
  abstract public function close();
  
  // Obtener el número del último error generado en la conexión
  abstract public function getErrNo();
  
  // Obtener la descripcion del último error generado en la conexión
  abstract public function getError();

  // Devuelve un tipo de datos en el gestor de BD
  abstract public function getTypeOf($type);

  // Obtener el siguiente registro de un resultado
  abstract public function getFetchAssoc($result);

  // Obtener el ID del ultimo registro insertado
  abstract public function getLastInsertedId();

  // Realizar una consulta SQL
  abstract protected function query($sql);

  //---------------------------------------------------------------------------
  // Metodo para obtener los SQL a ejecutar
  //---------------------------------------------------------------------------

  // Devuelve un nombre entre comillas simples entendibles por el gesto
  abstract public function getParseName($identifier);

  // Set de Caracteres
  abstract public function sqlCharset();

  // Colecion de caracteres
  abstract public function sqlCollage();

  // Devuelve un String con el SQL para crear la base de datos
  abstract public function sqlCreate();

  // SQL para seleccionar la BD
  abstract public function sqlSelectDatabase();

  // SQL para obtener el listado de tablas
  abstract public function sqlGetTables();
  
  /////////////////////////////////////////////////////////////////////////////
  // Metodos Abstractos que deben ser definidos en las implementaciones
  /////////////////////////////////////////////////////////////////////////////
  
  // Devuelve la carpeta destino para los orm
  public static function getFolderOrm(){
    return getcwd() . self::$ORM_FOLDER;
  }

}

<?php

/**
 * Abstraccion para las conexiones a las base de datos
 */

abstract class AmSource extends AmObject{


  // Propiedades
  protected
    $name = null,     // Nombre clave para la fuente. Se Asumo es unico
    $prefix = null,   // Prefijo para las clases nacidas de esta fuente
    $driver = null,   // Driver utilizado en la fuente
    $database = null, // Nombre de la base de datos para conectarse
    $server = null,   // Nombre del servidor
    $port   = null,   // Puerto de conexion
    $user   = null,   // Usuario para la conexion
    $pass   = null,   // Password para la conexion
    $charset = null,  // Codificacion de caracteres
    $collate = null;  // Colexion de caracteres

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
  public function getTables($type = null){
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
  
}

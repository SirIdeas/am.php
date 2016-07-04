<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para conexión con BD en MySQL
 */
final class MysqlScheme extends AmScheme{

  // Puerto por defecto para la conexion
  const DEFAULT_PORT = 3306;

  // Propiedades del driver
  protected

    /**
     * Identificador de la conexion
     */
    $handler = null,

    /**
     * Equivalencias entre los tipos de datos del DBMS y PHP
     */
    $types = array(
      // Enteros
      'tinyint'    => 'int',
      'smallint'   => 'int',
      'mediumint'  => 'int',
      'int'        => 'int',
      'bigint'     => 'int',

      // Flotantes
      'decimal'    => 'float',
      'float'      => 'float',
      'double'     => 'float',

      // Fechas y tiempo
      'date'       => 'date',
      'datetime'   => 'datetime',  // 8 bytes
      'timestamp'  => 'timestamp', // 4 bytes
      'time'       => 'time',

      // Cadenas de caracteres
      'char'       => 'char',
      'varchar'    => 'varchar',
      'tinytext'   => 'text',
      'text'       => 'text',
      'mediumtext' => 'text',
      'longtext'   => 'text',

      // Otros
      'year'       => 'year',
      'bit'        => 'bit',

    ),

    /**
     * Hash de tamaños de subtipos
     */
    $lenSubTypes = array(
      // Tamaños de enteros
      'int' => array(
        'tinyint'     => 1,
        'smallint'    => 2,
        'mediumint'   => 3,
        'int'         => 4,
        'bigint'      => 8,
      ),

      // Tamaños de punto flotante
      'float' => array(
        'decimal' => 0,
        'float'   => 1,
        'double'  => 2,
      ),

      // Tamaños de textos
      'text' => array(
        'tinytext'    => 1,
        'text'        => 2,
        'mediumtext'  => 3,
        'longtext'    => 4,
      ),
    ),

    /**
     * Tipos por defecto de cada subtipo
     */
    $defaultsBytes = array(
      'int'   => 'int',
      'float' => 'float',
      'text'  => 'text'
    );

  /**
   * Devuelve un nombre entre comillas simples entendibles por el gestor
   * @param  string $name Nombre a parchar.
   * @return string       Nombre parchado.
   */
  public function getParseName($name){

    // Verificar si ya no está parchado.
    if(preg_match('/[`\\.]/', $name))
      return $name;

    return "`{$name}`";

  }

  /**
   * Devuelve el puerto por defecto.
   * @return int Puerto por defecto.
   */
  public function getDefaultPort(){

    return self::DEFAULT_PORT;

  }

  /**
   * Crear una conexión
   * @return Resource Recurso (manejdador del recurso) para manejar la conexión.
   */
  protected function start(){

    return $this->handler = mysqli_connect(
      $this->getServer(),
      $this->getUser(),
      $this->getPass(),
      $this->getDatabase(),
      $this->getPort()
    );
    
  }

  /**
   * Realiza la conexión.
   * @return Resource Handle de conexión establecida o FALSE si falló.
   */
  public function connect(){

    $ret = parent::connect();

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
   * Cierra la conexión.
   * @return int Resultado de la operación.
   */
  public function close() {

    if($this->handler)
      return mysqli_close($this->handler);
    return false;

  }

  /**
   * Obtener el número del último error generado en la conexión.
   * @return int Nro del error.
   */
  public function getErrNo(){
    
    if($this->handler)
      return mysqli_errno($this->handler);
    return null;

  }

  /**
   * Obtener la descripción del último error generado en la conexión.
   * @return string Mensaje del error.
   */
  public function getError(){
    
    if($this->handler)
      return mysqli_error($this->handler);
    return null;

  }

  /**
   * Devuelve una cadena con los caracteres especiales escapados para usar en
   * una sentencia SQL.
   * @param  string $value Cadena a escapar.
   * @return string        Cadena escapada.
   */
  public function realScapeString($value){

    if($this->handler)
      return mysqli_real_escape_string($this->handler, $value);
    return $value;

  }

  /**
   * Ejecuta un query SQL.
   * @param  string $sql SQL del query a ejecutar.
   * @return bool        Resultado de la operación.
   */
  protected function query($sql){
    
    if($this->handler)
      return mysqli_query($this->handler, $sql);
    return false;

  }

  /**
   * Obtener el siguiente registro de un resultado de un query.
   * @param  int  $result Puntero del resultado.
   * @return hash         Hash de valores.
   */
  public function getFetchAssoc($result){

    if($this->handler)
      return mysqli_fetch_assoc($result);
    return false;

  }

  /**
   * Obtener el ID del ultimo registro insertado.
   * @return int ID del último registro insertado.
   */
  public function getLastInsertedId(){

    if($this->handler)
      return mysqli_insert_id($this->handler);
    return false;

  }

  /**
   * Ingresa el nombre de un objeto de la BD dentro de las comillas
   * correspondientes a los nombres.
   * @param  string $name Nombre que se desea entre comillas.
   * @return string       Nombre entre comillas.
   */
  public function nameWrapper($name){

    return "`{$name}`";

  }

  /**
   * Devuelve una cadena de caracteres entre comillas.
   * @param  string $string Cadena que se desea entre comillas.
   * @return string         Cadena entre comillas.
   */
  public function stringWrapper($string){

    if($string === null)
      return 'NULL';
    if($string === true)
      return 'TRUE';
    if($string === false)
      return 'FALSE';

    return "'{$string}'";

  }

  /**
   * SQL del query para obtener la informacion de la BD.
   * @return string SQL correspondiente.
   */
  public function queryGetInfo(){

    $query = $this
      ->q('information_schema.SCHEMATA')
      ->where("SCHEMA_NAME='{$this->getDatabase()}'")
      ->selectAs('DEFAULT_CHARACTER_SET_NAME', 'charset')
      ->selectAS('DEFAULT_COLLATION_NAME', 'collation');

    return $query;

  }

  /**
   * SQL del query para obtener el listado de tablas de la BD.
   * @return string SQL correspondiente.
   */
  public function queryGetTables(){

    $query = $this
      ->q('information_schema.TABLES', 't')
      ->join(
        'information_schema.COLLATION_CHARACTER_SET_APPLICABILITY', 'c',
        't.TABLE_COLLATION = c.COLLATION_NAME')
      ->where(
        "t.TABLE_SCHEMA='{$this->getDatabase()}'",
        'and', 't.TABLE_TYPE=\'BASE TABLE\'')
      ->selectAs('t.TABLE_NAME', 'tableName')
      ->selectAS('t.ENGINE', 'engine')
      ->selectAS('t.TABLE_COLLATION', 'collation')
      ->selectAS('c.CHARACTER_SET_NAME', 'charset');

    return $query;

  }
  
  /**
   *  SQL del query para obtener el listado de columnas de una tabla.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function queryGetTableColumns($tableName){

    $query = $this
      ->q('information_schema.COLUMNS')
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        'and', "TABLE_NAME='{$tableName}'")

      // Basic data
      ->selectAs('COLUMN_NAME', 'name')
      ->selectAs('DATA_TYPE', 'type')
      ->selectAs('COLUMN_TYPE', 'columnType')
      ->selectAs('COLUMN_DEFAULT', 'defaultValue')
      ->selectAs('COLUMN_KEY=\'PRI\'', 'pk')
      ->selectAs('IS_NULLABLE=\'YES\'', 'allowNull')

      // Strings
      ->selectAs('CHARACTER_MAXIMUM_LENGTH', 'len')
      ->selectAs('COLLATION_NAME', 'collation')
      ->selectAs('CHARACTER_SET_NAME', 'charset')

      // Numerics
      ->selectAs('NUMERIC_PRECISION', 'precision')
      ->selectAs('NUMERIC_SCALE', 'scale')
      ->orderBy('ORDINAL_POSITION')

      // Others
      ->selectAs('EXTRA', 'extra');

    return $query;

  }

  /**
   * SQL del query para obtener el lista campos únicos.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function queryGetTableUniques($tableName){

    $query = $this
      ->q('information_schema.KEY_COLUMN_USAGE', 'k')
      ->join(
        'information_schema.COLUMNS', 'c',
        'k.TABLE_SCHEMA = c.TABLE_SCHEMA AND '.
        'k.TABLE_NAME   = c.TABLE_NAME AND '.
        'k.COLUMN_NAME  = c.COLUMN_NAME')
      ->where(
        "k.TABLE_SCHEMA='{$this->getDatabase()}'",
        'and', "k.TABLE_NAME='{$tableName}'",
        'and', 'k.CONSTRAINT_NAME<>\'PRIMARY\'',
        'and', 'k.REFERENCED_TABLE_NAME IS NULL',
        'and', 'c.COLUMN_KEY <> \'PRI\'')
      ->selectAs('k.CONSTRAINT_NAME', 'name')
      ->selectAs('k.COLUMN_NAME', 'columnName')
      ->orderBy('k.CONSTRAINT_NAME', 'k.ORDINAL_POSITION');

    return $query;

  }

  /**
   * SQL del query para obtener el lista de foreign keys de una tabla.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function queryGetTableForeignKeys($tableName){

    $query = $this
      ->q('information_schema.KEY_COLUMN_USAGE')
      ->selectAs('CONSTRAINT_NAME', 'name')
      ->selectAs('COLUMN_NAME', 'columnName')
      ->selectAs('REFERENCED_TABLE_NAME', 'toTable')
      ->selectAs('REFERENCED_COLUMN_NAME', 'toColumn')
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        'and', "TABLE_NAME='{$tableName}'",
        'and', 'NOT REFERENCED_TABLE_NAME IS NULL',
        'and', 'CONSTRAINT_NAME<>\'PRIMARY\'')
      ->orderBy('CONSTRAINT_NAME', 'ORDINAL_POSITION');

    return $query;

  }

  /**
   * SQL del query para obtener el lista de de referencias a una tabla
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function queryGetTableReferences($tableName){

    $query = $this
      ->q('information_schema.KEY_COLUMN_USAGE')
      ->selectAs('CONSTRAINT_NAME', 'name')
      ->selectAs('COLUMN_NAME', 'columnName')
      ->selectAs('TABLE_NAME', 'fromTable')
      ->selectAs('REFERENCED_COLUMN_NAME', 'toColumn')
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        'and', "REFERENCED_TABLE_NAME='{$tableName}'",
        'and', 'NOT REFERENCED_TABLE_NAME IS NULL',
        'and', 'CONSTRAINT_NAME<>\'PRIMARY\'')
      ->orderBy('CONSTRAINT_NAME', 'ORDINAL_POSITION');

    return $query;

  }

  public function _sqlWrapperSql($sql){

    return "({$sql})";

  }

  public function _sqlElementWithAlias($element, $alias){

    return "{$element} AS {$alias}";

  }

  public function _sqlOrderBy($field, $dir){

    return "{$field} {$dir}";
  
  }

  public function _sqlJoin($type, $table, $alias, $on){

    $type = $this->_sqlJoinType($type);
    $on = $this->_sqlOn($on);
    $table = $this->_sqlElementWithAlias($table, $alias);

    return "{$type} {$table}{$on}";

  }

  public function _sqlJoinType($type){

    return (!empty($type)? "{$type} " : '') . 'JOIN';

  }

  public function _sqlOn($on){

    return !empty($on)? " ON {$on} " : '';

  }

  public function _sqlQueryGroup(array $queries){

    return implode(';', $queries);

  }

  /**
   * SQL para setear un valor a una variable de servidor.
   * @param  string $varName Nombre de la variable.
   * @param  string $value   Valor a asignar a la variable.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return string          SQL correspondiente.
   */
  public function _sqlSetServerVar($varName, $value, $scope = ''){

    $scope = $scope === true? 'GLOBAL ' : $scope === false? 'SESSION ' : '';

    return "SET {$scope}{$varName}={$value}";

  }

  /**
   * Set de caracteres en un query SQL.
   * @param  string $charset Set de caracteres.
   * @return string          SQL correspondiente.
   */
  public function _sqlCharset($charset = null){

    return empty($charset) ? '' : " CHARACTER SET {$charset}";

  }

  /**
   * Coleccion de caracteres en un query SQL.
   * @param  string $collatin Colección de caracteres.
   * @return string           SQL correspondiente.
   */
  public function _sqlCollation($collation = null){

    return empty($collation) ? '' : " COLLATE {$collation}";

  }

  /**
   * SQL Para crear la BD.
   * @param  boolean $ifNotExists Si se agrega la cláusula IF NOT EXISTS.
   * @return string               SQL correspondiente.
   */
  public function _sqlCreate($database, $charset, $collation, $ifNotExists = true){

    $ifNotExists = $ifNotExists? 'IF NOT EXISTS ' : '';

    return "CREATE DATABASE {$ifNotExists}{$database}{$charset}{$collation}";

  }

  /**
   * SQL para seleccionar la BD.
   * @return string SQL correspondiente.
   */
  public function _sqlSelectDatabase($database){

    return "USE {$database}";
    
  }

  /**
   * SQL para eliminar la BD.
   * @param  boolean $ifExists Si se agrega la cláusula IF EXISTS.
   * @return string            SQL correspondiente.
   */
  public function _sqlDrop($database, $ifExists = true){

    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP DATABASE {$ifExists}{$database}";

  }

  /**
   * Obtener el SQL para eliminar una tabla.
   * @param  AmTable/string $table     Instancia o nombre de la tabla.
   * @param  bool           $orReplace Si se agrega la cláusula OR REPLACE.
   * @return string                    SQL correspondiente.
   */
  public function _sqlCreateView($queryName, $sql, $orReplace = true){

    $orReplace = $orReplace? 'OR REPLACE ' : '';

    return "CREATE {$replace}VIEW {$queryName} AS {$sql}";

  }

  /**
   * Obtener el SQL para eliminar una vista.
   * @param  AmQuery/string $q        Instancia o SQL del query.
   * @param  bool           $ifExists Si se debe agregar la cláusula IF EXISTS.
   * @return string                   SQL correspondiente.
   */
  public function _sqlDropView($queryName, $ifExists = true){

    // SQLSQLSQL
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    // SQLSQLSQL
    return "DROP VIEW {$ifExists}{$queryName}";

  }

}
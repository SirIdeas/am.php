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
      $value = mysqli_real_escape_string($this->handler, $value);
    
    // Si no tiene valor asignar NULL
    return isset($value)? "'{$value}'" : 'NULL';

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
   * SQL del query para obtener la informacion de la BD.
   * @return string SQL correspondiente.
   */
  public function queryGetInfo(){

    $query = $this
      ->q('information_schema.SCHEMATA', 's')
      ->where("SCHEMA_NAME='{$this->getDatabase()}'")
      ->selectAs('s.DEFAULT_CHARACTER_SET_NAME', 'charset')
      ->selectAS('s.DEFAULT_COLLATION_NAME', 'collation');

    return $query;

  }

  /**
   * SQL del query para obtener el listado de tablas de la BD.
   * @return string SQL correspondiente.
   */
  public function queryGetTables(){

    $query = $this
      ->q('information_schema.TABLES', 't')
      ->innerJoin(
        'information_schema.COLLATION_CHARACTER_SET_APPLICABILITY',
        't.TABLE_COLLATION = c.COLLATION_NAME', 'c')
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
      ->innerJoin(
        'information_schema.COLUMNS',
        'k.TABLE_SCHEMA = c.TABLE_SCHEMA AND '.
        'k.TABLE_NAME   = c.TABLE_NAME AND '.
        'k.COLUMN_NAME  = c.COLUMN_NAME',
        'c')
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

}
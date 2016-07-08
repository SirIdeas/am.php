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

    if($this->handler && is_string($value))
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
  public function valueWrapper($string){

    if(is_int($string))
      return $string;
    if(is_float($string))
      return $string;
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
      ->where('SCHEMA_NAME', $this->getDatabase())
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
      ->where('t.TABLE_SCHEMA', $this->getDatabase())
      ->andWhere('t.TABLE_TYPE', 'BASE TABLE')
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
      ->where('TABLE_SCHEMA', $this->getDatabase())
      ->andWhere('TABLE_NAME', $tableName)

      // Basic data
      ->selectAs('COLUMN_NAME', 'name')
      ->selectAs('DATA_TYPE', 'type')
      ->selectAs('COLUMN_TYPE', 'columnType')
      ->selectAs('COLUMN_DEFAULT', 'defaultValue')
      ->selectAs(Am::raw("COLUMN_KEY='PRI'"), 'pk')
      ->selectAs(Am::raw("IS_NULLABLE='YES'"), 'allowNull')

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
      ->where('k.TABLE_SCHEMA', $this->getDatabase())
      ->andWhere('k.TABLE_NAME', $tableName)
      ->andWhere('k.CONSTRAINT_NAME', '<>', 'PRIMARY')
      ->andWhereIs('k.REFERENCED_TABLE_NAME', null)
      ->andWhere('c.COLUMN_KEY', '<>', 'PRI')
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
      ->andWhere('TABLE_SCHEMA', $this->getDatabase())
      ->andWhere('TABLE_NAME', $tableName)
      ->andWhereIsNot('REFERENCED_TABLE_NAME', null)
      ->andWhere('CONSTRAINT_NAME', '<>', 'PRIMARY')
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
      ->andWhere('TABLE_SCHEMA', $this->getDatabase())
      ->andWhere('REFERENCED_TABLE_NAME', $tableName)
      ->andWhere('CONSTRAINT_NAME', 'PRIMARY')
      ->andWhereIsNot('REFERENCED_TABLE_NAME', null)
      ->orderBy('CONSTRAINT_NAME', 'ORDINAL_POSITION');

    return $query;

  }

  public function _sqlSqlWrapper($sql){

    return "({$sql})";

  }

  public function _sqlElementWithAlias($element, $alias){

    return "{$element} AS {$alias}";

  }

  public function _sqlDistinct(){

    return 'DISTINCT ';

  }

  public function _sqlSelectAll(){

    return '*';

  }

  public function _sqlSelectGroup(array $selects){

    return implode(',', $selects);

  }

  public function _sqlSelect($selects, $distinct){

    return "SELECT {$distinct}{$selects}";

  }

  public function _sqlFromGroup(array $froms){
    
    return implode(', ', $froms);

  }

  public function _sqlFrom($froms){

    return "FROM {$froms}";

  }

  public function _sqlOrderByItem($field, $dir){

    return "{$field} {$dir}";
  
  }

  public function _sqlOrderByGroup(array $orders){

    return implode(', ', $orders);

  }

  public function _sqlOrderBy($orders){

    return "ORDER BY {$orders}";
  
  }

  public function _sqlGroupByGroup(array $groups){

    return implode(', ', $groups);
    
  }

  public function _sqlGroupBy($groups){

    return "GROUP BY {$groups}";
  
  }

  public function _sqlJoinGroup($joins){

    return implode(' ', $joins);

  }

  public function _sqlJoin($type, $table, $on){

    return "{$type} JOIN {$table}{$on}";

  }

  public function _sqlOn($on){

    return " ON {$on} ";

  }

  public function _sqlNot(){

    return 'NOT ';
    
  }

  public function _sqlArray(array $arr){

    return '('.implode(',', $arr).')';

  }

  public function _sqlWhereItem($not, $field, $operator, $value){

    return "{$not}{$field} {$operator} {$value}";

  }

  public function _sqlWhereGroup(array $wheres){

    return implode(' ', $wheres);
    
  }

  public function _sqlWhereWrapper($wheres){

    return "({$wheres})";
    
  }

  public function _sqlWhere($wheres){

    return "WHERE {$wheres}";
    
  }

  public function _sqlLimit($limit){

    return "LIMIT {$limit}";

  }

  public function _sqlOffset($offset){
    
    return "OFFSET {$offset}";

  }

  public function _sqlQueryGroup(array $queries){

    return implode('; ', $queries);

  }

  public function _sqlScope($scope){

    return $scope === true? 'GLOBAL ' : $scope === false? 'SESSION ' : '';

  }

  /**
   * SQL para setear un valor a una variable de servidor.
   * @param  string $varName Nombre de la variable.
   * @param  string $value   Valor a asignar a la variable.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return string          SQL correspondiente.
   */
  public function _sqlSetServerVar($varName, $value, $scope){

    return "SET {$scope}{$varName}={$value}";

  }

  /**
   * Set de caracteres en un query SQL.
   * @param  string $charset Set de caracteres.
   * @return string          SQL correspondiente.
   */
  public function _sqlEngine($engine){

    return " ENGINE={$engine}";

  }

  /**
   * Set de caracteres en un query SQL.
   * @param  string $charset Set de caracteres.
   * @return string          SQL correspondiente.
   */
  public function _sqlCharset($charset){

    return " CHARACTER SET {$charset}";

  }

  /**
   * Coleccion de caracteres en un query SQL.
   * @param  string $collatin Colección de caracteres.
   * @return string           SQL correspondiente.
   */
  public function _sqlCollation($collation){

    return " COLLATE {$collation}";

  }

  public function _sqlIfNotExists(){

    return 'IF NOT EXISTS ';

  }

  /**
   * SQL Para crear la BD.
   * @param  boolean $ifNotExists Si se agrega la cláusula IF NOT EXISTS.
   * @return string               SQL correspondiente.
   */
  public function _sqlCreate($database, $charset, $collation, $ifNotExists){

    return "CREATE DATABASE {$ifNotExists}{$database}{$charset}{$collation}";

  }

  /**
   * SQL para seleccionar la BD.
   * @return string SQL correspondiente.
   */
  public function _sqlSelectDatabase($database){

    return "USE {$database}";
    
  }

  public function _sqlIfExists(){

    return 'IF EXISTS ';

  }

  /**
   * SQL para eliminar la BD.
   * @param  boolean $ifExists Si se agrega la cláusula IF EXISTS.
   * @return string            SQL correspondiente.
   */
  public function _sqlDrop($database, $ifExists){

    return "DROP DATABASE {$ifExists}{$database}";

  }

  public function _sqlPrimaryKeyGroup(array $pks){
    
    return implode(', ', $pks);

  }

  public function _sqlPrimaryKey($pks){

    return "PRIMARY KEY ({$pks})";

  }

  public function _sqlFieldsGroup(array $fields){
    
    return implode(', ', $fields);

  }

  public function _sqlDefaultValue($value){

    return "DEFAULT {$value}";

  }

  public function _sqlUnsigned(){

    return 'unsigned';

  }

  public function _sqlZerofill(){

    return 'zerofill';

  }

  public function _sqlNotNull(){

    return 'NOT NULL';

  }

  public function _sqlCurrentTimestamp(){

    return 'NOT NULL';

  }

  public function _sqlAutoIncrement(){

    return 'AUTO_INCREMENT';

  }

  public function _sqlField($name, $type, $unsigned, $zerofill, $charset, $collation, $notNull, $autoIncrement, $default, $extra){
    return "{$name}{$type}{$unsigned}{$zerofill}{$charset}{$collation}{$notNull}{$autoIncrement}{$default}{$extra}";
  }

  public function _sqlCreateTable($tableName, $fields, $engine, $charset, $collation, $ifNotExists){

    return "CREATE TABLE {$ifNotExists}{$tableName}($fields){$engine}{$charset}{$collation}";

  }

  public function _sqlDropTable($tableName, $ifExists){

    return "DROP TABLE {$ifExists}{$tableName}";

  }

  public function _sqlTruncate($tableName){

    return "TRUNCATE {$tableName}";

  }

  public function _sqlOrReplace(){

    return 'OR REPLACE ';

  }

  /**
   * Obtener el SQL para eliminar una tabla.
   * @param  AmTable/string $table     Instancia o nombre de la tabla.
   * @param  bool           $orReplace Si se agrega la cláusula OR REPLACE.
   * @return string                    SQL correspondiente.
   */
  public function _sqlCreateView($queryName, $sql, $orReplace){

    return "CREATE {$orReplace}VIEW {$queryName} AS {$sql}";

  }

  /**
   * Obtener el SQL para eliminar una vista.
   * @param  AmQuery/string $q        Instancia o SQL del query.
   * @param  bool           $ifExists Si se debe agregar la cláusula IF EXISTS.
   * @return string                   SQL correspondiente.
   */
  public function _sqlDropView($queryName, $ifExists){

    return "DROP VIEW {$ifExists}{$queryName}";

  }

  public function _sqlQuerySelect($select, $from, $joins, $where, $groups, $orders, $limit, $offSet){

    return "{$select}{$from}{$joins}{$where}{$groups}{$orders}{$limit}{$offSet}";

  }

  public function _sqlSetGroup(array $sets){

    return implode(', ', $sets);

  }

  public function _sqlSetItem($field, $value){

    return "{$field} = {$value}";

  }

  public function _sqlSet($sets){

    return "SET {$sets}";

  }

  public function _sqlQueryUpdate($tableName, $joins, $sets, $where){

    return "UPDATE {$tableName} {$joins}{$sets}{$where}";

  }

  public function _sqlQueryDelete($tableName, $where){

    return "DELETE FROM {$tableName} {$where}";

  }

}
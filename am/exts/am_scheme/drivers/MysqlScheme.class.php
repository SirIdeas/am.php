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
class MysqlScheme extends AmScheme{

  // Puerto por defecto para la conexion
  const DEFAULT_PORT = 3306;

  // Equivalencias entre los tipos de datos del DBMS y PHP
  protected static

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

    // Tamaños de enteros
    $intBytes = array(
      'tinyint'     => 1,
      'smallint'    => 2,
      'mediumint'   => 3,
      'int'         => 4,
      'bigint'      => 8,
    ),

    // Tamaños de punto flotante
    $floatBytes = array(
      'decimal' => 0,
      'float'   => 1,
      'double'  => 2,
    ),

    // Tamaños de textos
    $textBytes = array(
      'tinytext'    => 1,
      'text'        => 2,
      'mediumtext'  => 3,
      'longtext'    => 4,
    ),

    // Tipos por defectos
    $defaultsByte = array(
      'int'   => 'int',
      'float' => 'float',
      'text'  => 'text'
    );

  // Propiedades del driver
  protected

    /**
     * Identificador de la conexion
     */
    $handler = null;

  /**
   * Devuelve un nombre entre comillas simples entendibles por el gestor
   * @param  string $name Nombre a parchar.
   * @return string       Nombre parchado.
   */
  public function getParseName($name){

    // Verificar si ya no está parchado.
    if(preg_match('/[`\\.]/', $name))
      return $name;

    return "`'{$name}'`";

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

    return $this->handler = @mysql_connect(
      $this->getServerString(),
      $this->getUser(),
      $this->getPass(),
      true
    );
    
  }

  /**
   * Cierra la conexión.
   * @return int Resultado de la operación.
   */
  public function close() {
    
    return @mysql_close($this->handler);

  }

  /**
   * Obtener el número del último error generado en la conexión.
   * @return int Nro del error.
   */
  public function getErrNo(){
    
    return @mysql_errno($this->handler);

  }

  /**
   * Obtener la descripción del último error generado en la conexión.
   * @return string Mensaje del error.
   */
  public function getError(){
    
    return @mysql_error($this->handler);

  }

  /**
   * Devuelve una cadena con los caracteres especiales escapados para usar en
   * una sentencia SQL.
   * @param  string $value Cadena a escapar.
   * @return string        Cadena escapada.
   */
  public function realScapeString($value){

    $value = @mysql_real_escape_string($value);
    // Si no tiene valor asignar NULL
    return isset($value)? "'{$value}'" : 'NULL';

  }

  /**
   * Ejecuta un query SQL.
   * @param  string $sql SQL del query a ejecutar.
   * @return bool        Resultado de la operación.
   */
  protected function query($sql){
    
    return @mysql_query($sql, $this->handler);

  }

  /**
   * Obtener el siguiente registro de un resultado de un query.
   * @param  int  $result Puntero del resultado.
   * @return hash         Hash de valores.
   */
  public function getFetchAssoc($result){

    return @mysql_fetch_assoc($result);

  }

  /**
   * Obtener el ID del ultimo registro insertado.
   * @return int ID del último registro insertado.
   */
  public function getLastInsertedId(){

    return @mysql_insert_id();

  }

  /**
   * Prepara una columna para ser creada en una tabla de la BD.
   * @param  array  $column Datos de una columna.
   * @return string
   */
  public function sanitize(array $column){
    // Si no se encuentra el tipo se retorna el tipo recibido

    $nativeType = $column['type'];
    $column['type'] = itemOr($column['type'], self::$types, $column['type']);

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
                self::$intBytes,
                self::$floatBytes,
                self::$textBytes
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
   * Devuelve el SQL de un query SELECT
   * @param  AmQuery $q Query.
   * @return string     SQL del query.
   */
  public function sqlSelectQuery(AmQuery $q){

    return !empty($q->sql) ? $q->sql :
      trim(implode(' ', array(
      trim($this->sqlSelect($q)),
      trim($this->sqlFrom($q)),
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
  public function sqlSelect(AmQuery $q){

    $selectsOri = $q->getSelects();  // Obtener argmuentos en la clausula SELECT
    $distinct = $q->getDistinct();
    $selects = array();  // Lista de retorno

    // Recorrer argumentos del SELECT
    foreach($selectsOri as $alias => $field){

      // Si es una consulta se incierra entre parentesis
      if($field instanceof AmQuery)
        $field = "({$field->sql()})";

      // Agregar parametro AS
      $selects[] = isNameValid($alias) ? "{$field} AS '{$alias}'" :
        (string)$field;

    }

    // Unir campos
    $selects = trim(implode(', ', $selects));

    // Si no se seleccionó ningun campo entonces se tomaran todos
    $selects = empty($selects) ? '*' : $selects;

    // Agregar SELECT
    return 'SELECT '.trim(($distinct ? 'DISTINCT ' : '').$selects);

  }

  /**
   * Obtener el SQL para la clausula FROM.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlFrom(AmQuery $q){

    $fromsOri = $q->getFroms();   // Listado de argumentos de la clausula FROM
    $froms = array();             // Listado de retorno

    // Recorrer lista del FROM
    foreach($fromsOri as $alias => $from){

      if($from instanceof AmTable || isNameValid($from)){
        // Si es una tabla se concatena el nombre de la BD y el de la tabla como strin
        $from = $this->getParseObjectDatabaseName($from);
      }elseif($from instanceof AmQuery){
        // Si es una consulta se encierra en parentesis
        $from = "({$from->sql()})";
      }elseif(false !== (preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*)\.([a-zA-Z_][a-zA-Z0-9_]*)$/', $from, $matches)!= 0)){
        // Dividir por el punto
        $from = $this->getParseName($matches[1]).'.'.$this->getParseName($matches[2]);
      }elseif(is_string($from)){
        $from = $from = "({$from})";
      }

      // Agregar parametro AS
      $froms[] = isNameValid($alias) ? "{$from} AS {$alias}" : $from;

    }

    // Unir argumentos procesados
    $froms = trim(implode(', ', $froms));

    // Agregar FROM
    return (empty($froms) ? '' : "FROM {$froms}");

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
   * Obtener el SQL para la clausula JOIN.
   * @param  AmQuery $q Query.
   * @return string     SQL correspondiente.
   */
  public function sqlJoins(AmQuery $q){

    // Resultado
    $joins = $q->getJoins();
    $joinsResult = array();

    // Recorrer cada join
    foreach($joins as $join){

        // Declarar posiciones del array como variables
        // Define $on, $as y $table
        extract($join);

        // Eliminar espacios iniciales y finales
        $on = trim($on);
        $as = trim($as);

        // Si los parametros quedan vacios
        if(!empty($on)) $on = " ON {$on}";
        if(!empty($as)) $as = " AS {$as}";

        if($table instanceof AmQuery){
          // Si es una consulta insertar SQL dentro de parenteris
          $table = "({$table->sql()})";
        }elseif($table instanceof AmTable){
          // Si es una tabla obtener el nombre
          $table = $table->getTableName();
        }

        // Agrgar parte de join
        $joinsResult[] = " $type JOIN {$table}{$as}{$on}";

        // Liberar variables
        unset($table, $as, $on);

    }

    // Unir todas las partes
    return trim(implode(' ', $joinsResult));

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
        $sets[] = "{$set['field']} = " . $this->realScapeString($value);
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
   * Devuelve un tipo de datos para el DBMS dependiendo de un tipo de datos
   * de lenguaje y la longuitud del mismo.
   * @param  string $type Tipo de datos en el lenguaje.
   * @param  int    $len  Longuitud del tipo de datos.
   * @return string       Tipo de datos en el DBMS.
   */
  private function getTypeByLen($type, $len){
    $seudoType = $type.'Bytes';
    $types = self::$$seudoType;
    $defaultType = itemOr($type, self::$defaultsByte);
    $index = array_search($len, $types);
    return $index? $index: $defaultType;
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
      if(!$len)
        $len = itemOr($type, self::$defaultsLen);
      
      if($len)
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
    $engine = empty($table->getEngine()) ? '' : "ENGINE={$table->getEngine()} ";
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
              $this->sqlSetServerVar('FOREIGN_KEY_CHECKS', 0);

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
   * SQL para setear un valor a una variable de servidor.
   * @param  string $varName Nombre de la variable.
   * @param  string $value   Valor a asignar a la variable.
   * @param  bool   $scope   Si se agrega la cláusula GLOBAL o SESSION.
   * @return string          SQL correspondiente.
   */
  public function sqlSetServerVar($varName, $value, $scope = false){

    $scope = $scope === true? 'GLOBAL ' : $scope === false? 'SESSION ' : '';
    return "SET {$scope}{$varName}={$value}";

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
   * SQL del query para obtener la informacion de la BD.
   * @return string SQL correspondiente.
   */
  public function sqlGetInfo(){

    $sql = $this
      ->q('information_schema.SCHEMATA')
      ->where("SCHEMA_NAME='{$this->getDatabase()}'")
      ->selectAs('s.DEFAULT_CHARACTER_SET_NAME', 'charset')
      ->selectAS('s.DEFAULT_COLLATION_NAME', 'collation')
      ->sql();

    return $sql;

  }

  /**
   * SQL del query para obtener el listado de tablas de la BD.
   * @return string SQL correspondiente.
   */
  public function sqlGetTables(){

    $sql = $this
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
      ->selectAS('c.CHARACTER_SET_NAME', 'charset')
      ->sql();

    return $sql;

  }
  
  /**
   *  SQL del query para obtener el listado de columnas de una tabla.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function sqlGetTableColumns($tableName){

    $sql = $this
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
      ->selectAs('EXTRA', 'extra')

      ->sql();

    return $sql;

  }

  /**
   * SQL del query para obtener el lista campos únicos.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function sqlGetTableUniques($tableName){

    $sql = $this
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
      ->orderBy('k.CONSTRAINT_NAME', 'k.ORDINAL_POSITION')
      ->sql();

    return $sql;

  }

  /**
   * SQL del query para obtener el lista de foreign keys de una tabla.
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function sqlGetTableForeignKeys($tableName){

    $sql = $this
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
      ->orderBy('CONSTRAINT_NAME', 'ORDINAL_POSITION')
      ->sql();

    return $sql;

  }

  /**
   * SQL del query para obtener el lista de de referencias a una tabla
   * @param  string $tableName Nombre de la tabla.
   * @return string            SQL correspondiente.
   */
  public function sqlGetTableReferences($tableName){

    $sql = $this
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
      ->orderBy('CONSTRAINT_NAME', 'ORDINAL_POSITION')
      ->sql();

    return $sql;

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
            $value = $this->realScapeString($value);
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
      $collation = $collation->sqlSelectQuery();

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

}
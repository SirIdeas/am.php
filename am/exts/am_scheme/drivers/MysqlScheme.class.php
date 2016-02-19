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

  // Equivalencias entre los tipos de datos del Gesto de BD y el Lenguaje de programacion
  protected static

    $types = array(
      // Enteros
      "tinyint"    => "integer",
      "smallint"   => "integer",
      "mediumint"  => "integer",
      "int"        => "integer",
      "bigint"     => "integer",

      // Flotantes
      "decimal"    => "float",
      "float"      => "float",
      "double"     => "float",

      "bit"        => "bit",

      // Fechas
      "date"       => "date",
      "datetime"   => "datetime",
      "timestamp"  => "timestamp",
      "time"       => "time",
      "year"       => "year",

      // Cadenas de caracteres
      "char"       => "char",
      "varchar"    => "varchar",
      "tinytext"   => "text",
      "text"       => "text",
      "mediumtext" => "text",
      "longtext"   => "text",

    ),

    $integerBytes = array(
      "tinyint"     => 1,
      "smallint"    => 2,
      "mediumint"   => 3,
      "int"         => 4,
      "bigint"      => 8,
    ),

    $floatBytes = array(
      "decimal" => 0,
      "float"   => 1,
      "double"  => 2,
    ),

    $textBytes = array(
      "tinytext"    => 1,
      "text"        => 2,
      "mediumtext"  => 3,
      "longtext"    => 4,
    ),

    $defaultsByte = array(
      'integer' => 'int',
      'float'   => 'float',
      'text'    => 'text'
    );

  // Propiedades propias para el Driver
  protected
    $handler = null; // Identificador de la conexion

  // Devuelve un nombre entre comillas simples entendibles por el gesto
  public function getParseName($name){

    if(preg_match("/[`\\.]/", $name))
      return $name;

    return "`$name`";

  }

  // Obtener el puerto por defecto
  public function getDefaultPort(){

    return self::DEFAULT_PORT;

  }

  // Crear una conexión
  protected function start(){
    // error_reporting(E_STRICT);
    return $this->handler = @mysql_connect(
      $this->getServerString(),
      $this->getUser(),
      $this->getPass(),
      true
    );
    
  }

  // Cerrar una conexion
  public function close() {
    
    return @mysql_close($this->handler);

  }

  // Obtener el número del último error generado en la conexión
  public function getErrNo(){
    
    return @mysql_errno($this->handler);

  }

  // Obtener la descripcion del último error generado en la conexión
  public function getError(){
    
    return @mysql_error($this->handler);

  }

  // Devuelve una cadena con un valor valido en el gesto de BD
  public function realScapeString($value){

    $value = @mysql_real_escape_string($value);
    // Si no tiene valor asignar NULL
    return isset($value)? "'$value'" : "NULL";

  }

  // Realizar una consulta SQL
  protected function query($sql){
    
    return @mysql_query($sql, $this->handler);

  }

  // Obtener el siguiente registro de un resultado
  public function getFetchAssoc($result){

    return @mysql_fetch_assoc($result);

  }

  // Obtener el ID del ultimo registro insertado
  public function getLastInsertedId(){

    return @mysql_insert_id();

  }

  // Devuelve el tipo de datos del gestor para un tipo de datos en el lenguaje
  public function sanitize(array $column){
    // Si no se encuentra el tipo se retorna el tipo recibido

    $nativeType = $column["type"];
    $column["type"] = itemOr($column["type"], self::$types, $column["type"]);

    // Parse bool values
    $column["pk"] = parseBool($column["pk"]);
    $column["allowNull"]  = parseBool($column["allowNull"]);

    // Get len of field
    // if is a bit, char or varchar take len
    if(in_array($nativeType, array("char", "varchar")))
      $column["len"] = itemOr("len", $column);

    elseif($nativeType == "bit")
      $column["len"] = itemOr("precision", $column);

    // else look len into bytes used for native byte
    else
      $column["len"]  = itemOr($nativeType, array_merge(
                self::$integerBytes,
                self::$floatBytes,
                self::$textBytes
              ));

    if(in_array($column["type"], array("integer", "float"))){

      $column["unsigned"] = preg_match("/unsigned/",
        $column["columnType"]) != 0;

      $column["zerofill"] = preg_match("/unsigned zerofill/",
        $column["columnType"]) != 0;

      $column["autoIncrement"] = preg_match("/auto_increment/",
        $column["extra"]) != 0;

    }

    // Unset scale is not is a float
    if($column["type"] != "float")
      unset($column["precision"], $column["scale"]);

    else
      $column["scale"] = itemOr("scale", $column, 0);

    // Unset columnType an prescicion
    unset($column["columnType"]);

    // Drop auto_increment of extra param
    $column["extra"] = trim(str_replace("auto_increment", "", $column["extra"]));

    // Eliminar campos vacios
    foreach(array(
      "defaultValue",
      "collage",
      "charset",
      "len",
      "extra"
    ) as $attr)
      if(!isset($column[$attr]) || trim($column[$attr])==="")
        unset($column[$attr]);

    return $column;
    
  }

  // Consulta select
  public function sqlSelectQuery(AmQuery $q){

    return !empty($q->sql) ? $q->sql :
      trim(implode(" ", array(
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

  protected function sqlInsertValues($values){

    if(empty($values))
      return '';

    if(is_array($values) && count($values)>0){

      // Preparar registros para crear SQL
      foreach($values as $i => $v)
        // Unir todos los valores con una c
        $values[$i] = "(" . implode(",", $v) . ")";

      // Unir todos los registros
      $values = implode(",", $values);

      // Obtener Str para los valores
      $values = "VALUES $values";

    }

    return $values;

  }

  protected function sqlInsertFields(array $fields){

    // Unir campos
    if(!empty($fields))
      return '(' . implode(',', $fields) . ')';

    return '';

  }

  // Obtener el SQL para una consulta de inserción
  public function sqlInsert($values, $model, array $fields = array()){

    $q = $this->prepareInsert(
      $values, $model, $fields
    );

    if(empty($q['values']))
      return '';

    // Generar SQL
    return "INSERT INTO {$q['table']}{$q['fields']} {$q['values']}";

  }

  // Obtener el SQL para una consulta UPDATE
  public function sqlUpdateQuery(AmQuery $q){

    return implode(" ", array(
      "UPDATE",
      trim($this->getParseObjectDatabaseName($q)),
      trim($this->sqlJoins($q)),
      trim($this->sqlSets($q)),
      trim($this->sqlWhere($q))
    ));

  }

  // Obtener el SQL para una consulta DELETE
  public function sqlDeleteQuery(AmQuery $q){

    // Agregar DELETE FROM
    return implode(" ", array(
      "DELETE FROM",
      trim($this->getParseObjectDatabaseName($q)),
      trim($this->sqlWhere($q))
    ));

  }

  // Obtener el SQL para la clausula SELECT
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
      $selects[] = isNameValid($alias) ? "$field AS '$alias'" : (string)$field;

    }

    // Unir campos
    $selects = trim(implode(', ', $selects));

    // Si no se seleccionó ningun campo entonces se tomaran todos
    $selects = empty($selects) ? '*' : $selects;

    // Agregar SELECT
    return 'SELECT '.trim(($distinct ? 'DISTINCT ' : '').$selects);

  }

  // Obtener el SQL para la clausula FROM
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
      }elseif(false !== (preg_match("/^([a-zA-Z_][a-zA-Z0-9_]*)\.([a-zA-Z_][a-zA-Z0-9_]*)$/", $from, $matches)!= 0)){
        // Dividir por el punto
        $from = $this->getParseName($matches[1]).".".$this->getParseName($matches[2]);
      }elseif(is_string($from)){
        $from = $from = "($from)";
      }

      // Agregar parametro AS
      $froms[] = isNameValid($alias) ? "$from AS $alias" : $from;

    }

    // Unir argumentos procesados
    $froms = trim(implode(', ', $froms));

    // Agregar FROM
    return (empty($froms) ? '' : "FROM {$froms}");

  }

  // Obtener SQL para la clausula WHERE de una consulta
  public function sqlWhere(AmQuery $q){

    $where = trim($this->parseWhere($q->getWheres()));

    return (empty($where) ? '' : "WHERE {$where}");

  }

  // Obtener el SQL para la clausula JOIN de una consulta
  public function sqlJoins(AmQuery $q){

    // Resultado
    $joinsOri = $q->getJoins();
    $joinsResult = array();

    //Recorrer cada tipo de join
    foreach($joinsOri as $type => $joins){
      // Recorrer cada join
      foreach($joins as $join){

          // Declarar posiciones del array como variables
          // Define $on, $as y $table
          extract($join);

          // Eliminar espacios iniciales y finales
          $on = trim($on);
          $as = trim($as);

          // Si los parametros quedan vacios
          if(!empty($on)) $on = " ON $on";
          if(!empty($as)) $as = " AS $as";

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
    }

    // Unir todas las partes
    return trim(implode(' ', $joinsResult));

  }

  // Obtener el SQL de una clasula ORDER BY
  public function sqlOrders(AmQuery $q, $with = true){

    $ordersOri = $q->getOrders(); // Obtener orders agregados
    $orders = array();  // Orders para retorno

    // Recorrer lista de campos para ordenar
    foreach($ordersOri as $order => $dir){
      $orders[] = "$order $dir";
    }

    // Unir resultado
    $orders = trim(implode(', ', $orders));

    // Agregar ORDER BY
    return (empty($orders) ? '' : "ORDER BY {$orders}");

  }

  // Obtener el SQL de una clasula GROUP BY
  public function sqlGroups(AmQuery $q){

    // Unir grupos
    $groups = trim(implode(', ', $q->getGroups()));

    // Agregar GROUP BY
    return (empty($groups) ? '' : "GROUP BY {$groups}");

  }

  // Obtener SQL para la clausula LIMIT
  public function sqlLimit(AmQuery $q){

    // Obtener limite
    $limit = trim($q->getLimit());

    // Agregar LIMIT
    return (empty($limit) ? '' : "LIMIT {$limit}");

  }

  // Obtener SQL para la clausula OFFSET
  public function sqlOffset(AmQuery $q, $with = true){

    // Obtener punto de partida
    $offset = $q->getOffset();
    $limit = $q->getLimit();

    // Agregar OFFSET
    return (!isset($offset) || !isset($limit) ? '' : "OFFSET {$offset}");

  }

  // Obtener el SQL para la clausula SET de una consulta UPDATE
  public function sqlSets(AmQuery $q){

    // Obtener sets
    $setsOri = $q->getSets();
    $sets = array(); // Lista para retorno

    // Recorrer los sets
    foreach($setsOri as $set){

      $value = $set['value'];

      // Acrear asignacion
      if($value === null){
        $sets[] = "{$set["field"]} = NULL";
      }elseif($set["const"] === true){
        $sets[] = "{$set["field"]} = " . $this->realScapeString($value);
      }elseif($set["const"] === false){
        $sets[] = "{$set["field"]} = $value";
      }

    }

    // Unir resultado
    $sets = implode(',', $sets);

    // Agregar SET
    return "SET {$sets}";

  }

  // Set de Caracteres
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

  // Coleccion de caracteres
  public function sqlCollage($collage = null){

    // Si no recibió argumentos obtener el college de la BD
    if(!count(func_get_args())>0)
      $collage = $this->getCollage();

    // El el argumento esta vacío retornar cadena vacia
    if(empty($collage))
      return "";

    $collage = empty($collage) ? '' : " COLLATE {$collage}";

    return $collage;

  }

  private function getTypeByLen($type, $len){
    $seudoType = $type.'Bytes';
    $types = self::$$seudoType;
    $defaultType = itemOr($type, self::$defaultsByte);
    $index = array_search($len, $types);
    return $index? $index: $defaultType;
  }

  // Obtener el SQL para un campo de una tabla al momento de crear la tabla
  public function sqlField(AmField $field){

    // Preparar las propiedades
    $name = $this->getParseName($field->getName());
    $type = $field->getType();
    $len = $field->getLen();
    $charset = $this->sqlCharset($field->getCharset());
    $collage = $this->sqlCollage($field->getCollage());
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

    if($field->isUnsigned())      $attrs[] = "unsigned";
    if($field->isZerofill())      $attrs[] = "zerofill";
    if(!empty($charset))          $attrs[] = $charset;
    if(!empty($collage))          $attrs[] = $collage;
    if(!$field->allowNull())      $attrs[] = "NOT NULL";
    if($field->isAutoIncrement()) $attrs[] = "AUTO_INCREMENT";
    if(isset($default))           $attrs[] = "DEFAULT {$default}";
    if(!empty($extra))            $attrs[] = $extra;

    $attrs = implode(" ", $attrs);

    // Get type
    // As integer
    if($type === "integer")
      $type = self::getTypeByLen($type, $len);

    // As text
    elseif($type === "text")
      $type = self::getTypeByLen($type, $len);

    // as float precision
    elseif($type == "float"){

      $type = self::getTypeByLen($type, $len);

      $precision = $field->getPrecision();
      $scale = $field->getScale();

      if($precision && $precision)
        $type = "$type($precision, $scale)";

    // with var len
    }elseif(in_array($type, array("bit", "char", "varchar"))){
      if(!$len)
        $len = itemOr($type, self::$defaultsLen);
      
      if($len)
        $type = "$type($len)";

    }

    return "$name $type $attrs";

  }

  // Obtener el SQL para crear una tabla
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
    $engine = empty($table->getEngine()) ? "" : "ENGINE={$table->getEngine()} ";
    $charset = $this->sqlCharset($table->getCharset());
    $collage = $this->sqlCollage($table->getCollage());

    // Agregar los primaris key al final de los campos
    $fields[] = empty($pks) ? "" : "PRIMARY KEY (" . implode(", ", $pks). ")";

    // Unir los campos
    $fields = "\n".implode(",\n", $fields);

    $ifNotExists = $ifNotExists? "IF NOT EXISTS " : '';

    // Preparar el SQL final
    return "CREATE TABLE {$ifNotExists}{$tableName}($fields){$engine}{$charset}{$collage};";

  }

  // Obtener el SQL para una consulta TRUNCATE: Vaciar una tabla
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

  // Obtener el SQL para eliminar una tabla
  public function sqlDropTable($table, $ifExists = true){

    // Obtener nombre de la tabla
    $tableName = $this->getParseObjectDatabaseName($table);
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP TABLE {$ifExists}{$tableName}";

  }

  public function sqlCreateView(AmQuery $q, $orReplace = true){

    $queryName = $this->getParseObjectDatabaseName($q->getName());
    $orReplace = $orReplace? 'OR REPLACE ' : '';

    return "CREATE {$replace}VIEW {$queryName} AS {$q->sql()}";

  }

  public function sqlDropView($q, $ifExists = true){
    
    if($q instanceof AmQuery)
      $q = $q->getName();

    $queryName = $this->getParseObjectDatabaseName($q);
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP VIEW {$ifExists}{$queryName}";

  }


  // Setear un valor a una variable de servidor
  public function sqlSetServerVar($varName, $value){

    return "set {$varName}={$value}";

  }

  // SQL para crear la BD
  public function sqlCreate($ifNotExists = true){

    $database = $this->getParseDatabaseName();
    $charset = $this->sqlCharset();
    $collage = $this->sqlCollage();
    $ifNotExists = $ifNotExists? 'IF NOT EXISTS ' : '';
    $sql = "CREATE DATABASE {$ifNotExists}{$database}{$charset}{$collage}";
    return $sql;

  }

  // SQL para seleccionar la BD
  public function sqlSelectDatabase(){

    $database = $this->getParseDatabaseName();
    return "USE {$database}";
    
  }

  // SQL para eliminar la BD
  public function sqlDrop($ifExists = true){

    $database = $this->getParseDatabaseName();
    $ifExists = $ifExists? 'IF EXISTS ' : '';

    return "DROP DATABASE {$ifExists}{$database}";

  }

  // SQL par aobtener la informacion de la BD
  public function sqlGetInfo(){

    $sql = $this
      ->q("information_schema.SCHEMATA")
      ->where("SCHEMA_NAME='{$this->getDatabase()}'")
      ->selectAs("s.DEFAULT_CHARACTER_SET_NAME", "charset")
      ->selectAS("s.DEFAULT_COLLATION_NAME", "collage")
      ->sql();

    return $sql;

  }

  // SQL para obtener el listado de tablas
  public function sqlGetTables(){

    $sql = $this
      ->q("information_schema.TABLES", "t")
      ->innerJoin(
        "information_schema.COLLATION_CHARACTER_SET_APPLICABILITY",
        "t.TABLE_COLLATION = c.COLLATION_NAME", "c")
      ->where(
        "t.TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "t.TABLE_TYPE='BASE TABLE'")
      ->selectAs("t.TABLE_NAME", "tableName")
      ->selectAS("t.ENGINE", "engine")
      ->selectAS("t.TABLE_COLLATION", "collage")
      ->selectAS("c.CHARACTER_SET_NAME ", "charset")
      ->sql();

    return $sql;

  }
  
  // SQL para obtener el listado de columnas de una tabla
  public function sqlGetTableColumns($tableName){

    $sql = $this
      ->q("information_schema.COLUMNS")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "TABLE_NAME='{$tableName}'")

      // Basic data
      ->selectAs("COLUMN_NAME", "name")
      ->selectAs("DATA_TYPE", "type")
      ->selectAs("COLUMN_TYPE", "columnType")
      ->selectAs("COLUMN_DEFAULT", "defaultValue")
      ->selectAs("COLUMN_KEY='PRI'", "pk")
      ->selectAs("IS_NULLABLE='YES'", "allowNull")

      // Strings
      ->selectAs("CHARACTER_MAXIMUM_LENGTH", "len")
      ->selectAs("COLLATION_NAME", "collage")
      ->selectAs("CHARACTER_SET_NAME", "charset")

      // Numerics
      ->selectAs("NUMERIC_PRECISION", "precision")
      ->selectAs("NUMERIC_SCALE", "scale")
      ->orderBy("ORDINAL_POSITION")

      // Others
      ->selectAs("EXTRA", "extra")

      ->sql();

    return $sql;

  }

  // SQL para obtener el lista campos unicos
  public function sqlGetTableUniques($tableName){

    $sql = $this
      ->q("information_schema.KEY_COLUMN_USAGE", "k")
      ->innerJoin(
        "information_schema.COLUMNS",
        "k.TABLE_SCHEMA = c.TABLE_SCHEMA AND ".
        "k.TABLE_NAME   = c.TABLE_NAME AND ".
        "k.COLUMN_NAME  = c.COLUMN_NAME",
        "c")
      ->where(
        "k.TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "k.TABLE_NAME='{$tableName}'",
        "and", "k.CONSTRAINT_NAME<>'PRIMARY'",
        "and", "k.REFERENCED_TABLE_NAME IS NULL",
        "and", "c.COLUMN_KEY <> 'PRI'")
      ->selectAs("k.CONSTRAINT_NAME", "name")
      ->selectAs("k.COLUMN_NAME", "columnName")
      ->orderBy("k.CONSTRAINT_NAME", "k.ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // SQL para obtener el lista de foreign keys de una tabla
  public function sqlGetTableForeignKeys($tableName){

    $sql = $this
      ->q("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("REFERENCED_TABLE_NAME", "toTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "TABLE_NAME='{$tableName}'",
        "and", "NOT REFERENCED_TABLE_NAME IS NULL",
        "and", "CONSTRAINT_NAME<>'PRIMARY'")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // SQL para obtener el lista de de referencias a una tabla
  public function sqlGetTableReferences($tableName){

    $sql = $this
      ->q("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("TABLE_NAME", "fromTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "REFERENCED_TABLE_NAME='{$tableName}'",
        "and", "NOT REFERENCED_TABLE_NAME IS NULL",
        "and", "CONSTRAINT_NAME<>'PRIMARY'")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // Obtener el SQL para una condicion IN
  public function in($field, $collection){

    // Si es un array se debe preparar la condició
    if(is_array($collection)){

        // Filtrar elementos repetidos
        $collection = array_filter($collection);

        // Si no esta vacía la colecion
        if(!empty($collection)){

          // Agregar cadenas dentro de los comillas simple
          foreach ($collection as $i => $value){
            $value = $this->realScapeString($value);
            $collection[$i] = is_numeric($value) ? $value : "\'{$value}\'";
          }

          // Unir colecion por comas
          $collection = implode($collection, ",");

        }else{
          // Si es una colecion vacía
          $collection = null;
        }

    }elseif($collection instanceof AmQuery){

      // Si es una consulta entonces se obtiene el SQL
      $collection = $collection->sqlSelectQuery();

    }

    // Agregar el comando IN
    return isset($collection) ? "$field IN($collection)" : "false";

  }

  // Helper para obtener el SQL de la clausula WHERE
  private function parseWhere($condition, $prefix = null, $isIn = false){

    if($isIn){

      // Es una condicion IN
      $condition = $this->in($condition[0], $condition[1]);

    }elseif(is_array($condition)){

      $str = "";
      $lastUnion = "";

      // Recorrer condiciones
      foreach($condition as $c){

        // Obtener siguiente condicion
        $next = $this->parseWhere($c["condition"], $c["prefix"], $c["isIn"]);

        // Es la primera condicion
        if(empty($str)){
          $str = $next;
        }else{

          // Si el operador de union es igual al anterior o no hay una anterior
          if($c["union"] == $lastUnion || empty($lastUnion)){
            $str = "$str {$c["union"]} $next";
          }else{
            // Cuando cambia el operador de union se debe agregar la condicion anterior
            // entre parentesis
            $str = "($str) {$c["union"]} $next";
          }

          // guardar para la siguiente condicion
          $lastUnion = $c["union"];

        }

      }

      // Agregar parentesis a la condicion
      $condition = empty($str) ? "" : "($str)";

    }

    // Eliminar espacios al principio y al final
    $condition = trim($condition);

    // Agregar el prefix (NOT) si existe
    return empty($condition) ? "" : trim($prefix." ".$condition);

  }

}
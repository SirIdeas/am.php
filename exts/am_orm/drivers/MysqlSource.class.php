<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/
 
/**
 * Fuente de datos para MySQL
 */

class MysqlSource extends AmSource{

  // Puerto por defecto para la conexion
  const DEFAULT_PORT = 3306;

  // Equivalencias entre los tipos de datos del Gesto de BD y el Lenguaje de programacion
  protected static

    $TYPES = array(
      // Enteros
      "tinyint"    => "integer",
      "smallint"   => "integer",
      "mediumint"  => "integer",
      "int"        => "integer",
      "bigint"     => "integer",

      // Flotantes
      "decimal"    => "decimal",
      "float"      => "decimal",
      "double"     => "decimal",

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

    $INTEGER_BYTES = array(
      "tinyint"     => 1,
      "smallint"    => 2,
      "mediumint"   => 3,
      "int"         => 4,
      "bigint"      => 8,
    ),

    $DECIMAL_BYTES = array(
      "decimal" => 0,
      "float"   => 1,
      "double"  => 2,
    ),

    $TEXT_BYTES = array(
      "tinytext"    => 1,
      "text"        => 2,
      "mediumtext"  => 3,
      "longtext"    => 4,
    );

  // Propiedades propias para el Driver
  protected
    $handle = null; // Identificador de la conexion

  // Obtener el puerto por defecto
  public function getDefaultPort(){
    return self::DEFAULT_PORT;
  }

  // Crear una conexión
  protected function initConnect(){
    return $this->handle = mysql_connect(
      $this->getServerString(),
      $this->getUser(),
      $this->getPass(),
      true
    );
  }

  // Cerrar una conexion
  public function close() {
    if($this->handle)
      return mysql_close($this->handle);
    return false;
  }

  // Obtener el número del último error generado en la conexión
  public function getErrNo(){
    if($this->handle)
      return mysql_errno($this->handle);
    return false;
  }

  // Obtener la descripcion del último error generado en la conexión
  public function getError(){
    if($this->handle)
      return mysql_error($this->handle);
    return false;
  }

  // Obtener el siguiente registro de un resultado
  public function getFetchAssoc($result){
    return mysql_fetch_assoc($result);
  }

  // Obtener el ID del ultimo registro insertado
  public function getLastInsertedId(){
    return mysql_insert_id();
  }

  // Realizar una consulta SQL
  protected function query($sql){

    if($this->handle){
      return mysql_query($sql, $this->handle);
    }
    return false;
  }

  // Devuelve un nombre entre comillas simples entendibles por el gesto
  public function getParseName($identifier){
    if(preg_match("/[`\\.]/", $identifier))
      return $identifier;
    return "`$identifier`";
  }

  // Set de Caracteres
  public function sqlCharset($charset = null){

    // Si no recibió argumentos obtener el charset de la BD
    if(!count(func_get_args())>0)
      $charset = $this->getCharset();

    // El el argumento esta vacío retornar cadena vacia
    if(empty($charset))
      return "";

    $charset = empty($charset) ? "" : " CHARACTER SET {$charset}";

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

    $collage = empty($collage) ? "" : " COLLATE {$collage}";

    return $collage;

  }

  // Setear un valor a una variable de servidor
  public function sqlSetServerVar($varName, $value){
    return "set {$varName}={$value}";
  }

  // SQL para crear la BD
  public function sqlCreate(){
    $database = $this->getParseNameDatabase();
    $charset = $this->sqlCharset();
    $collage = $this->sqlCollage();
    $sql = "CREATE DATABASE IF NOT EXISTS {$database}{$charset}{$collage}";
    return $sql;
  }

  // SQL para eliminar la BD
  public function sqlDrop(){
    $database = $this->getParseNameDatabase();
    $sql = "DROP DATABASE {$database}";
    return $sql;
  }

  // SQL para seleccionar la BD
  public function sqlSelectDatabase(){
    $database = $this->getParseNameDatabase();
    $sql = "USE {$database}";
    return $sql;
  }

  //OSQL par aobtener la informacion de la BD
  public function sqlGetInfo(){

    $sql = $this
      ->newQuery("information_schema.SCHEMATA", "s")
      ->where("SCHEMA_NAME='{$this->getDatabase()}'")
      ->selectAs("s.DEFAULT_CHARACTER_SET_NAME", "charset")
      ->selectAS("s.DEFAULT_COLLATION_NAME", "collage")
      ->sql();

    return $sql;

  }

  // SQL para obtener el listado de tablas
  public function sqlGetTables(){

    $sql = $this
      ->newQuery("information_schema.TABLES", "t")
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

  // SQL par obtener los primary keys de una tabla
  public function sqlGetTablePrimaryKeys(AmTable $t){

    $sql = $this
      ->newQuery("information_schema.COLUMNS")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "TABLE_NAME='{$t->getTableName()}'")
      ->selectAs("COLUMN_NAME", "name")
      ->orderBy("ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // SQL para obtener el listado de columnas de una tabla
  public function sqlGetTableColumns(AmTable $t){

    $sql = $this
      ->newQuery("information_schema.COLUMNS")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "TABLE_NAME='{$t->getTableName()}'")

      // Basic data
      ->selectAs("COLUMN_NAME", "name")
      ->selectAs("DATA_TYPE", "type")
      ->selectAs("COLUMN_TYPE", "columnType")
      ->selectAs("COLUMN_DEFAULT", "defaultValue")
      ->selectAs("COLUMN_KEY='PRI'", "primaryKey")
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
  public function sqlGetTableUniques(AmTable $t){

    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE", "k")
      ->innerJoin(
        "information_schema.COLUMNS",
        "k.TABLE_SCHEMA = c.TABLE_SCHEMA AND ".
        "k.TABLE_NAME   = c.TABLE_NAME AND ".
        "k.COLUMN_NAME  = c.COLUMN_NAME",
        "c")
      ->where(
        "k.TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "k.TABLE_NAME='{$t->getTableName()}'",
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
  public function sqlGetTableForeignKeys(AmTable $t){

    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("REFERENCED_TABLE_NAME", "toTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "TABLE_NAME='{$t->getTableName()}'",
        "and", "NOT REFERENCED_TABLE_NAME IS NULL",
        "and", "CONSTRAINT_NAME<>'PRIMARY'")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // SQL para obtener el lista de de referencias a una tabla
  public function sqlGetTableReferences(AmTable $t){

    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("TABLE_NAME", "fromTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where(
        "TABLE_SCHEMA='{$this->getDatabase()}'",
        "and", "REFERENCED_TABLE_NAME='{$t->getTableName()}'",
        "and", "NOT REFERENCED_TABLE_NAME IS NULL",
        "and", "CONSTRAINT_NAME<>'PRIMARY'")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();

    return $sql;

  }

  // Obtener el SQL para la clausula SELECT
  public function sqlSelect(AmQuery $q, $with = true){

    $selectsOri = $q->getSelects();  // Obtener argmuentos en la clausula SELECT
    $distinct = $q->getDistinct();
    $selects = array();  // Lista de retorno

    // Recorrer argumentos del SELECT
    foreach($selectsOri as $as => $field){

      // Si es una consulta se incierra entre parentesis
      if($field instanceof AmQuery)
        $field = "({$field->sql()})";

      // Agregar parametro AS
      $selects[] = AmORM::isNameValid($as) ? "$field AS '$as'" : (string)$field;

    }

    // Unir campos
    $selects = implode(", ", $selects);

    // Si no se seleccionó ningun campo entonces se tomaran todos
    $selects = (empty($selects) ? "*" : $selects);

    // Agregar SELECT
    return trim(($with ? "SELECT ".($distinct ? "DISTINCT " : "") : "").$selects);

  }

  // Obtener el SQL para la clausula FROM
  public function sqlFrom(AmQuery $q, $with = true){

    $fromsOri = $q->getFroms();  // Listado de argumentos de la clausula FROM
    $froms = array();   // Listado de retorno

    // Recorrer lista del FROM
    foreach($fromsOri as $as => $from){

      if($from instanceof AmQuery){
        // Si es una consulta se encierra en parentesis
        $from = "({$from->sql()})";
      }elseif($from instanceof AmTable){
        // Si es una tabla se concatena el nombre de la BD y el de la tabla
        $from = $this->getParseNameTable($from->getTableName());
      }elseif(AmORM::isNameValid($from)){
        // Si es una tabla se concatena el nombre de la BD y el de la tabla como strin
        $from = $this->getParseNameTable($from);
      }elseif(false !== (preg_match("/^([a-zA-Z_][a-zA-Z0-9_]*)\.([a-zA-Z_][a-zA-Z0-9_]*)$/", $from, $matches)!= 0)){
        // Dividir por el punto
        $from = $this->getParseName($matches[1]).".".$this->getParseName($matches[2]);
      }elseif(is_string($from)){
        $from = $from = "($from)";
      }

      // Agregar parametro AS
      $froms[] = AmORM::isNameValid($as) ? "$from AS $as" : $from;

    }

    // Unir argumentos procesados
    $froms = implode(", ", $froms);

    // Agregar FROM
    return trim(empty($froms) ? "" : (($with ? "FROM " : "").$froms));

  }

  // Obtener el SQL para una condicion IN
  public static function in($field, $collection){

    // Si es un array se debe preparar la condició
    if(is_array($collection)){

        // Filtrar elementos repetidos
        $collection = array_filter($collection);

        // Si no esta vacía la colecion
        if(!empty($collection)){

          // Agregar cadenas dentro de los comillas simple
          $func = create_function('$c', 'return is_numeric($c) ? $c : "\'$c\'";');
          $collection = array_map($func, array_values($collection));

          // Unir colecion por comas
          $collection = implode($collection, ",");

        }else{
          // Si es una colecion vacía
          $collection = null;
        }

    }elseif($collection instanceof AmQuery){

      // Si es una consulta entonces se obtiene el SQL
      $collection = $collection->sql();

    }

    // Agregar el comando IN
    return isset($collection) ? "$field IN($collection)" : "false";

  }

  // Helper para obtener el SQL de la clausula WHERE
  protected function parseWhere($condition, $prefix = null, $isIn = false){

    if($isIn){

      // Es una condicion IN
      $condition = self::in($condition[0], $condition[1]);

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

  // Obtener SQL para la clausula WHERE de una consulta
  public function sqlWhere(AmQuery $q, $with = true){
    $where = $this->parseWhere($q->getWheres());
    return trim(empty($where) ? "" : (($with ? "WHERE " : "").$where));
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
          $joinsResult[] = " $type JOIN $table$as$on";

          // Liberar variables
          unset($table, $as, $on);

      }
    }

    // Unir todas las partes
    return trim(implode(" ", $joinsResult));

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
    $orders = implode(", ", $orders);

    // Agregar ORDER BY
    return trim(empty($orders) ? "" : (($with ? "ORDER BY " : "").$orders));

  }

  // Obtener el SQL de una clasula GROUP BY
  public function sqlGroups(AmQuery $q, $with = true){

    // Unir grupos
    $groups = implode(", ", $q->getGroups());

    // Agregar GROUP BY
    return trim(empty($groups) ? "" : (($with ? "GROUP BY " : "").$groups));

  }

  // Obtener SQL para la clausula LIMIT
  public function sqlLimit(AmQuery $q, $with = true){

    // Obtener limite
    $limit = $q->getLimit();

    // Agregar LIMIT
    return trim(!isset($limit) ? "" : (($with ? "LIMIT " : "").$limit));

  }

  // Obtener SQL para la clausula OFFSET
  public function sqlOffset(AmQuery $q, $with = true){

    // Obtener punto de partida
    $offset = $q->getOffset();
    $limit = $q->getLimit();

    // Agregar OFFSET
    return trim(!isset($offset) || !isset($limit) ? "" : (($with ? "OFFSET " : "").$offset));

  }

  // Obtener el SQL para la clausula SET de una consulta UPDATE
  public function sqlSets(AmQuery $q, $with = true){

    // Obtener sets
    $setsOri = $q->getSets();
    $sets = array(); // Lista para retorno

    // Recorrer los sets
    foreach($setsOri as $set){

      $value = $set["value"];

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
    $sets = implode(",", $sets);

    // Agregar SET
    return ($with? "SET " : "") . $sets;

  }

  // Obtener el SQL para una consulta UPDATE
  public function sqlUpdate(AmQuery $q){

    return implode(" ", array(
      "UPDATE",
      trim($q->sqlFrom(false)),
      trim($q->sqlJoins()),
      trim($q->sqlSets()),
      trim($q->sqlWhere())
    ));

  }

  // Obtener el SQL para una consulta DELETE
  public function sqlDelete(AmQuery $q){

    // Obtener el nombre de la tabla
    $tableName = $this->getParseNameTable($q->getTable());

    // Agregar DELETE FROM
    return implode(" ", array(
      "DELETE FROM",
      trim($tableName),
      trim($q->sqlWhere())
    ));

  }

  // Obtener el SQL para una consulta de insercon
  public function sqlInsertInto($table, $values, array $fields = array()){

    // Si es una consulta
    if($values instanceof AmQuery){

      // Los valores a insertar son el SQL de la consulta
      $strValues = $values->sql();

    // Si los valores es un array con al menos un registro
    }elseif(is_array($values) && count($values)>0){

      // Preparar registros para crear SQL
      foreach($values as $i => $v)
        // Unir todos los valores con una c
        $values[$i] = "(" . implode(",", $v) . ")";

      // Unir todos los registros
      $values = implode(",", $values);

      // Obtener Str para los valores
      $strValues = "VALUES $values";

    }

    // Si el Str de valores no está vacío
    if(!empty($strValues)){

      // Obtener nombre de la tabla
      $tableName = $this->getParseNameTable($table);

      // Obtener el listado de campos
      foreach ($fields as $key => $field)
        $fields[$key] = $this->getParseName($field);

      // Unir campos
      if(empty($fields))
        $fields = '';

      else
        $fields = '(' . implode(',', $fields) . ')';
      

      // Generar SQL
      return "INSERT INTO $tableName{$fields} $strValues";

    }

    // Consulta invalida
    return "";

  }

  // Devuelve una cadena con un valor valido en el gesto de BD
  public function realScapeString($value){
    $value = mysql_real_escape_string($value);
    // Si no tiene valor asignar NULL
    return isset($value)? "'$value'" : "NULL";
  }

  // Obtener el SQL de la consulta
  public function sql(AmQuery $q){

    return !empty($q->sql) ? $q->sql :
      trim(implode(" ", array(
      trim($q->sqlSelect(true)),
      trim($q->sqlFrom(true)),
      trim($q->sqlJoins()),
      trim($q->sqlWhere(true)),
      trim($q->sqlGroups(true)),
      trim($q->sqlOrders(true)),
      trim($q->sqlLimit(true)),
      trim($q->sqlOffSet(true))
    )));

  }

  // Devuelve el tipo de datos del gestor para un tipo de datos en el lenguaje
  public function sanitize(array $column){
    // Si no se encuentra el tipo se retorna el tipo recibido

    $nativeType = $column["type"];
    $column["type"] = itemOr($column["type"], self::$TYPES, $column["type"]);

    // Parse bool values
    $column["primaryKey"] = parseBool($column["primaryKey"]);
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
                self::$INTEGER_BYTES,
                self::$DECIMAL_BYTES,
                self::$TEXT_BYTES
              ));

    if(in_array($column["type"], array("integer", "decimal"))){
      $column["unsigned"]       = preg_match("/unsigned/", $column["columnType"]) != 0;
      $column["zerofill"]       = preg_match("/unsigned zerofill/", $column["columnType"]) != 0;
      $column["autoIncrement"]  = preg_match("/auto_increment/", $column["extra"]) != 0;
    }

    // Unset scale is not is a decimal
    if($column["type"] != "decimal")
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

      if(in_array($type, array("text", "char", "varchar", "bit")) ||
        (in_array($type, array("date", "datetime", "timestamp", "time")) &&
          $default != "CURRENT_TIMESTAMP"
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
      $type = array_search($len, self::$INTEGER_BYTES);

    // As text
    elseif($type === "text")
      $type = array_search($len, self::$TEXT_BYTES);

    // As Decimal
    elseif($type === "text")
      $type = array_search($len, self::$TEXT_BYTES);

    // with var len
    elseif(in_array($type, array("bit", "char", "varchar")))
      $type = "$type($len)";

    // as decimal precision
    elseif($type == "decimal"){
      $type = array_search($len, self::$DECIMAL_BYTES);
      $precision = $field->getPrecision();
      $scale = $field->getScale();
      $type = "$type($precision, $scale)";
    }

    return "$name $type $attrs";

  }

  // Obtener el SQL para crear una tabla
  public function sqlCreateTable(AmTable $t){

    // Obtener nombre de la tabla
    $tableName = $this->getParseNameTable($t->getTableName());

    // Lista de campos
    $fields = array();
    $realFields = $t->getFields();

    // Obtener el SQL para cada camppo
    foreach($realFields as $field)
      $fields[] = $this->sqlField($field);

    // Obtener los nombres de los primary keys
    $pks = $t->getPks();
    foreach($pks as $offset => $pk)
      $pks[$offset] = $this->getParseName($t->getField($pk)->getName());

    // Preparar otras propiedades
    $engine = empty($t->engine) ? "" : "ENGINE={$t->engine} ";
    $charset = $this->sqlCharset($t->getCharset());
    $collage = $this->sqlCollage($t->getCollage());

    // Agregar los primaris key al final de los campos
    $fields[] = empty($pks) ? "" : "PRIMARY KEY (" . implode(", ", $pks). ")";

    // Unir los campos
    $fields = "\n".implode(",\n", $fields);

    // Preparar el SQL final
    return "CREATE TABLE IF NOT EXISTS $tableName($fields)$engine$charset$collage;";

  }

  // Obtener el SQL para una consulta TRUNCATE: Vaciar una tabla
  public function sqlTruncate(AmTable $table){

    // Obtener nombre de la tabla
    $tableName = $this->getParseNameTable($table);

    return "TRUNCATE $tableName";

  }

  // Obtener el SQL para eliminar una tabla
  public function sqlDropTable(AmTable $table){

    // Obtener nombre de la tabla
    $tableName = $this->getParseNameTable($table);

    return "DROP TABLE $tableName";

  }


}

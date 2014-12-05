<?php

/**
 * Fuente de datos para MySQL
 */

class mysqlAmSource extends AmSource{
  
  // Puerto por defecto para la conexion
  const DEFAULT_PORT = 3306;

  // Equivalencias entre los tipos de datos del Gesto de BD y el Lenguaje de programacion
  protected static
    $TYPES = array(
      "tinyinteger" => "tinyint",
      "integer"     => "int",
      "biginteger"  => "bigint",
      "float"       => "float",
      "text"        => "text",
      "string"      => "varchar",
      "datetime"    => "datetime",
      "date"        => "date",
      "time"        => "time",
    );

  // Propiedades propias para el Driver
  protected
    $handle = null; // Identificador de la conexion

  // Obtener el puerto por defecto
  public function getDefaultPort(){
    return self::DEFAULT_PORT;
  }

  // Crear una conexión
  public function connect(){
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

  // Devuelve el tipo de datos del gestor para un tipo de datos en el lenguaje
  public function getTypeOf($type){
    return array_search($type, self::$TYPES);
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

  }

  // Coleccion de caracteres
  public function sqlCollage(){

    // Si no recibió argumentos obtener el college de la BD
    if(!count(func_get_args())>0)
      $collate = $this->getCollage();
    
    // El el argumento esta vacío retornar cadena vacia
    if(empty($collate))
      return "";

    $collate = empty($collate) ? "" : " COLLATE {$collate}";
  }

  // SQL para crear la BD
  public function sqlCreate(){
    $database = $this->getParseName($this->getDatabase());
    $charset = $this->sqlCharset();
    $collate = $this->sqlCollage();
    return "CREATE DATABASE IF NOT EXISTS {$database}{$charset}{$collate}";
  }

  // SQL para eliminar la BD
  public function sqlDrop(){
    $database = $this->getParseName($this->getDatabase());
    return "DROP DATABASE {$database}";
  }
  
  // SQL para seleccionar la BD
  public function sqlSelectDatabase(){
    $database = $this->getParseName($this->getDatabase());
    return "USE {$database}";
  }

  // SQL para obtener el listado de tablas
  public function sqlGetTables(){

    $sql = $this
      ->newQuery("information_schema.TABLES", "t")
      ->innerJoin("information_schema.COLLATION_CHARACTER_SET_APPLICABILITY", "t.TABLE_COLLATION = c.COLLATION_NAME", "c")
      ->selectAs("t.TABLE_NAME", "tableName")
      ->selectAS("t.ENGINE", "engine")
      ->selectAS("t.TABLE_COLLATION", "collate")
      ->selectAS("c.CHARACTER_SET_NAME ", "charset")
      ->where("TABLE_SCHEMA='{$this->getDatabase()}'", "and", "TABLE_TYPE='BASE TABLE'")
      ->sql();

    return $sql;
      
  }

  // SQL par obtener los primary keys de una tabla
  public function sqlGetTablesPrimaryKey(AmTable $t){
    
    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("COLUMN_NAME", "name")
      ->where("TABLE_SCHEMA='{$this->database()}'", "and", "TABLE_NAME='{$t->tableName()}'", "and", "CONSTRAINT_NAME='PRIMARY'")
      ->orderBy("ORDINAL_POSITION")
      ->sql();
    
    return $sql;

  }

  // SQL para obtener el listado de columnas de una tabla
  public function sqlGetTablesColumns(AmTable $t){
    
    $sql = $this
      ->newQuery("information_schema.COLUMNS")
      ->selectAs("COLUMN_NAME", "name")
      ->selectAs("DATA_TYPE", "type")
      ->selectAs("CHARACTER_MAXIMUM_LENGTH", "charLenght")
      ->selectAs("NUMERIC_PRECISION", "floatPrecision")
      ->selectAs("IS_NULLABLE = 'NO'", "notNull")
      ->selectAs("COLUMN_DEFAULT", "defaultValue")
      ->selectAs("COLLATION_NAME", "collate")
      ->selectAs("CHARACTER_SET_NAME", "charset")
      ->selectAs("EXTRA", "extra")
      ->where("TABLE_SCHEMA='{$this->database()}'", "and", "TABLE_NAME='{$t->tableName()}'")
      ->orderBy("ORDINAL_POSITION")
      ->sql();
    
    return $sql;
    
  }
  
  // SQL para obtener el listade de foreign keys de una tabla
  public function sqlGetTablesForeignKeys(AmTable $t){
      
    $s = $t->source();
    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("REFERENCED_TABLE_NAME", "toTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where("TABLE_SCHEMA='{$this->database()}'", "and", "TABLE_NAME='{$t->tableName()}'", "and", "CONSTRAINT_NAME<>'PRIMARY'", "and", "REFERENCED_TABLE_SCHEMA=TABLE_SCHEMA")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();
        
    return $sql;

  }
  
  // SQL para obtener el lista de de referencias a una tabla
  public function sqlGetTablesReferences(AmTable $t){
      
    $s = $t->source();
    $sql = $this
      ->newQuery("information_schema.KEY_COLUMN_USAGE")
      ->selectAs("CONSTRAINT_NAME", "name")
      ->selectAs("COLUMN_NAME", "columnName")
      ->selectAs("TABLE_NAME", "fromTable")
      ->selectAs("REFERENCED_COLUMN_NAME", "toColumn")
      ->where("TABLE_SCHEMA='{$this->database()}'", "and", "REFERENCED_TABLE_NAME='{$t->tableName()}'", "and", "CONSTRAINT_NAME<>'PRIMARY'", "and", "REFERENCED_TABLE_SCHEMA=TABLE_SCHEMA")
      ->orderBy("CONSTRAINT_NAME", "ORDINAL_POSITION")
      ->sql();
    
    return $sql;
      
  }

  // Obtener el SQL para la clausula SELECT
  public function sqlSelect(AmQuery $q, $with = true){

    $selectsOri = $q->getSelects();  // Obtener argmuentos en la clausula SELECT
    $selects = array();  // Lista de retorno

    // Recorrer argumentos del SELECT
    foreach($selectsOri as $as => $field){

      // Si es una consulta se incierra entre parentesis
      if($field instanceof AmQuery)
        $field = "({$field->sql()})";
      
      // Agregar parametro AS
      $selects[] = Am::isNameValid($as) ? "$field AS '$as'" : (string)$field;

    }

    // Unir campos
    $selects = implode(", ", $selects);

    // Si no se seleccionó ningun campo entonces se tomaran todos
    $selects = (empty($selects) ? "*" : $selects);

    // Agregar SELECT
    return trim(($with ? "SELECT " : "").$selects);

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
        $from = $this->getParseName($this->getDatabase()).".".$this->getParseName($from->getTableName());
      }elseif(Am::isNameValid($from)){
        // Si es una tabla se concatena el nombre de la BD y el de la tabla como strin
        $from = $this->getParseName($this->getDatabase()).".".$this->getParseName($from);
      }elseif(false !== (preg_match("/^([a-zA-Z_][a-zA-Z0-9_]*)\.([a-zA-Z_][a-zA-Z0-9_]*)$/", $from, $matches)!= 0)){
        // Dividir por el punto
        $from = $this->getParseName($matches[1]).".".$this->getParseName($matches[2]);
      }elseif(is_string($from)){
        $from = $from = "($from)";
      }
            
      // Agregar parametro AS
      $froms[] = Am::isNameValid($as) ? "$from AS $as" : $from;
            
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
  public function sqlJoin(AmQuery $q){

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

    // Agregar OFFSET
    return trim(!isset($offset) ? "" : (($with ? "OFFSET " : "").$offset));

  }

  // Obtener el SQL para la clausula SET de una consulta UPDATE
  public function setsSql(AmQuery $q, $with = true){

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
        $value = mysql_real_escape_string($value);
        $sets[] = "{$set["field"]} = '$value'";
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
    $table = $q->getTable();
    $table = $table instanceof AmTable? $table->getTableName() : $table;

    // Obtener nombre de la tabla con la BD
    $tableName = $this->getParseName($this->getDatabase()).".".$this->getParseName($table);
    
    // Agregar DELETE FROM
    return implode(" ", array(
      "DELETE FROM",
      trim($tableName),
      trim($q->sqlWhere())
    ));

  }

  // Obtener el SQL para una consulta de insercon
  public function sqlInsertInto($table = null, $values = array(), array $fields = array()){
    
    // Si es una consulta
    if($values instanceof AmQuery){

      // Si los campos recibidos estan vacíos se tomará
      // como campos los de la consulta
      if(count($fields) == 0){
        $fields = array_keys($values->select());
      }

      // Los valores a insertar son el SQL de la consulta
      $strValues = $values->sql();

    // Si los valores es un array con al menos un registro
    }elseif(is_array($values) && count($values)>0){
        
        // Indica si
        $mergeWithFields = count($fields) == 0;
        
        // Recorrer cada registro en $values par obtener los valores a insertar
        foreach($values as $i => $v){

          if($v instanceof AmModel){
            // Si el registro es AmModel obtener sus valores como array
            // asociativo o simple
            $values[$i] = $v->dataToArray(!$mergeWithFields);
          }elseif($v instanceof AmObject){
            // Si es una instancia de AmObjet se obtiene como array asociativo
            $values[$i] = $v->toArray();
          }
          
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
            // Obtener el valor del registro actual en el campo actual
            $f = isset($v[$f])? $v[$f] : null;
            $f = mysql_real_escape_string($f);

            // Si no tiene valor asignar NULL
            $resultValues[$i][] = isset($f)? "'$f'" : "NULL";

          }

          // Unir todos los valores con una c
          $resultValues[$i] = implode(",", $resultValues[$i]);

        }

        // Unir todos los registros
        $resultValues = implode("),(", $resultValues);

        // Obtener Str para los valores
        $strValues = "VALUES ($resultValues)";

      }
      
      // Si el Str de valores no está vacío
      if(!empty($strValues)){

          // Obtener nombre de la tabla
          $table = $table instanceof AmTable? $table->getTableName() : $table;
          $tableName = $this->getParseName($this->getDatabase()).".".$this->getParseName($table);

          // Obtener el listado de campos
          foreach ($fields as $key => $field){
            $fields[$key] = $this->getParseName($field);
          }

          // Unir campos
          $fields = implode(",", $fields);
          
          // Generar SQL
          return "INSERT INTO $tableName($fields) $strValues";
          
      }
      
      // Consulta invalida
      return "";
      
  }

  // Obtener el SQL de la consulta
  public function sql(AmQuery $q){

    return !empty($q->sql) ? $q->sql :
      trim(implode(" ", array(
      trim($q->sqlSelect(true)),
      trim($q->sqlFrom(true)),
      trim($q->sqlJoin()),
      trim($q->sqlWhere(true)),
      trim($q->sqlGroups(true)),
      trim($q->sqlOrders(true)),
      trim($q->sqlLimit(true)),
      trim($q->sqlOffSet(true))
    )));

  }

  // Obtener el SQL para un campo de una tabla al momento de crear la tabla
  public function sqlField(AmField $field){

    // Preparar las propiedades  
    $name = $this->getParseName($field->name());
    $type = $field->type();
    $type = itemOr(self::$TYPES, $type, $type);
    $lenght = $field->charLenght();
    $lenght = !empty($lenght) ? "({$lenght})" : "";
    $notNull = $field->notNull() ? " NOT NULL" : "";
    $charset = $this->sqlCharset($field->charset());
    $collate = $this->sqlCollage($field->collate());

    $default = $field->defaultValue();
    $default = $default === null ? "" : " DEFAULT '{$default}'";
    
    $autoIncrement = $field->autoIncrement() ? " AUTO_INCREMENT" : "";
    
    return "$name$type$lenght$autoIncrement$charset$collate$notNull$default";

  }

  // Obtener el SQL para crear una tabla
  public function createTableSql(AmTable $t){
      
    // Obtener nombre de la tabla
    $tableName = $this->getParseName($this->getDatabase()).".".$this->getParseName($table->getTableName());

    // Lista de campos
    $fields = array();

    // Obtener el SQL para cada camppo
    foreach($t->fields() as $field){
      $fields[] = $this->fieldToSql($field);
    }
      
    // Obtener los nombres de los primary keys
    $pks = $t->pks();
    foreach($pks as $offset => $pk){
      $pks[$offset] = $this->getParseName($t->fields($pk)->name());
    }

    // Preparar otras propiedades
    $engine = empty($t->engine) ? "" : "ENGINE={$t->engine} ";        
    $charset = $this->sqlCharset($t->charset());
    $collate = $this->sqlCollage($t->collate());

    // Agregar los primaris key al final de los campos
    $fields[] = empty($pks) ? "" : "PRIMARY KEY (" . implode(", ", $pks). ")";
    
    // Unir los campos
    $fields = implode(", ", $fields);

    // Preparar el SQL final
    return "CREATE TABLE IF NOT EXISTS $tableName($fields)$engine$charset$collate;";

  }

  // Obtener el SQL para una consulta TRUNCATE: Vaciar una tabla
  public function sqlTruncate($table = null){
    
    // Obtener nombre de la tabla
    $table = $table instanceof AmTable? $table->getTableName() : $table;
    $tableName = $this->getParseName($this->getDatabase()).".".$this->getParseName($table);

    return "TRUNCATE $tableName";
    
  }

  // Obtener el SQL para eliminar una tabla
  public function sqlDropTable(AmTable $table){
    
    // Obtener nombre de la tabla
    $tableName = $this->getParseName($this->getDatabase()).".".$this->getParseName($table->getTableName());

    return "TRUNCATE $tableName";
    
  }


}

<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase los querys a las BD
 */
class AmQuery extends AmObject{

  protected
    
    /**
     * Nombre del modelo de la tabla.
     */
    $model = 'array',
    
    /**
     * Callback con el formateador del resultado del query.
     */
    $formatter = null,
    
    /**
     * Tipo de query: select, insert, update o delete.
     */
    $type = 'select',
    
    /**
     * Instancia del esquema.
     */
    $scheme = null,
    
    /**
     * Nombre del query.
     */
    $name = null,
    
    /**
     * Manejador para el resultado del query.
     */
    $result = null,
    
    /**
     * Posición actual en la iteración.
     */
    $index = 0,

    /**
     * Registros obtenidos.
     */
    $items = array(),
    
   /**
    * Array de campos de un query SELECT.
    */
    $selects = array(),
    
   /**
    * Para obtener solo los registros diferentes.
    */
    $distinct = false,
    
   /**
    * Hash de tablas para la cláusula FROM.
    */
    $froms = array(),

    /**
     * Posibles joins
     */
    $possibleJoins = array(),
    
   /**
    * Array de condiciones para la cláusula WHERE.
    */
    $wheres = array(),
    
   /**
    * Listado de tablas para la cláusula JOIN.
    */
    $joins = array(),
    
   /**
    * Hash de campos para la cláusula ORDER BY.
    */
    $orders = array(),
    
   /**
    * Hash de campos para la cláusula GROUP BY.
    */
    $groups = array(),
    
   /**
    * Int con la cantidad de de registros. Cláusula LIMIT.
    */
    $limit = null,
    
   /**
    * INt con la posicion de inicio. Cláusula OFFSET.
    */
    $offset = null,
    
   /**
    * Tabla donde se insertará los valores para queries INSERT.
    */
    $insertIntoTable = null,
    
   /**
    * Campos para el insert para queries INSERT.
    */
    $insertIntoFields = null,
    
   /**
    * Lista de cambios SETS para queries UPDATE.
    */
    $sets = array();

  /**
   * Devuelve el modelo del query.
   * @return string Nombre del modelo.
   */
  public function getModel(){

    return $this->model;

  }

  /**
   * Devuelve el callback con el formateador de resultado.
   * @return callback Formateador del resultado.
   */
  public function getFormatter(){

    return $this->formatter;

  }

  /**
   * Devuelve el tipo de query: select, insert, update o delete.
   * @return string Tipo de query.
   */
  public function getType(){

    return $this->type;

  }

  /**
   * Devuelve la instancia del esquema.
   * @return AmScheme Instancia del esquema.
   */
  public function getScheme(){

    return $this->scheme;

  }

  /**
   * Devuelve el nombre del query.
   * @return string Nombre del query.
   */
  public function getName(){

    return $this->name;

  }

  /**
   * Devuelve el hash de la cláusula SELECT.
   * @return hash Hash de campos a selecionar.
   */
  public function getSelects(){

    return $this->selects;

  }

  /**
   * Devuelve el hash de la cláusula FROM.
   * @return hash con las tablas para la cláusula FROM.
   */
  public function getFroms(){

    return $this->froms;

  }

  /**
   * Devuelve el listado de joins.
   * @return hash Lisado de joins.
   */
  public function getJoins(){

    return $this->joins;

  }

  /**
   * Devuelve Array de condiciones.
   * @return array Array de condiciones.
   */
  public function getWheres(){

    return $this->wheres;

  }

  /**
   * Devuelve el array de campos para ORDER BY.
   * @return array Array de campos para ORDER BY.
   */
  public function getOrders(){

    return $this->orders;

  }

  /**
   * Devuelve el array e campos para GROUP BY.
   * @return array Array de cmapos para GROUP BY.
   */
  public function getGroups(){

    return $this->groups;

  }

  /**
   * Devuelve cantidad de registros máximos paral query.
   * @return int cantidad de registros máximos.
   */
  public function getLimit(){

    return $this->limit;

  }

  /**
   * Devuelve la posición dede la cual se comienzan a tomar registros.
   * @return int Posición inicial del query.
   */
  public function getOffset(){

    return $this->offset;

  }

  /**
   * Devuelve el array de cambios para un query UPDATE.
   * @return array Array de sets.
   */
  public function getSets(){

    return $this->sets;

  }

  /**
   * Devuelve si se agregará la cláusula DISTINCT a un query select.
   * @return bool Si se agrega la cláusula.
   */
  public function getDistinct(){

    return $this->distinct;

  }

  /**
   * Devuelve la tabla donde se insertará el resultado del query para un
   * query insert.
   * @return string Nombre de tabla.
   */
  public function getInsertTable(){

    return $this->insertIntoTable;

  }

  /**
   * Devuelve el listado de campos para un query insert.
   * @return array Listado de campos.
   */
  public function getInsertFields(){

    return $this->insertIntoFields;

  }

  /**
   * Método para asignar array de valores por un metodo. Destinado al metodo
   * select y from.
   * @param string $method Método (select o from).
   * @param array  $args   Listado de argumentos.
   * @return $this
   */
  private function setArrayAttribute($method, $args){

    // Agregar cada argmento
    foreach($args as $arg){
      // Si es un array
      if(is_array($arg)){
        foreach($arg as $alias => $value){
          $this->$method($value, $alias);
        }
      }else{
        // Si es una cadena
        $this->$method($arg);
      }
    }

    return $this;

  }

  /**
   * Asigna el callback para formatear el resultado del query.
   * @param callback $value Callback a asignar.
   * @return $this
   */
  public function setFormatter($value){

    // Si es un callback válido se asigna.
    if(isValidCallback($value))
      $this->formatter = $value;

    return $this;
    
  }

  /**
   * Asiga el modelo de utilizado para el retorno.
   * @param string $value Nombre del modelo a asignar.
   * @return $this
   */
  public function setModel($value){

    $this->model = $value;
    return $this;

  }

  /**
   * Asigna el hash de la cláusula SELECT.
   * @param hash $value Hash de campos y aliases.
   * @return $this
   */
  public function setSelects(array $value){

    $this->selects = array();
    return $this->setArrayAttribute('selectAs', array($value));

  }

  /**
   * Asigna el hash de la cláusula FROM.
   * @param hash   $value Hash de la cláusula FROM.
   * @return $this
   */
  public function setFrom(array $value){

    $this->froms = array();
    return $this->setArrayAttribute('fromAs', array($value));

  }

  /**
   * Indica que se obtendrá solo los registro del query SELECT con la cláusula
   * DISTINCT.
   * @return $this
   */
  public function distinct(){

    $this->distinct = true;
    return $this;

  }
  
  /**
   * Indica que se obtendrá solo los registro del query SELECT sin la cláusula
   * DISTINCT.
   * @return $this
   */
  public function noDistinct(){

    $this->distinct = false;
    return $this;

  }

  /**
   * Devuelve una instancia copia de la actual.
   * @return AmQuery Nueva instancia.
   */
  public function copy(){

    return clone($this);

  }

  /**
   * Devuelve un query basado en el query actual.
   * @param  string $alias Alias del query actual en el query resultando.
   * @return AmQuery       Instancia creada.
   */
  public function encapsulate($alias = null){

    return $this->getScheme()->q($this, $alias);

  }

  /**
   * Obtener el SQL de un query.
   * @return string SQL del query.
   */
  public function sql(){
    
    return $this->getScheme()->sqlOf($this);

  }

  /**
   * Ejecutar el query.
   * @return handler Devuelve el muntore manejador del resultado del query.
   */
  public function execute(){

    $this->index = 0;
    $this->items = array();

    return $this->result = $this->getScheme()->execute($this);

  }

  /**
   * Cast del query a string.
   * El cast de un query a string obtiene el SQL del mismo.
   * @return string SQL del query.
   */
  public function __toString(){
    
    return $this->sql();

  }

  /**
   * Función que crea una vista con el query actual.
   * @param  bool $orReplace Si se agrega la cláusula OR REPLACE.
   * @return bool            Si se pudo o no crear la vista.
   */
  public function create($orReplace = true){

    return $this->getScheme()->createView($this, $orReplace);

  }

  /**
   * Elimina la vista.
   * @param  bool $ifExists Si se agrega la cláusula IF EXISTS
   * @return bool           Si se pudo eliminar la vista.
   */
  public function drop($ifExists = true){

    return $this->getScheme()->dropView($this, $ifExists);

  }


  /**
   * Insertar los registros resultantes del query select en una table
   * @param  string/AmTable $table  Nombre o instancia de la tabla donde se
   *                                desea hacer la inserción.
   * @param  array          $fields Lista de campos para la inserción.
   * @return bool           Se se pudo insertar los registros correctamente.
   */
  public function insertInto($table, array $fields = array()){

    $this->type = 'insert';

    $this->insertIntoTable = $table;
    $this->insertIntoFields = $fields;

    return $this->execute();

  }

  /**
   * Eliminar registros selecionados
   * @return bool Si se eliminó los registros satisfactoriamente.
   */
  public function delete(){

    $this->type = 'delete';

    return $this->execute();

  }

  /**
   * Devuelve la primera instancia deo nombre una tabla que se encuentre dentro
   * de la cláusula FROM. Tambien se busca dentro de la cláusula FROM del
   * queries anidadas.
   * @return string/Amtable                      Nombre o instancia de la tabla
   *                                             encontrada.
   */
  public function getTable(){

    // Obtener los froms del query
    $model = $this->getModel();

    if(is_subclass_of($model, 'AmModel')){
      return $model::me();
    }else{
      $scheme = $this->getScheme();
      if($scheme->hasTableInstance($model))
        return $scheme->getTableInstance($model);
    }

    return null;

  }

  /**
   * Realiza el update con el query actual.
   * @return bool Si se realizó la actualización satisfactoriamente.
   */
  public function update(){

    // Si no se han realizado asignaciones
    if(count($this->sets)==0)
      // Retornar verdadero.
      return true;

    // Obtener la cláusula FROM.
    $froms = $this->getFroms();

    // Recorrer para hasta obtener la primera instancia de AmTable.
    foreach ($froms as $fromClauseItem){
      $from = $fromClauseItem->getFrom();
      if($from instanceof AmTable){
        // Asignar la fecha de actualización.
        $from->setAutoUpdatedAt($this);
        break;
      }
    }

    // Cambiar el tipo de query.
    $this->type = 'update';
    
    // Ejecutar el query.
    return $this->execute();
    
  }

  /**
   * Agregar un campos a la cláusula SELECT.
   * @param  string $field Nombre del campo.
   * @param  string $alias Alias del campo.
   * @return $this
   */
  public function selectAs($field, $alias = null){

    $this->type = 'select';

    $item = new AmClauseSelectItem(array(
      'query' => $this,
      'field' => $field,
      'alias' => $alias,
    ));

    // Agregar al final
    $this->selects[$item->getAlias()] = $item;

    return $this;

  }

  /**
   * Agrega posibles joins a la consulta.
   * @param array  $joins Array de oposibles joins a agregar.
   * @param string $alias Alias al que pertenece los posibles joins.
   * @return $this
   */
  protected function addPossibleJoins(array $joins, $alias){

    foreach($joins as $aliasJoin => $join){
      $aliasAliasJoin = "{$alias}.{$aliasJoin}";

      if(isset($this->possibleJoins[$aliasJoin])
        && isset($this->possibleJoins[$aliasAliasJoin])){
        continue;
      }
      
      $on = array();
      foreach($join['cols'] as $from => $to){
        if($join['type'] == 'hasMany'){
          list($from, $to) = array($to, $from);
        }
        $on[] = array("{$aliasJoin}.{$from}", "{$alias}.{$to}");
      }
      
      $join = array(
        'as' => $aliasJoin,
        'on' => $on,
      );

      if(!isset($this->possibleJoins[$aliasJoin]))
        $this->possibleJoins[$aliasJoin] = $join;

      if(!isset($this->possibleJoins[$aliasAliasJoin]))
        $this->possibleJoins[$aliasAliasJoin] = $join;

    }

    return $this;
    
  }

  /**
   * Agregar un campos a la cláusula FROM.
   * @param  string $field Nombre de la tabla.
   * @param  string $alias Alias de la tabla.
   * @return $this
   */
  public function fromAs($from, $alias = null){

    $item = new AmClauseFromItem(array(
      'query' => $this,
      'from' => $from,
      'alias' => $alias,
    ));

    $table = null;
    if($from instanceof AmTable){
      $table = $from;
    }elseif(is_string($from) && is_subclass_of($from, 'AmModel')){
      $table = $from::me();
    }

    if($table instanceof AmTable){
      $this->possibleJoins[] = array(
        'alias' => $item->getAlias(),
        'table' => $table,
      );
    }

    // Agregar al final
    $this->froms[$item->getAlias()] = $item;

    return $this;

  }

  /**
   * Agregar un join.
   * @param  string/Amtable $from   Nombre o instancia de la tabla con la que se
   *                                realiza el join.
   * @param  string         $alias  Alias para la tabla agregada.
   * @param  string         $on     Condición para el join.
   * @param  string         $type   Tipo de join.
   * @return $this
   */
  public function join($from, $alias = null, $on = null, $type = null){

    $item = new AmClauseJoinItem(array(
      'query' => $this,
      'from' => $from,
      'alias' => $alias,
      'on' => $on, 
      'type' => strtoupper($type)
    ));

    // Agregar los joins
    $this->joins[$item->getAlias()] = $item;

    return $this;

  }

  /**
   * Agrega un inner join
   * @param  string/Amtable $from  Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $alias Alias para la tabla agregada.
   * @param  string         $on    Condición para el join.
   * @return $this
   */
  public function innerJoin($from, $alias = null, $on = null){

    return $this->join($from, $alias, $on, 'inner');

  }

  /**
   * Agrega un left join
   * @param  string/Amtable $from  Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $alias Alias para la tabla agregada.
   * @param  string         $on    Condición para el join.
   * @return $this
   */
  public function leftJoin($from, $alias = null, $on = null){

    return $this->join($from, $alias, $on, 'left');

  }

  /**
   * Agrega un right join
   * @param  string/Amtable $from  Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $alias Alias para la tabla agregada.
   * @param  string         $on    Condición para el join.
   * @return $this
   */
  public function rigthJoin($from, $alias = null, $on = null){

    return $this->join($from, $alias, $on, 'right');

  }

  /**
   * Agregar campos al SELECT.
   * @params Lista de campos a selecionar.
   * @return $this
   */
  public function select(/* campos */){

    return $this->setArrayAttribute('selectAs', func_get_args());

  }

  /**
   * Agregar tablas al FROM.
   * @return $this
   */
  public function from(){

    return $this->setArrayAttribute('fromAs', func_get_args());

  }

  /**
   * Prepara las condiciones para agregarlas al array de condiciones.
   * @param  string/array $conditions Condiciones o Array de condiciones.
   * @return string                   Condiciones preparadas.
   */
  protected function parseWhere($conditions){

    // Si no es un array de retornar tal cual
    if(!is_array($conditions))
      return $conditions;

    $ret = array();
    $nextPrefijo = '';  // Operador booleano de prefijo
    $nextUnion = 'AND'; // Operador booleano de enlace

    // Por cada condicione
    foreach($conditions as $condition){

      // Obtiene la condicion de union y la vuelve mayuscula
      if(!is_array($condition)){
        $upperCondition = strtoupper($condition);
      }elseif(count($condition)==3 && strtoupper($condition[1]) == 'IN'){
        // Eliminar condicion dle medio
        $condition = array($condition[0], $condition[2]);
        $upperCondition = 'IN';
      }else{
        $upperCondition = '';
      }

      if($upperCondition == 'AND' || $upperCondition == 'OR'){
        // Si la primera condicion es un operador boolean doble
        $nextUnion = $upperCondition;
      }elseif($upperCondition == 'NOT'){
        // Si es el operator booleano de negacion agregar para la siguiente condicion
        $nextPrefijo = $upperCondition;
      }elseif(!empty($condition)){

        // Sino es un operador booleano se agrega al listado de condiciones de retorno
        $ret[] = array(
          'union' => $nextUnion,
          'prefix' => $nextPrefijo,
          'condition' => $upperCondition == 'IN'? $condition : $this->parseWhere($condition),
          'isIn' => $upperCondition == 'IN'
        );

        $nextPrefijo = '';

      }

    }

    return $ret;

  }

  /**
   * Agregar condiciones al query.
   * @return $this
   */
  public function where(){

    $args = $this->parseWhere(func_get_args());

    // Parchar las condificones para luego agregarlas
    foreach($args as $where)
      $this->wheres[] = $where;

    return $this;

  }

  /**
   * Agregar condiciones con AND.
   * @return $this
   */
  public function andWhere(){

    return $this->where('and', func_get_args());

  }

  /**
   * Agregar condiciones con OR.
   * @return $this
   */
  public function orWhere(){

    return $this->where('or', func_get_args());

  }

  /**
   * Eliminar todas las condiciones
   * @return $this
   */
  public function clearWhere(){

    $this->conditions = array();
    return $this;

  }

  /**
   * Agregar campos a la cláusula ORDER BY.
   * @param  string/array $orders Nombre del campo para ordenado, o array con
   *                              los nombres de los campos para ordenar.
   * @param  string       $dir    Dirección del orden.
   * @return $this
   */
  public function orderBy($orders, $dir = 'ASC'){

    if(!is_array($orders))
      $orders = array($orders);

    // Recorrer para agregar
    foreach($orders as $order){

      // Liberar posicion para que al agregar quede en ultima posicion
      unset($this->orders[$order]);
      $this->orders[$order] = $dir;

    }

    return $this;

  }

  /**
   * Agregar campos de orden Ascendiente.
   * @params Listado de campos a agregar.
   * @return $this
   */
  public function orderByAsc(/**/){

    return $this->orderBy(func_get_args(), 'ASC');

  }

  /**
   * Agregar campos de orden Descendiente.
   * @params Listado de campos a agregar.
   * @return $this
   */
  public function orderByDesc(/**/){

    return $this->orderBy(func_get_args(), 'DESC');

  }

  /**
   * Agregar campos para la cláusula GROUP BY.
   * @param  array  $groups Lista de campos para agrupar.
   * @return $this
   */
  public function groups(array $groups){

    // Elimintar los campos que se agregaran de los existentes
    $this->groups = array_diff($this->groups, $groups);

    // Agregar cada campo
    foreach($groups as $group)
      $this->groups[] = $group;

    return $this;


  }

  /**
   * Agregar un campos para agrupar.
   * @return $this
   */
  public function groupBy(){

    return $this->groups(func_get_args());

  }

  /**
   * Asigna un límite al query.
   * @param  int   $limit cantidad máxima de registros a tomar.
   * @return $this
   */
  public function limit($limit){

    $this->limit = $limit;
    return $this;

  }

  /**
   * Agregar punto de inicio para el query.
   * @param  int   $offset Indica que página se tomará.
   * @return $this
   */
  public function offSet($offset){

    $this->offset = $offset;
    return $this;

  }

  /**
   * Agrega un SET al query.
   * Además convierte el query en un UPDATE.
   * @param  string $field Nombre del campo a setear.
   * @param  mixed  $value Valor a asignar
   * @param  bool   $const Si el valor es una constante de o un valor de
   *                       original del SMDB.
   * @return $this
   */
  public function set($field, $value, $const = true){

    $this->type = 'update';

    $this->sets[] = array(
      'field' => $field,
      'value' => $value,
      'const' => $const
    );
    return $this;

  }

  /**
   * Devuelve la cantidad de registros resultantes del query.
   * @return int Cantidad de registro que devolverá el query.
   */
  public function count(){

    // Crear el query para contar
    $ret = $this->copy()
                ->setSelects(array('count' => 'count(*)'))
                ->row('array');

    // Si se generó un error devolver cero, de lo contrari
    // devolver el valor obtenido
    return $ret === false ? 0 : intval($ret['count']);

  }

  /**
   * Obtener un registro del resultado del query.
   * @param  string   $as        Modelo con el que se devolverá el regsitro.
   *                             puede ser 'array', 'am', 'object', el nombre
   *                             de un modelom, o el nombre de una clase. Si no
   *                             se indica se utiliza el modelo del query.
   * @param  callback $formatter Callback para dar formato al registro.
   * @return mixed               Devuelve el registro obtenido como un modelo
   *                             señalado.
   */
  public function row($as = null, $formatter = null){

    if(isValidCallback($as)){
      $formatter = $as;
      $as = null;
    }
    
    // Obtener la fuente de datos
    $scheme = $this->getScheme();

    if(!isset($as))
      $as = $this->getModel();

    // Se ejecuta el query si no se ha ejecutado
    if(null === $this->result)
      $this->execute();

    // Si se generó un error en el query retornar false
    if(false === $this->result)
      return false;

    // Obtener el registro
    $r = $scheme->getFetchAssoc($this->result);

    // Si no existe mas registros
    if(false === $r || $r === null)
      return false;

    // Preparar valores del array mediante los campos de la tabla.
    
    // Obtner la tabla
    $table = $this->getTable();

    // Preparar valores si se logró obtener la instancia de la tabla.
    if($table instanceof AmTable)
      $r = $table->prepare($r);

    if($as == 'object'){
      // Retornar como objeto
      $r = (object)$r;

    }elseif($as == 'am'){

      // Retornar como objeto de Amathista
      $r = new AmObject($r);

    }elseif(class_exists($as)){
      // Clase especifica

      if(is_subclass_of($as, 'AmModel'))
        $r = new $as($r, false);
      else
        $r = new $as($r);

    // Si no se indicó como devolver el objeto
    }elseif($as != 'array' && !!($className = AmScheme::model($as))){

      // Se encontró el modelo
      $r = new $className($r, false);  // Crear instancia del modelo

    }elseif($as != 'array'){

      // Sino retornar null
      $r = new AmObject($r);

    }

    $localFormatter = $this->getFormatter();

    $realRecord = $r;

    // Formatear el valor
    if(isset($formatter)){
      $newRecord = call_user_func_array($formatter, array($r, $realRecord));
      if(isset($newRecord))
        $r = $newRecord;
    }

    if(isset($localFormatter)){
      $newRecord = call_user_func_array($localFormatter, array($r, $realRecord));
      if(isset($newRecord))
        $r = $newRecord;
    }

    return $r;

  }

  /**
   * Devuelve un array con los registros resultantes del query.
   * @param  string   $as       Modelo con el que se devolverá el registro.
   *                            puede ser 'array', 'am', 'object', el nombre
   *                            de un modelom, o el nombre de una clase. Si no
   *                            sae indica se utiliza el modelo del query.
   * @param  callback $formatter Callback para dar formato al registro.
   * @return array              Array de modelos resultantes del query
   */
  public function get($as = null, $formatter = null){

    // Clonar el query
    $q = $this->copy();

    // Array para retorno
    $ret = array();

    // Mientras exista resgistros en cola
    while(!!($row = $q->row($as, $formatter)))
      $ret[] = $row; // Agregar registros al array

    return $ret;

  }

  /**
   * Devuelve una columna del query.
   * @param  string $field Nombre del campo que se desea obtener.
   * @return array         Array con los valores resultantes en la columna
   *                       solicitada.
   */
  public function col($field){

    // Clonar el query
    $q = $this->copy()

      // PENDIENTE
      // Selecionar el campo.
      // ->selectAs($field)
      ;

    // Array para retorno
    $ret = array();

    // Mientras exista resgistros en cola
    while(!!($row = $q->row('array'))){
      $ret[] = $row[$field]; // Agregar registros al array
    }

    return $ret;

  }

  /**
   * Devuelve una copia del query con la siguiente página del resultado.
   * @return AmQuery Query resultante.
   */
  public function haveNextPage(){

    return !!$this->copy()
      ->offset($this->getLimit() + $this->getOffset())
      ->row('array');
      
  }

  //////////////////////////////////////////////////////////////////////////////
  // Sobre escrituras de los método para iterar la consulta
  //////////////////////////////////////////////////////////////////////////////

  /**
   * Rebobinar la Iterador al primer elemento.
   */
  public function rewind(){
    $this->index = 0;
  }

  /**
   * Comprueba si la posición actual del iterador es válida.
   * @return bool Si existe el elemento actual.
   */
  public function valid(){

    // Si no está cargada la posición actual
    if(!isset($this->items[$this->index])){

      // Se obtiene el siguiente elemento.
      $row = $this->row();

      // Si nbo se pudo obtener un elemento retornar falso.
      if(!$row)
        return false;

      // Guardar en los items obtenidos.
      $this->items[$this->index] = $row;

    }

    // Es válido
    return true;

  }

  /**
   * Devuelve el elemento actual del iterador.
   * @return mixed Elemento actual.
   */
  public function current(){

    return $this->items[$this->index];

  }

  /**
   * Devuelve la clave del elemento actual.
   * @return int Clave del elemento actual.
   */
  public function key(){

    return $this->index;

  }

  /**
   * Avanza al siguiente elemento de la iteración.
   */
  public function next(){

    $this->index++;

  }


}
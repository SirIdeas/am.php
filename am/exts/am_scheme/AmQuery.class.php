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
    $formater = null,
    
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
    * Hash de campos para la cláusula SELECT.
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
  public function getFormater(){

    return $this->formater;

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
  public function setFormater($value){

    // Si es un callback válido se asigna.
    if(isValidCallback($value))
      $this->formater = $value;

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

    $this->selects = $value;
    return $this;

  }

  /**
   * Asigna el hash de la cláusula FROM.
   * @param hash   $value Hash de la cláusula FROM.
   * @return $this
   */
  public function setFrom(array $value){

    $this->froms = $value;
    return $this;

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
  public function encapsulate($alias = 'q'){

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
   * @param  bool           $returnTableInstance Indica si al encontrar una
   *                                             instancia de AmTable debe ser
   *                                             retornada.
   * @return string/Amtable                      Nombre o instancia de la tabla
   *                                             encontrada.
   */
  public function getTable($returnTableInstance = false){

    // Obtener los froms del query
    $froms = $this->getFroms();

    foreach ($froms as $from) {

      // Si es un query
      if($from instanceof AmQuery)
        $from = $from->getTable($returnTableInstance);

      // Si es una tabla obtener el nombre
      if($from instanceof AmTable){
        if($returnTableInstance)
          return $from;
        $from = $from->getTableName();
      }

      // Si tiene un nombre válido retornar el nombre de la tabla
      if(isNameValid($from))
        return $from;
      
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
    foreach ($froms as $from)
      if($from instanceof AmTable){
        // Asignar la fecha de actualización.
        $from->setAutoUpdatedAt($this);
        break;
      }

    // Cambiar el tipo de query.
    $this->type = 'update';
    
    // Ejecutar el query.
    return $this->execute();
    
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
   * Agregar un campos a la cláusula SELECT.
   * @param  string $field Nombre del campo.
   * @param  string $alias Alias del campo.
   * @return $this
   */
  public function selectAs($field, $alias = null){

    $this->type = 'select';

    // Si no se indicó el argumetno $alias
    if(empty($alias)){
      if (isNameValid($field)){
        // Agregar en una posicion espeficia
        $this->selects[$field] = $field;
      }else{
        // Agregar al final
        $this->selects[] = $field;
      }
    }elseif(isNameValid($alias)){
      // Agregar en una posicion espeficia
      $this->selects[$alias] = $field;
    }else{
      // Agregar al final
      $this->selects[] = $field;
    }

    return $this;

  }

  /**
   * Agregar tablas al FROM.
   * @return $this
   */
  public function from(){

    return $this->setArrayAttribute('fromAs', func_get_args());

  }

  /**
   * Agregar un campos a la cláusula FROM.
   * @param  string $field Nombre de la tabla.
   * @param  string $alias Alias de la tabla.
   * @return $this
   */
  public function fromAs($from, $alias = null){

    // Asignacion del from
    if(empty($alias)){

      // Si no se indicó el parametro $alias
      if($from instanceof AmQuery){
        // Si es un query se agrega al final
        $this->froms[] = $from;
      }elseif($from instanceof AmTable){
        // Si es nua tabla se asigna en una posicion especifica
        $this->froms[$from->getTableName()] = $from;
      }elseif (isNameValid($from)){
        // Se asigna en una posicion especifica
        $this->froms[$from] = $from;
      }else{
        // Agregar al final
        $this->froms[] = $from;
      }

    }elseif(isNameValid($alias)){
      // Adicion en posicion determinada
      $this->froms[$alias] = $from;
    }else{
      // Adicion al final de la lista de tablas
      $this->froms[] = $from;
    }

    return $this;

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
      }else{

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
   * Agregar un join.
   * @param  string/Amtable $table Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $on    Condición para el join.
   * @param  string         $as    Alias para la tabla agregada.
   * @param  string         $type  Tipo de join.
   * @return $this
   */
  public function join($table, $on, $as, $type = 'inner'){

    // Agregar los joins
    $this->joins[] = array(
      'table' => $table, 
      'on' => $on, 
      'as' => $as, 
      'type' => strtoupper($type)
    );

    return $this;

  }

  /**
   * Agrega un inner join
   * @param  string/Amtable $table Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $on    Condición para el join.
   * @param  string         $as    Alias para la tabla agregada.
   * @return $this
   */
  public function innerJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'inner');

  }

  /**
   * Agrega un left join
   * @param  string/Amtable $table Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $on    Condición para el join.
   * @param  string         $as    Alias para la tabla agregada.
   * @return $this
   */
  public function leftJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'left');

  }

  /**
   * Agrega un right join
   * @param  string/Amtable $table Nombre o instancia de la tabla con la que se
   *                               realiza el join.
   * @param  string         $on    Condición para el join.
   * @param  string         $as    Alias para la tabla agregada.
   * @return $this
   */
  public function rigthJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'right');

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

    return $this->orderBy('ASC', func_get_args());

  }

  /**
   * Agregar campos de orden Descendiente.
   * @params Listado de campos a agregar.
   * @return $this
   */
  public function orderByDesc(/**/){

    return $this->orderBy('DESC', func_get_args());

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
   * @param  any    $value Valor a asignar
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
   * @param  string   $as       Modelo con el que se devolverá el regsitro.
   *                            puede ser 'array', 'am', 'object', el nombre
   *                            de un modelom, o el nombre de una clase. Si no
   *                            sae indica se utiliza el modelo del query.
   * @param  callback $formater Callback para dar formato al registro.
   * @return any                Devuelve el registro obtenido como un modelo
   *                            señalado.
   */
  public function row($as = null, $formater = null){
    
    // Obtener la fuente de datos
    $scheme = $this->getScheme();

    if(!isset($as))
      $as = $this->getModel();

    if(!isset($formater))
      $formater = $this->getFormater();

    // Se ejecuta el query si no se ha ejecutado
    if(null === $this->result)
      $this->execute();

    // Si se generó un error en el query retornar false
    if(false === $this->result)
      return false;

    // Obtener el registro
    $r = $scheme->getFetchAssoc($this->result);

    // Si no existe mas registros
    if(false === $r)
      return false;

    // Preparar valores del array mediante los campos de la tabla.
    
    // Obtner la tabla
    $table = $this->getTable(true);

    if(is_string($table))
      $table = $scheme->loadTable($table);

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

    // Formatear el valor
    if(isset($formater))
      $r = call_user_func_array($formater, array($r));

    return $r;

  }

  /**
   * Devuelve un array con los registros resultantes del query.
   * @param  string   $as       Modelo con el que se devolverá el regsitro.
   *                            puede ser 'array', 'am', 'object', el nombre
   *                            de un modelom, o el nombre de una clase. Si no
   *                            sae indica se utiliza el modelo del query.
   * @param  callback $formater Callback para dar formato al registro.
   * @return array              Array de modelos resultantes del query
   */
  public function get($as = null, $formater = null){

    // Clonar el query
    $q = $this->copy();

    // Array para retorno
    $ret = array();

    // Mientras exista resgistros en cola
    while(!!($row = $q->row($as, $formater)))
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

      // Selecionar el campo.
      ->selectAs($field);

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

}
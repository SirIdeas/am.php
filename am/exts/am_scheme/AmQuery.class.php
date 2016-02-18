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

  // Propidades
  protected
    
    /**
     * Formato de respuesta del los registros
     */
    $model = 'array',
    
    /**
     * Formateador el resultado de la consulta
     */
    $formater = null,
    
    /**
     * Tipo de consulta
     */
    $type = 'select',
    
    /**
     * Fuente de datos
     */
    $scheme = null,
    
    /**
     * Nombre de la fuente
     */
    $name = null,
    
    /**
     * Manejador para el resultado de la consulta
     */
    $result = null,
    
    
   /**
    * Lista de campos para la clausula SELECT
    */
    $selects = array(),
    
   /**
    * Para solo obtener los registros diferentes
    */
    $distinct = false,
    
   /**
    * Lista de tablas para la clausula FROM
    */
    $froms = array(),
    
   /**
    * Lista de condiciones para la clausula WHERE
    */
    $wheres = array(),
    
   /**
    * Lista de tablas para la clausula JOIN
    */
    $joins = array(),
    
   /**
    * Lista de campos para la clausula ORDER BY
    */
    $orders = array(),
    
   /**
    * Lista de campos para la clausula GROUP BY
    */
    $groups = array(),
    
   /**
    * Cantidad de registro: LIMIT
    */
    $limit = null,
    
   /**
    * Posicion de inicio: OFFSET
    */
    $offset = null,
    
   /**
    * Tabla donde se insertará los valores
    */
    $insertIntoTable = null,
    
   /**
    * Campos para el insert
    */
    $insertIntoFields = null,
    
   /**
    * Lista de cambios SETS para consultas UPDATE
    */
    $sets = array();

  /**
   * [getModel description]
   * @return [type] [description]
   */
  public function getModel(){

    return $this->model;

  }

  /**
   * [getFormater description]
   * @return [type] [description]
   */
  public function getFormater(){

    return $this->formater;

  }

  /**
   * [getType description]
   * @return [type] [description]
   */
  public function getType(){

    return $this->type;

  }

  /**
   * [getScheme description]
   * @return [type] [description]
   */
  public function getScheme(){

    return $this->scheme;

  }

  /**
   * [getName description]
   * @return [type] [description]
   */
  public function getName(){

    return $this->name;

  }

  /**
   * [getSelects description]
   * @return [type] [description]
   */
  public function getSelects(){

    return $this->selects;

  }

  /**
   * [getFroms description]
   * @return [type] [description]
   */
  public function getFroms(){

    return $this->froms;

  }

  // GET para los joins
  /**
   * [getJoins description]
   * @param  [type] $type [description]
   * @return [type]       [description]
   */
  public function getJoins($type = null){

    // Se solicito los joins de un tipo
    if(isset($type))
      return $this->joins[strtoupper($type)];

    // Devolver todos los joins
    return $this->joins;

  }

  /**
   * [getWheres description]
   * @return [type] [description]
   */
  public function getWheres(){

    return $this->wheres;

  }

  /**
   * [getOrders description]
   * @return [type] [description]
   */
  public function getOrders(){

    return $this->orders;

  }

  /**
   * [getGroups description]
   * @return [type] [description]
   */
  public function getGroups(){

    return $this->groups;

  }

  /**
   * [getLimit description]
   * @return [type] [description]
   */
  public function getLimit(){

    return $this->limit;

  }

  /**
   * [getOffset description]
   * @return [type] [description]
   */
  public function getOffset(){

    return $this->offset;

  }

  /**
   * [getSets description]
   * @return [type] [description]
   */
  public function getSets(){

    return $this->sets;

  }

  /**
   * [getDistinct description]
   * @return [type] [description]
   */
  public function getDistinct(){

    return $this->distinct;

  }

  /**
   * [getInsertTable description]
   * @return [type] [description]
   */
  public function getInsertTable(){

    return $this->insertIntoTable;

  }

  /**
   * [getInsertFields description]
   * @return [type] [description]
   */
  public function getInsertFields(){

    return $this->insertIntoFields;

  }

  // Método para asignar array de valores por un metodo
  // Destinado al metodo ->select y ->from
  /**
   * [setArrayAttribute description]
   * @param [type] $method [description]
   * @param [type] $args   [description]
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

  // Métodos SET para algunas propiedades
  /**
   * [setFormater description]
   * @param [type] $value [description]
   */
  public function setFormater($value){

    if(isValidCallback($value))
      $this->formater = $value;

    return $this;
    
  }

  /**
   * [setModel description]
   * @param [type] $value [description]
   */
  public function setModel($value){

    $this->model = $value;
    return $this;

  }

  // Métodos SET para algunas propiedades
  /**
   * [setScheme description]
   * @param [type] $value [description]
   */
  public function setScheme($value){

    $this->scheme = $value;
    return $this;
    
  }

  /**
   * [setSelects description]
   * @param array $value [description]
   */
  public function setSelects(array $value){

    $this->selects = $value;
    return $this;

  }

  /**
   * [setFrom description]
   * @param array $value [description]
   */
  public function setFrom(array $value){

    $this->froms = $value;
    return $this;

  }

  // Asignar signar la clausula distint
  /**
   * [distinct description]
   * @return [type] [description]
   */
  public function distinct(){

    $this->distinct = true;
    return $this;

  }
  
  /**
   * [noDistinct description]
   * @return [type] [description]
   */
  public function noDistinct(){

    $this->distinct = false;
    return $this;

  }

  // Devuelve otra instancia de la consulta actual
  /**
   * [copy description]
   * @return [type] [description]
   */
  public function copy(){

    return clone($this);

  }

  // Devuelve una copia aislada de la consulta actual
  /**
   * [encapsulate description]
   * @param  string $alias [description]
   * @return [type]        [description]
   */
  public function encapsulate($alias = 'q'){

    return $this->getScheme()->q($this, $alias);

  }

  // Metodos para obtener el SQL los diferentes tipos de consulta
  /**
   * [sql description]
   * @return [type] [description]
   */
  public function sql(){
    
    return $this->getScheme()->sqlOf($this);

  }

  // Ejecuta la consulta SQL
  /**
   * [execute description]
   * @return [type] [description]
   */
  public function execute(){

    // Ejecutar desde el driver
    return $this->result = $this->getScheme()->execute($this);

  }

  // Conver a Cadena de caracteres implica devolver el SQL de la consulta
  /**
   * [__toString description]
   * @return string [description]
   */
  public function __toString(){
    
    return $this->sql();

  }

  /**
   * [create description]
   * @param  boolean $orReplace [description]
   * @return [type]             [description]
   */
  public function create($orReplace = true){

    return $this->getScheme()->createView($this, $orReplace);

  }

  /**
   * [drop description]
   * @param  boolean $ifExists [description]
   * @return [type]            [description]
   */
  public function drop($ifExists = true){

    return $this->getScheme()->dropView($this, $ifExists);

  }


  // Insertar los registros resultantes de la consulta en una table
  /**
   * [insertInto description]
   * @param  [type] $table  [description]
   * @param  array  $fields [description]
   * @return [type]         [description]
   */
  public function insertInto($table, array $fields = array()){

    $this->type = 'insert';

    $this->insertIntoTable = $table;
    $this->insertIntoFields = $fields;

    return $this->execute();

  }

  // Eliminar registros selecionados
  /**
   * [delete description]
   * @return [type] [description]
   */
  public function delete(){

    $this->type = 'delete';

    return $this->execute();

  }

  /**
   * [getTable description]
   * @param  boolean $returnTableInstance [description]
   * @return [type]                       [description]
   */
  public function getTable($returnTableInstance = false){

    // Obtener los froms de la consulta
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

  // Eliminar registros selecionados
  /**
   * [update description]
   * @return [type] [description]
   */
  public function update(){


    if(count($this->sets)==0)
      return true;

    $froms = $this->getFroms();

    foreach ($froms as $from)
      if($from instanceof AmTable){
        $from->setAutoUpdatedAt($this);
        break;
      }

    $this->type = 'update';
    
    return $this->execute();

    
  }

  // Asignar los selects
  /**
   * [select description]
   * @return [type] [description]
   */
  public function select(){

    return $this->setArrayAttribute('selectAs', func_get_args());

  }

  // Método para agregar clausula SELECT
  /**
   * [selectAs description]
   * @param  [type] $field [description]
   * @param  [type] $alias [description]
   * @return [type]        [description]
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

  // Asignar los selects
  /**
   * [from description]
   * @return [type] [description]
   */
  public function from(){

    return $this->setArrayAttribute('fromAs', func_get_args());

  }

  // Método para agregar clausula FROM
  /**
   * [fromAs description]
   * @param  [type] $from  [description]
   * @param  [type] $alias [description]
   * @return [type]        [description]
   */
  public function fromAs($from, $alias = null){

    // Asignacion del from
    if(empty($alias)){

      // Si no se indicó el parametro $alias
      if($from instanceof AmQuery){
        // Si es una consulta se agrega al final
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

  // Preparar las condiciones para agregarlas al array de condiciones
  /**
   * [parseWhere description]
   * @param  [type] $conditions [description]
   * @return [type]             [description]
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

  // Metodo para agregar condiciones
  /**
   * [where description]
   * @return [type] [description]
   */
  public function where(){

    $args = $this->parseWhere(func_get_args());

    // Parchar las condificones para luego agregarlas
    foreach($args as $where)
      $this->wheres[] = $where;

    return $this;

  }

  // Agregar condiciones con AND y OR
  /**
   * [andWhere description]
   * @return [type] [description]
   */
  public function andWhere(){

    return $this->where('and', func_get_args());

  }

  /**
   * [orWhere description]
   * @return [type] [description]
   */
  public function orWhere(){

    return $this->where('or', func_get_args());

  }

  // Eliminar todas las condiciones
  /**
   * [clearWhere description]
   * @return [type] [description]
   */
  public function clearWhere(){

    $this->conditions = array();
    return $this;

  }

  // Agregar un join
  /**
   * [join description]
   * @param  [type] $table [description]
   * @param  [type] $on    [description]
   * @param  [type] $as    [description]
   * @param  string $type  [description]
   * @return [type]        [description]
   */
  public function join($table, $on, $as, $type = 'inner'){

    // Convertir a mayusculas
    $type = strtoupper($type);

    // Si no existe la colecion de join para el tipo indicado entonces se crea
    if(!isset($this->joins[$type]))
      $this->joins[$type] = array();

    // Agregar los joins
    $this->joins[$type][] = array('table' => $table, 'on' => $on, 'as' => $as);

    return $this;

  }

  // INNER, LEFT y RIGHT Join
  /**
   * [innerJoin description]
   * @param  [type] $table [description]
   * @param  [type] $on    [description]
   * @param  [type] $as    [description]
   * @return [type]        [description]
   */
  public function innerJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'inner');

  }

  /**
   * [leftJoin description]
   * @param  [type] $table [description]
   * @param  [type] $on    [description]
   * @param  [type] $as    [description]
   * @return [type]        [description]
   */
  public function leftJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'left');

  }

  /**
   * [rigthJoin description]
   * @param  [type] $table [description]
   * @param  [type] $on    [description]
   * @param  [type] $as    [description]
   * @return [type]        [description]
   */
  public function rigthJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'right');

  }
  // Agregar campos para ordenar por en un sentido determinado
  /**
   * [orderBy description]
   * @param  [type] $orders [description]
   * @param  string $dir    [description]
   * @return [type]         [description]
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

  // Agregar campos de orden Ascendiente
  /**
   * [orderByAsc description]
   * @return [type] [description]
   */
  public function orderByAsc(){

    return $this->orderBy('ASC', func_get_args());

  }

  // Agregar campos de orden Descendiente
  /**
   * [orderByDesc description]
   * @return [type] [description]
   */
  public function orderByDesc(){

    return $this->orderBy('DESC', func_get_args());

  }

  // Agregar campos para agrupar
  /**
   * [groups description]
   * @param  array  $groups [description]
   * @return [type]         [description]
   */
  public function groups(array $groups){

    // Elimintar los campos que se agregaran de los existentes
    $this->groups = array_diff($this->groups, $groups);

    // Agregar cada campo
    foreach($groups as $group)
      $this->groups[] = $group;

    return $this;


  }

  // Agregar un campos para agrupar
  /**
   * [groupBy description]
   * @return [type] [description]
   */
  public function groupBy(){

    return $this->groups(func_get_args());

  }

  // Agregar un límite a la consulta
  /**
   * [limit description]
   * @param  [type] $limit [description]
   * @return [type]        [description]
   */
  public function limit($limit){

    $this->limit = $limit;
    return $this;

  }

  // Agregar punto de inicio para la consulta
  /**
   * [offSet description]
   * @param  [type] $offset [description]
   * @return [type]         [description]
   */
  public function offSet($offset){

    $this->offset = $offset;
    return $this;

  }

  // Agregar un SET a la consulta. Es tomado en cuenta cuando se realiza una
  // actualizacio sobre la consulta
  /**
   * [set description]
   * @param [type]  $field [description]
   * @param [type]  $value [description]
   * @param boolean $const [description]
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

  // Obtener la cantidad de registros que devolverá la consulta
  /**
   * [count description]
   * @return [type] [description]
   */
  public function count(){

    // Crear la consulta para contar
    $ret = $this->copy()
                ->setSelects(array('count' => 'count(*)'))
                ->row('array');

    // Si se generó un error devolver cero, de lo contrari
    // devolver el valor obtenido
    return $ret === false ? 0 : intval($ret['count']);

  }

  // Obtener un registro del resultado de la consulta
  /**
   * [row description]
   * @param  [type] $as       [description]
   * @param  [type] $formater [description]
   * @return [type]           [description]
   */
  public function row($as = null, $formater = null){
    
    // Obtener la fuente de datos
    $scheme = $this->getScheme();

    if(!isset($as))
      $as = $this->getModel();

    if(!isset($formater))
      $formater = $this->getFormater();

    // Se ejecuta la consulta si no se ha ejecutado la consulta
    if(null === $this->result)
      $this->execute();

    // Si se generó un error en la consulta retornar false
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

    if($as == 'array'){
      // Retornar como erray
      // $r = $r;

    }elseif($as == 'object'){
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
    }elseif(!!($className = AmScheme::model($as))){

      // Se encontró el modelo
      $r = new $className($r, false);  // Crear instancia del modelo

    }else{

      // Sino retornar null
      $r = new AmObject($r);

    }

    // Formatear el valor
    if(isset($formater))
      $r = call_user_func_array($formater, array($r));

    return $r;

  }

  // Devuelve un array con los registros resultantes de la consulta
  /**
   * [get description]
   * @param  [type] $as       [description]
   * @param  [type] $formater [description]
   * @return [type]           [description]
   */
  public function get($as = null, $formater = null){

    // Crear consulta
    $q = $this->copy();

    // Array para retorno
    $ret = array();

    // Mientras exista resgistros en cola
    while(!!($row = $q->row($as, $formater)))
      $ret[] = $row; // Agregar registros al array

    return $ret;

  }

  // Devuelve una columna de la consulta.
  /**
   * [col description]
   * @param  [type] $field [description]
   * @return [type]        [description]
   */
  public function col($field){

    // Crear la consulta
    $q = $this->copy()->selectAs($field);

    // Array para retorno
    $ret = array();

    // Mientras exista resgistros en cola
    while(!!($row = $q->row('array'))){
      $ret[] = $row[$field]; // Agregar registros al array
    }

    return $ret;

  }

  /**
   * [haveNextPage description]
   * @return [type] [description]
   */
  public function haveNextPage(){

    return !!$this->copy()
      ->offset($this->getLimit() + $this->getOffset())
      ->row('array');
      
  }

}
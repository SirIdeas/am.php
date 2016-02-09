<?php


// Revisar Meodos getRow y fromAs y attributo model
class AmQuery extends AmObject{

  // Propidades
  protected
    $modelName = 'array',     // Formato de respuesta del los registros
    $formater = null,         // Formateador el resultado de la consulta
    $type = 'select',         // Tipo de consulta
    $scheme = null,           // Fuente de datos
    $name = null,             // Nombre de la fuente
    $result = null,           // Manejador para el resultado de la consulta
    
    $selects = array(),       // Lista de campos para la clausula SELECT
    $distinct = false,        // Para solo obtener los registros diferentes
    $froms = array(),         // Lista de tablas para la clausula FROM
    $wheres = array(),        // Lista de condiciones para la clausula WHERE
    $joins = array(),         // Lista de tablas para la clausula JOIN
    $orders = array(),        // Lista de campos para la clausula ORDER BY
    $groups = array(),        // Lista de campos para la clausula GROUP BY
    $limit = null,            // Cantidad de registro: LIMIT
    $offset = null,           // Posicion de inicio: OFFSET
    $insertIntoTable = null,  // Tabla donde se insertará los valores
    $insertIntoFields = null, // Campos para el insert
    $sets = array();          // Lista de cambios SETS para consultas UPDATE

  public function getModelName(){

    return $this->modelName;

  }

  public function getFormater(){

    return $this->formater;

  }

  public function getType(){

    return $this->type;

  }

  public function getScheme(){

    return $this->scheme;

  }

  public function getName(){

    return $this->name;

  }

  public function getSelects(){

    return $this->selects;

  }

  public function getFroms(){

    return $this->froms;

  }

  // GET para los joins
  public function getJoins($type = null){

    // Se solicito los joins de un tipo
    if(isset($type))
      return $this->joins[strtoupper($type)];

    // Devolver todos los joins
    return $this->joins;

  }

  public function getWheres(){

    return $this->wheres;

  }

  public function getOrders(){

    return $this->orders;

  }

  public function getGroups(){

    return $this->groups;

  }

  public function getLimit(){

    return $this->limit;

  }

  public function getOffset(){

    return $this->offset;

  }

  public function getSets(){

    return $this->sets;

  }

  public function getDistinct(){

    return $this->distinct;

  }

  public function getInsertTable(){

    return $this->insertIntoTable;

  }

  public function getInsertFields(){

    return $this->insertIntoFields;

  }

  // Método para asignar array de valores por un metodo
  // Destinado al metodo ->select y ->from
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
  public function setFormater($value){

    if(isValidCallback($value))
      $this->formater = $value;

    return $this;
    
  }

  public function setModelName($value){

    $this->modelName = $value;
    return $this;

  }

  // Métodos SET para algunas propiedades
  public function setScheme($value){

    $this->scheme = $value;
    return $this;
    
  }

  public function setSelects(array $value){

    $this->selects = $value;
    return $this;

  }

  public function setFrom(array $value){

    $this->froms = $value;
    return $this;

  }

  // Asignar signar la clausula distint
  public function distinct(){

    $this->distinct = true;
    return $this;

  }
  
  public function noDistinct(){

    $this->distinct = false;
    return $this;

  }

  // Devuelve otra instancia de la consulta actual
  public function copy(){

    return clone($this);

  }

  // Devuelve una copia aislada de la consulta actual
  public function encapsulate($alias = 'q'){

    return $this->getScheme()->q($this, $alias);

  }

  // Metodos para obtener el SQL los diferentes tipos de consulta
  public function sql(){
    
    return $this->getScheme()->sqlOf($this);

  }

  // Ejecuta la consulta SQL
  public function execute(){

    // Ejecutar desde el driver
    return $this->result = $this->getScheme()->execute($this);

  }

  // Conver a Cadena de caracteres implica devolver el SQL de la consulta
  public function __toString(){
    
    return $this->sql();

  }

  public function create($orReplace = true){

    return $this->getScheme()->createView($this, $orReplace);

  }

  public function drop($ifExists = true){

    return $this->getScheme()->dropView($this, $ifExists);

  }


  // Insertar los registros resultantes de la consulta en una table
  public function insertInto($table, array $fields = array()){

    $this->type = 'insert';

    $this->insertIntoTable = $table;
    $this->insertIntoFields = $fields;

    return $this->execute();

  }

  // Eliminar registros selecionados
  public function delete(){

    $this->type = 'delete';

    return $this->execute();

  }

  // Eliminar registros selecionados
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
  public function select(){

    return $this->setArrayAttribute('selectAs', func_get_args());

  }

  // Método para agregar clausula SELECT
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
  public function from(){

    return $this->setArrayAttribute('fromAs', func_get_args());

  }

  // Método para agregar clausula FROM
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
  public function where(){

    $args = $this->parseWhere(func_get_args());

    // Parchar las condificones para luego agregarlas
    foreach($args as $where)
      $this->wheres[] = $where;

    return $this;

  }

  // Agregar condiciones con AND y OR
  public function andWhere(){

    return $this->where('and', func_get_args());

  }

  public function orWhere(){

    return $this->where('or', func_get_args());

  }

  // Eliminar todas las condiciones
  public function clearWhere(){

    $this->conditions = array();
    return $this;

  }

  // Agregar un join
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
  public function innerJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'inner');

  }

  public function leftJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'left');

  }

  public function rigthJoin($table, $on = null, $as = null){

    return $this->join($table, $on, $as, 'right');

  }
  // Agregar campos para ordenar por en un sentido determinado
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
  public function orderByAsc(){

    return $this->orderBy('ASC', func_get_args());

  }

  // Agregar campos de orden Descendiente
  public function orderByDesc(){

    return $this->orderBy('DESC', func_get_args());

  }

  // Agregar campos para agrupar
  public function groups(array $groups){

    // Elimintar los campos que se agregaran de los existentes
    $this->groups = array_diff($this->groups, $groups);

    // Agregar cada campo
    foreach($groups as $group)
      $this->groups[] = $group;

    return $this;


  }

  // Agregar un campos para agrupar
  public function groupBy(){

    return $this->groups(func_get_args());

  }

  // Agregar un límite a la consulta
  public function limit($limit){

    $this->limit = $limit;
    return $this;

  }

  // Agregar punto de inicio para la consulta
  public function offSet($offset){

    $this->offset = $offset;
    return $this;

  }

  // Agregar un SET a la consulta. Es tomado en cuenta cuando se realiza una
  // actualizacio sobre la consulta
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
  public function row($as = null, $formater = null){
    
    // Obtener la fuente de datos
    $scheme = $this->getScheme();

    if(!isset($as))
      $as = $this->getModelName();

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

  public function haveNextPage(){

    return !!$this->copy()
      ->offset($this->getLimit() + $this->getOffset())
      ->row('array');
      
  }

}
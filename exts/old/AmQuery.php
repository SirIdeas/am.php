<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AmQuery
 *
 * @author alex
 */
class AmQuery extends AmHash implements Reflector{
    
    // protected
    //         $name = null,
    //         $model = null,
    //         $table = null,
    //         $source = null,
    //         $result = null,
            
    //         $fields = array(),
    //         $from = array(),
    //         $conditions = array(),
    //         $orderBy = array(),
    //         $groupBy = array(),
    //         $joins = array(),
    //         $limit = null,
    //         $offset = null,
            
    //         $sets = array();
    
    // public function __toString() {
    //     return $this->sql();
    // }
    
    // public static function export() {
        
    // }
    
    // public function name($value = null){ return $this->attr('name', $value); }
    // public function source($value = null){ return $this->attr('source', $value); }
    // public function model(){ return $this->model; }
    // public function table(){ return $this->table; }
    // public function sets(){ return $this->attr('sets'); }
    // public function result($value = null){ return $this->attr('result', $value); }
    // public function fields($value = null){ return $this->attr('fields', $value); }
    
    // protected function callDriverFuntion($functionName, $return){
        
    //     list($functionName, $return, $args) = params(func_get_args(), 2, true);
        
    //     $driver = $this->source()->driver();
        
    //     if($driver){
    //         return call_user_func_array(array($driver, $functionName), $args);
    //     }
        
    //     return $return; 
    // }
    
    public function selectAsStringField($as = null){
        
        return $this->selectAs(str_replace('{model}', 'q', $this->table()->fieldToString()), $as);
        
    }
    
    // public function selectAs($field, $as = null){
        
    //     if(empty($as)){
            
    //         if (AmRegex::isNameValid($field)){
    //             $this->fields[$field] = $field;
    //         }else{
    //             $this->fields[] = $field;
    //         }
            
    //     }elseif(AmRegex::isNameValid($as)){
            
    //         $this->fields[$as] = $field;
            
    //     }else{
            
    //         $this->fields[] = $field;
            
    //     }
        
    //     return $this;
        
    // }

    // public function select(){

    //     $args = func_get_args();
        
    //     if(count($args) == 0){

    //         return $this->fields;

    //     }

    //     foreach($args as $arg){

    //         if(is_array($arg)){

    //             foreach($arg as $as => $field){
    //                 $this->selectAs($field, $as);
    //             }

    //         }else{

    //             $this->selectAs($arg);

    //         }

    //     }

    //     return $this;
    // }

    // public function fromAs($from, $as = null){
        
    //     if(!isset($this->table)){
            
    //         if($from instanceof AmTable){
                
    //             $this->table = $from;
                
    //         }elseif($from instanceof AmQuery){
                
    //             $this->table = $from->table();
                
    //         }
            
    //     }

    //     if(empty($this->model)){

    //         if($from instanceof AmTable){
                
    //             $this->model = $from->modelName();

    //         }elseif($from instanceof AmQuery){
                
    //             $table = $from->table();
                
    //             if(isset($table) && $table instanceof AmTable){
    //                 $this->model = $table->modelName();
    //             }
                
    //         }elseif(is_string($from) && AmRegex::isNameValid($from)){

    //             $this->model = $from;

    //         }
            
    //     }
        
    //     if(empty($as)){

    //         if($from instanceof AmQuery){

    //             $this->from[] = $from;

    //         }elseif($from instanceof AmTable){

    //             $this->from[$from->tableName()] = $from;

    //         }elseif (AmRegex::isNameValid($from)){

    //             $this->from[$from] = $from;

    //         }else{
                
    //             $this->from[] = $from;
                
    //         }

    //     }elseif(AmRegex::isNameValid($as)){

    //         $this->from[$as] = $from;

    //     }else{

    //         $this->from[] = $from;

    //     }

    //     return $this;
        
    // }
    
    // public function from(){

    //     $args = func_get_args();

    //     if(empty($args)){

    //         return $this->from;

    //     }

    //     foreach($args as $arg){

    //         if(is_array($arg)){

    //             foreach($arg as $as => $from){
    //                 $this->fromAs($from, $as);
    //             }

    //         }else{

    //             $this->fromAs($arg);

    //         }

    //     }

    //     return $this;

    // }

    // public function orders($dir = null, array $orders = array()){

    //     if(empty($dir)){

    //         return $this->orderBy;

    //     }

    //     foreach($orders as $order){

    //         unset($this->orderBy[$order]);
    //         $this->orderBy[$order] = $dir;

    //     }

    //     return $this;
        
    // }

    // public function orderBy(){

    //     return $this->orders('ASC', func_get_args());

    // }

    // public function orderByDesc(){

    //     return $this->orders('DESC', func_get_args());

    // }

    // public function groups(array $groups = array()){
        
    //     if(empty($groups)){

    //         return $this->groupBy;

    //     }

    //     $this->groupBy = array_diff($this->groupBy, $groups);

    //     foreach($groups as $group){
    //         $this->groupBy[] = $group;
    //     }

    //     return $this;
        
    // }

    // public function groupBy(){

    //     return $this->groups(func_get_args());
        
    // }

    // public function limit($limit = null){

    //     if(isset($limit)){

    //         $this->limit = $limit;
    //         return $this;

    //     }
        
    //     return $this->limit;
        
    // }

    // public function offSet($offset = null){

    //     if(isset($offset)){

    //         $this->offset = $offset;
    //         return $this;
            
    //     }

    //     return $this->offset;

    // }

    // public function joins($type = null, $table = null, $on = null, $as = null){

    //     if(empty($type)){

    //         return $this->joins;
            
    //     }
        
    //     $type = strtoupper($type);

    //     if(!isset($this->joins[$type])){
            
    //         $this->joins[$type] = array();

    //     }

    //     if(empty($table)){

    //         return $this->joins[$type];

    //     }

    //     $this->joins[$type][] = array('table' => $table, 'on' => $on, 'as' => $as);
    //     return $this;
        
    // }

    // public function innerJoin($table, $on = null, $as = null){

    //     return $this->joins('inner', $table, $on, $as);

    // }

    // public function leftJoin($table, $on = null, $as = null){

    //     return $this->joins('left', $table, $on, $as);

    // }

    // public function rigthJoin($table, $on = null, $as = null){

    //     return $this->joins('right', $table, $on, $as);
        
    // }

    // protected function parseWhere($conditions){
        
    //     if(!is_array($conditions)){
    //         return $conditions;
    //     }

    //     $ret = array();
    //     $nextPrefijo = '';
    //     $nextUnion = 'AND';

    //     foreach($conditions as $condition){
            
    //         if(!is_array($condition)){
                
    //             $upperCondition = strtoupper($condition);

    //         }elseif(count($condition)==3 && strtoupper ($condition[1]) == 'IN'){
                
    //             $condition = array($condition[0], $condition[2]);
                
    //             $upperCondition = 'IN';
                
    //         }else{

    //             $upperCondition = '';

    //         }

    //         if($upperCondition == 'AND' || $upperCondition == 'OR'){

    //             $nextUnion = $upperCondition;

    //         }elseif($upperCondition == 'NOT'){

    //             $nextPrefijo = $upperCondition;
                
    //         }else{
                
    //             $ret[] = array(
    //                 'union' => $nextUnion,
    //                 'prefix' => $nextPrefijo,
    //                 'condition' => $upperCondition == 'IN'? $condition : $this->parseWhere($condition),
    //                 'isIn' => $upperCondition == 'IN'
    //             );
                
    //             $nextPrefijo = '';

    //         }

    //     }

    //     return $ret;

    // }
    
    // public function clearWhere(){
    //     $this->conditions = array();
    // }

    // public function where(){
        
    //     $args = func_get_args();
        
    //     if(empty($args)){

    //         return $this->conditions;
            
    //     }
        
    //     foreach($this->parseWhere($args) as $condition){

    //         $this->conditions[] = $condition;
            
    //     }

    //     return $this;
        
    // }
    
    // public function andWhere(){
        
    //     return $this->where('and', func_get_args());
        
    // }
    
    // public function orWhere(){
        
    //     return $this->where('or', func_get_args());
        
    // }

    // public function set($field, $value, $const = true){

    //     $this->sets[] = array('field' => $field, 'value' => $value, 'const' => $const);
    //     return $this;

    // }
    
    // public function insertInto($table, array $fields = array()){ return $this->callDriverFuntion('insertInto', false, $this->source(), $table, $this, $fields); }
    
    // public function update(){
        
    //     return (count($this->sets()) === 0) || (false !== $this->callDriverFuntion('executeSql', false, $this->source(), $this->updateSql()));
        
    // }
    
    // public function delete(){
        
    //     return false !== $this->callDriverFuntion('executeSql', false, $this->source(), $this->deleteSql());
        
    // }
    
    private function reference($method, $model, $as = null, $field = null, $tableTo = 'q'){
        
        $relations = $this->table()->$method();
        
        if(isset($relations->$model)){
            
            $rel = $relations->$model;
            $table = Am::table($rel->table(), $rel->source());
            
            if(!isset($field)){
                $field = $table->fieldToString();
            }
            
            if(!isset($as)){
                $as = $model;
            }
            
            $field = str_replace('{model}', "_{$model}_", $field);
            
            $q = $table->qAll("_{$model}_")->selectAs($field);
            
            foreach($rel->columns() as $to => $from){
                $q->andWhere("_{$model}_.$from = $tableTo.$to");
            }
            
            $this->selectAs($q, $as);
            
            return $q;
            
        }
        
        return $this;
    }
    
    public function referenceBy($model, $as = null, $field = 'count(*)', $tableTo = 'q'){
        $this->reference('referencesBy', $model, $as, $field, $tableTo);
    }
    
    public function referenceTo($model, $as = null, $field = null, $tableTo = 'q'){
        $this->reference('referencesTo', $model, $as, $field, $tableTo);
    }
    
//     public function countQuery(){
//         return $this->getCopy()->fields(array('count' => 'count(*)'));
//     }

//     public function getCopy(){
//         return clone($this);
//     }

//     public function getAlone($as = 'q'){
//         return $this->source()->newQuery($this, $as);
//     }
    
//     public function countSql(){
//         return $this->sql();
//     }
    
//     public function count(){

//         $ret = $this->countQuery()->getRow('hash');

//         return $ret === false ? 0 : intval($ret->count);
        
//     }

//     public function execute(){
        
//         $s = $this->source();
        
//         $s->selectDatabase();
        
//         return $this->result = $this->callDriverFuntion('executeSql', false, $s, $this->sql());

//     }

//     public function getRow($as = null, $formater = null){
        
//         $s = $this->source();
        
//         if(null === $this->result()){

//             $this->execute();

//         }

//         $r = $this->result() !== false ? $this->callDriverFuntion('getFetchAssoc', false, $this) : false;

//         if($r !== false){
            
//             if(!isset($as)){
                
//                 $className = Am::model($this->model(), $s->name());
                
//                 if (false !== $className){
                    
//                     $r['isNew'] = false;
//                     $r = new $className($r);
                    
//                 }else{
                    
//                     $r = new AmHash($r);
                    
//                 }
            
//             }elseif($as == 'array'){
                
// //                $r = $r;
                
//             }elseif($as == 'object'){
                
//                 $r = (object)$r;
                
//             }elseif($as == 'hash'){
                
//                 $r = new AmHash($r);
                
//             }else{
                
//                 $r = null;
//             }
            
//             if(isset($formater))
//                 $r = call_user_func_array($formater, array($r));

//         }

//         return $r;
        
//     }
    
//     public function getCol($field){
        
//         $q = $this->source()->newQuery($this)->selectAs($field);
        
//         $ret = array();
//         while(false !== ($row = $q->getRow('array'))){
            
//             $ret[] = $row[$field];
            
//         }
        
//         return $ret;
        
//     }
    
//     public function getResult($as = null, $formater = null){
        
//         $q = $this->source()->newQuery($this);
        
//         $ret = array();
//         while(false !== ($row = $q->getRow($as, $formater))){
            
//             $ret[] = $row;
            
//         }
        
//         return $ret;
        
//     }
    
    // public function selectSql($with = true){ return $this->callDriverFuntion('selectSql', '', $this, $with); }
    // public function fromSql($with = true){ return $this->callDriverFuntion('fromSql', '', $this, $with); }
    // public function whereSql($with = true){ return $this->callDriverFuntion('whereSql', '', $this, $with); }
    // public function ordersSql($with = true){ return $this->callDriverFuntion('ordersSql', '', $this, $with); }
    // public function groupsSql($with = true){ return $this->callDriverFuntion('groupsSql', '', $this, $with); }
    // public function limitSql($with = true){ return $this->callDriverFuntion('limitSql', '', $this, $with); }
    // public function offsetSql($with = true){ return $this->callDriverFuntion('offsetSql', '', $this, $with); }
    // public function joinsSql(){ return $this->callDriverFuntion('joinsSql', '', $this); }
    // public function sql(){ return $this->callDriverFuntion('sql', '', $this); }
    
    // public function insertIntoSql($table, array $fields = array()){ return $this->callDriverFuntion('insertIntoSql', '', $this->source(), $table, $this, $fields); }
    // public function setsSql($with = true){ return $this->callDriverFuntion('setsSql', '', $this, $with); }
    // public function updateSql(){ return $this->callDriverFuntion('updateSql', '', $this); }
    // public function deleteSql(){ return $this->callDriverFuntion('deleteSql', '', $this); }
    
    // public function createSql(){}
    // public function create(){}
    
    // public function dropSQL(){}
    // public function drop(){}

}

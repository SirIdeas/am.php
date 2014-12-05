<?php

/*******************************************************************************
 * Aastraccion de las tablas de la Base de datos
 */
class AmTable extends AmHash{
    
    // protected
    //         $source = null,
    //         $name = null,
    //         $tableName = null,
    //         $modelName = null,
    //         $validators = array(),
    //         $fields = null,
    //         $pks = array(),
    //         $referencesTo = array(),
    //         $referencesBy = array(),
    //         $engine = null,
    //         $charset = null,
    //         $collate = null;

    /***************************************************************************
     * El constructor
     */
    // public function __construct($params = null){
        
    //     $params = AmHash::parse($params);
        
    //     $source = is_string($params['source']) ? Am::source($params['source']) : $params['source'];
        
    //     if(get_class($this) != __CLASS__){
                    
    //         $tableClassName = _tcc($params['tableName'], true);
    //         $params = readConf("{$source->folderConfFilesPath()}/{$source->prefix()}$tableClassName");
    //         $params['source'] = $source;
        
    //     }
        
    //     parent::__construct($params);
        
    //     if(!isset($this->modelName)){
    //         $this->modelName = $this->tableName();
    //     }
        
    //     $this->describeTableInner(
    //             itemOr($params, 'referencesTo', array()),
    //             itemOr($params, 'referencesBy', array()),
    //             itemOr($params, 'pks', array()),
    //             itemOr($params, 'fields', array()));
        
    //     $this->initialize();
        
    // }
    
    public function fieldToString(){
        return 'concat({model}.' . implode(', "/", {model}.', $this->pks()) . ')';
    }
    
    // public function initialize(){}
    
    // public function tableName(){ return $this->tableName; }
    // public function modelName(){ return $this->modelName; }
    // public function source(){ return $this->source; }
    // public function referencesTo(){ return $this->referencesTo; }
    // public function referencesBy(){ return $this->referencesBy; }
    // public function pks(){ return $this->pks; }
    // public function engine(){ return $this->engine;}
    // public function charset(){ return $this->charset;}
    // public function collate(){ return $this->collate;}
    
    // public function fields($offset = null, AmField $f = null){
        
    //     if(isset($offset)){
            
    //         if(isset($f)){
                
    //             $this->fields->$offset = $f;
    //             return $this;
                
    //         }
    //         if(isset($this->fields->$offset)){
    //             return $this->fields->$offset;
    //         }
            
    //         return false;
            
    //     }
        
    //     return $this->fields;
        
    // }

    // public function isPk($fieldName){

    //     return in_array($fieldName, $this->pks());

    // }
    
    // public function describeTable(){
        
    //     $this->describeTableInner(
    //             $this->getTablesForeignKeys(),
    //             $this->getTablesReferences(),
    //             $this->getTablesPrimaryKey(),
    //             $this->getTablesColumns());
        
    // }
    
    // protected function describeTableInner(array $referencesTo = array(), array $referencesBy = array(), array $pks = array(), array $fields = array()){
        
    //     $this->referencesTo = new stdClass;
        
    //     foreach ($referencesTo as $name => $values){
    //         $this->referencesTo->$name = new AmRelation($values);
    //     }
        
    //     $this->referencesBy = new stdClass;
        
    //     foreach ($referencesBy as $name => $values){
    //         $this->referencesBy->$name = new AmRelation($values);
    //     }
        
    //     $this->fields = new stdClass;
        
    //     foreach($fields as $column){
            
    //         $column['primaryKey'] = in_array(itemOr($column, 'name'), $pks);
            
    //         $type = $this->callDriverFuntion('getTypeOf', itemOr($column, 'type'));
            
    //         if(false !== $type){
    //             $column['type'] = $type;
    //         }
            
    //         $this->addField(new AmField($column));
            
    //     }
        
    // }
    
    // protected function callDriverFuntion(){
        
    //     list($functionName, $args) = params(func_get_args(), 1, true);
        
    //     $driver = $this->source()->driver();
        
    //     if($driver){
    //         return call_user_func_array(array($driver, $functionName), $args);
    //     }
        
    //     return ''; 
    // }

    /***************************************************************************
     * Control de las tablas en la base de datos
     */
    // public function getTablesPrimaryKeySql(){ return $this->callDriverFuntion('getTablesPrimaryKeySql', $this); }
    
    // public function getTablesPrimaryKey(){
        
    //     $ret = array();
    //     $pks = $this->source()->newQuery($this->getTablesPrimaryKeySql())->getResult();
        
    //     foreach($pks as $pk){
    //         $ret[] = $pk->name;
    //     }
        
    //     return $ret;
        
    // }
    
    // public function getTablesColumnsSql(){ return $this->callDriverFuntion('getTablesColumnsSql', $this); }
    
    // public function getTablesColumns(){
        
    //     return $this->source()->newQuery($this->getTablesColumnsSql())->getResult('array');
        
    // }
    
    // public function getTablesForeignKeysSql(){ return $this->callDriverFuntion('getTablesForeignKeysSql', $this); }
    
    // public function getTablesForeignKeys(){
        
    //     $ret = array();
    //     $s = $this->source();
    //     $sourceName = $s->name();
    //     $fks = $s->newQuery($this->getTablesForeignKeysSql())->getResult();
        
    //     foreach($fks as $fk){
            
    //         $name = explode('.', $fk->name);
    //         $name = $name[count($name)-1];
            
    //         if(!isset($ret[$name])){
    //             $ret[$name] = array(
    //                 'source' => $sourceName,
    //                 'table' => $fk->toTable,
    //                 'columns' => array()
    //             );
    //         }
            
    //         $ret[$name]['columns'][$fk->columnName] = $fk->toColumn;
    //     }
        
    //     return $ret;
        
    // }
    
    // public function getTablesReferencesSql(){ return $this->callDriverFuntion('getTablesReferencesSql', $this); }
    
    // public function getTablesReferences(){
        
    //     $ret = array();
    //     $s = $this->source();
    //     $sourceName = $s->name();
    //     $fks = $s->newQuery($this->getTablesReferencesSql())->getResult();
        
    //     foreach($fks as $fk){
            
    //         $name = explode('.', $fk->name);
    //         $name = $name[0];
            
    //         if(!isset($ret[$name])){
    //             $ret[$name] = array(
    //                 'source' => $sourceName,
    //                 'table' => $fk->fromTable,
    //                 'columns' => array()
    //             );
    //         }
            
    //         $ret[$name]['columns'][$fk->toColumn] = $fk->columnName;
    //     }
        
    //     return $ret;
        
    // }
    
    // Crea la tabla
    // public function createTableSql(){ return $this->callDriverFuntion('createTableSql', $this); }
    
    // public function createTable(){
    //     return $this->source()->executeSql($this->createTableSql()) !== false;
    // }

    // // Elimina la Base de datos
    // public function dropTableSql(){ return $this->callDriverFuntion('dropTableSql', $this); }
    
    // public function dropTable(){
    //     return $this->source()->executeSql($this->dropTableSql()) !== false;
    // }

    // Indica si la tabla existe
    // public function existsTable(){
    //     return $this->getTableDescription($this->tableName()) !== false;
    // }

    /**************************************************************************
     * Manejo de la data en la tabla
     */
     
    // // Devuelve un Query que devuelve todos los registros de la Tabla
    // public function qAll($as = 'q', $withFields = false){
        
    //     if(is_bool($as)){
    //         $withFields = $as;
    //         $as = 'q';
    //     }
        
    //     $q = $this->source()->newQuery($this, $as);
        
    //     if($withFields){
            
    //         $fields = array_keys((array)$this->fields());
    //         $fields = array_combine($fields, $fields);
    //         $q->fields($fields);
        
    //     }
        
    //     return $q;
        
    // }
    
    // public function findBy($field, $value){
    //     return $this->qAll()->where("$field='$value'");
    // }
    
    // public function findAllBy($field, $value){
    //     return $this->findBy($field, $value)->getResult();
    // }
    
    // public function findOneBy($field, $value){
    //     return $this->findBy($field, $value)->getRow();
    // }
    
    // public function queryFind($id){
        
    //     $q = $this->qAll();
    //     $pks = $this->pks();
        
    //     if(is_array($id) && !AmArray::isAssocArray($id)){
    //         if(count($pks) === count($id)){
    //             $id = array_combine($pks, $id);
    //         }else{
    //             return null;
    //         }
    //     }
        
    //     if(1 == count($pks) && !is_array($id)){
    //         $id = array($pks[0] => $id);
    //     }
        
    //     foreach($pks as $pk){
            
    //         if(!isset($id[$pk]) && !array_key_exists($pk, $id)){
    //             return null;
    //         }
            
    //         $q->where("{$this->fields($pk)->name()}='{$id[$pk]}'");
            
    //     }
        
    //     return $q;
        
    // }
    
    // // Regresa un objeto con AmModel con el registro solicitado
    // public function find($id){
        
    //     $q = $this->queryFind($id);
    //     $r = isset($q)? $q->getRow() : false;
        
    //     return $r === false ? null : $r;
        
    // }
    
    public function sqlAllRecord(){
        
        $all = $this->qAll();
        
        $sql = '';
        
        while(false !== ($row = $all->getRow())){

            $sql .= $row->insertIntoSql() . "\n";

        }
        
        return $sql;
        
    }
    
    // public function addField(AmField $f, $as = null){
        
    //     $fieldName = $f->name();
    //     $name = empty($as) ? $fieldName : $as;
        
    //     if(empty($fieldName)){
    //         $f->name($name);
    //     }
        
    //     $this->fields($name, $f);
        
    //     if($f->primaryKey()){
    //         $this->addPk($name);
    //     }
        
    // }
    
    // public function addPk($fieldName){
        
    //     if(!in_array($fieldName, $this->pks())){
            
    //         $this->pks[] = $fieldName;
                    
    //     }
        
    //     $this->fields($fieldName)->primaryKey(true);
        
    // // }
    
    // public function dropValidator($name, $validatorName = null){
    //     if(isset($this->validators[$name][$validatorName])){
    //         unset($this->validators[$name][$validatorName]);
    //     }else if(isset($this->validators[$name])){
    //         unset($this->validators[$name]);
    //     }
    // }
    
    // public function validators($name = null, $validatorName = null, $validator = null, $options = array()){
        
    //     if($validatorName instanceof BaseValidator){
    //         return $this->validators($name, null, $validatorName);
    //     }
        
    //     if (isset($name)){
            
    //         if(isset($validator)){
                
    //             if(!$validator instanceof BaseValidator){
                    
    //                 Am::validator($validator);
    //                 $validator = _tcc($validator, true) . "Validator";
    //                 $validator = new $validator($options);
                    
    //             }
                
    //             $validator->fieldName($name);
                
    //             if(isset($validatorName)){
    //                 return $this->validators[$name][$validatorName] = $validator;
    //             }
                
    //             return $this->validators[$name][] = $validator;
    //         }
            
    //         if(isset($validatorName)){
    //             return itemOr(itemOr($this->validators, $name, array()), $validatorName, null);
    //         }
            
    //         return itemOr($this->validators, $name, array());
    //     }
        
    //     return $this->validators;
    // }
    
    public function fieldsName(){
        
        $fields = array_keys((array)$this->fields());
        $fields = array_combine($fields, $fields);
        
        foreach($this->referencesTo() as $i => $rel){
            $fields = array_merge($fields, array_fill_keys(array_keys($rel->columns()), $i));
        }
        
        foreach($this->referencesBy() as $i => $rel){
            $fields[$i] = $i;
        }
        
        return $fields;
        
    }
    
    // public function insertIntoSql($values, array $fields = array()){ return $this->callDriverFuntion('insertIntoSql', $this->source(), $this, $values, $fields); }
    // public function insertInto($values, array $fields = array()){ return $this->callDriverFuntion('insertInto', $this->source(), $this, $values, $fields); }
    
    // public function truncateSql(){ return $this->callDriverFuntion('truncateSql', $this->source(), $this); }
    // public function truncate(){
        
    //     return $this->source()->executeSql($this->truncateSql()) !== false;
        
    // }
    
    // public function toArray(){
        
    //     $fields = array();
    //     foreach($this->fields() as $offset => $field){
    //         $fields[$offset] = $field->toArray();
    //     }
        
    //     $referencesTo = array();
    //     foreach($this->referencesTo() as $offset => $field){
    //         $referencesTo[$offset] = $field->toArray();
    //     }
        
    //     $referencesBy = array();
    //     foreach($this->referencesBy() as $offset => $field){
    //         $referencesBy[$offset] = $field->toArray();
    //     }
        
    //     return array(
    //         'tableName' => $this->tableName(),
    //         'modelName' => $this->modelName(),
    //         'engine' => $this->engine(),
    //         'charset' => $this->charset(),
    //         'collate' => $this->collate(),
    //         'fields' => $fields,
    //         'pks' => $this->pks(),
    //         'referencesTo' => $referencesTo,
    //         'referencesBy' => $referencesBy,
    //     );
        
    // }
    
    // public function classTableName(){
    //     return "{$this->classModelName()}Table";
    // }
    
    // public function classTableBaseName(){
    //     return "{$this->classTableName()}Base";
    // }
    
    // public function classModelName(){
    //     return $this->source()->prefix() . _tcc($this->modelName(), true);
    // }
    
    // public function classModelBaseName(){
    //     return "{$this->classModelName()}Base";
    // }
    
    // public function confFilePath(){
    //     return $this->source()->folderConfFilesPath() . "/{$this->classModelName()}";
    // }
    
    // public function classTablePath(){
    //     return $this->source()->folderTablesPath() . "/{$this->classTableName()}.php";
    // }
    
    // public function classTableBasePath(){
    //     return $this->source()->folderTablesBasePath() . "/{$this->classTableBaseName()}.php";
    // }
    
    // public function classModelPath(){
    //     return $this->source()->folderModelsPath() . "/{$this->classModelName()}.php";
    // }
    
    // public function classModelBasePath(){
    //     return $this->source()->folderModelsBasePath() . "/{$this->classModelBaseName()}.php";
    // }
    
    // public function existsFileConf(){ return existsConf($this->confFilePath()); }
    
    // public function existsFileTable(){ return is_file($this->classTablePath()); }
    // public function existsFileTableBase(){ return is_file($this->classTableBasePath()); }
    
    // public function existsFileModel(){ return is_file($this->classModelPath()); }
    // public function existsFileModelBase(){ return is_file($this->classModelBasePath()); }
    
    /***************************************************************************
     * Crea el archivo que contiene la estructura de la tabla
     */
    // public function createFileConf($rw = false){
        
    //     if(!$this->existsFileConf() || $rw){
            
    //         writeConf($this->confFilePath(), $this->toArray());
            
    //         return true;
    //     }
        
    //     return false;

    // }

    // /***************************************************************************
    //  * Crea el archivo que contiene clase para la tabla
    //  */
    // public function createFileTable(){
        
    //     // Verificar que no exista
    //     if(!$this->existsFileTable()){

    //         // Crear la Carpeta
    //         file_put_contents($this->classTablePath(), "<?php\n\nclass {$this->classTableName()} extends {$this->classTableBaseName()}{\n\n}\n");
    //         return true;

    //     }
    //     return false;

    // }

    // /***************************************************************************
    //  * Crea el archivo que contiene clase para la tabla
    //  */
    // public function createFileTableBase(){
        
    //     Am::core('AmGenerator');
        
    //     file_put_contents($this->classTableBasePath(), "<?php\n\n" . AmGenerator::classTableBase($this));
    //     return true;

    // }

    // /***************************************************************************
    //  * Crea el archivo que contiene clase para la tabla
    //  */
    // public function createFileModel(){
        
    //     // Verificar que no exista
    //     if(!$this->existsFileModel()){

    //         // Crear la Carpeta
    //         file_put_contents($this->classModelPath(), "<?php\n\nclass {$this->classModelName()} extends {$this->classModelBaseName()}{\n\n}\n");
    //         return true;

    //     }
    //     return false;

    // }
    
    // /***************************************************************************
    //  * Crea el archivo que contiene clase para la tabla
    //  */
    // public function createFileModelBase(){
        
    //     Am::core('AmGenerator');
        
    //     file_put_contents($this->classModelBasePath(), "<?php\n\n" . AmGenerator::classModelBase($this));
    //     return true;
        
    // }
    
    // public function createFiles(){
    //     return array(
    //         'fileConf' => $this->createFileConf(),
    //         'fileTable' => $this->createFileTable(),
    //         'fileTableBase' => $this->createFileTableBase(),
    //         'fileModel' => $this->createFileModel(),
    //         'fileModelBase' => $this->createFileModelBase()
    //     );
    // }

}

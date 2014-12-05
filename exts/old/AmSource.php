<?php

Am::core('AmFileSystem');

/*******************************************************************************
 * Abstraccion para las fuentes de datos
 */
class AmSource extends AmHash{
    
    // protected
    //         $name = null,
    //         $database = null,
    //         $driver = null,
    //         $handle = null,
    //         $prefix = null,
            
    //         $server = null,
    //         $port = null,
    //         $user = null,
    //         $pass = null,
            
    //         $charset = null,
    //         $collate = null,
            
    //         $tables = array();

    /***************************************************************************
     * En el constructos se asigna el driver, se preparan los nombres de las
     * tablas y se preparan los paramstros
     */
    // public function  __construct($name = null, $params = array()){

    //     parent::__construct($params);
        
    //     $this->name = $name;
    //     $database = $this->database();
    //     $this->database(isset($database) ? $database : $name);

    //     $driver = $this->driver();
    //     // Configuracion del driver
    //     if(!$driver instanceof AmDriver && !empty($driver)){
            
    //         $this->driver = Am::driver($driver);
            
    //     }
        
    // }

    // public function __destruct() {
        
    //     $this->disconnect();

    // }
    
    // protected function callDriverFuntion(){
        
    //     list($functionName, $return, $args) = params(func_get_args(), 2, true);
        
    //     $driver = $this->driver();
        
    //     if($driver){
    //         return call_user_func_array(array($driver, $functionName), $args);
    //     }
        
    //     return $return; 
    // }
    
    // public function name(){ return $this->name; }
    // public function prefix(){ return $this->prefix; }
    // public function driver(){ return $this->driver; }
    // public function handle(){ return $this->handle; }
    
    // public function database(){ return $this->database; }
    // public function server(){ return $this->server; }
    // public function port(){ return $this->port; }
    // public function user(){ return $this->user; }
    // public function pass(){ return $this->pass; }
    
    // public function charset(){ return $this->charset; }
    // public function collage(){ return $this->collage; }
    
    // public function tables($offset = null, AmTable $t = null){
        
    //     if(isset($offset)){
            
    //         if(isset($t)){
                
    //             $this->tables[$offset] = $t;
    //             return $this;
                
    //         }
            
    //         return itemOr($this->tables, $offset);
            
    //     }
        
    //     return $this->tables;
        
    // }
    
    // public function connect(){
        
    //     $driver = $this->driver();
        
    //     if(isset($this->handle)){
    //         return true;
    //     }
        
    //     if($driver instanceof AmDriver && false !== ($this->handle = $driver->createConnexion($this))){
    //         return true;
    //     }
        
    //     $this->handle = null;
    //     return false;
        
    // }
    
    // public function disconnect(){
        
    //     if(isset($this->handle) && isset($driver)){
            
    //         $ret = $this->callDriverFuntion('closeConnexion', false, $this);
    //         $this->handle = null;
            
    //         return $ret;
            
    //     }
        
    //     return false;
        
    // }
    
    // public function reconnect(){
        
    //     $this->disconnect();
    //     return $this->connect();
        
    // }
    
    // public function error(){
    //     return $this->callDriverFuntion('error', '', $this);
    // }
    
    // public function errNo(){
    //     return $this->callDriverFuntion('errNo', -1, $this);
    // }
    
    // **************************************************************************
    //  * Carpetas utilizadas por la fuente
     
    // public function folderPath(){ return SITE_FOLDER . '/orm/' . $this->name(); }
    // public function folderConfFilesPath(){ return $this->folderPath() . '/conf'; }
    // public function folderTablesPath(){ return $this->folderPath() . '/tables'; }
    // public function folderTablesBasePath(){ return $this->folderTablesPath() . '/base'; }
    // public function folderModelsPath(){ return $this->folderPath() . '/models'; }
    // public function folderModelsBasePath(){ return $this->folderModelsPath() . '/base'; }
    
    // public function existsfolderPath(){ return is_dir($this->folderPath()); }
    // public function existsfolderConfFilesPath(){ return is_dir($this->folderConfFilesPath()); }
    // public function existsfolderTablesPath(){ return is_dir($this->folderTablesPath()); }
    // public function existsfolderTablesBasePath(){ return is_dir($this->folderTablesBasePath()); }
    // public function existsfolderModelsPath(){ return is_dir($this->folderModelsPath()); }
    // public function existsfolderModelsBasePath(){ return is_dir($this->folderModelsBasePath()); }
    
    // /***************************************************************************
    //  * Crea la Carpeta donde se alojaran los archivos de configuracion de los
    //  * Modelos pertenecientes a esta BD
    //  */
    
    // private static function _createFolder($path){
    //     return AmFileSystem::mkdir($path);
    // }
    
    // public function createFolder(){ return self::_createFolder($this->folderPath()); }
    // public function createFolderConf(){ return self::_createFolder($this->folderConfFilesPath()); }
    // public function createFolderTables(){ return self::_createFolder($this->folderTablesPath()); }
    // public function createFolderTablesBase(){ return self::_createFolder($this->folderTablesBasePath()); }
    // public function createFolderModels(){ return self::_createFolder($this->folderModelsPath()); }
    // public function createFolderModelsBase(){ return self::_createFolder($this->folderModelsBasePath()); }
    
    // public function createFolders(){
        
    //     return array(
    //         'folder' => $this->createFolder(),
    //         'folderTables' => $this->createFolderTables(),
    //         'folderTablesBase' => $this->createFolderTablesBase(),
    //         'folderModels' => $this->createFolderModels(),
    //         'folderModelsBase' => $this->createFolderModelsBase(),
    //         'folderConf' => $this->createFolderConf()
    //     );
        
    // }
    
    // Ejacuta un SQL
    public function executeSqlFile($fileName){
        
        $sql = file_get_contents($fileName);

        $sql = explode("\n", $sql);

        foreach($sql as $sqlCommand){
            
            $sqlCommand = trim($sqlCommand);

            if(!empty($sqlCommand)){
                
                $this->executeSql($sqlCommand);
            }

        }
        
    }
    
    // // Ejacuta un SQL
    // public function executeSql($sql){ return $this->callDriverFuntion('executeSql', '', $this, $sql); }

    // /***************************************************************************
    //  * Manipulacion de la BD
    //  */
    // // Crea la BD
    // public function createDatabaseSql(){ return $this->callDriverFuntion('createDatabaseSql', '', $this); }
    
    // public function createDatabase(){
        
    //     if(!$this->existsDatabase()){
            
    //         $sql = $this->createDatabaseSql();
    //         return $this->callDriverFuntion('executeSql', false, $this, $sql) !== false;
            
    //     }
        
    //     return false;
    
    // }

    // // Elimina la BD
    // public function dropDatabaseSql(){ return $this->callDriverFuntion('dropDatabaseSql', '', $this); }
    
    // public function dropDatabase(){
        
    //     if(!$this->existsDatabase()){
            
    //         $sql = $this->dropDatabaseSql();
    //         return $this->callDriverFuntion('executeSql', false, $this, $sql) !== false;

    //     }
        
    //     return false;
        
    // }

    // // Indica si existe la BD
    // public function selectDatabaseSql(){ return $this->callDriverFuntion('selectDatabaseSql', '', $this); }
    
    // public function selectDatabase(){
        
    //     $sql = $this->selectDatabaseSql();
        
    //     return $this->callDriverFuntion('executeSql', false, $this, $sql, false) !== false;
        
    // }
    
    // public function existsDatabase(){
        
    //     return $this->selectDatabase();
        
    // }
    
    // /***************************************************************************
    //  * Manipulacion de la tablas
    //  */
    // public function getTablesSql(){ return $this->callDriverFuntion('getTablesSql', '', $this); }
    
    // public function getTables(){
        
    //     return $this->newQuery($this->getTablesSql())->getResult();
        
    // }
    
    // public function getTableDescription($table){
    //     return $this->newQuery($this->getTablesSql())->where("tableName = '$table'")->getRow();
    // }
    
    // // Devuelve la descripcion de una tabla
    // public function describeTable($tableName){
        
    //     $table = $this->getTableDescription($tableName);
        
    //     if($table !== false){
        
    //         $table->source = $this;

    //         $table = new AmTable($table);
    //         $table->describeTable();
            
    //         return $table;
        
    //     }
        
    //     return false;
        
    // }

    // /***************************************************************************
    //  * Manipulacion de la Consultas
    //  */

    // // Crea una instancia de una consulta
    // public function newQuery($from = null, $as = 'q'){
        
    //     $q = new AmQuery();
        
    //     $q->source($this);
        
    //     return empty($from) ? $q : $q->fromAs($from, $as);
        
    // }
    
    public function getExistingModels(){
        
        $ret = null;
        
        $paths = array(
            $this->folderConfFilesPath() => '/^.+\/(.+)\\'.'.php'.'$/',
            $this->folderModelsPath() => '/^.+\/(.+)\\'.'.php'.'.php$/',
            $this->folderModelsBasePath() => '/^.+\/(.+)Base\\'.'.php'.'.php$/',
            $this->folderTablesPath() => '/^.+\/(.+)Table\\'.'.php'.'.php$/',
            $this->folderTablesBasePath() => '/^.+\/(.+)TableBase\\'.'.php'.'.php$/',
        );
        
        foreach($paths as $path => $regex){
            
            $retLocal = glob("$path/*");
            
            foreach($retLocal as $i => $model){
                if(preg_match($regex, $model, $matches)){
                    $retLocal[$i] = $matches[1];
                }
            }
            
            if(!isset($ret)){
                $ret = $retLocal;
            }else{
                $ret = array_intersect($ret, $retLocal);
            }
            
        }
        
        return $ret;
        
    }
    
    public function getTableRecordCount($tables = null){
        
        $ret = array();
        $source = $this->name();
        
        foreach($this->getTables() as $table){
            if(!isset($tables) || in_array($table->tableName, $tables)){
                $ret[$table->tableName] = Am::table($table->tableName, $source)->qAll()->count();
            }
        }
        
        return $ret;
        
    }
    
}

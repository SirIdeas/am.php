<?php

/**
 * Clase para las tablas de la BD
 */

class AmTable extends AmObject{

  // Propiedades de la tabla
  protected
    $name = null,             // Alias
    $tableName = null,        // Nombre en la BD
    $fields = null,           // Lista de campos
    $engine = null,           // Motor
    $charset = null,          // Set de caracteres
    $collate = null,          // Coleción de caracteres
    $pks = array(),           // Array de nombres de campos que forman el PK
    $referencesTo = array(),  // Tablas a las que hace referencia
    $referencesBy = array(),  // Tablas que le hacen referencia
    $validators = array(),    // Validadores
    $source = null,           // Fuente de datos
    $modelName = null;        // Nombre del model

  // Constructor para la clase
  public function __construct($params = null){

    // Obtener los parametros parseados
    $params = AmObject::parse($params);
    
    // Obtener la instancia del source
    $source = is_string($params['source']) ? AmORM::source($params['source']) : $params['source'];
    
    if(get_class($this) != __CLASS__){
      // $tableClassName = _tcc($params['tableName'], true);
      // $params = readConf("{$source->folderConfFilesPath()}/{$source->prefix()}$tableClassName");
      $params['source'] = $source;
    }
    
    // Llamar al constructor heredado
    parent::__construct($params);
    
    // Asignar el modelo
    if(!isset($this->modelName))
        $this->modelName = $this->getTableName();

    // Describir tabla    
    $this->describeTableInner(
      isset($params["referencesTo"])? $params["referencesTo"] : array(),
      isset($params["referencesBy"])? $params["referencesBy"] : array(),
      isset($params["pks"])? $params["pks"] : array(),
      isset($params["fields"])? $params["fields"] : array()
    );

    // Inicializar
    $this->initialize();

  }

  // Método para inicializar clases implementadas
  public function initialize(){}

  // Métodos GET de algunas propiedades
  public function getTableName(){ return $this->tableName; }
  public function getModelName(){ return $this->modelName; }
  public function getSource(){ return $this->source; }
  public function getReferencesTo(){ return $this->referencesTo; }
  public function getReferencesBy(){ return $this->referencesBy; }
  public function getPks(){ return $this->pks; }
  public function getEngine(){ return $this->engine;}
  public function getCharset(){ return $this->charset;}
  public function getCollate(){ return $this->collate;}
  public function getFields(){ return $this->fields; }
  public function getField($name){ return $this->fields->$name; }

  // Nombre de las clases relacionadas a una tabla
  public function getClassNameModel(){      return $this->getSource()->getClassNameModel($this->getModelName()); }
  public function getClassNameModelBase(){  return $this->getSource()->getClassNameModelBase($this->getModelName()); }
  public function getClassNameTable(){      return $this->getSource()->getClassNameTable($this->getModelName()); }
  public function getClassNameTableBase(){  return $this->getSource()->getClassNameTableBase($this->getModelName()); }

  // Metodos GET para obtener las carpetas pertinentes
  public function getFolder(){              return $this->getSource()->getFolderModel($this->getModelName()); }
  public function getFolderBase(){          return $this->getSource()->getFolderModelBase($this->getModelName()); }
  public function getPathConf(){            return $this->getSource()->getPathConf($this->getModelName()); }
  public function getPathClassTableBase(){  return $this->getSource()->getPathClassTableBase($this->getModelName()); }
  public function getPathClassTable(){      return $this->getSource()->getPathClassTable($this->getModelName()); }
  public function getPathClassModelBase(){  return $this->getSource()->getPathClassModelBase($this->getModelName()); }
  public function getPathClassModel(){      return $this->getSource()->getPathClassModel($this->getModelName()); }

  public function mkdirModel(){
    return Am::mkdir($this->getFolderBase());
  }

  // Crea el archivo de configuracion para una tabla
  public function createFileConf($rw = false){
    // Obtener de el nombre del archivo destino
    $path = $this->getPathConf() . ".php";
    if(!file_exists($path) || $rw){
      AmCoder::write($path, $this->toArray());
      return true;
    }
    return false;
  }

  // Crea el archivo que contiene clase para la tabla
  public function createFileTableBase(){

    // Incluir la clase para generar
    AmORM::requireFile('AmGenerator.class');
    
    // Obtener el nombre del archivo destino
    $path = $this->getPathClassTableBase() . ".php";

    // Generar el archivo
    file_put_contents($path, "<?php\n\n" . AmGenerator::classTableBase($this));
    return true;

  }

  // Crea el archivo que contiene clase para la tabla
  public function createFileTable(){
    
    // Obtener el nombre del archivo destino
    $path = $this->getPathClassTable() . ".php";
    
    // Verificar que no exista
    if(!file_exists($path)){
      // Crear la Carpeta
      file_put_contents($path, "<?php\n\nclass {$this->getClassNameTable()} extends {$this->getClassNameTableBase()}{\n\n}\n");
      return true;
    }

    return false;

  }
    
  // Crea el archivo que contiene clase para la tabla
  public function createFileModelBase(){
      
    // Incluir la clase para generar
    AmORM::requireFile('AmGenerator.class');
    
    // Obtener el nombre del archivo destino
    $path = $this->getPathClassModelBase() . ".php";

    // Generar el archivo
    file_put_contents($path, "<?php\n\n" . AmGenerator::classModelBase($this));
    return true;
      
  }

  //Crea el archivo que contiene clase para la tabla
  public function createFileModel(){

    // Obtener el nombre del archivo destino
    $path = $this->getPathClassModel() . ".php";
    
    // Verificar que no exista
    if(!file_exists($path)){
      // Crear la Carpeta
      file_put_contents($path, "<?php\n\nclass {$this->getClassNameModel()} extends {$this->getClassNameModelBase()}{\n\n}\n");
      return true;
    }
    
    return false;

  }

  // Crear los archivos para las clases del modelo
  public function createClassModels(){
    return array(
      "folders"   => $this->mkdirModel(),
      "conf"      => $this->createFileConf(),
      "table"     => $this->createFileTable(),
      "tableBase" => $this->createFileTableBase(),
      "model"     => $this->createFileModel(),
      "modelBase" => $this->createFileModelBase()
    );
  }

  // Devuelve todos lo validators de la tabla o los de un campo
  public function getValidators($name = null){
    if(isset($name))
      return isset($this->validators[$name])? $this->validators[$name] : null;
    return $this->validators;
  }

  // Devuelve un validator en especifico
  public function getValidator($name, $validatorName){
    return isset($this->validators[$name][$validatorName])?
      $this->validators[$name][$validatorName] : null;
  }

  // Indica su un campo forma o no parte del primary key de la tabla
  public function isPk($fieldName){
    return in_array($fieldName, $this->getPks());
  }

  // Agregar el nombre del campo a la lista de
  // claves primarias
  public function addPk($fieldName){
    
    // Agregar el campo a la lista de campos
    // primarios si este no existe en la tabla
    if(!in_array($fieldName, $this->getPks()))
      $this->pks[] = $fieldName;
    
    // Marcar el campo como primario
    $this->getField($fieldName)->getPrimaryKey(true);
      
  }
  
  // Asigna un campo
  public function setField($name, AmField $field){
    $this->fields->$name = $field;
    return $this;
  }

  // Agregar una campo a la lista de campos
  public function addField(AmField $f, $as = null){
    
    // Obtener el nombre para el campo
    $fieldName = $f->getName();
    $name = empty($as) ? $fieldName : $as;
    
    // Asignar nombre al campo
    if(empty($fieldName))
      $f->getName($name);
    
    // Agregar campo a la lista de campos
    $this->setField($name, $f);
    
    // Si es campo primario se agrega a la
    // lista de campos primarios
    if($f->getPrimaryKey())
      $this->addPk($name);
      
  }

  // Agrega un validator a la tabla
  public function setValidator($name, $validatorName, $validator = null, $options = array()){

    // Si el segundo parámetro es una instancia de un validator
    // se agrega
    if($validatorName instanceof BaseValidator)
      return $this->validators($name, null, $validatorName);

    if(!$validator instanceof BaseValidator){
        
        $validator = AmORM::validator($validator);
        $validator = new $validator($options);
        
    }
    
    // Asignar el nombre al validator
    $validator->setFieldName($name);
    
    // Agregar el validator a la tabla
    if(isset($validatorName))
      return $this->validators[$name][$validatorName] = $validator;

    // Agregar al final
    return $this->validators[$name][] = $validator;

  }
  
  // Metodo para eliminar validator
  public function dropValidator($name, $validatorName = null){
    if(isset($this->validators[$name][$validatorName])){
      // Si esta definido el validator en la posicion especifica se eliminan
      unset($this->validators[$name][$validatorName]);
    }else if(isset($this->validators[$name])){
      // Sino esta definido los validators para un atributo se eliminan
      unset($this->validators[$name]);
    }
  }

  // Obtener un listado de los campos primarios de una tabla
  public function sqlGetTablePrimaryKey(){  return $this->getSource()->sqlGetTablePrimaryKey($this); }
  public function getTablePrimaryKey(){     return $this->getSource()->getTablePrimaryKey($this); }

  // Obtener un listado de las columnas de una tabla
  public function sqlGetTableColumns(){ return $this->getSource()->sqlGetTableColumns($this); }
  public function getTableColumns(){    return $this->getSource()->getTableColumns($this); }

  // Obtener un listados de las referencias de la tabla
  public function sqlGetTableForeignKeysSql(){ return $this->getSource()->sqlGetTableForeignKeysSql($this); }
  public function getTableForeignKeys(){       return $this->getSource()->getTableForeignKeys($this); }
  
  // Obtener un listado de las referencias a la tabla
  public function sqlGetTableReferences(){  return $this->getSource()->sqlGetTableReferences($this); }
  public function getTableReferences(){     return $this->getSource()->getTableReferences($this); }

  // Crea la tabla
  public function sqlCreate(){  return $this->getSource()->sqlCreateTable($this); }
  public function create(){     return $this->getSource()->createTable($this); }

  // Elimina tabla de la Base de datos
  public function sqlDrop(){  return $this->getSource()->sqlDropTable($this); }
  public function drop(){     return $this->getSource()->dropTable($this) !== false; }

  // Vaciar una tabla
  public function sqlTruncate(){  return $this->getSource()->sqlTruncate($this); }
  public function truncate(){     return $this->getSource()->truncate($this) !== false; }

  // Insertar registros en la tabla
  public function sqlInsertInto($values, array $fields = array()){  return $this->getSource()->sqlInsertInto($this, $values, $fields); }
  public function insertInto($values, array $fields = array()){     return $this->getSource()->insertInto($this, $values, $fields); }

  // Devuelve si la tabla existe o no en la BD
  public function exist(){      return $this->getSource()->existsTable($this); }

  // Carga los columnas, referencias y PKs de la tabla desde la BD
  public function describeTable(){

    // Realizar asignacion
    $this->describeTableInner(
      $this->getTableForeignKeys(),
      $this->getTableReferences(),
      $this->getTablePrimaryKey(),
      $this->getTableColumns()
    );

  }

  // Cargar columnas, referencias y PKs a la tabla
  protected function describeTableInner(array $referencesTo = array(), array $referencesBy = array(), array $pks = array(), array $fields = array()){

    // Preparar referencias
    $this->referencesTo = new stdClass;
    foreach ($referencesTo as $name => $values){
      $this->referencesTo->$name = new AmRelation($values);
    }
    
    // Preparar referencias a
    $this->referencesBy = new stdClass;
    foreach ($referencesBy as $name => $values){
      $this->referencesBy->$name = new AmRelation($values);
    }
    
    // Preparar campos
    $this->fields = new stdClass;
    foreach($fields as $column){
      
      // Determinar si es o no parte del primary key
      $column["primaryKey"] = in_array(isset($column["name"])? $column["name"] : null, $pks);
      
      // Obtener el tipo
      $type = $this->getSource()->getTypeOf(isset($column["type"])? $column["type"] : null);
      
      // Sino es un tipo reconocido
      // volver al anterior
      if(false !== $type)
        $column["type"] = $type;
      
      // Agregar instancia del campo
      $this->addField(new AmField($column));
        
    }
      
  }

  // Convertir la tabla a Array
  public function toArray(){
    
    // Convertir campos
    $fields = array();
    foreach($this->getFields() as $offset => $field){
      $fields[$offset] = $field->toArray();
    }
    
    // Convertir refencias
    $referencesTo = array();
    foreach($this->getReferencesTo() as $offset => $field){
      $referencesTo[$offset] = $field->toArray();
    }
    
    // Convertir referencias a
    $referencesBy = array();
    foreach($this->getReferencesBy() as $offset => $field){
      $referencesBy[$offset] = $field->toArray();
    }
    
    // Unir todas las partes
    return array(
      "tableName" => $this->getTableName(),
      "modelName" => $this->getModelName(),
      "engine" => $this->getEngine(),
      "charset" => $this->getCharset(),
      "collate" => $this->getCollate(),
      "fields" => $fields,
      "pks" => $this->getPks(),
      "referencesTo" => $referencesTo,
      "referencesBy" => $referencesBy,
    );
      
  }

  // Devuelve un Query que devuelve todos los registros de la Tabla
  public function qAll($as = "q", $withFields = false){
      
    // por si se obvio el primer parametro
    if(is_bool($as)){
      $withFields = $as;
      $as = "q";
    }

    // Crear consultar
    $q = $this->getCource()->newQuery($this, $as);

    // Asignar campos
    if($withFields){
      $fields = array_keys((array)$this->getFields());
      $fields = array_combine($fields, $fields);
      $q->setSelects($fields);
    }
    
    return $q;
      
  }

  // Obtener consulta para buscar por un campos
  public function findBy($field, $value){
    return $this->qAll()->where("$field='$value'");
  }

  // Obtener todos los registros de buscar por un campos
  public function findAllBy($field, $value, $type = null){
    return $this->findBy($field, $value)->getResult($type);
  }

  // Obtener el primer registro de la busqueda por un campo
  public function findOneBy($field, $value, $type = null){
    return $this->findBy($field, $value)->getRow($type);
  }

  // Obtener la consulta para encontrar el registro con un determinado ID
  public function findById($id){
    
    $q = $this->qAll();   // Obtener consultar para obtener todos los registros  
    $pks = $this->getPks();  // Obtener el primary keys
    
    // Si es un array no asociativo
    if(is_array($id) && !Am::isAssocArray($id)){
      // Si la cantidad de campos del PKs es igual
      // a la cantidad de valores recibidos del ID
      if(count($pks) === count($id)){
        $id = array_combine($pks, $id);
      }else{
        // No es valido
        return null;
      }
    }
    
    // El primary key es un solo campo y los valores del ID no son un array
    if(1 == count($pks) && !is_array($id)){
      $id = array($pks[0] => $id);
    }
    
    // Recorrer los campos del PK
    foreach($pks as $pk){
      
      // Si no existe el valor para el campo devolver null
      if(!isset($id[$pk]) && !array_key_exists($pk, $id))
        return null;
      
      // Agregar condicion
      $q->where("{$this->fields($pk)->getName()}='{$id[$pk]}'");
      
    }
    
    return $q;
      
  }

  // Regresa un objeto con AmModel con el registro solicitado
  public function find($id, $type = null){
    
    $q = $this->findById($id);
    $r = isset($q)? $q->getRow($type) : false;
    
    return $r === false ? null : $r;
    
  }

}
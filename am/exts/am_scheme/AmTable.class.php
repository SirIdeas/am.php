<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para representar las tablas de las BD
 */
class AmTable extends AmObject{

  protected static
    
    /**
     * Nombre por defecto para el campo que guarda el momento de creación.
     */
    $defCreatedAtFieldName = 'created_at',
    
    /**
     * Nombre por defecto para el campo que guarda el momento de actualización.
     */
    $defUpdatedAtFieldName = 'updated_at';


  protected
    
    /**
     * Nombre del modelo de la tabla.
     */
    $model = 'array',
    
    /**
     * Nombre del campo para la momento de creación.
     */
    $createdAtField = null,
    
    /**
     * Nombre del campo para la momento de actualización.
     */
    $updatedAtField = null,
    
    /**
     * Instancia del esquema.
     */
    $scheme = '',
    
    /**
     * Nombre del esquema.
     */
    $schemeName = '',
    
    /**
     * Nombre en la tabla en la BD.
     */
    $tableName = null,
    
    /**
     * Hash de campos.
     */
    $fields = null,
    
    /**
     * Indica si la tabla cargó la estructura de la BD.
     */
    $schemeStruct = false,
    
    /**
     * Hash de validadores.
     */
    $validators = null,
    
    /**
     * Motor de la tabla en la BD.
     */
    $engine = null,
    
    /**
     * Set de caracteres de la tabla en la BD.
     */
    $charset = null,
    
    /**
     * Reglas para los caracteres.
     */
    $collation = null,
    
    /**
     * Lista de campos PKs.
     */
    $pks = array(),
    
    /**
     * Hash de referencias a otras tablas.
     */
    $referencesTo = array(),
    
    /**
     * Hash de referencias a esta tabla.
     */
    $referencesBy = array(),
    
    /**
     * Array de índices únicos.
     */
    $uniques = array();

  /**
   * Constructor para la clase.
   * @param mixed $params Hash de valores iniciales para el objeto.
   */
  public function __construct($params = null){

    // Obtener los parametros parseados
    $params = AmObject::parse($params);

    // Obtener la instancia del esquema
    $scheme = AmScheme::get($params['schemeName']);

    // Asignar nombre de los campos de fecha
    $params = array_merge(array(
      'createdAtField'  => self::$defCreatedAtFieldName,
      'updatedAtField'  => self::$defUpdatedAtFieldName,
    ), $params);

    // Obtener configuracion del modelo
    $conf = $scheme->getBaseModelConf($params['tableName']);

    // Si se pudo obtener la configuración se mezcla con la recibida
    if($conf)
      $params = array_merge($conf, $params, array('schemeStruct' => true));

    // Aaignar modelo
    $params['scheme'] = $scheme;

    // Llamar al constructor heredado
    parent::__construct($params);

    // Obtener como retornará los resultados y asignarlo a la consulta
    if(!$this->getModel())
      $this->model = $scheme->getSchemeModelName($params['tableName']);

    // Obtener los campos y primary keys
    $fields = $this->fields;
    $pks = $this->pks? $this->pks : array();
    $pks = is_array($pks)? $pks : array($pks);

    // Luego recetearlos para prepararlos
    $this->pks = array();
    $this->fields = array();

    // Preparar los campos
    if(is_array($fields))
      foreach($fields as $fieldName => $column)
        // Agregar instancia del campo
        $this->addField($fieldName, array_merge(
          is_string($column)?array('type' => $column) : $column,
          array('pk' => in_array($fieldName, $pks))
        ));

    // Preparar los primary keys.
    if(is_array($pks))
      foreach($pks as $pk)
        $this->addPk($pk);
      
    // Preparar referencias a
    if(!is_array($this->referencesTo))
      $this->referencesTo = array();

    // Preparar referencias a
    foreach($this->referencesTo as $name => $relation)
      if(!$relation instanceof AmRelation)
        $this->referencesTo[$name] = new AmRelation($relation);

    // Preparar referencias de
    if(!is_array($this->referencesBy))
      $this->referencesBy = array();

    // Preparar referencias de
    foreach($this->referencesBy as $name => $relation)
      if(!$relation instanceof AmRelation)
        $this->referencesBy[$name] = new AmRelation($relation);

    // Agregar tabla al esquema
    $scheme->addTable($this);

  }

  /**
   * Devuelve el modelo de la tabla.
   * @return string Nombre del modelo.
   */
  public function getModel(){

    return $this->model;

  }

  /**
   * Devuelve el nombre de la tabla.
   * @return string Nombre de la tabla.
   */
  public function getTableName(){

    return $this->tableName;

  }

  /**
   * Devuelve el nombre del esquema.
   * @return string Nombre del esquema.
   */
  public function getSchemeName(){

    return $this->schemeName;

  }

  /**
   * Devuelve la instancia del esquema.
   * @return AmScheme Instancia del esquema.
   */
  public function getScheme(){

    return $this->scheme;

  }


  /**
   * Devuelve la Hash de referencias a otras tablas.
   * @return Hash Hash de referencias a otras tablas.
   */
  public function getReferencesTo(){

    return $this->referencesTo;

  }
  
  /**
   * Devuelve la Hash de referencias de otras tablas.
   * @return hash Hash de referencias de otras tablas.
   */
  public function getReferencesBy(){

    return $this->referencesBy;

  }
  
  /**
   * Devuelve el listado de claves únicas.
   * @return array Listado de clavees únicas.
   */
  public function getUniques(){

    return $this->uniques;

  }

  /**
   * Devuelve el listado de nombre de campos del primary keys de la tabla.
   * @return array Listado de nombre del PK.
   */
  public function getPks(){

    return $this->pks;

  }

  /**
   * Indica su un campo forma o no parte del primary key de la tabla
   * @param  string  $fieldName Nombr del campo consultado.
   * @return bool               Si forma parte del PK.
   */
  public function isPk($fieldName){

    return in_array($fieldName, $this->getPks());
    
  }

  /**
   * Devuelve a la tabla se le cargó la estructura de la BD.
   * @return bool Si tiene la estructura de la BD.
   */
  public function isSchemeStruct(){

    return $this->schemeStruct;

  }


  /**
   * Devuelve el nombre del motor de la tabla en la BD.
   * @return string Nombre del motor de la tabla en la BD.
   */
  public function getEngine(){

    return $this->engine;

  }

  /**
   * Devuelve el Set de caracteres.
   * @return string Set de caracteres.
   */
  public function getCharset(){

    return $this->charset;

  }

  /**
   * Devuelve la coleción de caracteres.
   * @return string Devuelve la coleción de caracteres.
   */
  public function getCollation(){

    return $this->collation;

  }
  
  /**
   * Devuelve el Hash de campos.
   * @return hash Hash de campos.
   */
  public function getFields(){

    return $this->fields;

  }
  
  /**
   * Devuelve el campo correspondiente a un nombre.
   * @param  string  $name Nombre del campo que se desea obtener.
   * @return AmField       Instancia del campo o null si no existe.
   */
  public function getField($name){

    return itemOr($name, $this->fields, null);

  }

  /**
   * Devuelve si existe un campo con el nombre especificado.
   * @param  string  $name Nombre del campo.
   * @return bool          Si el campo existe.
   */
  public function hasField($name){

    return isset($this->fields[$name]);
    
  }

  /**
   * Devuelve el nombre del campo destinado a guardar el momento de creación de
   * un registro.
   * @return string Nombre del campo.
   */
  public function getCreatedAtField(){

    return $this->createdAtField;

  }

  /**
   * Devuelve le nombre del campo destinado a guardar el momento de la
   * actualizaciónde un registro.
   * @return string Nombre del campo.
   */
  public function getUpdatedAtField(){

    return $this->updatedAtField;

  }


  /**
   * Asigna el modelo a una tabla.
   * Si ya tiene un modleoa asignado y es diferente al actual entonces se clona
   * la instancia de la tabla y se asigna el nuevo modelo a esta.
   * @param string   $value Nombre del modelo.
   * @return AmTabla        Si el model cambia devuelve la tabla nueva, del
   *                        contrario devuelve la tabla actual.
   */
  public function setModel($value){

    $table = $this;

    if(isset($this->model) && isset($value) && $this->model !== $value){

      // Clonar la tabla actual
      $model = $this->model;
      $this->model = $value;
      $table = clone($this);
      $this->model = $model;

      // Agregar la tabla al esquema.
      $table->getScheme()->addTable($table);

    }elseif(!isset($this->model) && isset($value)){
      
      $this->model = $value;

    }
    
    return $table;

  }

  /**
   * Asigna el nombre del campo de momento de creación del registro.
   * @param  string $value Nombre del campo.
   * @return $this
   */
  public function setCreatedAtField($value){

    $this->createdAtField = $value;
    return $this;

  }

  /**
   * Asigna el nombre del campo de momento de actualización del registro.
   * @param  string $value Nombre del campo.
   * @return $this
   */
  public function setUpdatedAtField($value){

    $this->updatedAtField = $value;
    return $this;

  }

  /**
   * Devuelve si existe un campo con el nombre asignado de campo de creación de
   * registro.
   * @return bool Si existe el campo.
   */
  public function hasCreatedAtField(){

    return $this->hasField($this->getCreatedAtField());

  }

  /**
   * Devuelve si existe un campo con el nombre asignado de campo de
   * actualización de registro.
   * @return bool Si existe el campo.
   */
  public function hasUpdatedAtField(){

    return $this->hasField($this->getUpdatedAtField());

  }

  /**
   * Agrega el campo de creación de registro con el nombre pasado por parámetro.
   * @param  string $name Nombre del campo a agregar.
   * @return $this
   */
  public function addCreatedAtField($name = null){

    // Si no se recibió el nombre del campo se toma el nombre por defecto.
    if(!isset($name))
      $name = self::$defCreatedAtFieldName;
    
    // Asignar el nombre del campo
    $this->setCreatedAtField($name);

    // Agregar campo como datatime.
    return $this->addField($name, 'timestamp');

  }

  /**
   * Agrega el campo de creación de registro con el nombre pasado por parámetro.
   * @param  string $name Nombre del campo a agregar.
   * @return $this
   */
  public function addUpdatedAtField($name = null){

    // Si no se recibió el nombre del campo se toma el nombre por defecto.
    if(!isset($name))
      $name = self::$defUpdatedAtFieldName;
    
    // Asignar el nombre del campo
    $this->setUpdatedAtField($name);

    // Agregar campo como datatime.
    return $this->addField($name, 'timestamp');

  }

  /**
   * Agrega los campos de creación y actualización.
   * @param  string $createAtFieldName Nombre del campo de creación.
   * @param  string $updateAtFieldName Nombre del campo de actualización.
   * @return $this
   */
  public function addCreatedAtAndUpdateAtFields($createAtFieldName = null,
    $updateAtFieldName = null){

    return $this
      ->addCreatedAtField($createAtFieldName)
      ->addUpdatedAtField($updateAtFieldName);

  }

  /**
   * Agregar fecha al campo de fecha de creacion.
   * @param  array/AmQuery $values Array de modelos o una instancia de AmQuery.
   * @return $this
   */
  public function setAutoCreatedAt($values){

    // Si la tabla tiene un campo llamado 'created_at' se asigna a todos los
    // valores la fecha now
    if($this->hasCreatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getCreatedAtField());

    return $this;

  }

  /**
   * Agregar fecha al campo de fecha de mpdificacion
   * @param  array/AmQuery $values Array de modelos o una instancia de AmQuery.
   * @return $this
   */
  public function setAutoUpdatedAt($values){

    // Si la tabla tiene un campo llamado 'updated_at'
    // Se asigna a todos los valores la fecha now
    if($this->hasUpdatedAtField())
      self::setNowDateValueToAllRecordsInField($values,
        $this->getUpdatedAtField());

    return $this;

  }

  /**
   * Asigna el valor valor de NOW a los modelos o consulta AmQuery en un
   * determinado campo.
   * @param array/AmQuery $values Array de modelos o una instancia de AmQuery.
   * @param string        $field  Nombre del campo donde se asignará.
   */
  private static function setNowDateValueToAllRecordsInField($values, $field){

    // Fecha a signar.
    $now = date('c');

    // Si es una instancia de AmQuery
    if($values instanceof AmQuery){

      // Si es una consulta de actualización.
      if($values->getType() == 'update')
        $values->set($field, $now);

      else
        // Agregar campo a la consulta
        $values->selectAs("'{$now}'", $field);

    }elseif(is_array($values)){

      // Agregar created_ad a cada registro
      foreach (array_keys($values) as $i)
        $values[$i][$field] = $now;

    }

  }

  /**
   * Agregar el nombre del campo a la lista de laves primarias.
   * @param  string $fieldName Nombre del campo.
   * @return $this
   */
  public function addPk($fieldName){

    // Agregar el campo a la lista de campos
    // primarios si este no existe en la tabla
    if(!in_array($fieldName, $this->getPks()))
      $this->pks[] = $fieldName;

    // Marcar el campo como primario
    $field = $this->getField($fieldName);

    // Si el campo ya existe y no es un campo de la PK se debe crear una copia
    // marcandolo como PK.
    if(isset($field) && !$field->isPk())
      $this->fields[$fieldName] = $this->fields[$fieldName]->cp(array(
        'pk' => true
      ));

    return $this;

  }

  /**
   * Agregar una campo a la lista de campos.
   * @param string               $name  Nombre del campo a insertar.
   * @param array/AmField/string $field Hash de atributos del campo o instancia
   *                             de AmField o tipo de datos del campo.
   * @return $this
   */
  public function addField($name = null, $field = null){

    // Si en $field se recibe solo el tipo de datos.
    if(is_string($field))
      // Se convierte en array.
      $field = array('type' => $field);

    // Si el primer parametro es una instanca de AmFiel o un array
    // se tomará como el campo
    if($name instanceof AmField || is_array($name)){
      $field = $name;

      // Obtener el nombre del campo.
      if($name instanceof AmField)
        $name = $field->getName();
      else
        $name = itemOr('name', $field);

    }

    // Si el campo es una rray se convierte en una instancia de AmField.
    if(is_array($field))
      $field = new AmField(array_merge(array('name' => $name), $field));

    // Obtener el nombre del campo.
    $name = $field->getName();

    // Si ya existe un campo con el mismo nombre generar una excepción.
    if($this->hasField($name))
      throw Am::e('AMSCHEMA_TABLE_ALREADY_HAVE_A_FIELD_NAMED',
        $this->getTableName(), $name);

    // Agregar el campo
    $this->fields[$name] = $field;

    // Si es campo primario se agrega a la lista de campos primarios
    if($field->isPk())
      $this->addPk($name);

    return $this;

  }

  /**
   * Devuelve los validadores para un campo.
   * @param  string $name Nombre del campo que se desea obtener.
   * @return array        Array de los validators en el campo indicado. Si $name
   *                      es null devuelve todos los validators.
   */
  public function getValidators($name = null){

    if(isset($name))
      return isset($this->validators->$name)? $this->validators->$name : null;

    return $this->validators;

  }

  /**
   * Devuelve un validator específico de un campo.
   * @param  string $name          Nombre del campo.
   * @param  string $validatorName Nombre del validador.
   * @return AmValidator           Instancia del validator si existe.
   */
  public function getValidator($name, $validatorName){

    return isset($this->validators->$name[$validatorName])?
      $this->validators->$name[$validatorName] : null;

  }

  /**
   * Metodo para eliminar validator.
   * @param  string $name          Nombre del campo.
   * @param  string $validatorName Nombre del validador.
   */
  public function dropValidator($name, $validatorName = null){

    if(isset($this->validators->$name[$validatorName])){
      // Si esta definido el validator en la posicion especifica se eliminan
      unset($this->validators->$name[$validatorName]);

    }else if(isset($this->validators->$name)){
      // Sino esta definido los validators para un atributo se eliminan
      unset($this->validators->$name);
    }

  }

  /**
   * Asigna un validator a la tabla.
   * @param string/array             $name          Nombre del campo o array de
   *                                                campos a los que se
   *                                                aplicará el validator.
   * @param string/array/AmValidator $validatorName Nombre o instancia del
   *                                                validador o array de
   *                                                validadore a agregar.
   * @param string/array/AmValidator $validator     Tipo de validador o
   *                                                instancia o array de
   *                                                validadores.
   * @param array                    $options       opciones para instanciar
   *                                                el validador.
   */
  public function setValidator($name, $validatorName, $validator = null,
    $options = array()){

    // Si el nombre es un array, entonces
    if(is_array($name)){
      // Agregar un  validator por cada elemento
      foreach ($name as $value)
        $this->setValidator($value, $validatorName, $validator, $options);
      return;
    }

    // Si el segundo parámetro es una instancia de un validator
    // se agrega
    if($validatorName instanceof AmValidator)
      return $this->setValidator($name, null, $validatorName);

    // Si el segundo parámetro es un array entonces representa
    // que se agregaran varios validators
    if(is_array($validatorName)){
      foreach ($validatorName as $value)
        $this->setValidator($name, $value, $validator, $options);
      return;
    }

    // Si el tercer parametro es un array, entonces representa las opciones.
    // El nombre del parametro pasa a ser tambien el validator que se buscara.
    if(is_array($validator))
      return $this->setValidator($name, $validatorName, null, array_merge($validator, $options));

    // Si no se indico el 3er parametros, entonces se tomara el nombre como validador
    if(!isset($validator))
      return $this->setValidator($name, $validatorName, $validatorName, $options);

    // Si el validator no es una instancia de un validador
    // Entonce obtener instancia del validador.
    if(!$validator instanceof AmValidator){
      $validator = AmScheme::validator($validator);
      $validator = new $validator($options);
    }

    // Asignar el nombre al validator
    $validator->setFieldName($name);
    $validators = $this->validators;

    // Crear array si no ha sido creado
    if(!isset($validators->$name))
      $validators->$name = array();

    // Agregar el validator a la tabla
    if(isset($validatorName)){
      $validators->$name = array_merge($validators->$name, array(
        $validatorName => $validator
      ));
      return $validator;
    }

    // Agregar al final
    array_push($validators->$name, $validator);

    return $validator;

  }

  /**
   * Ejecuta validadores en un modelo.
   * @param AmModel $model Instancia del registro a validar.
   * @param string  $field Nombre del campo a validar.
   */
  public function validate(AmModel $model, $field){

    if(isset($field)){

      // Obtener validator del campo
      $validators = $this->getValidators($field);

      // Sino se obtiene un array de validators retornar
      // sine valuar
      if(is_array($validators))

        foreach($validators as $name => $validator)
          // Si el modelo no cumple con la validacion
          if(!$validator->isValid($model))
            // Se agrega el error
            $model->addError($field, $name, $validator->getMessage($model));

    }else{

      // Preparar campos
      $model->prepare();

      // Obtener nombre de validator definidos
      $validators = (array)$this->getValidators();

      // Validar todos los campos
      foreach($validators as $field => $_)
        $table->validate($field, $model);

    }

  }

  /**
   * Parse los campos de un hash de valores mediante los campos de la tabla.
   * @param  hash $r Hash de valores
   * @return hash    Hash de valores parseado.
   */
  public function prepare(array $r){

    foreach ($this->fields as $key => $field)
      $r[$key] = $field->parseValue(itemOr($key, $r));

    return $r;

  }

  /**
   * Crea la tabla en la BD.
   * @param  bool $ifNotExists Se agrega el parémtro IS NOT EXISTS.
   * @return bool              Si se creó la tabla. Si la tabla existe y el
   *                           parámetro $ifNotExists == true, retornará true.
   */
  public function create($isNotExists = true){

    return $this->getScheme()->createTable($this, $isNotExists);

  }

  /**
   * Elimina la tabla de la BD.
   * @param  bool $ifExists Si se agrega la clausula IF EXISTS.
   * @return bool           Si se eliminó la Tabla. Si la Tabla no existe y el
   *                        parémetro $ifExists==true entonces retorna true.
   */
  public function drop($isExists = true){

    return $this->getScheme()->dropTable($this, $isExists);

  }

  /**
   * Indica si existe la tabla en la BD.
   * @return bool Si existe la tabla.
   */
  public function exists(){

    return $this->getScheme()->existsTable($this);

  }

  /**
   * Vacía la tabla.
   * @param  bool $ignoreFk Si se ingorará los Foreing Keys.
   * @return bool           Si se vació la tabla satisfactoriamente.
   */
  public function truncate($ignoreFK = false){

    return $this->getScheme()->truncate($this, $ignoreFK);

  }

  /**
   * Devuelve el índice correspondiente a un registro.
   * @param  AmModel  $model Instancia del registroe. 
   * @return int/hash        ID del registro o hash con los valores de los
   *                         campos primarios.
   */
  public function indexOf(AmModel $model){

    $ret = array(); // Para el retorno
    $pks = $this->getPks(); // Obtener PKs

    if(empty($pks))
      throw Am::e('AMSCHEME_MODEL_DONT_HAVE_PK', get_class($model));

    // Agregar los IDs
    foreach($pks as $pk)
      $ret[$pk] = $model->getRealValue($pk);

    return $ret;

  }

  /**
   * Insertar valores en una tabla.
   * @param  array/AmQuery $values Array de modelos o AmQuery select con los
   *                               calores a insertar.
   * @param  array         $fields Lista de campos para la consulta insert.
   * @return boolen/int            Si se realizó la inserción correstamente. Si
   *                               Se inserta un solo elemento y la tabla
   *                               contiene un único campo autoincrementable
   *                               entonces devuelve este campo.
   */
  public function insertInto($values, array $fields = array()){

    return $this->getScheme()->insertInto($values, $this, $fields);

  }

  /**
   * Devuelve un AmQuery de seleción de todos registros de la tabla
   * @param  string $alias      Alias de la tabla en el query.
   * @param  bool   $withFields Si la clausula SELECT se genera con los campos
   *                            de la tabla (true) o con * (false).
   * @return AmQuery            Query select.
   */
  public function all($alias = 'q', $withFields = false){

    // por si se obvio el primer parametro
    if(is_bool($alias)){
      $withFields = $alias;
      $alias = 'q';
    }

    // Crear consultar
    $q = $this->getScheme()->q($this, $alias);

    // Obtener como retornará los resultados y asignarlo a la consulta
    $q->setModel($this->getModel());

    // Asignar clausula SELECT
    if($withFields){
      $fields = array_keys($this->getFields());
      $fields = array_combine($fields, $fields);
      $q->setSelects($fields);
      
    }

    // Devolver consulta
    return $q;

  }

  /**
   * Obtener consulta para buscar registro por un campo.
   * @param  string $field      Nombre del campo donde se buscará.
   * @param  mixed  $value      Valor a buscar.
   * @param  string $alias      Alias de la tabla en el query.
   * @param  bool   $withFields Si la clausula SELECT se genera con los campos
   *                            de la tabla (true) o con * (false).
   * @return AmQuery            Query select.
   */
  public function findBy($field, $value, $alias = 'q', $withFields = false){

    return $this->all($alias, $withFields)->where("{$field}='{$value}'");

  }

  // Obtener todos los registros de buscar por un campos
  /**
   * [findAllBy description]
   * @param  string $field      Nombre del campo donde se buscará.
   * @param  mixed  $value      Valor a buscar.
   * @param  string $as         String con el nombre del modelo o formato de
   *                            retorno. Puede ser 'array', 'am', 'object',
   *                            nombre de una clase existente o identificador de
   *                            un modelo.
   * @param  bool   $withFields Si la clausula SELECT se genera con los campos
   *                            de la tabla (true) o con * (false).
   * @return AmQuery            Query select.
   */
  public function findAllBy($field, $value, $as = null, $withFields = false){

    return $this->findBy($field, $value, $withFields)->get($as);

  }

  /**
   * Obtener el primer registro de la busqueda por un campo.
   * @param  string $field      Nombre del campo donde se buscará.
   * @param  mixed  $value      Valor a buscar.
   * @param  string $as         String con el nombre del modelo o formato de
   *                            retorno. Puede ser 'array', 'am', 'object',
   *                            nombre de una clase existente o identificador de
   *                            un modelo.
   * @param  bool   $withFields Si la clausula SELECT se genera con los campos
   *                            de la tabla (true) o con * (false).
   * @return mixed/bool         El modelo en el formato especificado por el
   *                            parámetro $as o false si no se consigió alguna
   *                            coincidencia.
   */
  public function findOneBy($field, $value, $type = null, $withFields = false){

    return $this->findBy($field, $value, $withFields)->row($type);
    
  }

  /**
   * Obtener la consulta para encontrar el registro con un determinado ID.
   * @param  string/int/array $id         Id del registro. Si la tabla tiene un
   *                                      PK con un único campo entonces puede
   *                                      ser un int o string, si es un PK
   *                                      compuesto estonces debe ser un hash
   *                                      con los valores del id a buscar.
   * @param  string           $alias      Alias de la tabla en el query.
   * @param  bool             $withFields Si la clausula SELECT se genera con
   *                                      los campos de la tabla (true) o con *
   *                                      (false).
   * @return AmQuery                      Query select.
   */
  public function findById($id, $alias = 'q', $withFields = false){

    // Obtener consultar para obtener todos los registros
    $q = $this->all($alias, $withFields);
    $pks = $this->getPks();  // Obtener el primary keys

    // Si es un array no asociativo
    if(is_array($id) && !isHash($id)){
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

      $fieldName = $pk;

      // Agregar condicion
      $field = $this->getField($pk);
      if($field)
        $fieldName = $field->getName();

      // Agegar condición
      $q->where("{$fieldName}='{$id[$pk]}'");

    }

    return $q;

  }

  /**
   * Devuelve un modelo con el registro solicitado.
   * @param  string/int/array $id         Id del registro. Si la tabla tiene un
   *                                      PK con un único campo entonces puede
   *                                      ser un int o string, si es un PK
   *                                      compuesto estonces debe ser un hash
   *                                      con los valores del id a buscar.
   * @param  string           $alias      Alias de la tabla en el query.
   * @param  string           $as         String con el nombre del modelo o
   *                                      formato de retorno. Puede ser 'array',
   *                                      'am', 'object', nombre de una clase
   *                                      existente o identificador de un
   *                                      modelo.
   * @param  bool             $withFields Si la clausula SELECT se genera con
   *                                      los campos de la tabla (true) o con *
   *                                      (false).
   * @return mixed/bool                   El modelo en el formato especificado
   *                                      por el parámetro $as o false si no se
   *                                      consigió alguna coincidencia.
   */
  public function find($id, $as = null, $withFields = false){

    // Obtener consulta de búsqueda por id.
    $q = $this->findById($id, $withFields);

    // Si se obtuno la consulta devolver obtener el primer registro.
    return isset($q)? $q->row($as) : null;

  }

  /**
   * Devuelve un query que selecciona un registro.
   * @param  AmModel $model      Instancia del registro.
   * @param  string  $alias      Alias para la tabla de en el query.
   * @param  bool    $withFields Si la consulta incluirá la seleción de todos
   *                             los campos especificados en el modelo.
   * @return AmQuery             Query select para obtener el registro de la BD.
   */
  public function querySelectModel(AmModel $model, $alias = 'q',
    $withFields = false){

    // Obtener el índice del modelo
    $index = $this->indexOf($model);

    // Realizar la busqueda
    return $this->findById($index, $alias, $withFields);

  }

  /**
   * Inserta el registro en la tabla como nuevo.
   * @param  AmModel $model Instancia del registro a insertar.
   * @return int/bool       Id del último registro insertado o false si se
   *                        generó un error.
   */
  public function insert(AmModel $model){

    // Si se inserta satisfactoriamente
    if($this->insertInto($model))

      // Devolver el último id insertado.
      return $this->getScheme()->getLastInsertedId();

    // De lo contrario devolver falso.
    return false;

  }

  /**
   * Realiza la actualización de un registro en la tabla.
   * @param  AmModel $model Instancia del registro a actualizar.
   * @return bool           Indica si se realizó la actualización
   *                        satisfactoriamente.
   */
  public function update(AmModel $model){

    // Obtener los campos
    if($this->isSchemeStruct())
      $fields = array_keys($this->getFields());
    else
      $fields = array_keys($model->toArray());

    // Obtener una consulta para selecionar el registro
    $q = $this->querySelectModel($model);

    // Recorrer los campos para agregar los sets
    // de los campos que cambiaron
    foreach($fields as $fieldName){

      // Si el campo cambió
      if($model->hasChanged($fieldName))
        // Agregar set a la consulta
        $q->set($fieldName, $model->$fieldName);

    }

    return !!$q->update();

  }

  /**
   * Guarda los cambios del registro. Si es un registro nuevo entonces el
   * registro se intentará insertar en la tabla, de lo contrario se intentará
   * actualizar los datos del registro.
   * @param  AmModel  $model Instancia del modelo.
   * @return int/bool        Si se insertó como un nuevo registro y la tabla
   *                         donde se se insertó posee un único campo
   *                         autoincrementable se devuelve el valor de dicho
   *                         campo, de lo contrario solo devolverá si la
   *                         operación se efectuó satisfactoriamente.
   */
  public function save(AmModel $model){

    // Si todos los campos del registro son válidos
    if($model->isValid()){

      // Si es un registro nuevo se insertará
      if($model->isNew()){

        // Insetar en la BD. Ret será igual a de generado del registro en el
        // caso de tener como PK un campo autoincrementable o false si se
        // generá un error
        if(false !== ($ret = $this->insert($model))){
          // Obtener todos los campos de la tabla del modelo
          $fields = $this->getFields();

          // Recorrer campos
          foreach($fields as $f)

            // Agregar el valor que retorno el insert si se trata de un campo
            // autoincrementable
            if($f->isAutoIncrement()){

              // Obtener el nombre del método SET
              $fieldName = $f->getName();

              // para asigar el valor autoincrementado
              $model->$fieldName = $ret;

              // Se supone que sebe ser un solo campo autoincrementable
              break;

            }

          // Si ret == 0 es xq se interto correctamenre pero la tabla no tiene
          // una columna autoincrement Se retorna verdadero o el valor del ID
          // generado para el registro si se agregó correctamenre de lo
          // contrario se retorna el valor de $ret.
          return $ret == 0 ? true : $ret;

        }

      }else{

        // Se intenta actualizar los datos del registro en la BD
        if($this->update($model))

          // retornar true indicando el exito de la operacion
          return true;


      }

      // Obtener el esquema
      $scheme = $this->getScheme();

      // Si se llega a este punto es porque se generó un error en la inserción
      // o actualizacion, por lo que se agrega un error global con el último
      // error generado en el Gestor.
      $model->addError('__global__',
        $scheme->getErrNo(),
        $scheme->getError()
      );

    }

    return false;

  }

  /**
   * Devuelve un hash con los valores de un registro correspondientes a los
   * campos de la tabla.
   * @param  AmModel $model  Instancia del modelo.
   * @param  bool    $withAI Si se incluirá los valore Autoincrementables.
   * @return hash            Hash de valores.
   */
  public function dataToArray(AmModel $model, $withAI = true){

    $ret = array(); // Para el retorno

    // Obtener los campos
    if(!$this->isSchemeStruct())
      return $model->toArray();
    
    $fields = array_keys($this->getFields());

    foreach($fields as $fieldName){
      $field = $this->getField($fieldName);  // Obtener el campos
      // Si se pidió incorporar los valores autoincrementados
      // o si el campo no es autoincrementado
      if($withAI || !$field || !$field->isAutoIncrement())
        // Se agrega el campo al array de retorno
        $ret[$fieldName] = $model->$fieldName;
    }

    return $ret;

  }

  /**
   * Convertir la tabla en un Array.
   * @return hash Hash de propiedades de la tabla.
   */
  public function toArray(){

    // Convertir campos
    $fields = array();
    foreach($this->getFields() as $offset => $field)
      $fields[$offset] = $field->toArray();

    // Convertir refencias
    $referencesTo = array();
    foreach($this->getReferencesTo() as $offset => $field)
      $referencesTo[$offset] = $field->toArray();

    // Convertir referencias a
    $referencesBy = array();
    foreach($this->getReferencesBy() as $offset => $field)
      $referencesBy[$offset] = $field->toArray();

    // Unir todas las partes
    return array(
      'tableName' => $this->getTableName(),
      'engine' => $this->getEngine(),
      'charset' => $this->getCharset(),
      'collation' => $this->getCollation(),
      'fields' => $fields,
      'pks' => $this->getPks(),
      'uniques' => $this->getUniques(),
      'referencesTo' => $referencesTo,
      'referencesBy' => $referencesBy,
    );

  }

}
<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase base para los modelos
 */
class AmModel extends AmObject{

  protected

    /**
     * Propiedades internas del Modelo.
     */
    $__p = array();

  /**
   * El constructor se encarga instancia la tabla correspondiente al modelo si
   * esta aún no ha sido instanciada.
   * @param array $params Valores iniciales del registro.
   * @param bool  $isNew  Si es un resgistro nuevo.
   */
  final public function __construct($params = array(), $isNew = true) {

    // Instancia el objeto de las propiedades internas
    $this->__p = new AmObject;

    // Inicializar propiedades
    $this->__p->extend(array_merge(array(
      // Nombre del esquema a la que pertenece el modelo.
      'schemeName' => '',
      // Nombre de la tabla a la que pertenece el modelo.
      'tableName' => null,
      // Hash con las definiciones de los campos.
      'fields' => array(),
      // Configuración de los validadores.
      'validators' => true,
      // Indica si los campos estarán basado en los campos del modelo o en los campos de la tabla.
      'autoFields' => false,
      // Nombre del campo para la fecha de creación.
      'createdAtField' => true,
      // Nombre del campo para la fecha de actualización.
      'updatedAtField' => true,
      // Definición relaciones belongTo.
      'belongTo' => array(),
      // Definición relaciones hasMany.
      'hasMany' => array(),
      // Definición relaciones hasManyAndBelongTo.
      'hasManyAndBelongTo' => array(),
      // Definición de llaves únicas.
      'uniques' => array(),
      // Instancia de la tabla del modelo.
      'table' => null,
      // Indica si es un registro nuevo.
      'isNew' => true,
      // Hash de errores.
      'errors' => array(),
      // Indica que valores de las propiedades de la instancia del objeto son nativos del BDMS.
      'rawValues' => array(),
      // Valores reales.
      'realValues' => array(),
      // Cantidad de errores
      'errorsCount' => 0,
      // Hash con las relaciones del modelo.
      'foreigns' => array(),
    ), is_array($this->sketch)? $this->sketch : array()));

    // Inicializar la tabla si no ha sido inicializada
    $className = get_class($this);

    // Obtener la instancia del esquema.
    $scheme = AmScheme::get($this->__p->schemeName);

    // Signar si es nuevo.
    $this->__p->isNew = $isNew;

    // Tomar como nombre de la tabla el nombre de la clase del modelo en camel
    // case y plurar según el inlés.
    if(empty($this->__p->tableName))
      $this->__p->tableName = pluralize(underscore($className));

    // Obtener la instancia de la tabla desde el esquema si esta cargada.
    $this->__p->table = $scheme->getTableInstance($className);

    // Si no esta cargada la tabla
    if(!$this->__p->table){

      // Crear instancia anonima de la tabla
      $this->__p->table = new AmTable(array(

        // Asignar fuente
        'schemeName'         => $this->__p->schemeName,
        'tableName'          => $this->__p->tableName,
        'model'              => $className,

        // Si los campos son tomados automaticamente del modelo
        'autoFields'         => $this->__p->autoFields,

        // configuración de los validators
        'validators'         => $this->__p->validators,

        // Detalle de la tabla
        'fields'             => $this->__p->fields,
        'pks'                => $this->__p->pks,
        'belongTo'           => $this->__p->belongTo,
        'hasMany'            => $this->__p->hasMany,
        'hasManyAndBelongTo' => $this->__p->hasManyAndBelongTo,
        'uniques'            => $this->__p->uniques,

      ));

      // Señalar campo createdAt si ha sido señalado.
      if($this->__p->createdAtField)
        $this->__p->table->addCreatedAtField(
          $this->__p->createdAtField===true? null : $this->__p->createdAtField
        );

      // Señalar campo updatedAt si ha sido señalado.
      if($this->__p->updatedAtField)
        $this->__p->table->addUpdatedAtField(
          $this->__p->updatedAtField===true? null : $this->__p->updatedAtField
        );

      // Inicializar los validators
      $this->start($this->__p->table);
      
    }

    // Obtener los campos
    $fields = $this->__p->table->getFields();

    // Por cada campo
    foreach($fields as $fieldName => $field){

      // Obtener nombre del campo
      $fieldNameBD = $field->getName();

      $value = null;

      // Si el campo existe en los parametros
      if(isset($params[$fieldNameBD])){
        // Obtener el valor
        $value = $params[$fieldNameBD];
        // Eliminar de los parametros
        unset($params[$fieldNameBD]);
      }else{
        // Si no existe en los parametros se toma el valor
        // por defecto del campo
        $value = $field->getDefaultValue();
      }

      // Asignar valor mediante el metodo set
      $this->set($fieldName, $field->parseValue($value));

    }

    // Llamar al constructor
    parent::__construct($params);

    // Limpiar errores
    $this->clearErrors();

    // Tomar valores reales
    $this->__p->realValues = $this->toArray();

    // Llamar el metodo init del modelo
    $this->init();

  }

  /**
   * Para la inizialización de los validadores de la tabla.
   */
  protected function start(AmTable $table){

  }

  /**
   * Método redefinido el usuario para inicializaciones customizadas del modelo.
   */
  public function init(){

  }

  /**
   * Funcion para preparar los valores del model antes de guardar.
   */
  public function prepare(){

  }

  /**
   * Devuelve el valor de un registro en un campo.
   * @param  string $field Nombre del campo.
   * @return mixed         Valor del registro en el campo.
   */
  public function get($field){

    return $this->$field;

  }

  /**
   * Asignar valores a un campo del reistro.
   * @param  string  $field Nombre del campo a asignar.
   * @param  string  $value Valor a asignar.
   * @param  boolean $isRaw Indica si el valor asigando es un valor puro o no.
   * @return $this
   */
  public function set($field, $value, $isRaw = false){

    $this->$field = $value;
    $this->__p->rawValues[$field] = $isRaw;
    return $this;

  }

  /**
   * Devuelve la tabla del modelo.
   * @return AmTable Instancia de la tabla.
   */
  public function getTable(){

    return $this->__p->table;

  }

  /**
   * Devuelve si es una instancia del modelo nueva o fué cargado desde la BD.
   * @return bool Si es una instancia del modelo nueva.
   */
  public function isNew(){

    return $this->__p->isNew;

  }
  /**
   * Devuelve le hash con los campos que poseen valores nativos del BDMS.
   * @return hash Hash de booleans.
   */
  public function getRawValues(){

    return $this->__p->rawValues;

  }

  /**
   * Devuelve el hash de valores reales del registro.
   * @return hash Hash de valores.
   */
  public function getRealValues(){

    return $this->__p->realValues;

  }

  /**
   * Dvuelve el valore real de un campo.
   * @param  string $name Nombre del campo.
   * @return mixed        Valore real del campo.
   */
  public function getRealValue($name){

    return itemOr($name, $this->__p->realValues);

  }

  /**
   * Devuelve un error específico del registro. Si no se especifíca el nombre
   * del error se devuelve un hash con los errores en un campo, y si no se
   * especifíca el nombre del campo se devuelve un hash de hash de errores de
   * todo el registro.
   * @param  string $field     Nombre del campo.
   * @param  string $errorName Nombre del error.
   * @return string/hash       Mensaje del error o Hash de mensajes de errores.
   */
  public function getErrors($field = null, $errorName = null){

    // Se retorna todo los errores
    if(!isset($field))
      return $this->__p->errors;

    // Si no existe se crea el array
    if(!isset($this->__p->errors[$field]))
      $this->__p->errors[$field] = array();

    // Se devuelve el hash de errores del campo consultado
    if(!isset($errorName))
      return $this->__p->errors[$field];

    // Se devuelve el error especifico del campo consultado
    if(!isset($errorMsg))
      return $this->__p->errors[$field][$errorName];

    // Devolver todos los errores
    return $this->__p->errors;

  }

  /**
   * Limpiar los errores.
   */
  public function clearErrors(){

    $this->__p->errors = array(); // Resetear los errores
    $this->__p->errorsCount = 0;  // Resetear la cantidad de errores
    
  }

  /**
   * Agregar un error con un nombre a un campo.
   * @param strin $field     Nombre del campo.
   * @param strin $errorName Nombre del error.
   * @param strin $errorMsg  Mensaje de error a agregar.
   */
  public function addError($field, $errorName, $errorMsg){

    // Se asigna el error
    $this->__p->errors[$field][$errorName] = $errorMsg;
    $this->__p->errorsCount++;
    return $this;

  }

  /**
   * Funcion para signar valores a los atributos en lote.
   * @param hash  $values Hash de valores a agregar.
   * @param array $fields Lista de campo que se deben tomar en cuenta.
   */
  public function setValues(assh $values, array $fields = array()){

    // Obtener la tabla
    $table = $this->__p->table;

    // Si no se recibió la lista de campos a asignar, se tomarán
    // todos los campos de la tabla
    if(empty($fields))
      $fields = array_keys($table->getFields());

    if(empty($fields))
      $fields = array_keys($values);

    foreach($fields as $fieldName){
      $field = $table->getField($fieldName);  // Obtener el campos
      // Si exist el campo y es no es un campo autoincrementable
      if((!$field || !$field->isAutoIncrement()) && isset($values[$fieldName]))
        // Se asigna el valor
        $this->set($fieldName, $values[$fieldName]);
    }

  }

  /**
   * Indica si un campo ha cambiado de valor ono.
   * @param  string $name Nombre del campo.
   * @return bool         Si cambió o nó e valor.
   */
  public function changed($name){

    return $this->getRealValue($name) != $this->get($name);

  }

  /**
   * Realiza la validación del modelo e indica si cumple con todas las
   * validaciones. En el caso de el nombre del campo sea especificado entonces
   * realizará solo la validación del dicho campo y retornará si dicho campo es
   * válido.
   * @param  string $field Nombre del campo que se desea validar.
   * @return bool          Si es válido.
   */
  public function isValid($field = null){

    // Limpiar los errores
    $this->clearErrors();

    // Si se indico un campo
    // Validar solo el campo
    $this->__p->table->validate($this, $field);

    // Es valido si no se generaron errores
    return $this->__p->errorsCount === 0;

  }

  /**
   * Guarda los cambios del registro. Si es un registro nuevo entonces el
   * registro se intentará insertar en la tabla, de lo contrario se intentará
   * actualizar los datos del registro.
   * @return int/bool Si se insertó como un nuevo registro y la tabla donde se
   *                  se insertó posee un único campo autoincrementable se
   *                  devuelve el valor de dicho campo, de lo contrario solo
   *                  devolverá si la operación se efectuó satisfactoriamente.
   */
  public function save(){

    // Guardar relaciones belongTo
    foreach(array_keys($this->__p->table->getForeigns()) as $relationName){
      $relation = $this->getRelation($relationName);
      $relation->beforeSave($relationName);
    }

    // Obener la tabla
    $ret = $this->__p->table->save($this);

    // Si retorna false salir.
    if($ret === false)
      return false;

    // Se guardó satisfactoriamente
    // Indicar que ya no es registro nuevo
    $this->__p->isNew = false;

    // Los nuevo valores reales del registro serán los guardados
    $this->__p->realValues = $this->toArray();

    // Guardar relaciones hasMany y hasManyAndBelongTo
    foreach(array_keys($this->__p->table->getForeigns()) as $relationName){
      $relation = $this->getRelation($relationName);
      $relation->afterSave($relationName);
    }

    return true;

  }

  /**
   * Elimina el registro de la tabla.
   * @return bool Indica si se eliminó el registro correctamente.
   */
  public function delete(){

    return !!$this->__p->table->querySelectModel($this)->delete();

  }

  public function getRelation($foreignName){

    $foreign = itemOr($foreignName, $this->__p->foreigns);

    // Si no se ha generado una consulta para la relación
    if(!isset($foreign)){

      // Obtener la relación
      $foreign = $this->__p->table->getForeign($foreignName);

      // Si no existe la relación
      if(!isset($foreign))
        throw Am::e('AMSCHEME_RELATION_NOT_EXISTS', get_class($this),
          $foreignName);

      $this->__p->foreigns[$foreignName] = AmRelation::create($this, $foreign);

    }

    return $this->__p->foreigns[$foreignName];

  }

  /**
   * Los métodos no definidos son tratados como relaciones. Para esto se
   * sobreescribe el método __call.
   * @param  string $foreignName Nombre del método llamado.
   * @param  array $arguments    Argumentos con los que se llamó el método.
   * @return                     Instancia de la relación.
   */
  public function __call($foreignName, $arguments){

    $relation = $this->getRelation($foreignName);

    // Si se pasó un elmenento entonces se está asignando el modelo
    if(count($arguments) > 0){

      $relation->_set($arguments[0]);

      return $this;

    }

    // Query para de la relación
    return $relation;

  }

  /**
   * Devuelve la instancia de la tabla correspondiente al modelo.
   * @return AmTable Instancia de la tabla.
   */
  public static function me(){

    $className = get_called_class();

    $instance = new $className;

    return $instance->getTable();

  }

  /**
   * Devuelve un query para obtener todos los registro de la tabla
   * correspondiente al modelo actual.
   * @param  string  $alias      Alias para la tabla en el query.
   * @param  bool    $withFields Si la consulta incluirá la seleción de todos
   *                             los campos especificados en el modelo.
   * @return AmQuery             Query select para obtener todos los registro de
   *                             la tabla.
   */
  public static function all($alias = 'q', $withFields = false){

    return self::me()->all($alias, $withFields);

  }

  /**
   * Crea la tabla en la BD.
   * @param  bool $ifNotExists Se agrega el parémtro IS NOT EXISTS.
   * @return bool              Si se creó la tabla. Si la tabla existe y el
   *                           parámetro $ifNotExists == true, retornará true.
   */
  public static function create($isNotExists = true){

    return self::me()->create($isNotExists);

  }

  /**
   * Elimina la tabla de la BD.
   * @param  bool $ifExists Si se agrega la clausula IF EXISTS.
   * @return bool           Si se eliminó la Tabla. Si la Tabla no existe y el
   *                        parémetro $ifExists==true entonces retorna true.
   */
  public static function drop($isExists = true){

    return self::me()->drop($isExists);

  }

  /**
   * Indica si existe la tabla en la BD.
   * @return bool Si existe la tabla.
   */
  public static function exists(){

    return self::me()->exists();

  }

  /**
   * Vacía la tabla.
   * @param  bool $ignoreFk Si se ingorará los Foreing Keys.
   * @return bool           Si se vació la tabla satisfactoriamente.
   */
  public static function truncate($ignoreFK = false){

    return self::me()->truncate($ignoreFK);

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
  public static function insertInto($values, array $fields = array()){

    return self::me()->insertInto($values, $this, $fields);

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
  public static function by($field, $value, $alias = 'q', $withFields = false){

    return self::me()->by($field, $value, $alias, $withFields);

  }

  /**
   * Obtener todos los registros de buscar por un campos
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
  public static function allBy($field, $value, $as = null, $withFields = false){

    return self::me()->allBy($field, $value, $as, $withFields);

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
  public static function oneBy($field, $value, $type = null,
    $withFields = false){

    return self::me()->oneBy($field, $value, $type, $withFields);
    
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
  public static function byId($id, $alias = 'q', $withFields = false){

    return self::me()->byId($id, $alias, $withFields);

  }

  /**
   * Devuelve un modelo con el registro solicitado.
   * @param  string/int/array $id    Id del registro. Si la tabla tiene un PK
   *                                 con un único campo entonces puede ser un
   *                                 int o string, si es un PK compuesto
   *                                 estonces debe ser un hash con los valores
   *                                 del id a buscar.
   * @param  string           $alias Alias de la tabla en el query.
   * @param  string           $as    String con el nombre del modelo o formato
   *                                 de retorno. Puede ser 'array', 'am',
   *                                 'object', nombre de una clase existente o
   *                                 identificador de un modelo.
   * @return AmModel/bool            El modelo en el formato especificado por
   *                                 el parámetro $as o false si no se consigió
   *                                 alguna coincidencia.
   */
  public static function find($id, $as = null){

    return self::me()->find($id, $as);

  }
  
}

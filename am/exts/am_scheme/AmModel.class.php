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
     * Nombre del esquema a la que pertenece el modelo.
     */
    $schemeName = '',
    
    /**
     * Nombre de la tabla a la que pertenece el modelo.
     */
    $tableName = null,
    
    /**
     * Hahs con las definiciones de los campos.
     */
    $fields = null,
    
    /**
     * Nombre del campo para la fecha de creación.
     */
    $createdAtField = false,
    
    /**
     * Nombre del campo para la fecha de actualización.
     */
    $updatedAtField = false,
    
    /**
     * Definición de relaciones a otros modelos.
     */
    $referencesTo = null,
    
    /**
     * Definición de relaciones de otros modelos.
     */
    $referencesBy = null,
    
    /**
     * Definición de llaves únicas.
     */
    $uniques = null,
    
    /**
     * Instancia de la tabla del modelo.
     */
    $table = null,
    
    /**
     * Indica si es un registro nuevo.
     */
    $isNew = true,
    
    /**
     * Hash de errores.
     */
    $errors = array(),
    
    /**
     * Indica que valores de las propiedades de la instancia del objeto son
     * nativos del BDMS.
     */
    $rawValues = array(),
    
    /**
     * Valores reales.
     */
    $realValues = array(),
    
    /**
     * Validadores del modelo.
     */
    $validators = null,
    
    /**
     * Cantidad de errores
     */
    $errorsCount = 0;

  /**
   * El constructor se encarga instancia la tabla correspondienteal modelo si
   * esta aún no ha sido instanciada.
   * @param array   $params Valores iniciales del modelo.
   * @param boolean $isNew  Si es un resgistro nuevo.
   */
  final public function __construct($params = array(), $isNew = true) {

    // Inicializar la tabla si no ha sido inicializada
    $className = get_class($this);

    // Obtener la instancia del modelo.
    $scheme = AmScheme::get($this->schemeName);

    // Signar si es nuevo.
    $this->isNew = $isNew;

    // Obtener la instancia de la tabla desde el esquema si esta cargada.
    $this->table = $scheme->getTableInstance($className);

    // Si no esta cargada la tabla
    if(!$this->table){

      // Crear instancia anonima de la tabla
      $this->table = new AmTable(array(

        // Asignar fuente
        'schemeName'    => $this->schemeName,
        'tableName'     => $this->tableName,
        'model'         => $className,

        // Detalle de la tabla
        'fields'        => $this->fields,
        'pks'           => $this->pks,
        'referencesTo'  => $this->referencesTo,
        'referencesBy'  => $this->referencesBy,
        'uniques'       => $this->uniques,

      ));

      // Señalar campo createdAt si ha sido señalado.
      if($this->createdAtField)
        $this->table->addCreatedAtField(
          $this->createdAtField===true? null : $this->createdAtField);

      // Señalar campo updatedAt si ha sido señalado.
      if($this->updatedAtField)
        $this->table->addUpdatedAtField(
          $this->updatedAtField===true? null : $this->updatedAtField);

      // Obtener validadores de la tabla.
      $this->validators = $this->table->getValidators();

      // Inicializar los validators
      $this->start();
      
    }

    // Obtener los campos
    $fields = $this->table->getFields();

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
      $this->$fieldName = $field->parseValue($value);

    }

    // Llamar al constructor
    parent::__construct($params);

    // Limpiar errores
    $this->clearErrors();

    // Tomar valores reales
    $this->realValues = $this->toArray();

    // Llamar el metodo init del model
    $this->init();

  }

  /**
   * Para la inizialización de los validadores de la tabla.
   */
  protected function start(){

  }

  /**
   * Funcion para preparar los valores del model antes de guardar.
   */
  public function prepare(){

  }

  /**
   * Método redefinido el usuario para inicializaciones customizadas del modelo.
   */
  public function init(){

  }

  /**
   * Devuelve la tabla del modelo.
   * @return AmTable Instancia de la tabla.
   */
  public function getTable(){

    return $this->table;

  }

  /**
   * Devuelve si es una instancia del modelo nuevo o fué cargado desde la BD.
   * @return boolean Si es una instancia del modelo nuevo.
   */
  public function isNew(){

    return $this->isNew;

  }

  /**
   * Cantidad de errores generados en los validadores.
   * @return int Cantidad de errores.
   */
  public function errorsCount(){

    return $this->errorsCount;

  }

  /**
   * Devuelve le hash con los campos que poseen valores nativos del BDMS.
   * @return hash Hash de booleans.
   */
  public function getRawValues(){

    return $this->rawValues;

  }

  /**
   * Devuelve el hash de valores reales de la instancia del modelo.
   * @return hash Hash de valores.
   */
  public function getRealValues(){

    return $this->realValues;

  }

  /**
   * Dvuelve el valore real de un campo.
   * @param  string $name Nombre del campo.
   * @return any          Valore real del campo.
   */
  public function getRealValue($name){

    return itemOr($name, $this->realValues);

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
   * Devuelve un validator de un campo especifico.
   * @param  [type] $name          [description]
   * @param  [type] $validatorName [description]
   * @return [type]                [description]
   */
  public function getValidator($name, $validatorName){

    return isset($this->validators->$name[$validatorName])?
      $this->validators->$name[$validatorName] : null;

  }

  // Metodo para eliminar validator
  /**
   * [dropValidator description]
   * @param  [type] $name          [description]
   * @param  [type] $validatorName [description]
   * @return [type]                [description]
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

  // Agrega un validator a la tabla
  /**
   * [setValidator description]
   * @param [type] $name          [description]
   * @param [type] $validatorName [description]
   * @param [type] $validator     [description]
   * @param array  $options       [description]
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

    // Si el tercer parametro es un array, entonces este representa las opciones.
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

  // Devuelve el valor de un campo. Si existe un metodo get para dicho campo
  // se obtiene el valor mediante este. De lo contrario se obtiene
  // directamente.
  /**
   * [getFieldValue description]
   * @param  [type] $field [description]
   * @return [type]        [description]
   */
  public function getFieldValue($field){

    // Obtener el valor del campo
    return $this->$field;

  }

  // Devuelve todos los errores del model, los errores de un campo, o un error especifico
  /**
   * [getErrors description]
   * @param  [type] $field     [description]
   * @param  [type] $errorName [description]
   * @return [type]            [description]
   */
  public function getErrors($field = null, $errorName = null){

    // Se retorna todo los errores
    if(!isset($field))
      return $this->errors;

    // Si no existe se crea el array
    if(!isset($this->errors[$field]))
      $this->errors[$field] = array();

    // Se devuelve el hash de errores del campo consultado
    if(!isset($errorName))
      return $this->errors[$field];

    // Se devuelve el error especifico del campo consultado
    if(!isset($errorMsg))
      return $this->errors[$field][$errorName];

    // Devolver todos los errores
    return $this->errors;

  }

  // Limpiar los errores
  /**
   * [clearErrors description]
   * @return [type] [description]
   */
  public function clearErrors(){

    $this->errors = array(); // Resetear los errores
    $this->errorsCount = 0;  // Resetear la cantidad de errores
    
  }

  // Agregar error
  /**
   * [addError description]
   * @param [type] $field     [description]
   * @param [type] $errorName [description]
   * @param [type] $errorMsg  [description]
   */
  public function addError($field, $errorName, $errorMsg){

    // Se asigna el error
    $this->errors[$field][$errorName] = $errorMsg;
    $this->errorsCount++;
    return $this;

  }

  // Método get para asignar si es o no un registro nuevo
  /**
   * [setIsNew description]
   * @param [type] $value [description]
   */
  public function setIsNew($value){

    return $this->isNew = $value;

  }

  // Asignar valores a un campo por modelo
  /**
   * [setValue description]
   * @param [type]  $field [description]
   * @param [type]  $value [description]
   * @param boolean $isRaw [description]
   */
  public function setValue($field, $value, $isRaw = false){

    $this->$field = $value;
    $this->rawValues[$field] = $isRaw;

    return $this;

  }

  // Funcion para signar valores a los atributos en lote
  /**
   * [setValues description]
   * @param [type] $values [description]
   * @param array  $fields [description]
   */
  public function setValues($values, array $fields = array()){

    // Obtener la tabla
    $table = $this->getTable();

    // Recorrer cada columan de cada referencia
    $references = $table->getReferencesTo();
    foreach($references as $rel){
      $cols = array_keys($rel->getColumns());
      foreach($cols as $from){

        // Las referencias si es un valor vacío se debe setear a null
        $value = trim(itemOr($from, $values));
        $values[$from] = empty($value) ? null : $value;

      }
    }

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
        $this->$fieldName = $values[$fieldName];
    }

  }

  // Método que indica si un campo ha cambiado o no de valor desde
  // su inicialización
  /**
   * [hasChanged description]
   * @param  [type]  $name [description]
   * @return boolean       [description]
   */
  public function hasChanged($name){

    return $this->getRealValue($name) != $this->$name;

  }

  // Obtener los campos los cambios de los campos que se ha realizado
  /**
   * [getChanges description]
   * @return [type] [description]
   */
  public function getChanges(){

    $changes = array(); // Para el retorno

    // Recorrer los valores reales
    foreach($this->realValues as $name => $value){
      // Si el campo cambió se agrega al listado de cambios
      if($this->hasChanged($name)){
        $changes[$name] = array(
          'from' => $value,
          'to' => $this->$name,
        );
      }
    }
    
    return $changes;

  }

  // Devuelve el indice correspondiente al registro
  /**
   * [index description]
   * @return [type] [description]
   */
  public function index(){

    $ret = array(); // Para el retorno
    $pks = $this->getTable()->getPks(); // Obtener PKs

    if(empty($pks))
      throw Am::e('AMSCHEME_MODEL_DONT_HAVE_PK', get_class($this));

    // Agregar los IDs
    foreach($pks as $pk)
      $ret[$pk] = $this->getRealValue($pk);

    return $ret;

  }

  // Devuelve un array con los valores de los campos de la tabla
  /**
   * [dataToArray description]
   * @param  boolean $withAI [description]
   * @return [type]          [description]
   */
  public function dataToArray($withAI = true){

    // Obtener la tabla
    $table = $this->getTable();

    $ret = array(); // Para el retorno

    // Obtener los campos
    if(!$table->isSchemeStruct())
      return $this->toArray();
    
    $fields = array_keys($table->getFields());

    foreach($fields as $fieldName){
      $field = $table->getField($fieldName);  // Obtener el campos
      // Si se pidió incorporar los valores autoincrementados
      // o si el campo no es autoincrementado
      if($withAI || !$field || !$field->isAutoIncrement())
        // Se agrega el campo al array de retorno
        $ret[$fieldName] = $this->$fieldName;
    }

    return $ret;

  }

  // Obtener lo valores de un registro en forma de array
  /**
   * [getValues description]
   * @param  boolean $mask [description]
   * @return [type]        [description]
   */
  public function getValues($mask = false){

    $ret = $this->toArray();

    foreach($ret as $field){
      if(is_array($mask) && !in_array($field, $mask)){
        unset($ret[$field]);
      }
    }

    return $ret;

  }

  // Devuelve una consulta que selecciona el registro actual
  /**
   * [getQuerySelectItem description]
   * @param  string  $alias      [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public function getQuerySelectItem($alias = 'q', $withFields = false){

    return $this->getTable()->findById($this->index(), $alias, $withFields);

  }

  // Devuelve una consulta para realizar los campos realizados en el modelo
  /**
   * [getQueryUpdate description]
   * @return [type] [description]
   */
  protected function getQueryUpdate(){

    $table = $this->getTable();

    // Obtener los campos
    if($table->isSchemeStruct())
      $fields = array_keys($table->getFields());
    else
      $fields = array_keys($this->toArray());

    // Obtener una consulta para selecionar el registro
    $q = $this->getQuerySelectItem();

    // Recorrer los campos para agregar los sets
    // de los campos que cambiaron
    foreach($fields as $fieldName){

      // Si el campo cambió
      if($this->hasChanged($fieldName))
        // Agregar set a la consulta
        $q->set($fieldName, $this->$fieldName);

    }

    // Devolver consulta generada
    return $q;

  }

  // Validar todo el modelo
  /**
   * [validate description]
   * @return [type] [description]
   */
  public function validate(){

    // Limpiar los errores
    $this->clearErrors();

    // Obtener nombre de validator definidos
    $validatorNames = array_keys((array)$this->getValidators());

    // Preparar campos
    $this->prepare();

    // Validar todos los campos
    foreach($validatorNames as $field)
      $this->validateField($field);

  }

  // Ejecuta las validaciones para un campo
  /**
   * [validateField description]
   * @param  [type] $field [description]
   * @return [type]        [description]
   */
  public function validateField($field){
    // Obtener validator del campo
    $validators = $this->getValidators($field);

    // Sino se obtiene un array de validators retornar
    // sine valuar
    if(!is_array($validators))
      return;

    foreach($validators as $nameValidator => $validator){
      // Si el modelo no cumple con la validacion
      if(!$validator->isValid($this))
        // Se agrega el error
        $this->addError($field, $nameValidator, $validator->getMessage($this));
    }

  }

  /**
   * [isValid description]
   * @param  [type]  $field [description]
   * @return boolean        [description]
   */
  public function isValid($field = null){

    // Si se indico un campo
    if(isset($field)){
      // Limpiar los errores
      $this->clearErrors();
      // Validar solo el campo
      $this->validateField($field);
    }else{

      // Validar todos los campos
      $this->validate();
    }

    // Es valido si no se generaron errores
    return $this->errorsCount() === 0;

  }

  // Guarda los cambios del registro
  // Si es un registro nuevo entonces el registro
  // se intentará insertar en la tabla.
  // Si no es un registro nuevo entonces
  // Se intentará actualziar los datos del registro
  // imagen en la tabla
  /**
   * [save description]
   * @return [type] [description]
   */
  public function save(){

    // Si todos los campos del registro son válidos
    if($this->isValid()){

      // Si es un registro nuevo se insertará
      if($this->isNew()){

        // Insetar en la BD. Ret será igual a de generado
        // del registro en el caso de tener como PK un campo
        // autoincrementable o false si se generá un error
        if(false !== ($ret = $this->insertInto())){
          // Obtener todos los campos de la tabla del modelo
          $fields = $this->getTable()->getFields();

          // Recorrer campos
          foreach($fields as $f){

            // Agregar el valor que retorno el insert
            // si se trata de un campo autoincrementable
            if($f->isAutoIncrement()){

              // Obtener el nombre del método SET
              $fieldName = $f->getName();

              // para asigar el valor autoincrementado
              $this->$fieldName = $ret;

            }

          }

          // Si el valor es diferente de false
          // Indicar que ya no es registro nuevo
          $this->isNew = false;

          // Los nuevo valores reales del registro serán
          // los que se tiene desdpue de insertar
          $this->realValues = $this->toArray();

          // Si ret == 0 es xq se interte correctamenre
          // pero la tabla no tiene una columna autoincrement
          // Se retorna verdadero o el valor del ID generado
          // para el registro si se agregó correctamenre
          // de lo contrario se retorna falso
          return $ret === 0 ? true : $ret;

        }

      }else{

        // Se intenta actualizar los datos del registro en la BD
        if($this->update()){

          // Si se actualiza correctamente entonces
          // los datos reales nuevos seran los que
          // tiene el registro
          $this->realValues = $this->toArray();

          // retornar true indicando el exito de la operacion
          return true;

        }

      }

      // Si se llega a este punto es porque se generó un error
      // en la insercion o actualizacion, por lo que se agrega un
      // error global con el ultimo error generado en  el Gestor
      $this->addError('__global__',
        $this->getTable()->getScheme()->getErrNo(),
        $this->getTable()->getScheme()->getError());

    }

    return false;

  }

  /**
   * [insertInto description]
   * @return [type] [description]
   */
  public function insertInto(){

    // Si se inserta satisfactoriamente
    if($this->getTable()->insertInto(array($this)))

      // Devolver el último id insertado.
      return $this->getTable()->getScheme()->getLastInsertedId();

    // De lo contrario devolver falso.
    return false;

  }

  /**
   * [update description]
   * @return [type] [description]
   */
  public function update(){

    return !!$this->getQueryUpdate()->update();

  }

  /**
   * [delete description]
   * @return [type] [description]
   */
  public function delete(){

    return !!$this->getQuerySelectItem()->delete();

  }

  /**
   * [me description]
   * @return [type] [description]
   */
  public static function me(){

    $className = get_called_class();

    $instance = new $className;

    return $instance->getTable();

  }

  // GET QUERY TO ALL RECORDS
  /**
   * [all description]
   * @param  string  $alias      [description]
   * @param  boolean $withFields [description]
   * @return [type]              [description]
   */
  public static function all($alias = 'q', $withFields = false){

    return self::me()->all($alias, $withFields);

  }
  
  // // Convirte el ID del registro en un string con cada uno de sus valores
  // // separados por '/'
  // public function pkToString($encode = false){
  //   $ret = array();
  //   foreach($this->index() as $index)
  //     $ret[] = ($encode===true)? urlencode($index) : $index;
  //   return implode('/', $ret);
  // }

}
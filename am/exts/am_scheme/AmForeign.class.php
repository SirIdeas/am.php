<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para las relaciones entre las tablas.
 */
class AmForeign extends AmObject{

  protected 

    /**
     * String que determina el tipo de relación.
     */
    $type = null,
    
    /**
     * Modelo al que apunta la relación.
     */
    $model = '',

    /**
     * Campos extras a selecionar
     */
    $selects = array(),
    
    /**
     * Hash de columnas relacionadas.
     */
    $cols = array(),
    
    /**
     * Hash de columnas relacionadas.
     */
    $table = null,

    /**
     * Indica si la relación puede tener valores nulos
     */
    $allowNull = true,
    
    /**
     * Nombre de la tabla através de la cual se realiza la realción.
     */
    $through = null;
    
  /**
   * Devuelve el tipo de relación.
   * @return string Tipo de relación.
   */
  public function getType(){
    
    return $this->type;

  }
    
  /**
   * Devuelve el nombre del model a la que referencia.
   * @return string Nombre del model.
   */
  public function getModel(){
    
    return $this->model;

  }

  /**
   * Devuelve el hash con las columnas relacionadas.
   * @return hash Hash con las columnas relacionadas.
   */
  public function getCols(){
    
    return $this->cols;

  }

  /**
   * Devuelve si el foreing key puede o no ser null.
   * @return bool.
   */
  public function getAllowNull(){
    
    return $this->allowNull;

  }

  /**
   * Devuelve el hash con las relaciones extras.
   * @return hash Hash con las relaciones extras.
   */
  public function getTable(){
    
    return $this->table;

  }

  /**
   * Nombre de la tabla a través la cual se relacionan los elementos.
   * @return string Nombre de la tabla.
   */
  public function getThrough(){
    
    return $this->through;

  }
    
  /**
   * Devuelve el intancia de la tabla a la que apunta la realación.
   * @return AmTable Instancia de la tabla.
   */
  public function getTableInstance(){

    // Obtener el modelo
    $classModel = $this->model;
    
    // Devolver la tabla
    return $classModel::me();

  }

  /**
   * Formateador del resultado del query. Convierte las columnas tomadas de la
   * tabla intermedia y las guarda en un array en un atributo con el mismo
   * nombre de la tabla relacional. Además borra los campos tomados.
   * @param  array/AmModel $record Registro a formatear.
   * @return array/AmModel         Registro formateado.
   */
  public function queryFormatter($record){

    // Obtener la tabla a través de la cual se realiza la realación.
    $through = $this->getThrough();
    
    $ret = array();
    // Agregar campos extras a selecionar
    foreach($this->selects as $as => $field){
      $field = $through? "{$through}.$as" : $field;
      $ret[$as] = $record[$field];
      unset($record[$field]);
    }

    $record[$through] = $ret;
    return $record;
  }

  /**
   * Generador de la consulta para la relación basado en un modelo.
   * @param  AmModel $model Instancia de AmModel a la que se quiere obtener
   *                        la relación.
   * @return AmQuery        Consulta generada.
   */
  public function getQuery(AmModel $model){

    // Obtener la tabla
    $table = $this->getTableInstance();

    // Obtener el nombre de la tabla.
    $tableName = $table->getTableName();

    // Obtener la tabla a través de la cual se realiza la realación.
    $through = $this->through;

    // Obtener una consulta con todos los elmentos.
    $query = $table->all($tableName)->select(Am::raw("{$tableName}.*"));

    // Agregar campos extras a selecionar
    if(!empty($through)){

      foreach($this->selects as $as => $field){
        $query->selectAs("{$through}.$field", $as);
      }

      // Si tiene una tabla a traves y tiene campos selecionados entonces se
      // agrega el formateador
      if(!empty($this->select)){
        $query->setFormatter(array($this, 'queryFormatter'));
      }

    }

    // Obtener las columnas
    $cols = $this->getCols();

    // Agregar condiciones de la relacion
    foreach($cols as $from => $field){
      $query->andWhere("{$tableName}.{$field}", $model->getRealValue($from));
    }

    // Devolver query
    return $query;

  }

  /**
   * Devuelve una array con los datos de la relación.
   * @return array Relación como un array.
   */
  public function toArray(){

    return array(
      'type' => $this->type,
      'cols' => $this->cols,
      'model' => $this->model,
      'table' => $this->table,
      'selects' => $this->selects,
      'through' => $this->through,
    );

  }

  /**
   * Permite construir la configuración de una relación.
   * @param  AmTable     $tbl  Instancia de la tabla a la que se le desea
   *                           crear la relación.
   * @param  string      $type Tipo de relación (belongTo, hasMany o 
   *                           hasManyAndBelongTo).
   * @param  string      $name Nombre de la relación
   * @param  string/hash $conf Modelo con el que se está relacionando o hash con
   *                           la configuración inicial de la relación.
   * @return hash              Configuración completa
   */
  public static function foreignConf($tbl, $type, $name, $conf){

    // Se utilizará la configuración automática de la relación
    $confOf = null;
    if(is_string($conf)){
      list($model, $confOf) = explode('::', $conf.'::');
      $conf = array('model' => $model);
    }

    // Obtener el modelo de la relación
    $model = AmScheme::model($conf['model']);

    // Generar error si el modelo no existe 
    if(!$model){
      throw Am::e('AMSCHEME_MODEL_NOT_EXISTS', $conf['model']);
    }

    // Asignar el modelo real
    $conf['model'] = $model;

    if($type !== 'hasManyAndBelongTo'){

      // Definir columnas si no esta definidas
      if(!isset($conf['cols']) || empty($conf['cols'])){
        $toTbl = $model::me();

        // Obtener tabla y campos PK
        $pks = array();

        if($type === 'hasMany'){
          $pks = $tbl->getPks();
        }else{
          $pks = $toTbl->getPks();
        }

        $conf['cols'] = array();
      
        $foreign = null;
        if(!empty($confOf)){
          $foreign = self::foreignConf($toTbl, 'belongTo', $confOf, $tbl->getModel());
        }

        // Guardar columnas
        if(isset($foreign)){
          foreach ($foreign['cols'] as $colFrom => $colTo){
            $conf['cols'][$colTo] = $colFrom;
          }
        }elseif($type == 'belongTo'){
          foreach ($pks as $pk){
            $conf['cols']["{$pk}_{$name}"] = $pk;
          }
        }elseif($type == 'hasMany'){
          foreach ($pks as $pk){
            $conf['cols'][$pk] = "{$pk}_{$name}";
          }
        }

      }

    }else{

      // Tabla relacionada
      $refTbl = $model::me();

      // Para obtener le nombre de la tabla intermedia
      $tn1 = $tbl->getTableName();
      $tn2 = $refTbl->getTableName();

      // Definir el nombre de la tabla mediante la cual enlaza las clases si no
      // está definida
      if(!isset($conf['through'])){

        // Obtener el nombre de la tabla intermedia en la BD
        if($tn1 < $tn2){
          $conf['through'] = "{$tn1}_{$tn2}";
        }elseif($tn1 > $tn2){
          $conf['through'] = "{$tn2}_{$tn1}";
        }else{
          $conf['through'] = "{$tn1}_{$tn2}";
        }

      }

      if(!is_array($conf['through'])){
        $conf['through'] = array('table' => $conf['through']);
      }

      if(!isset($conf['through']['model']) && is_subclass_of($conf['through']['table'], 'AmModel')){
        $conf['through']['model'] = $conf['through']['table'];
      }

      $conf['table'] = $conf['through']['table'];

      if(!isset($conf['through']['cols']) || empty($conf['through']['cols'])){
        $conf['through']['cols'] = array();
        $pks = $refTbl->getPks();
        foreach ($pks as $pk) {
          $conf['through']['cols'][$pk] = $pk;
        }
      }

      if(!isset($conf['cols']) || empty($conf['cols'])){
        $conf['cols'] = array();
        $pks = $tbl->getPks();
        foreach ($pks as $pk) {
          $conf['cols'][$pk] = $pk;
        }
      }

    }

    return $conf;
    
  }

}

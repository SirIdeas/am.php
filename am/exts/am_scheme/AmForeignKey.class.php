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
class AmForeignKey extends AmObject{

  protected
    
    /**
     * Modelo al que apunta la relación.
     */
    $model = '',

    /**
     * Joins de la relación
     */
    $joins = array(),

    /**
     * Campos extras a selecionar
     */
    $select = array(),
    
    /**
     * Hash de columnas relacionadas.
     */
    $cols = array();
    
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
   * Devuelve el hash con las relaciones extras.
   * @return hash Hash con las relaciones extras.
   */
  public function getJoins(){
    
    return $this->joins;

  }
    
  /**
   * Devuelve el intancia de la tabla a la que apunta la realación.
   * @return AmTable Instancia de la tabla.
   */
  public function getTable(){

    // Obtener el modelo
    $classModel = $this->model;
    
    // Devolver la tabla
    return $classModel::me();

  }

  /**
   * Generador de la consulta para la relación basado en un modelo.
   * @param  AmModel $model Instancia de AmModel a la que se quiere obtener
   *                        la relación.
   * @return AmQuery        Consulta generada.
   */
  public function getQuery(AmModel $model){

    // Obtener la tabla
    $table = $this->getTable();

    // Obtener el nombre de la tabla.
    $tableName = $table->getTableName();

    // Obtener una consulta con todos los elmentos.
    $query = $table->all($tableName)
      ->select("{$tableName}.*");

    // Agregar campos extras a selecionar
    foreach ($this->select as $as => $field)
      $query->selectAs($field, $as);

    // Obtener las columnas
    $cols = $this->getCols();

    // Obtener las joins
    $joins = $this->getJoins();

    foreach ($joins as $refTableName => $on) {
      foreach ($on as $from => $to) {
        $on[$from] = "{$from}={$to}";
      }
      $query->innerJoin($refTableName, implode('-', $on), $refTableName);
    }

    // Agregar condiciones de la relacion
    foreach($cols as $from => $to)
      $query->where("{$to}='{$model->$from}'");

    // Devolver query
    return $query;

  }

  /**
   * Devuelve una array con los datos de la relación.
   * @return array Relación como un array.
   */
  public function toArray(){

    return array(
      'model' => $this->model,
      'cols' => $this->cols,
      'joins' => $this->joins,
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

    // Si esta definida una relación con el mismo nombre generar un error.
    if($tbl->getForeingKey($name))
      throw Am::e('AMSCHEME_FOREIGN_ALREADY_EXISTS', $tbl->getModel(), $name);

    // Se utilizará la configuracoin automática de la relación
    if(is_string($conf))
      $conf = array('model' => $conf);

    // Obtener el modelo de la relación
    $model = AmScheme::model($conf['model']);

    // Generar error si el modelo no existe 
    if(!$model)
      throw Am::e('AMSCHEME_MODEL_NOT_EXISTS', $conf['model']);

    // Asignar el modelo real
    $conf['model'] = $model;

    if($type !== 'hasManyAndBelongTo'){

      // Definir columnas si no esta definidas
      if(!isset($conf['cols'])){

        $formTbl = array(
          'belongTo' => $model::me(),
          'hasMany' => $tbl,
        )[$type];

        // Obtener tabla y campos PK
        $pks = $formTbl->getPks();

        // Si la clave primaria no tiene campos generar un error.
        if(empty($pks))
          throw Am::e('AMSCHEME_MODEL_DONT_HAVE_PK', $formTbl->getModel());

        // Guardar columnas
        $conf['cols'] = array_combine($pks, $pks);

      }

    }else{

      // Tabla relacionada
      $refTbl = $model::me();

      // Generar error si el esquema de la tabla actual no es el mismo de la
      // tabla refrenciada
      if($tbl->getSchemeName() !== $refTbl->getSchemeName())
        throw Am::e(
          'AMSCHEME_HAS_MANY_AND_BELONG_TO_FOREIGN_DIFFERENT_SCHEMES', $name,
          $tbl->getModel(),    $tbl->getSchemeName(),
          $refTbl->getModel(), $refTbl->getSchemeName()
        );

      // Para obtener le nombre de la tabla intermedia
      $tn1 = $tbl->getTableName();
      $tn2 = $refTbl->getTableName();

      // Definir el nombre de la tabla mediante la cual enlaza las clases si no
      // está definida
      if(!isset($conf['through'])){

        // Obtener el nombre de la tabla intermedia en la BD
        if($tn1 < $tn2)     $conf['through'] = "{$tn1}_{$tn2}";
        elseif($tn1 > $tn2) $conf['through'] = "{$tn2}_{$tn1}";
        else                $conf['through'] = "{$tn1}_{$tn2}";

      }

      // Definir los joins si no han sido asignados
      if(!isset($conf['joins'])){

        // Para crear los joins con la tabla intermedia
        $joins = array();
        $pks = $refTbl->getPks();
        foreach($pks as $pk)
          $joins["{$tn2}.{$pk}"] = "{$conf['through']}.{$pk}_{$tn2}";

        // Guardar joins
        $conf['joins'] = array($conf['through'] => $joins);

      }

      // Si el select no es un array entonces se debe generar un error
      if(isset($conf['select']) && !is_array($conf['select']))
        throw Am::e(
          'AMSCHEME_HAS_MANY_AND_BELONG_TO_FOREIGN_SELECT_PARAM_MAY_BE_ARRAY',
          $name, $tbl->getModel()
        );

      // Definir columnas si no han sido asignadas
      if(!isset($conf['cols'])){

        // Para crear relaciones con el modleo actual
        $pks = $tbl->getPks();
        if(empty($pks))
          throw Am::e('AMSCHEME_MODEL_DONT_HAVE_PK', $tbl->getModel());

        $cols = array();
        foreach($pks as $pk)
          $cols[$pk] = "{$conf['through']}.{$pk}_{$tn1}";

        // Guardar columnas
        $conf['cols'] = $cols;

      }

    }

    return $conf;
    
  }

}

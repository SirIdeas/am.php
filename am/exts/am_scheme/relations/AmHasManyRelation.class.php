<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para instancias de relaciones hasMany.
 */
class AmHasManyRelation extends AmRelation{

  protected

    /**
     * Registros nuevos agregados.
     */
    $news = array(),

    /**
     * Registros agregados.
     */
    $addeds = array(),

    /**
     * Registros removidos
     */
    $removeds = array();

  /**
   * Las relaciones hasMany devuelven un query.
   * @return AmQuery Query de la relación.
   */
  public function _get(){

    return $this->getQuery();

  }

  /**
   * Contar la cantidad de registros de la relación. Se define este método
   * debido a que el métod mágico __call nunca podrá llamar el método count de
   * la consulta porque AmRelacion hereda de AmObjeto el cual ya tiene un método
   * count definido.
   * @return int Cantidad de registro que devolverá la relación.
   */
  public function count(){

    return $this->getQuery()->count();

  }

  /**
   * Hace que una instancia de un modelo pertenezca el registro dueño de la
   * relación
   * @param  AmModel $model Instancia del modelo a agregar.
   */
  private function sync(AmModel $model){

    // Obtener el reistro propietario de la relación.
    $record = $this->getRecord();

    // Obtener las columnas relacionadas.
    $index = $this->getForeign()->getCols();

    // Realizar asignaciones
    foreach ($index as $from => $to)
      $model->set($from, $record ? $record->get($to) : null);

  }

  /**
   * Devuelve la clave para un modelo. La clave consta solo en convertir en
   * json el identificador del registro.
   * @param  AmModel $model Registro del que se quiere obtener la clave.
   * @return string         Identificadoir del registro en json.
   */
  private function keyOf(AmModel $model){

    return json_encode($model->getTable()->indexof($model));

  }

  /**
   * Agrega un modelo al registro.
   * @param  AmModel $model Registro a agregar.
   * @return $this
   */
  public function add(AmModel $model){

    // chequear que el registro pertenezca al modelo.
    $this->checkModel($record);

    // Sincronizar el modelo a agregar.
    $this->sync($model);

    // Primero remueve el registro.
    $this->remove($model);

    // Si es un registro nuevo se agrega dentro de los nuevos.
    if($model->isNew())
      $this->news[] = $model;

    // De lo contrario de agrega en los agregados.
    else
      $this->addeds[$this->keyOf($model)] = $model;

    return $this;

  }

  /**
   * Marca un registro para eliminar.
   * @param  AmModel $model Registro a eliminar.
   * @return $this
   */
  public function remove(AmModel $model){

    // chequear que el registro pertenezca al modelo.
    $this->checkModel($record);

    // Se busca el registro dentro de los nuevos. Si existe se elimina.
    if($key = array_search($model, $this->news, true))
      unset($this->news[$key]);

    // Si no es un registro nuevo se agrega a los marcados para eliminar.
    if(!$model->isNew())
      $this->removeds[$this->keyOf($model)] = $model;

    return $this;

  }

  /**
   * Método que se ejecuta antes de guardar del registro propietario. En las
   * relaciones hasMany no se hace nada
   */
  public function beforeSave(){}

  /**
   * Después de guardar el registro propietario se debe guardar los registro
   * relacionados.
   */
  public function afterSave(){

    // Se debe actualizar todos los registros relacionados
    $record = $this->getRecord();
    $index = $this->getForeign()->getCols();
    $query = $this->getQuery();

    // Asignar cada campo de la relación
    foreach ($index as $from => $to)
      $query->set($from, $record->get($to));

    // Ejecutar acctualización
    $query->update();

    foreach($this->news as $i => $model){
      $this->sync($model);
      if($model->save())
        unset($this->news[$i]);
    }

    foreach($this->addeds as $i => $model){
      $this->sync($model);
      if($model->save())
        unset($this->addeds[$i]);
    }

    foreach($this->removeds as $i => $model){
      $this->sync($model);
      if($model->delete())
        unset($this->removeds[$i]);
    }


  }

}
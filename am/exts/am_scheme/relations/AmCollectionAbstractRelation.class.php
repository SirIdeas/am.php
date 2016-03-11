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
abstract class AmCollectionAbstractRelation extends AmRelation{

  protected

    /**
     * Registros nuevos agregados.
     */
    $news = array(),

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
    $this->checkModel($model);

    // Sincronizar el modelo a agregar.
    $this->sync($model);

    // Buscar el registro dentro de los agregados.
    $key = array_search($model, $this->news, true);

    // Si es un registro nuevo se agrega dentro de los nuevos.
    if($model->isNew() && !$key)
      $this->news[] = $model;

    // De lo contrario de agrega en los agregados.
    else
      $this->news[$this->keyOf($model)] = $model;

    return $this;

  }

  /**
   * Marca un registro para eliminar.
   * @param  AmModel $model Registro a eliminar.
   * @return $this
   */
  public function remove(AmModel $model){

    // chequear que el registro pertenezca al modelo.
    $this->checkModel($model);

    // Se busca el registro dentro de los nuevos. Si existe se elimina.
    if($key = array_search($model, $this->news, true))
      unset($this->news[$key]);

    unset($this->news[$this->keyOf($model)]);

    // Si no es un registro nuevo se agrega a los marcados para eliminar.
    if(!$model->isNew())
      $this->removeds[$this->keyOf($model)] = $model;

    return $this;

  }

  /**
   * Método que se ejecuta antes de guardar del registro propietario. Se
   * utiliza para obtener el query con el que se actualizará los registro
   * relacionados antes de que se actualice.
   */
  public function beforeSave(){

    $record = $this->getRecord();
    $this->beforeIndex = $record->getTable()->indexof($record);

  }

  /**
   * Hace que una instancia de un modelo pertenezca el registro dueño de la
   * relación
   * @param  AmModel $model Instancia del modelo a agregar.
   */
  abstract protected function sync(AmModel $model);

}
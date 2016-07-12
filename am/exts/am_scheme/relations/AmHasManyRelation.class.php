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
class AmHasManyRelation extends AmCollectionAbstractRelation{

  /**
   * Después de guardar el registro propietario se debe guardar los registro
   * relacionados.
   */
  public function afterSave(){

    // Se debe actualizar todos los registros relacionados
    $record = $this->getRecord();

    // Si no es un regsitro nuevo se deben acutalizar todas las referencias
    if(!$record->isNew()){

      $foreign = $this->getForeign();
      $index = $foreign->getCols();
      $table = $foreign->getTableInstance();
      $query = $table->all();
      $update = false;

      // Asignar cada campo de la relación
      foreach ($index as $from => $to){

        $value = itemOr($to, $this->beforeIndex);
        $newValue = $record->get($to);

        // WHEREWHERE
        $query->andWhere($from, $value);

        if($value != $newValue){
          $update = true;
          $query->set($from, $record->get($to));
        }

      }

      // Ejecutar acctualización
      if($update)
        $query->update();

    }

    foreach($this->news as $i => $model){
      $this->sync($model);
      if($model->save())
        unset($this->news[$i]);
    }

    foreach($this->removeds as $i => $model){
      $this->sync($model);
      if($model->delete())
        unset($this->removeds[$i]);
    }

  }

  /**
   * Hace que una instancia de un modelo pertenezca el registro dueño de la
   * relación
   * @param  AmModel $model Instancia del modelo a agregar.
   */
  protected function sync(AmModel $model){

    // Obtener el reistro propietario de la relación.
    $record = $this->getRecord();

    // Obtener las columnas relacionadas.
    $index = $this->getForeign()->getCols();

    // Realizar asignaciones
    foreach ($index as $from => $to)
      $model->set($from, $record ? $record->get($to) : null);

  }

}
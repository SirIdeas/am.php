<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para instancia las claves foráneas entre las tablas.
 */
class AmHasManyAndBelongToRelation extends AmCollectionAbstractRelation{

  /**
   * Después de guardar el registro propietario se debe guardar los registro
   * relacionados.
   */
  public function afterSave(){

    // Se debe actualizar todos los registros relacionados
    $record = $this->getRecord();
    
    $foreign = $this->getForeign();
    $index = $foreign->getCols();
    $through = $foreign->getThrough();
    $joins = itemOr($through, $foreign->getJoins(), array());
    $table = $foreign->getTableInstance();
    $scheme = $table->getScheme();

    // Si no es un regsitro nuevo se deben acutalizar todas las referencias
    if(!$record->isNew()){

      // Crear query
      $query = $scheme->q($through);
      $update = false; // Indica si se actualizará

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

    // Crear registros nuevos
    foreach($this->news as $i => $model){
      // Gaurdar el registro relacionado
      if($model->save()){
        // Obtener la data del registro intermedio
        $data = $this->sync($model);
        // Insertar el registro
        if($scheme->insertInto(array($data), $through))
          // Si se logra insertar se quita de los nuevos.
          unset($this->news[$i]);
      }
    }

    // Crear query
    $query = $scheme->q($through);

    $remove = false; // Indicará si se debe eliminar o no

    foreach($this->removeds as $i => $model){
      if($model->save()){
        $conds = array();
        $data = $this->sync($model);

        // Realizar asignaciones
        foreach ($index as $from => $to)
          $conds[] = array($from, $data[$from]);

        // Realizar asignaciones
        foreach ($joins as $to => $from)
          $conds[] = array($from, $data[$from]);

        // Agrear condiciones
        // WHEREWHERE
        $query->whereArray('OR', $conds);

        $remove = true; // Para que elimine.

        // Quitar el modelo de los registros eliminados.
        unset($this->removeds[$i]);

      }
    }

    // Eliminar.
    if($remove)
      $query->delete();

  }

  /**
   * Hace que una instancia de un modelo pertenezca el registro dueño de la
   * relación
   * @param  AmModel $model Instancia del modelo a agregar.
   */
  protected function sync(AmModel $model){

    // Obtener el reistro propietario de la relación.
    $record = $this->getRecord();
    $foreign = $this->getForeign();

    // Obtener las columnas relacionadas.
    $index = $foreign->getCols();
    $through = $foreign->getThrough();
    $joins = itemOr($through, $foreign->getJoins(), array());

    // Obtener la data del registro
    $data = itemOr($through, $model->toArray(), array());

    // Realizar asignaciones
    foreach ($index as $from => $to)
      $data[$from] = $record->get($to);

    // Realizar asignaciones
    foreach ($joins as $to => $from)
      $data[$from] = $model->get($to);

    return $model[$through] = $data;

  }
  
}
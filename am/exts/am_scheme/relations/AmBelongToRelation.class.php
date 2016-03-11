<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para instancias relaciones belongTo de un modelo.
 */
class AmBelongToRelation extends AmRelation{

  protected

    /**
     * Modelo por la relación.
     */
    $value = null,

    /**
     * Modelo real actual de la relación.
     */
    $current = null;

  /**
   * Este método es sobreescrito para que devuelva la instancia del registro
   * relacionado en lugar de devolverse a si mismo.
   * @return $this
   */
  public function val(){
    
    return $this->_get();

  }

  /**
   * Devuelve el modelo relacionado.
   * @return AmModel/bool Devuelve el primer registro de la query de la relación
   *                      o false si no encuentra nada.
   */
  public function _get(){

    // Obtener el primer registro del query de la relación.
    return $this->current = $this->getQuery()->row();

  }

  /**
   * Asigna el modelo a la realación.
   * @param  AmModel $record Modelo a asignar a la relación.
   * @return $this
   */
  public function _set($record){

    // Cargar registro si no ha sido cargado
    if(!$this->current)
      $this->current = $this->_get();

    // chequear que el registro pertenezca al modelo.
    $this->checkModel($record);

    // Asignar valor.
    $this->value = $record;

    return $this;

  }

  /**
   * Método que se ejecuta antes de guardar del registro propietario. Se debe
   * asignar igualar las columnas relacionadas.
   */
  public function beforeSave(){

    // Si se asignó un valor diferente a la relación se guarda.
    if($this->value !== $this->current){

      // Obtener el registro al que pertenece la relación
      $record = $this->getRecord();

      // Obtener el valor asignado a la relación
      $value = $this->value;

      // Guardar registro relacionado
      if(isset($value) && !$value->save())
        return false;

      // Obtener los campos relacionados.
      $index = $this->getForeign()->getCols();

      // Asignar los valores del registro relacionado al los attributos
      // del registro al que pertence la relación.
      foreach ($index as $from => $to)
        $record->set($from, $value ? $value->get($to) : null);

    }

    return true;

  }

  /**
   * Método que se ejecuta después de guardar del registro propietario. En las
   * relaciones belongTo no se hace nada.
   */
  public function afterSave(){}


}
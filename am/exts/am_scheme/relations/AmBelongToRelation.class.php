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
class AmBelongToRelation extends AmRelation{

  protected
    $current = null,
    $realCurrent = null;

  /**
   * [get description]
   * @return [type]           [description]
   */
  public function get($reload = false){

    if(!$this->realCurrent || $reload === true)
      $this->realCurrent = $this->current = $this->getQuery()->row();

    return $this->current;

  }

  public function set($record){

    $this->get();
    $model = $this->getForeign()->getModel();

    if($record !== null && !$record instanceof $model)
      throw Am::e('AMSCHEME_RELATION_SET_MUST_RECIVED_AMMODEL', $model);

    $this->current = $record;

  }

  public function changed(){

    return $this->current !== $this->realCurrent;

  }

  public function updateRecord(){

    if($this->changed()){

      $record = $this->getRecord();
      $current = $this->current;
      $index = $this->getForeign()->getCols();

      foreach ($index as $from => $to){
        $record->$from = $current ? $current->$to : null;
      }


    }

  }


}
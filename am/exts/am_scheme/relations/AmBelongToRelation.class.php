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
    $value = null,
    $current = null;

  public function _getValue($reload = false){

    if(!$this->current || $reload === true)
      $this->current = $this->value = $this->_get();

    return $this->value;

  }

  public function _get(){

    return $this->getQuery()->row();

  }

  public function _set($record){

    $this->_getValue();
    $model = $this->getForeign()->getModel();

    if($record !== null && !$record instanceof $model)
      throw Am::e('AMSCHEME_RELATION_SET_MUST_RECIVED_AMMODEL', $model);

    $this->value = $record;

  }

  public function save(){

    if($this->value !== $this->current){

      $record = $this->getRecord();
      $value = $this->value;
      $index = $this->getForeign()->getCols();

      foreach ($index as $from => $to)
        $record->set($from, $value ? $value->get($to) : null);

    }

  }


}
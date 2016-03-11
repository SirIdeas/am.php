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
class AmHasManyRelation extends AmRelation{

  protected
    $news = array(),
    $addeds = array(),
    $removeds = array();

  public function _get(){

    return $this->getQuery();

  }

  public function count(){

    return $this->getQuery()->count();

  }

  private function sync(AmModel $model){

    $record = $this->getRecord();
    $index = $this->getForeign()->getCols();

    foreach ($index as $from => $to)
      $model->set($from, $record ? $record->get($to) : null);

  }

  private function keyOf(AmModel $model){

    return json_encode($model->getTable()->indexof($model));

  }

  public function add(AmModel $model){

    $this->sync($model);

    $this->remove($model);

    if($model->isNew())
      $this->news[] = $model;
    else
      $this->addeds[$this->keyOf($model)] = $model;

    return $this;

  }

  public function remove(AmModel $model){

    if($key = array_search($model, $this->news, true))
      unset($this->news[$key]);

    if(!$model->isNew())
      $this->removeds[$this->keyOf($model)] = $model;

    return $this;

  }

  public function save(){

    $record = $this->getRecord();
    $index = $this->getForeign()->getCols();
    $query = $this->getQuery();
    $update = false;

    foreach ($index as $from => $to)
      if($record->changed($to)){
        $update = true;
        $query->set($from, $record->get($to));
      }

    if($update)
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
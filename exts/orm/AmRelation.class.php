<?php

class AmRelation extends AmObject{
  
  protected
    $source = 'default',
    $table = null,
    $columns = array();
  
  // Métodos GET para las propiedades
  public function getSource(){ return $this->source; }
  public function getTable(){ return $this->table; }
  public function getColumns(){ return $this->columns; }
  
  // Generador de la consulta para la relación
  public function getQuery($model){
    
    $q = Am::table($this->table(), $this->source())->qAll();
    
    foreach($this->columns() as $from => $to){
      $q->where("$to='{$model->$from}'");
    }
    
    return $q;
    
  }
  
  // Convertir a Array
  public function toArray(){
    
    return array(
      'source' => $this->source(),
      'table' => $this->table(),
      'columns' => $this->columns()
    );
    
  }
  
}

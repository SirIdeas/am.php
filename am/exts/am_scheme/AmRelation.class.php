<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/
 
class AmRelation extends AmObject{

  protected
    $scheme = '',
    $table = null,
    $tableName = null,
    $columns = array();

  public function __construct($data = null){
    
    parent::__construct($data);
    $this->table = AmScheme::table($this->getTableName(), $this->getScheme());

  }

  // Métodos GET para las propiedades
  public function getScheme(){
    
    return $this->scheme;

  }

  public function getTable(){
    
    return $this->table;

  }

  public function getTableName(){
    
    return $this->tableName;

  }

  public function getColumns(){
    
    return $this->columns;

  }

  // Generador de la consulta para la relación
  public function getQuery($model){

    // Una consulta para todos los registros de la tabla
    $q = $this->table->all();

    foreach($this->getColumns() as $from => $to){
      $q->where("{$to}='{$model->$from}'");
    }

    return $q;

  }

  // Convertir a Array
  public function toArray(){

    return array(
      'scheme' => $this->getScheme(),
      'tableName' => $this->getTable(),
      'columns' => $this->getColumns()
    );

  }

}

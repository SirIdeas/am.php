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
 
/**
 * Validacion de un campo unico
 */

class UniqueValidator extends AmValidator{

  protected
    $fields = array(), // Campos de la clave unica
    $conditions = array();  // Condiciones para los indixes

  protected function validate(AmModel &$model){

    // Obtener la tabla para el modelo
    $table = $model->getTable();

    // Crear una consulta de todos los registro en la tabla del model
    // Con el mismo valor de actual del modelo en el campo evaluado
    $query = $table->all()->where($this->conditions);

    // Obtener los campos del indice unico
    $fields = $this->fields;

    // Agregamos el campo que se evalua a la lista de campos
    if($table->getField($this->getFieldName()))
      array_unshift($fields, $this->getFieldName());

    // Eliminar campos repetidos
    $fields = array_unique($fields);

    // Se agrega una condicion and por cada campo extra configurado
    foreach($fields as $field){
      // Obtener el valor del campo en el modelo
      $value = $model->getValue($field);
      // Agregar la condicion
      $query->andWhere("{$field}='{$value}'");
    }

    // Agregar condiciones para excluir el registro evaluado
    $index = $model->index();
    foreach ($index as $key => $value) {
      $index[$key] = "{$key}='{$value}'";
    }

    // Agregar condiciones para excluir el registro evaluado
    $query->andWhere("not", array_values($index));

    // Si la consulta devuelve 0 registro entonces el modelo
    // tiene un valor unico
    return $query->count() == 0;

  }

  // Lista de campos para la llave unica
  public function getFields(){ return $this->fields; }
  public function setFields($value){ $this->fields = $value; return $this; }

  // Lista de condiciones extras a aplicar la llave unica
  public function getConditions(){ return $this->conditions; }
  public function setConditions($value){ $this->conditions = $value; return $this; }


}

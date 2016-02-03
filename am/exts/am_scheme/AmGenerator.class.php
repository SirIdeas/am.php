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
 * Clase para generar clases de los modeles del ORM
 */

final class AmGenerator{

  // Generar clase base para un model
  public final static function classBaseModel(AmScheme $scheme, AmTable $table){

    $existMethods = get_class_methods('AmModel');
    $fields = array_keys((array)$table->getFields());
    $newMethods = array();
    $lines = array();

    // Agregar métodos GET para cada campos

    $lines[] = "class {$scheme->getBaseModelClassname($table->getTableName())} extends AmModel{\n";

    $lines[] = '  protected static';
    $lines[] = "    \$schemeName = '{$table->getScheme()->getName()}',";
    $lines[] = "    \$tableName  = '{$table->getTableName()}';\n";

    // Add references to this class
    $methodsAddeds = array();
    foreach(array_keys((array)$table->getReferencesBy()) as $relation){
      $prefix = in_array($relation, $existMethods)? '//' : '';
      $existMethods[] = $relation;
      $lines[] = "  {$prefix}public function $relation(){ return \$this->getTable()->getReferencesTo()->{$relation}->getQuery(\$this); }";
    }

    // Add validators if has any
    if(!empty($methodsAddeds)){
      $lines[] = '  // REFERENCES BY';
      $lines = array_merge($lines, $methodsAddeds);
      $lines[] = '';
    }


    // Add references to other class
    $methodsAddeds = array();
    foreach(array_keys((array)$table->getReferencesTo()) as $relation){
      $prefix = in_array($relation, $existMethods)? '//' : '';
      $existMethods[] = $relation;
      $methodsAddeds[] = "  {$prefix}public function $relation(){ return \$this->getTable()->getReferencesTo()->{$relation}->getQuery(\$this)->getRow(); }";
    }

    // Add validators if has any
    if(!empty($methodsAddeds)){
      $lines[] = '  // REFERENCES TO';
      $lines = array_merge($lines, $methodsAddeds);
      $lines[] = '';
    }


    // Method for customization model
    $lines[] = '  // METHOD FOR INIT MODEL';
    $lines[] = "  protected function start(){\n";

    // Add vaidators for fields
    foreach($table->getFields() as $f){
      if($f->isAutoIncrement()) continue;

      $validators = array();
      $type       = $f->getType();
      $len        = $f->getLen();
      $fieldName  = $f->getName();

      // Integer validator, dates, times and Bit validator
      if(in_array($type, array('integer', 'bit', 'date', 'datetime', 'timestamp', 'time')))
        $validators[] = "    \$this->setValidator('{$fieldName}', '{$type}');";

      // If have validate strlen of value.
      if(in_array($type, array('char', 'varchar', 'bit')))
        $validators[] = "    \$this->setValidator('{$fieldName}', 'max_length',".
                              "array('max' => $len));";

      // To integer fields add range validator
      if($type == 'integer'){
        // $max = pow(256, $len);
        // $min = $f->isUnsigned() ? 0 : -($max>>1);
        // $max = $min + $max - 1;
        // $prefix = is_int($min) && is_int($max)? '' : '// ';
        // // if the max limit is integer yet then add validator
        // $validators[] = "    {$prefix}\$this->setValidator('{$fieldName}', 'range', ".
        //                     " array('min' => {$min}, 'max' => {$max}));";

      // To text fiels add strlen validator
      }elseif($type == 'text'){
        $len = pow(256, $len) - 1;
        $validators[] = "    \$this->setValidator('{$fieldName}', 'max_length', ".
                              "array('max' => $len));";

      // To decimal fiels add float validator
      }elseif($type == 'decimal'){
        $precision = $f->getPrecision();
        $decimals = $f->getScale();
        $validators[] = "    \$this->setValidator('{$fieldName}', 'float', ".
                              "array('precision' => {$precision}, 'decimals' => {$decimals}));";
      }

      // If is a PK not auto increment adde unique validator
      if($f->isPrimaryKey() && count($table->getPks()) == 1)
        $validators[] = "    \$this->setValidator('{$fieldName}', 'unique');";

      // Add notnull validator if no added any validators
      if(empty($validators) && !$f->allowNull())
        $validators[] = "    \$this->setValidator('{$fieldName}', 'null');";

      // Add validators if has any
      if(!empty($validators)){
        // $lines[] = "    // {$fieldName}";
        $lines = array_merge($lines, $validators);
      }

      switch ($type){
        case 'year':
        break;
      }

    }

    $lines[] = '';

    // Add validator of relations
    $validators = array();
    foreach($table->getReferencesTo() as $r){
      $cols = $r->getColumns();
      if(count($cols) == 1){
        $colName = array_keys($cols);
        $f = $table->getField($colName[0]);
        if(!$f->allowNull()){
          $colStr = $cols[$colName[0]];
          $validators[] = "    \$this->setValidator('{$f->getName()}', 'in_query', array('query' => AmScheme::table('{$r->getTable()}', '{$table->getScheme()->getName()}')->all(), 'field' => '{$colStr}'));";
        }
      }
    }

    // Add validators if has any
    if(!empty($validators)){
      $lines[] = '    // RELATIONS';
      $lines = array_merge($lines, $validators);
      $lines[] = '';
    }

    // Add validator of uniques group values
    $validators = array();
    foreach($table->getUniques() as $constraint => $cols){
      if(count($cols) > 1){
        $cols = implode('", "', $cols);
        $validators[] = "    \$this->setValidator('{$constraint}', 'unique', ".
                              "array('fields' => array('{$cols}')));";
      }
    }

    // Add validators if has any
    if(!empty($validators)){
      $lines[] = '    // UNIQUE';
      $lines = array_merge($lines, $validators);
      $lines[] = '';
    }


    $lines[] = "  }\n";

    // Method to get table of model
    $lines[] = '  // GET TABLE OF MODEL';
    $lines[] = '  public static function me(){';
    $lines[] = "    return AmScheme::table('{$table->getTableName()}', '{$table->getScheme()->getName()}');";
    $lines[] = "  }\n";

    // Method to get query to all records of model
    $lines[] = '  // GET QUERY TO ALL RECORDS';
    $lines[] = "  public static function all(\$as = 'q', \$withFields = false){";
    $lines[] = '    return self::me()->all($as, $withFields);';
    $lines[] = "  }\n";

    // Method to get query to all records of model
    $lines[] = '  // GET QUERY TO SELECT';
    $lines[] = "  public static function q(\$limit, \$offset, \$as = 'q', \$withFields = false){";
    $lines[] = "    return self::me()->q(\$limit, \$offset, \$as, \$withFields);";
    $lines[] = "  }\n";

    $lines[] = '}';

    // Preparacion de los metodos Get
    return implode("\n", $lines);

  }

}

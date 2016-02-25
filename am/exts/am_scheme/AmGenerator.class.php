<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

/**
 * Clase para generar clases de los modeles del ORM
 */
final class AmGenerator{

  /**
   * Generar clase base para un modelo
   * @param  AmScheme $scheme Esquema del modelo.
   * @param  AmTable  $table  Tabla del model.
   * @return string           Código de la clase.
   */
  public final static function classBaseModel(AmScheme $scheme, AmTable $table){

    $existMethods = get_class_methods('AmModel');
    $fields = array_keys($table->getFields());
    $newMethods = array();
    $lines = array();

    // Agregar métodos GET para cada campos

    $lines[] = "class {$scheme->getBaseModelClassName($table->getTableName())} extends AmModel{\n";

    $lines[] = '  protected';
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
      $methodsAddeds[] = "  {$prefix}public function $relation(){ return \$this->getTable()->getReferencesTo()->{$relation}->getQuery(\$this)->row(); }";
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
      if(in_array($type, array('integer', 'bit', 'date', 'datetime', 'timestamp', 'time', 'year')))
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
        $validators[] = "    \$this->setValidator('{$fieldName}', 'max_length', ".
                              "array('max' => $len));";

      // To decimal fiels add float validator
      }elseif($type == 'float'){
        $precision = $f->getPrecision();
        $decimals = $f->getScale();
        $validators[] = "    \$this->setValidator('{$fieldName}', 'float', ".
                              "array('precision' => {$precision}, 'decimals' => {$decimals}));";
      }

      // If is a PK not auto increment adde unique validator
      if($f->isPk() && count($table->getPks()) == 1)
        $validators[] = "    \$this->setValidator('{$fieldName}', 'unique');";

      // Add notnull validator if no added any validators
      if(empty($validators) && !$f->allowNull())
        $validators[] = "    \$this->setValidator('{$fieldName}', 'null');";

      // Add validators if has any
      if(!empty($validators)){
        // $lines[] = "    // {$fieldName}";
        $lines = array_merge($lines, $validators);
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
    $lines[] = '}';

    // Preparacion de los metodos Get
    return implode("\n", $lines);

  }

}

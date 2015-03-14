<?php

/**
 * Clase para generar clases de los modeles del ORM
 */

final class AmGenerator{

  // Generar clase base para un model
  public final static function classModelBase(AmTable $table){

    $newMethods = get_class_methods("AmModel");

    $fields = array_keys((array)$table->getFields());

    $getFieldMethods = array();
    $setFieldMethods = array();
    $hasManyMethods = array();
    $hasOneMethods = array();

    // Agregar métodos GET para cada campos
    foreach($fields as $attr){

      $methodName = "get_{$attr}";

      $prefix = in_array($methodName, $newMethods)? "//" : "";
      $newMethods[] = $methodName;
      $getFieldMethods[] = "{$prefix}public function get_$attr(){ return \$this->$attr; }";

    }

    // Agregar métodos SET para cada campo
    foreach($fields as $attr){

      $methodName = "set_{$attr}";

      $prefix = in_array($methodName, $newMethods)? "//" : "";
      $newMethods[] = $methodName;
      $setFieldMethods[] = "{$prefix}public function set_$attr(\$value){ \$this->$attr = \$value; return \$this; }";

    }

    // Agregar metodos para referencias de este modelo
    foreach(array_keys((array)$table->getReferencesBy()) as $relation){

      $prefix = in_array($relation, $newMethods)? "//" : "";
      $newMethods[] = $relation;
      $hasManyMethods[] = "{$prefix}public function $relation(){ return \$this->getTable()->getReferencesTo()->{$relation}->getQuery(\$this); }";

    }

    // Agregar metodos para referencias a este modelo
    foreach(array_keys((array)$table->getReferencesTo()) as $relation){

      $prefix = in_array($relation, $newMethods)? "//" : "";
      $newMethods[] = $relation;
      $hasOneMethods[] = "{$prefix}public function $relation(){ return \$this->getTable()->getReferencesTo()->{$relation}->getQuery(\$this)->getRow(); }";

    }

    $nullValidators = array();
    $emptyValidators = array();
    $uniqueValidators = array();
    $strlenValidators = array();
    $integerValidators = array();
    $floatValidators = array();
    $dateValidators = array();
    $timeValidators = array();
    $datetimeValidators = array();
    $relationsValidators = array();

    // Agregar validators
    foreach($table->getFields() as $f){

      if(!$f->getAutoIncrement()){

        // Agregar validador de campo unico para los primary keys
        if(count($table->getPks()) == 1 && $f->getPrimaryKey()){
          $uniqueValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"unique\", \"unique\");";
        }

        $type = $f->getType();

        // Agregar validator para el tipo de datos seleccionado
        switch ($type){

          case "string":
            $len = $f->getCharLenght();
            if(isset($len)){
              $strlenValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"max_length\", \"max_length\", array(\"max\" => $len));";
            }
            // $emptyValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"empty\", \"empty\");";
            break;

          case "integer":
          case "biginteger":
            $integerValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"integer\", \"integer\");";
            break;

          case "float":
            $floatValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"float\", \"float\");";
            break;

          case "date":
            $dateValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"date\", \"date\");";
            break;

          case "time":
    //        $timeValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"time\", \"time\");";
    //        break;

          case "datetime":
    //        $datetimeValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"datetime\", \"datetime\");";
    //        break;

          default:
            if($f->getNotNull()){
              $nullValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"null\", \"null\");";
            }

        }
      }

    }

    // Agregar validators de de referencias
    foreach($table->getReferencesTo() as $r){

      $cols = $r->getColumns();

      if(count($cols) == 1){

        $colName = array_keys($cols);
        $f = $table->getField($colName[0]);

        if($f->getNotNull()){

          $relationsValidators[] = "\$table->setValidator(\"{$f->getName()}\", \"fk_{$r->getTable()}\", \"in_query\", array(\"query\" => AmORM::table(\"{$r->getTable()}\", \"{$table->getSource()->getName()}\")->all(), \"field\" => \"{$cols[$colName[0]]}\"));";

        }

      }

    }

    // Generar clase Base
    ob_start();

    echo "class {$table->getClassNameModelBase()} extends AmModel{";
    echo "\n";
    echo "\n  protected static";
    echo "\n    \$sourceName = \"{$table->getSource()->getName()}\",";
    echo "\n    \$tableName  = \"{$table->getTableName()}\";";
    echo "\n";

    // Preparacion de los metodos Get
    echo "\n  // GETTERS\n  ";
    echo implode("\n  ", $getFieldMethods);
    echo "\n";

    // Preparacion de los metodos Set
    echo "\n  // SETTERS\n  ";
    echo implode("\n  ", $setFieldMethods);
    echo "\n";

    // Has Many
    echo "\n  // HAS MANY\n  ";
    echo implode("\n  ", $hasManyMethods);
    echo "\n";

    // Has One
    echo "\n  // HAS ONE\n  ";
    echo implode("\n  ", $hasOneMethods);
    echo "\n";

    echo "\n  protected static function initialize(AmTable \$table){";
    echo "\n    parent::initialize(\$table);";

    if(!empty($uniqueValidators)){
      echo "\n";
      echo "\n    // UNIQUE\n    ";
      echo implode("\n    ", $uniqueValidators);
    }

    if(!empty($nullValidators)){
      echo "\n";
      echo "\n    // NOT NULLS\n    ";
      echo implode("\n    ", $nullValidators);
    }

    if(!empty($emptyValidators)){
      echo "\n";
      echo "\n    // EMPTYS\n    ";
      echo implode("\n    ", $emptyValidators);
    }

    if(!empty($integerValidators)){
      echo "\n";
      echo "\n    // INTEGERS\n    ";
      echo implode("\n    ", $integerValidators);
    }

    if(!empty($floatValidators)){
      echo "\n";
      echo "\n    // FLOATS\n    ";
      echo implode("\n    ", $floatValidators);
    }

    if(!empty($dateValidators)){
      echo "\n";
      echo "\n    // DATE\n    ";
      echo implode("\n    ", $dateValidators);
    }

    if(!empty($timeValidators)){
      echo "\n";
      echo "\n    // TIME\n    ";
      echo implode("\n    ", $timeValidators);
    }

    if(!empty($datetimeValidators)){
      echo "\n";
      echo "\n    // DATE TIME\n    ";
      echo implode("\n    ", $datetimeValidators);
    }
    if(!empty($strlenValidators)){
      echo "\n";
      echo "\n    // STRING LENGTH\n    ";
      echo implode("\n    ", $strlenValidators);
    }

    if(!empty($relationsValidators)){
      echo "\n";
      echo "\n    // RELATIONS\n    ";
      echo implode("\n    ", $relationsValidators);
    }

    echo "\n  }";

    echo "\n";
    echo "\n  public static function me(){";
    echo "\n    return AmORM::table(\"{$table->getTableName()}\", \"{$table->getSource()->getName()}\");";
    echo "\n  }";
    echo "\n";

    echo "\n}";
    echo "\n";

    return ob_get_clean();

  }

}

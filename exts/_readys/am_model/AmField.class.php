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
 * Clase para los campos de las tablas de las BD
 */

// class AmField extends AmObject{

//   protected static
//     $PARSE_FUNCS = array(
//       // Enteros
//       'integer'     => 'intval',
//       'bit'         => 'strval',
//       // Flotantes
//       'decimal'     => 'floatval',
//       // Cadenas de caracteres
//       'char'        => 'strval',
//       'varchar'     => 'strval',
//       'text'        => 'strval',
//       // Fechas
//       'date'        => 'strval',
//       'datetime'    => 'strval',
//       'timestamp'   => 'strval',
//       'time'        => 'strval',
//       'year'        => 'strval',
//     );

//   // Propiedades del campo
//   protected
//     $name = null,             // Nombre
//     $type = null,             // Tipo de datos
//     $defaultValue = null,     // Valor por defecto
//     $primaryKey = false,      // Indica si es o no una clave primaria
//     $allowNull = true,        // Indica si se admite o no valores nulos

//     $len = null,              // Tamanio máximo para campos tipo cadena y tipos enteros
//     $charset = null,          // Set de caracteres
//     $collage = null,          // Coleccion de caracteres

//     $unsigned = null,         // Indica si el campo admite valores negitos
//     $zerofill = null,         // Indica si se completa con zeros

//     $precision = null,        // Numero de digitos del numero
//     $scale = null,            // Numero de digitos decimales
//     $autoIncrement = false,   // Indica si es un campo autoincrementable
//     $extra = null;            // Atributos extras

//     // Métodos get para las propiedades del campo
//     public function getName(){ return $this->name;}
//     public function getType(){ return $this->type; }
//     public function getLen(){ return $this->len; }
//     public function getPrecision(){ return $this->precision; }
//     public function getScale(){ return $this->scale; }
//     public function getDefaultValue(){ return $this->defaultValue; }
//     public function allowNull(){ return $this->allowNull; }
//     public function isAutoIncrement(){ return $this->autoIncrement; }
//     public function isUnsigned(){ return $this->unsigned; }
//     public function isZerofill(){ return $this->zerofill; }
//     public function getExtra(){ return $this->extra; }
//     public function getCharset(){ return $this->charset; }
//     public function getCollage(){ return $this->collage; }
//     public function isPrimaryKey(){ return $this->primaryKey; }

//     // Métodos set para algunas propiedades
//     public function setPrimaryKey($value){ $this->primaryKey = $value; return $this; }

//     // Convertir campo en array
//     public function toArray(){

//       $ret = array(
//         'name' => $this->getName(),
//         'type' => $this->getType(),
//         'primaryKey' => $this->isPrimaryKey(),
//         'allowNull' => $this->allowNull(),
//       );

//       if(in_array($this->type, array('integer', 'decimal'))){
//         $ret['unsigned'] = $this->isUnsigned();
//         $ret['zerofill'] = $this->isZerofill();
//         $ret['autoIncrement'] = $this->isAutoIncrement();
//       }

//       // Eliminar campos vacios
//       foreach(array(
//         'defaultValue',
//         'collage',
//         'charset',
//         'extra',
//         'len',
//       ) as $attr)
//         if(isset($this->$attr) && trim($this->$attr)!=='')
//           $ret[$attr] = $this->$attr;

//       if($this->type == 'decimal'){
//         $ret['precision'] = $this->getPrecision();
//         $ret['scale'] = $this->getScale();
//       }

//       return $ret;

//     }

//     // Realizar casting a un valor por el tipo de datos del campo
//     public function parseValue($value){
//       $fn = self::$PARSE_FUNCS[$this->getType()];
//       return $fn($value);
//     }

// }

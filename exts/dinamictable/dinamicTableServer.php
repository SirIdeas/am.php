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
 
function dinamicTableServer(AmObject $params, AmQuery $query, $formater = null, $toArray = true){

  $fields = $query->getSelects();

  if(empty($fields))
    $fields = (array)$query->getTable()->getFields();

  $fields = array_keys($fields);

  $query = $query->getSource()->newQuery($query)->setSelects(array_combine($fields, $fields));

  $count = $query->count();

  if(isset($params->oSorts))
    foreach($params->oSorts as $dir)
      if(!empty($dir))
        $query->orders($dir['dir'], array($fields[$dir['pos']]));

  if(!empty($params->sSearch))
    foreach($fields as $f)
      $query->orWhere("{$f} LIKE '%{$params->sSearch}%'");

  if(is_array($params->oSearch))
    foreach($params->oSearch as $pos => $val)
      $query->andWhere("{$fields[$pos]} LIKE '%{$val}%'");

  $countResult = $query->count();

  if($params->iLen != -1)
    $query
      ->limit($params->iLen)
      ->offset($params->iPage * $params->iLen);

  $records = $query->getResult('array', $formater);

  if($toArray === true)
    foreach($records as $i => $value)
      $records[$i] = array_values($value);

  return array(
    'aoData' => $records,
    'iRecordCount' => $count,
    'iRecordFilteredCount' => $countResult
  );

}

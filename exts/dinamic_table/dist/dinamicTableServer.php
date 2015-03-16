<?php

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
        $query->orders($dir["dir"], array($fields[$dir["pos"]]));
  
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
  
  $records = $query->getResult("array", $formater);
  
  if($toArray === true)
    foreach($records as $i => $value)
      $records[$i] = array_values($value);
  
  return array(
    "aoData" => $records,
    "iRecordCount" => $count,
    "iRecordFilteredCount" => $countResult
  );
    
}

<?php

function dinamicTableServer(AmObject $params, AmQuery $query, $toArray = true){

  $fields = $query->getSelects();
  $formatter = $query->getFormatter();

  if(empty($fields)){
    $table = $query->getTable();
    if($table){
      $fields = $query->getFields();
    }
  }
  
  $fields = array_keys($fields);
  $query = $query->getScheme()->q($query)->setSelects(array_combine($fields, $fields));
  
  $count = $query->count();
  
  if(isset($params->oSorts))
    foreach($params->oSorts as $dir)
      if(!empty($dir))
        $query->orderBy(array($fields[$dir['pos']]), $dir['dir']);
  
  if(!empty($params->sSearch))
    foreach($fields as $f)
      $query->orWhere("{$f} LIKE '%{$params->sSearch}%'");
  
  if(is_array($params->oSearch))
    foreach($params->oSearch as $pos => $val)
      $query->andWhere("{$fields[$pos]} LIKE '%{$val}%'");
  
  $countResult = $query->copy()->count();
  
  if($params->iLen != -1)
    $query
      ->limit($params->iLen)
      ->offSet($params->iPage * $params->iLen);

  $query->setFormatter($formatter);

  $records = $query->get(function($record, $realRecord) use ($toArray, $fields){
    $record = AmObject::mask($record, $fields);
    if($toArray === true)
      return array_values($record);
    return $record;
  });

  return array(
    'aoData' => $records,
    'iRecordCount' => $count,
    'iRecordFilteredCount' => $countResult
  );
    
}

<?php

function getInfoFiles(array $files){
  
  $filesInfo = array();
  foreach ($files as $file) {
    $fileUrl = substr($file, 3);
    $filesInfo[$fileUrl] = array(filesize($file), filemtime($file)*1000);
  }
  return $filesInfo;
}

function getFilesWithInfo($folders, array $options = array()){
  return getInfoFiles(amGlob($folders, $options));
}

function getInfoFilesAsServices(array $folders, array $options = array()){
  return array(
    'success' => true,
    'data' => getFilesWithInfo($folders, $options),
  );
}

function returnInfoFilesAsServices(array $folders, array $options = array()){
  $files = getInfoFilesAsServices($folders, $options);
  header('content-type:application/json');
  echo json_encode($files);
  exit;
}
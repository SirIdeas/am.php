<?php


$timezone = Am::getProperty("timezone");

if(!empty($timezone))
  date_default_timezone_set($timezone);

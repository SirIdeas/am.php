<?php
/**
 * Amathista - PHP Framework
 *
 * @author Alex J. Rondón <arondn2@gmail.com>
 * 
 */

$timezone = Am::getProperty("timezone");

if(!empty($timezone))
  date_default_timezone_set($timezone);

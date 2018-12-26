<?php
/*
* qhtgeneric_poll.php
* Version: $Id$
* Created: Dec 12, 2018
* ContecUAE- i2i Telesource pvt ltd
* Name : Dhanasekara Pandian
* dhana.s@contecuae.com
* Available global variables
*/

// Enter Script description here


require_once 'smsd/sms_common.php';
require_once load_once('virtuosys', 'device_connect.php');

require_once "$db_objects";

try
{
  device_connect();
  device_disconnect();
  return SMS_OK;
}
catch (Exception $e)
{
  $msg = $e->getMessage();
  $code = $e->getCode();
  sms_log_error("connection error : $msg ($code)");
  return $code;
}
?>
<?php

// Get router configuration, not JSON response format

require_once 'smsd/sms_common.php';
require_once load_once('systrome', 'device_connect.php');
require_once load_once('systrome', 'device_configuration.php');

try
{

//$conn = new device_configuration();
device_connect();
$conf = new device_configuration($sdid);
$SMS_RETURN_BUF = $conf->get_running_conf();
}
catch(Exception $e)
{
sms_log_error($e->getMessage());
return $e->getCode();
}
return SMS_OK;
?>

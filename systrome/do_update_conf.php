<?php
/*
 * Version: $Id: do_update_conf.php 38235 2010-12-27 08:49:22Z tmt $
 * Created: Dec 12, 2018
 * ContecUAE- i2i Telesource pvt ltd
 * Name : Dhanasekara Pandian
 * dhana.s@contecuae.com
 
* Available global variables
* 	$sms_sd_ctx        pointer to sd_ctx context to retreive usefull field(s)
*  $sms_sd_info        sd_info structure
*  $sms_csp            pointer to csp context to send response to user
*  $sdid
*  $sms_module         module name (for patterns)
* 	$SMS_RETURN_BUF    string buffer containing the result
*
*  $flag_update       flags to update (string like CONF_VPN|CONF_QOS|CONF_IPS|CONF_AV|CONF_URL|CONF_AS)
*/

// Enter Script description here

require_once 'smsd/sms_common.php';
require_once 'smsd/expect.php';
require_once load_once('systrome', 'device_connect.php');
require_once load_once('systrome', 'device_configuration.php');
require_once load_once('systrome', 'device_common.php');

try
{
  $ret = sms_sd_lock($sms_csp, $sms_sd_info);

  if ($ret !== 0)
  {
    sms_send_user_error($sms_csp, $sdid, "", $ret);
    sms_close_user_socket($sms_csp);
    return SMS_OK;
  }

  $operation = "PUSH CONFIG";
  #sms_set_status_update($sms_csp, $sdid, SMS_OK,$operation, 'W', '');
  sms_set_update_status($sms_csp, $sdid, SMS_OK,$operation, 'W', '');
  sms_send_user_ok($sms_csp, $sdid, "");
  sms_close_user_socket($sms_csp);
  // Asynchronous mode, the user socket is now closed, the results are written in database

  device_connect();

  $conf = new device_configuration($sdid);

  $ret = $conf->update_conf();
  if ($ret !== SMS_OK)
  {
    throw new SmsException(get_device_error($SMS_OUTPUT_BUF), $ret);
  }

  #sms_set_status_update($sms_csp, $sdid, SMS_OK, 'E', '');
  sms_set_update_status($sms_csp, $sdid, SMS_OK, $operation, 'E', '');
  sms_sd_unlock($sms_csp, $sms_sd_info);
  device_disconnect(true);
}
catch (Exception $e)
{
  sms_set_status_update($sms_csp, $sdid, $e->getCode(), 'F', $e->getMessage());
  sms_sd_unlock($sms_csp, $sms_sd_info);
  device_disconnect();
  exit ($e->getCode());
}


return SMS_OK;

?>

<?php

// -------------------------------------------------------------------------------------
// UPDATE CONFIGURATION
// -------------------------------------------------------------------------------------
function prov_init_conf($sms_csp, $sdid, $sms_sd_info, $stage)
{
  $conf = new device_configuration($sdid, true);
  #$ret = $conf->provisioning();
  $ret = SMS_OK;
  if ($ret !== SMS_OK)
  {
    sms_set_status_update($sms_csp, $sdid, $ret, 'F', $SMS_OUTPUT_BUF);
    sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'F', $ret, null, "");
    device_disconnect();
    return $ret;
  }
  
  device_disconnect();
  
  return SMS_OK;
}

?>
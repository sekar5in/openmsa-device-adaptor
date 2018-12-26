<?php

// -------------------------------------------------------------------------------------
// INITIAL CONNECTION
// -------------------------------------------------------------------------------------
function prov_init_conn($sms_csp, $sdid, $sms_sd_info, $stage)
{
  global $ipaddr;
  global $login;
  global $passwd;
  global $adminpasswd;
  global $port;
  
  // PROV_INIT_CONN: 122.176.59.218, demo, Admin@123, Admin@123, 22
  $ret =  device_connect($ipaddr, $login, $passwd, $adminpasswd, $port);
  if ($ret != SMS_OK)
  {
    sms_set_status_update($sms_csp, $sdid, $ret, 'F', '');
    sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'F', $ret, null, "");
    return $ret;
  }
  return SMS_OK;
}

?>

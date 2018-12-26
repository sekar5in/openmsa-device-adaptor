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

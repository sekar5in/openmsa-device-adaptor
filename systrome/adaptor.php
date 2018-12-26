<?php
/*
 * Version: $Id$

 * Created: Dec 12, 2018
 * ContecUAE- i2i Telesource pvt ltd
 * Name : Dhanasekara Pandian
 * dhana.s@contecuae.com
 * Available global variables
 *  $sms_csp            pointer to csp context to send response to user
 *  $sms_sd_ctx         pointer to sd_ctx context to retreive usefull field(s)
 *  $sms_sd_info        pointer to sd_info structure
 *  $SMS_RETURN_BUF     string buffer containing the result
 *****
 */

// Device adaptor

require_once 'smserror/sms_error.php';
require_once 'smsd/sms_common.php';

require_once load_once('systrome', 'device_connect.php');
require_once load_once('systrome', 'device_apply_conf.php');

require_once "$db_objects";

/**
 * Connect to device
 * @param  $login
 * @param  $passwd
 * @param  $adminpasswd
 * @throws SmsException
 */
function sd_connect($login = null, $passwd = null, $adminpasswd = null)
{
  //echo ("SD_DEVICE_CONNECT $sd_ip_addr, $login, $passwd, $adminpasswd, $port_to_use");	
  device_connect($login, $passwd, $adminpasswd);
}

/**
 * Disconnect from device
 * @param $clean_exit
 * @throws SmsException
 */
function sd_disconnect($clean_exit = false)
{
  device_disconnect($clean_exit);
}

/**
 * Apply a configuration buffer to a device
 * @param  $configuration
 * @param  $need_sd_connection
 * @throws SmsException
 */
function sd_apply_conf($configuration, $need_sd_connection = false)
{
  if ($need_sd_connection)
  {
    sd_connect();
  }
  //echo ("Configuration Value is ". $configuration);
  device_apply_conf($configuration, false);

  if ($need_sd_connection)
  {
    sd_disconnect();
  }
}


function sd_execute_command($cmd, $need_sd_connection = false)
{
    global $sms_sd_ctx;
    
    if ($need_sd_connection)
    {
        $ret = sd_connect();
        if ($ret !== SMS_OK)
        {
            return false;
        }
    }
    
    //echo("#############################################");
    //echo("Execute COMMAND");
    $ret = sendexpectone(__FILE__.':'.__LINE__, $sms_sd_ctx, $cmd);
    
    if ($need_sd_connection)
    {
        sd_disconnect(true);
    }
    
    return $ret;
}

?>

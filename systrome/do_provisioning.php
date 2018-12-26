<?php
// Initial provisioning
/*
 * Version: $Id: do_provisioning.php 34480 2010-08-26 12:08:23Z tmt $
 * Created: Dec 12, 2018
 * ContecUAE- i2i Telesource pvt ltd
 * Name : Dhanasekara Pandian
 * dhana.s@contecuae.com
 
* 	Available global variables
*  	$sms_sd_info        sd_info structure
* 	$sms_sd_ctx         pointer to sd_ctx context to retreive usefull field(s)
*  	$sms_csp            pointer to csp context to send response to user
*  	$sdid
*  	$sms_module         module name (for patterns)
*  	$ipaddr             ip address of the router
*  	$login              current login
*  	$passwd             current password
*  	$adminpasswd        current administation **PORT**
*/
require_once 'smsd/sms_common.php';
require_once 'smserror/sms_error.php';

require_once load_once('systrome', 'adaptor.php');
require_once load_once('systrome', 'device_common.php');
require_once load_once('systrome', 'device_configuration.php');
require_once "$db_objects";


try {
  $network = get_network_profile();
  $SD = &$network->SD;
  if (empty($ipaddr))
  {
    $ipaddr = $SD->SD_IP_CONFIG;
  }
  if (empty($login))
  {
    $login = $SD->SD_LOGIN_ENTRY;
  }
  if (empty($passwd))
  {
    $passwd = $SD->SD_PASSWD_ENTRY;
  }
  if (empty($adminpasswd))
  {
    //$adminpasswd = $SD->SD_PASSWD_ADM;
    $adminpasswd = $SD->SD_PASSWD_ENTRY;
  }
  if (empty($port))
  {
    // normal provisioning (not ZTD)
    if($SD->SD_MANAGEMENT_PORT !== 0)
    {
      $port = $SD->SD_MANAGEMENT_PORT;
    }
    else
    {
      $port = 9922;
    }
  }

  // -------------------------------------------------------------------------------------
  // USER PARAMETERS CHECK
  // -------------------------------------------------------------------------------------
  if (empty($ipaddr) || empty($login) || empty($adminpasswd) || empty($passwd) || empty($port))
  {
    sms_send_user_error($sms_csp, $sdid, "addr=$ipaddr login=$login pass=$passwd adminpass=$adminpasswd port=$port", ERR_VERB_BAD_PARAM);
    return SMS_OK;
  }

  // -------------------------------------------------------------------------------------
  // Set the provisioning stages
  // -------------------------------------------------------------------------------------
  require_once load_once('systrome', 'provisioning_stages.php');

  // Reset the provisioning status in the database
  // all the stages are marked "not run"
  $nb_stages = count($provisioning_stages);
  $ret = sms_bd_init_provstatus($sms_csp, $sms_sd_info, $nb_stages, $provisioning_stages);
  if ($ret)
  {
    sms_send_user_error($sms_csp, $sdid, "", $ret);
    sms_close_user_socket($sms_csp);
    return SMS_OK;
  }
  sms_send_user_ok($sms_csp, $sdid, "");
  sms_close_user_socket($sms_csp);

  // -------------------------------------------------------------------------------------
  // Asynchronous mode, the user socket is now closed, the results are written in database
  // -------------------------------------------------------------------------------------

  $stage = 0;
  $nb_stages -= 1;
  sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'W', 0, null, ""); // working status
  foreach ($provisioning_stages as $provisioning_stage)
  {
    $prog = $provisioning_stage['prog'];
    include_once load_once('systrome', "{$prog}.php");
    if (call_user_func_array($prog, array($sms_csp, $sdid, $sms_sd_info, $stage,$provisioning_stage)) !== SMS_OK)
    {
      // Error end of the provisioning
      return SMS_OK;
    }
    if ($stage === $nb_stages)
    {
      sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'E', 0, null, ""); // End
    }
    else
    {
      sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'E', 0, 'W', ""); // End
    }
    $stage += 1;
  }

}
catch (Exception $e)
{
  sms_set_status_update($sms_csp, $sdid, $e->getCode(), 'F', $e->getMessage());
  sms_bd_set_provstatus($sms_csp, $sms_sd_info, $stage, 'F', $e->getCode(), null, $e->getMessage());
  device_disconnect();
}

// End of the script
return SMS_OK;

?>

<?php
/*
 * Version: $Id: device_configuration.php 58927 2012-06-11 15:15:18Z abr $
 * Created: Dec 12, 2018
 * ContecUAE- i2i Telesource pvt ltd
 * Name : Dhanasekara Pandian
 * dhana.s@contecuae.com
*/

require_once 'smsd/sms_common.php';
require_once 'smsd/pattern.php';

require_once load_once('systrome', 'device_apply_conf.php');
require_once "$db_objects";


class device_configuration
{
  var $conf_path;           // Path for previous stored configuration files
  var $sdid;                // ID of the SD to update
  var $running_conf;        // Current configuration of the router
  var $profile_list;        // List of managed profiles
  var $previous_conf_list;  // Previous generated configuration loaded from files
  var $conf_list;           // Current generated configuration waiting to be saved
  var $addon_list;          // List of managed addon cards
  var $fmc_repo;            // repository path without trailing /
  var $sd;

  // ------------------------------------------------------------------------------------------------
  /**
  * Constructor
  */
  function device_configuration($sdid, $is_provisionning = false)
  {
    $this->conf_path = $_SERVER['GENERATED_CONF_BASE'];
    $this->sdid = $sdid;
    $this->conf_pflid = 0;
    $this->fmc_repo = $_SERVER['FMC_REPOSITORY'];
  }

  // ------------------------------------------------------------------------------------------------
  /**
   *
   */
   function update_conf()
   {
   $this->build_conf($generated_configuration);
   $ret = device_update_apply_conf($generated_configuration);

   return $ret;
   }

  // ------------------------------------------------------------------------------------------------
  /**
  * Get running configuration from the router
  */
  function get_running_conf()
  {
    global $sms_sd_ctx;

    $running_conf = sendexpectone(__FILE__.':'.__LINE__, $sms_sd_ctx, "sh int");
	$sms_sd_ctx->sendCmd(__FILE__.':'.__LINE__, "\r");
    
	/*if (!empty($running_conf))
    {
      // trimming first and last lines
      $pos = strpos($running_conf, 'Current configuration');
      if ($pos !== false)
      {
        $running_conf = substr($running_conf, $pos);
      }
      // remove 'ntp clock-period' line
      $running_conf = remove_end_of_line_starting_with($running_conf, 'ntp clock-period');
      $running_conf = remove_end_of_line_starting_with($running_conf, 'enable secret 5');
      $running_conf = remove_end_of_line_starting_with($running_conf, ' create profile sync');
      $running_conf = remove_end_of_line_starting_with($running_conf, 'username device password 7');
      $running_conf = remove_end_of_line_starting_with($running_conf, ' create cnf-files version-stamp');
      $pos = strrpos($running_conf, "\n");
      if ($pos !== false)
      {
        $running_conf = substr($running_conf, 0, $pos + 1);
      }
    }*/

    $this->running_conf = $running_conf;
    return $this->running_conf;
  }

  function get_current_firmware_name()
  {
    global $sms_sd_ctx;

    // Grab current firmware file name
    $line = sendexpectone(__FILE__.':'.__LINE__, $sms_sd_ctx, "show ver");
    $current_firmware = substr($line, strpos($line, '"')+1, strrpos($line, '"')-strpos($line, '"')-1);

    return $current_firmware;
  }

  function update_firmware($param='')
  {
    return SMS_OK;
  }

  // ------------------------------------------------------------------------------------------------
  /**
   * Generate the general pre-configuration
   * @param $configuration   configuration buffer to fill
   */
  function generate_pre_conf(&$configuration)
  {
    //$configuration .= "!PRE CONFIG\n";
    get_conf_from_config_file($this->sdid, $this->conf_pflid, $configuration, 'PRE_CONFIG', 'Configuration');
    return SMS_OK;
  }

  // ------------------------------------------------------------------------------------------------
  /**
   * Generate a full configuration
   * Uses the previous conf if present to perform deltas
   */
  function generate(&$configuration, $use_running = false)
  {
    //$configuration .= "! CONFIGURATION GOES HERE\n";
	$configuration;
    return SMS_OK;
  }

  // ------------------------------------------------------------------------------------------------
  /**
   * Generate the general post-configuration
   * @param $configuration   configuration buffer to fill
   */
  function generate_post_conf(&$configuration)
  {
    //$configuration .= "!POST CONFIG\n";
    get_conf_from_config_file($this->sdid, $this->conf_pflid, $configuration, 'POST_CONFIG', 'Configuration');
    return SMS_OK;
  }

  // ------------------------------------------------------------------------------------------------
  /**
   *
   */
  function build_conf(&$generated_configuration)
  {
    $ret = $this->generate_pre_conf($generated_configuration);
    if ($ret !== SMS_OK)
    {
      return $ret;
    }
    $ret = $this->generate($generated_configuration);
    if ($ret !== SMS_OK)
    {
      return $ret;
    }

    $ret = $this->generate_post_conf($generated_configuration);
    if ($ret !== SMS_OK)
    {
      return $ret;
    }

    return SMS_OK;
  }

  function provisioning()
  {
    return $this->update_conf();
  }


  function get_staging_conf()
  {
    $staging_conf = PATTERNIZETEMPLATE("staging_conf.tpl");
    get_conf_from_config_file($this->sdid, $this->conf_pflid, $staging_conf, 'STAGING_CONFIG', 'Configuration');
    return $staging_conf;
  }

}

?>

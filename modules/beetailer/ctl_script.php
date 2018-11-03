<?php
/*
 * Script used for installing/uninstalling the module from command line using php-cli
 * Usage:
 * php ctl-script.php [install|uninstall]
*/

// Force execution via php cli
if(PHP_SAPI != 'cli') return;

require(dirname(__FILE__).'/../../config/config.inc.php');

$module = Module::getInstanceByName("beetailer");

if(!isset($argv[1]) || $argv[1] != 'uninstall'){
  $module->install();
  echo "Beetailer module installed\n";
}else{
  $module->uninstall();
  echo "Beetailer module uninstalled\n";
}


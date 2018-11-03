<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

if (PHP_SAPI !== 'cli' || php_sapi_name() !== 'cli') {
    die("CLI mode only");
} // if
require_once dirname(__FILE__).'/../../config/config.inc.php';
require_once _PS_MODULE_DIR_.'beezup/inc/BeezupAutoloader.php';

BeezupAutoloader::register();

require_once dirname(__FILE__).'/../../init.php';
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = preg_replace(
        '#^https?://([^/]+)/?.*#',
        '$1',
        Configuration::get('BEEZUP_SITE_ADDRESS')
    );
}

if (Configuration::get('BEEZUP_ALL_SHOPS')) {
    Shop::setContext(Shop::CONTEXT_ALL);
}

$beezup = Module::getInstanceByName('beezup');
BeezupGlobals::$smartyPrefilterActive = $argv;
echo $beezup->cron();

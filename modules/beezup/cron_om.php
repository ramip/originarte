<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

$fStartTime = microtime(true);
if (PHP_SAPI !== 'cli' || php_sapi_name() !== 'cli') {
    die("CLI mode only");
} // if
if (count(getopt('h', array('help')))) {
    print PHP_EOL.'Beezup Order Management Connector version 3.3.3';
    print PHP_EOL
        .'   (build phing;beezup;Atlas;2016-01-26 11:58:15;v3x-evolutions-1512;41bd044ba78708d24fc63b84286f8a05e4cdb39e;3.3.3)';
    print PHP_EOL.'Usage:';
    print PHP_EOL.'   php '.$argv[0]
        .' --host=my.shop.url --callback=callback [--arg1=value1 ...]';
    print PHP_EOL;
    die();
}
$aOptions = getopt('', array('host::'));
$_SERVER['HTTP_HOST'] = isset($aOptions['host']) && $aOptions['host']
&& filter_var('http://'.$aOptions['host'], FILTER_VALIDATE_URL)
    ? $aOptions['host'] : 'localhost';
$_SERVER['REQUEST_URI'] = DIRECTORY_SEPARATOR.ltrim(
    basename(__FILE__),
    DIRECTORY_SEPARATOR
);
require_once implode(
    DIRECTORY_SEPARATOR,
    array(dirname(__FILE__), '..', '..', 'config', 'config.inc.php')
);
$oBeezup = Module::getInstanceByName('beezup');
if ($oBeezup && $oBeezup->active) {
    $oBeezup->getBeezupOMController()->executeShellCallback();
    $oBeezup->cleanOMLog();
    unset($oBeezup);
} // if

print PHP_EOL.'#'.PHP_EOL;

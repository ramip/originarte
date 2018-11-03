<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once implode(
    DIRECTORY_SEPARATOR,
    array(dirname(__FILE__), '..', '..', 'config', 'config.inc.php')
);

$oBeezup = Module::getInstanceByName('beezup');

if ($oBeezup && $oBeezup->active) {
    if (array_key_exists('debug', $_REQUEST)) {
        $oBeezup->getBeezupOMController()
            ->setDebugMode((bool)$_REQUEST['debug']);
    }
    if (!isset($_REQUEST['key']) || empty($_REQUEST['key'])
        || $_REQUEST['key'] !== $oBeezup->getBeezupOMController()
            ->getHarvestKey()
    ) {
        die('invalid key');
    }
    set_time_limit(0);
    $oBeezup->getBeezupOMController()->synchronizeOrders();
    $oBeezup->cleanOMLog();
} // if
print 'Harvest finished';

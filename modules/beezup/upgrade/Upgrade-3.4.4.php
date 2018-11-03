<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_4_4($module)
{
    $configs = array(
        'PS_BEEZUP_ENABLE_CATEGORY_FILTER',
        'PS_BEEZUP_SELECTED_CATEGORIES',
        'PS_BEEZUP_CARRIERS_FEED',
        'PS_BEEZUP_FEED_CONCURRENT_CALL',
        'BEEZUP_MARKETCHANNEL_FILTERS',
        'BEEZUP_OM_IMPORT_FILTER_DAYS',
        'BEEZUP_OM_IMPORT_FILTER_STATUS',
        'BEEZUP_OM_IMPORT_FILTER_DAYS_ON',
    );

    foreach ($configs as $config) {
        $val = Configuration::get($config);
        if (!isset($val) || empty($val)) {
            Configuration::updateValue($config, "");
        }
    }
    Configuration::updateValue(
        "BEEZUP_OM_IMPORT_FILTER_STATUS",
        "New,InProgress,Shipped,Closed,Aborted,Pending"
    );

    return true;
}

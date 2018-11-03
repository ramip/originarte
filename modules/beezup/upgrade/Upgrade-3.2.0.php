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

function upgrade_module_3_2_0($module)
{
    $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'update_3.2.sql';
    $log_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'update-'.date('YmdHis')
        .'.log';
    file_put_contents(
        $log_file,
        'BEEZUP: update 3.2.0 '.$module->version.PHP_EOL,
        FILE_APPEND
    );
    $module->registerHook('actionCarrierUpdate');
    // @todo multiboutique?
    Configuration::updateValue(
        'BEEZUP_OM_CARRIERS_MAPPING',
        json_encode(array())
    );
    foreach (file($file) as $query) {
        $query = str_replace('::DB_PREFIX::', _DB_PREFIX_, trim($query));
        file_put_contents($log_file, $query, FILE_APPEND);
        $result = Db::getInstance()->execute($query);
        file_put_contents(
            $log_file,
            ($result ? ' [OK]' : ' [FAIL]: '.Db::getInstance()->getMsgError())
            .PHP_EOL,
            FILE_APPEND
        );
        if (!$result
            && stristr(Db::getInstance()->getMsgError(), 'Duplicate') === false
        ) {
            return false;
        }
    }

    return true;
}

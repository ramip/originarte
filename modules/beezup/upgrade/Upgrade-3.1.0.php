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

function upgrade_module_3_1_0($module)
{
    $sQuery = str_replace(
        '::DB_PREFIX::',
        _DB_PREFIX_,
        (string)Tools::file_get_contents(
            dirname(__FILE__).DIRECTORY_SEPARATOR
            .'add_fields_3.1.sql'
        )
    );
    file_put_contents(
        dirname(__FILE__).DIRECTORY_SEPARATOR.'log.txt',
        ($sQuery.' '.$module->version)
    );

    return $sQuery && Db::getInstance()->execute($sQuery);
}

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

function upgrade_module_3_5_2($module)
{
    Db::getInstance()->execute(
        "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."beezupom_log (
            id_beezupom_log int(255) not null auto_increment,
            beezup_order_id varchar(100) not null,
            message_type varchar(50) not null,
            message varchar(350) not null,
            `date` datetime not null,
            primary key(id_beezupom_log)
        ) ENGINE=INNODB;
        "
    );

    $id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
    $languages = Language::getLanguages();
    $tab = new Tab();
    foreach ($languages as $lang) {
        $tab->name[$lang['id_lang']] = 'BeezUP Orders Log';
    }
    $tab->class_name = 'AdminBeezupLog';
    $tab->id_parent = $id_parent;
    $tab->module = 'beezup';
    $tab->add();

    return true;
}

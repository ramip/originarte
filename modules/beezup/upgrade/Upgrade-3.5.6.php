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

function upgrade_module_3_5_6($module)
{
    Db::getInstance()->execute(
        "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."beezupom_product_template (
            id_beezupom_product_template int(255) not null auto_increment,
            field_type varchar(30) not null,
            search_value varchar(100) not null,
            replace_value varchar(100) not null,
            marketplace varchar(350) not null,
            primary key(id_beezupom_product_template)
        ) ENGINE=INNODB;
        "
    );


    return true;
}

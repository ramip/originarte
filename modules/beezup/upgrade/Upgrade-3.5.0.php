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

function upgrade_module_3_5_0($module)
{
    $ret = Db::getInstance()->execute(
        "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_
        ."beezup_order_status` (
			`id_beezup_order_status`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_order` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
			`id_order_status` INT(11) UNSIGNED DEFAULT NULL DEFAULT 0 ,
			KEY `id_order` (`id_order`),
			KEY `id_order_status` (`id_order_status`)
			) ENGINE=INNODB;"
    );

    Configuration::updateValue("BEEZUP_ORDER_STATUS_FILTER", 1);

    Db::getInstance()->execute(
        "insert into `"._DB_PREFIX_."beezup_field`
		(`id_configuration`,`free_field`, `active`,`forced`,`editable`,`default`,`id_feature`,`id_attribute_group`,`values_list`,`balise`,`function`,`fields_group`)
		VALUES
		(1,0,0,0,0,'',0,0,'','variation_theme','getVariationtheme','02.Descriptif produit')
		"
    );

    return $ret;
}

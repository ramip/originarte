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

function upgrade_module_3_5_1($module)
{
    Db::getInstance()->execute(
        "insert into `"._DB_PREFIX_."beezup_field`
		(`id_configuration`,`free_field`, `active`,`forced`,`editable`,`default`,`id_feature`,`id_attribute_group`,`values_list`,`balise`,`function`,`fields_group`)
		VALUES
		(1,0,0,0,0,'',0,0,'','parent_ref_produit','getParentProductReference','01.Référence produit')
		"
    );

    return true;
}

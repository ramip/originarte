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

function upgrade_module_3_4_7($module)
{
    $carriers = array(
        "PriceMinister",
        "Fnac",
        "DARTY",
        "BOULANGER",
        "LEQUIPE",
        "COMPTOIRSANTE",
        "RUEDUCOMMERCE",
        "BLEUBONHEUR",
        "Bol",
        "RealDE",
    );
    $retorno = array();
    foreach ($carriers as $data) {
        $retorno[$data] = "";
    }
    Configuration::updateValue(
        "PS_BEEZUP_CARRIER_MAP_UP",
        json_encode($retorno)
    );

    return true;
}

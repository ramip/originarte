<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BeezupOMCarrierMapper.php';

/**
 * Factory for BeezupOMStoreMapper
 */
class BeezupOMCarrierMapperFactory
{
    /**
     * Creates instance of BeezupOMStoreMapper
     * Mappings are defined in prestashop config variable BEEZUP_OM_STORES_MAPPING, in JSON format
     *
     * @return BeezupOMStoreMapper
     */
    public static function create()
    {
        $aMappings
            = json_decode(
                Configuration::get('BEEZUP_OM_CARRIERS_MAPPING'),
                true
            );
        if (!is_array($aMappings)) {
            $aMappings = array();
        } // if

        return new BeezupOMCarrierMapper($aMappings);
    }
}

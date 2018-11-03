<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BeezupOMStoreMapper.php';

/**
 * Factory for BeezupOMStoreMapper
 */
class BeezupOMStoreMapperFactory
{
    /**
     * Creates instance of BeezupOMStoreMapper
     * Mappings are defined in prestashop config variable BEEZUP_OM_STORES_MAPPING, in JSON format
     *
     * @return BeezupOMStoreMapper
     */
    public static function create()
    {
        $aStoresMappings
            = json_decode(Configuration::get('BEEZUP_OM_STORES_MAPPING'), true);
        if (!is_array($aStoresMappings)) {
            $aStoresMappings = array();
        } // if

        return new BeezupOMStoreMapper($aStoresMappings);
    }
}

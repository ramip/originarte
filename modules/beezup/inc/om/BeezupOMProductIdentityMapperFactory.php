<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR
    .'BeezupOMProductIdentityMapper.php';

/**
 * Factory for BeezupOMProductIdentityMapper
 */
class BeezupOMProductIdentityMapperFactory
{
    /**
     * Creates instance of BeezupOMProductIdentityMapper
     * Mappings are defined in prestashop config variable BEEZUP_OM_ID_FIELD_MAPPING, in JSON format
     *
     * @return BeezupOMProductIdentityMapper
     */
    public static function create($bDebugMode = false)
    {
        $aFieldMappings
            = json_decode(
                Configuration::get('BEEZUP_OM_ID_FIELD_MAPPING'),
                true
            );
        if (!is_array($aFieldMappings)) {
            $aFieldMappings = array();
        } // if
        $oResult = new BeezupOMProductIdentityMapper($aFieldMappings);
        $oResult->setDebugMode($bDebugMode);

        return $oResult;
    }
}

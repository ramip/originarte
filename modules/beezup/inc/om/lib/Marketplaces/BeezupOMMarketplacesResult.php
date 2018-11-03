<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMMarketplacesResult extends BeezupOMResult
{
    protected $aMarketplaces = array();


    /**
     * @return the $aValues
     */
    public function getMarketplaces()
    {
        return $this->aMarketplaces;
    }

    /**
     * @param multitype: $aValues
     */
    public function setMarketplaces($aMarketplaces)
    {
        $this->aMarketplaces = $aMarketplaces;

        return $this;
    }

    public function addMarketplace(BeezupOMMarketplace $oMarketplace)
    {
        $this->aMarketplaces[] = $oMarketplace;

        return $this;
    }

    public static function fromArray(array $aData = array())
    {
        $oResult = new BeezupOMMarketplacesResult();
        if (array_key_exists('marketPlaceAccountStores', $aData)
            && is_array($aData['marketPlaceAccountStores'])
        ) {
            foreach ($aData['marketPlaceAccountStores'] as $aMarketplace) {
                $oResult->addMarketplace(
                    BeezupOMMarketplace::fromArray($aMarketplace)
                );
            }
        }

        return $oResult;
    }
}

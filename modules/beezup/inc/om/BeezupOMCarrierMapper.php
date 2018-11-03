<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMCarrierMapper
{
    protected $aCarrierMapping = array();


    /**
     * Gets stores mappings for all stores
     *
     * @return array Defined mappings for all Beezup stores
     */
    public function __construct(array $aCarrierMapping = array())
    {
        $this->aCarrierMapping = $aCarrierMapping;
    }

    /**
     * Gets identity mapping for given store
     *
     * @param string $sStore Store name (as defined by client in Beezup and disponible in BeezupOMOrderItem::getOrderItemBeezUPStoreId())
     *
     * @return string|null Name of the mapping or null
     */
    public function getCarrierMapping()
    {
        return $this->aCarrierMapping;
    }


    /**
     * Gets identity mapping for given store
     *
     * @param string $sStore Store name (as defined by client in Beezup and disponible in BeezupOMOrderItem::getOrderItemBeezUPStoreId())
     *
     * @return string|null Name of the mapping or null
     */
    public function getCarrierMappingForMarketplace($sMarketplace)
    {
        return isset($this->aCarrierMapping[$sMarketplace])
            ? $this->aCarrierMapping[$sMarketplace] : null;
    }

    public function getCurrentCarrierMappingForMarketplace($sMarketplace)
    {
        return isset($this->aCarrierMapping[$sMarketplace])
            ? max($this->aCarrierMapping[$sMarketplace]) : null;
    }
}

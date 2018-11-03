<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMStoreMapper
{
    protected $aStoreMapping = array();


    /**
     * Gets stores mappings for all stores
     *
     * @return array Defined mappings for all Beezup stores
     */
    public function __construct(array $aStoreMapping = array())
    {
        $this->aStoreMapping = $aStoreMapping;
    }

    /**
     * Gets identity mapping for given store
     *
     * @param string $sStore Store name (as defined by client in Beezup and disponible in BeezupOMOrderItem::getOrderItemBeezUPStoreId())
     *
     * @return string|null Name of the mapping or null
     */
    public function getStoreMapping()
    {
        return $this->aStoreMapping;
    }


    /**
     * Gets identity mapping for given store
     *
     * @param string $sStore Store name (as defined by client in Beezup and disponible in BeezupOMOrderItem::getOrderItemBeezUPStoreId())
     *
     * @return string|null Name of the mapping or null
     */
    public function getStoreMappingForStore($sStore)
    {
        $sStore = (string)$sStore;

        return isset($this->aStoreMapping[$sStore])
            ? $this->aStoreMapping[$sStore] : null;
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMStoresResult extends BeezupOMResult
{
    protected $aStores = array();


    /**
     * @return the $aValues
     */
    public function getStores()
    {
        return $this->aStores;
    }

    /**
     * @param multitype: $aValues
     */
    public function setStores($aStores)
    {
        $this->aStores = $aStores;

        return $this;
    }

    public function addStore(BeezupOMStore $oStore)
    {
        $this->aStores[] = $oStore;

        return $this;
    }

    public static function fromArray(array $aData = array())
    {
        $oResult = new BeezupOMStoresResult();
        if (array_key_exists('beezUPStores', $aData)
            && is_array($aData['beezUPStores'])
        ) {
            foreach ($aData['beezUPStores'] as $aStore) {
                $oResult->addStore(BeezupOMStore::fromArray($aStore));
            }
        }

        return $oResult;
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingComposed extends BeezupOMIdMapping
{
    const STRATEGY_ALL = 'ALL';
    const STRATEGY_MAX = 'MAX';
    const STRATEGY_ALL_NOT_EMPTY = 'ALL_NOT_EMPTY';


    protected $aMappings = array();
    protected $sStrategy = self::STRATEGY_MAX;

    public function addMapping(BeezupOMIdMapping $oMapping)
    {
        $this->aMappings[] = $oMapping;
    }

    /**
     * Searches for prestashop product
     * We are trying to find declination matching, if there is any, we are searching for product
     * We are returning only one match; if there is more matches, an exception is thrown
     *
     * @param BeezupOMOrderItem $oItem
     *
     * @return array (int: id product, int: id product attribute)
     */
    public function find($sSelector)
    {
        $oItem = "";
        $aResult = array();
        $aFound = array();
        foreach ($this->aMappings as $oMapping) {
            $aFound[] = $oMapping->find($oItem);
        }

        return array_unique($aResult, SORT_REGULAR);
    }

    /**
     * Searches for prestashop product
     * We are trying to find all declinations and products matching
     *
     * @param BeezupOMOrderItem $oItem
     *
     * @return array array of array of (int: id product, int: id product attribute)
     */
    public function findAll($sSelector)
    {
        $aResult = array();
        $aFound = array();
        foreach ($this->aMappings as $oMapping) {
            $aResult = array_merge($aResult, $oMapping->findAll($sSelector));
        }

        return array_unique($aResult, SORT_REGULAR);
    }
}

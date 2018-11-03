<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

abstract class BeezupOMIdMapping
{
    public $mappingIdentifier = '';

    public function setMappingIdentifier($id)
    {
        $this->mappingIdentifier = $id;
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
        $sQuery = "";
        $nIdProduct = 0;
        $nIdProductAttribute = 0;
        list(
            $nIdProduct, $nIdProductAttribute
            )
            = $this->findProductAndAttributeId(
                $this->getProductAttributeQuery($sSelector)
            );
        if ($nIdProduct === 0) {
                $nIdProduct = $this->findProductId($this->getProductQuery($sQuery));
        } // if

        return array($nIdProduct, $nIdProductAttribute);
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
        $sQueryAttrs = $this->getProductAttributeQuery($sSelector);
        $sQueryProducts = $this->getProductQuery($sSelector);

        $aResult
            = array_merge(
                $this->findAllProductAndAttributeIds($sQueryAttrs),
                $this->findAllProductIds($sQueryProducts)
            );

        return array_unique($aResult, SORT_REGULAR);
    }

    /**
     * If we need to use shop association (PS 1.5 +)
     *
     * @return mixed
     */
    protected function useShopAssociation()
    {
        return version_compare(_PS_VERSION_, '1.5.0', 'ge');
    }

    /**
     *
     * @param string $sQuery
     *
     * @return array array of array of (int: id product, int: id product attribute)
     */
    protected function findAllProductAndAttributeIds($sQuery)
    {
        $aResult = array();
        $aRows = Db::getInstance()->executeS($sQuery);
        if ($aRows && is_array($aRows)) {
            foreach ($aRows as $aRow) {
                $aResult[] = array(
                    (int)$aRow['id_product'],
                    (int)$aRow['id_product_attribute'],
                );
            }
        }

        return $aResult;
    }

    /**
     *
     * @param string $sQuery
     *
     * @return array array of array of (int: id product, int: id product attribute)
     */
    protected function findAllProductIds($sQuery)
    {
        $aResult = array();
        $aRows = Db::getInstance()->executeS($sQuery);
        if ($aRows && is_array($aRows)) {
            foreach ($aRows as $aRow) {
                $aResult[] = array((int)$aRow['id_product'], 0);
            }
        }

        return $aResult;
    }

    protected function findProductIdAndAttributeId($sQuery, $sSelector)
    {
        $nIdProduct = 0;
        $nIdProductAttribute = 0;
        $aRows = $this->findAllProductAndAttributeIds($sQuery);
        if ($aRows && is_array($aRows)) {
            if (count($aRows) === 1) {
                $nIdProduct = (int)$aRows[0]['id_product'];
                $nIdProductAttribute = (int)$aRows[0]['id_product_attribute'];
            } else {
                throw new RuntimeException(
                    sprintf(
                        'BeezupOM : found %d possible matches for %s using %s',
                        count($aRows),
                        $sSelector,
                        get_class($this)
                    )
                );
            } // if
        }

        return array($nIdProduct, $nIdProductAttribute);
    }

    protected function findProductId($sQuery, $sSelector)
    {
        $nIdProduct = 0;
        $aRows = $this->findAllProductIds($sQuery);
        if ($aRows && is_array($aRows)) {
            if (count($aRows) === 1) {
                $nIdProduct = (int)$aRows[0]['id_product'];
            } else {
                throw new RuntimeException(
                    sprintf(
                        'BeezupOM : found %d possible matches for %s using %s',
                        count($aRows),
                        $sSelector,
                        get_class($this)
                    )
                );
            } // if
        }

        return $nIdProduct;
    }

    protected function getProductAttributeQuery($sSelector)
    {
        return '';
    }

    protected function getProductQuery($sSelector)
    {
        return '';
    }

    protected function existsProduct($nIdProduct, $nIdProductAttribute)
    {
        if ($nIdProduct > 0 && $nIdProductAttribute == 0) {
            return ((int)Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM '
                    ._DB_PREFIX_.'product WHERE id_product='.(int)$nIdProduct
            )
                === 1);
        } else {
            if ($nIdProduct > 0) {
                return ((int)Db::getInstance()->getValue(
                    'SELECT COUNT(*) FROM '
                        ._DB_PREFIX_.'product_attribute WHERE id_product='
                        .(int)$nIdProduct.' AND id_product_attribute= '
                        .(int)$nIdProductAttribute
                ) === 1);
            }
        }

        return false;
    }
}

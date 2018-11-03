<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingReferenceSupplier extends BeezupOMIdMappingField
{
    protected $sFieldName = 'product_supplier_reference';


    protected function getProductAttributeQuery($sSelector)
    {
        $sQuery
            = 'SELECT DISTINCT ps.id_product, ps.id_product_attribute FROM `'
            ._DB_PREFIX_.'product_supplier` ps';
        if ($this->useShopAssociation()) {
            $sQuery .= ' '.Shop::addSqlAssociation('ps_product_supplier', 'ps');
        }
        $sQuery .= ' WHERE ps.'.$this->sFieldName.' = "'.pSQL($sSelector).'"';

        return $sQuery;
    }

    /**
     * (non-PHPdoc)
     * @see BeezupOMIdMapping::findProductIdBySelector()
     */
    protected function getProductQuery($sSelector)
    {
        $sQuery = 'SELECT DISTINCT ps.id_product FROM `'._DB_PREFIX_
            .'product_supplier` ps';
        if ($this->useShopAssociation()) {
            $sQuery .= ' '.Shop::addSqlAssociation('product', 'ps');
        }
        $sQuery .= ' WHERE ps.'.$this->sFieldName.' = "'.pSQL($sSelector)
            .'" AND ps.id_product_attribute=0';

        return $sQuery;
    }
}

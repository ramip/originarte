<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingField extends BeezupOMIdMapping
{
    protected $sFieldName = '';


    public function find($sSelector)
    {
        parent::find($sSelector);
    }

    /**
     * (non-PHPdoc)
     * @see BeezupOMIdMapping::findProductIdAndAttributeIdBySelector()
     */
    protected function getProductAttributeQuery($sSelector)
    {
        $sQuery = 'SELECT pa.id_product, pa.id_product_attribute FROM `'
            ._DB_PREFIX_.'product_attribute` pa';
        if ($this->useShopAssociation()) {
            $sQuery .= ' '.Shop::addSqlAssociation('product_attribute', 'pa');
        }
        $sQuery .= ' WHERE pa.'.$this->sFieldName.' = "'.pSQL($sSelector).'"';

        return $sQuery;
    }

    /**
     * (non-PHPdoc)
     * @see BeezupOMIdMapping::findProductIdBySelector()
     */
    protected function getProductQuery($sSelector)
    {
        $sQuery = 'SELECT p.id_product FROM `'._DB_PREFIX_.'product` p';
        if ($this->useShopAssociation()) {
            $sQuery .= ' '.Shop::addSqlAssociation('product', 'p');
        }
        $sQuery .= ' WHERE p.'.$this->sFieldName.' = "'.pSQL($sSelector).'"';

        return $sQuery;
    }
}

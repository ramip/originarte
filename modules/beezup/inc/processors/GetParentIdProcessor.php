<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetParentIdProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert parent id from product into xml
     *
     * @param Product             $product
     * @param BeezupField         $field
     * @param DOMElement          $xml
     * @param BeezupConfiguration $config
     * @param integer             $idDeclension
     * @param string              $productType
     */
    public function process(
        Product $product,
        BeezupField $field,
        DOMElement $xml,
        BeezupConfiguration $config,
        $idDeclension = null,
        $productType = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $id = $productType == BeezupProduct::PRODUCT_TYPE_CHILD
            ? BeezupProduct::getIdProductAndAttribute($product->id, 0)
            : null;
        $this->addDOMElement($field, $id, $xml);
    }
}
<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetWholesalePriceProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert wholesale price information from product into xml
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
        $price = $product->wholesale_price;
        if ($idDeclension) {
            $price = $product->combinations[$idDeclension]->wholesale_price;
        }
        $this->addDOMElement(
            $field,
            round($price, 2),
            $xml,
            array('currency' => BeezupProduct::getCurrency()->iso_code)
        );
    }
}

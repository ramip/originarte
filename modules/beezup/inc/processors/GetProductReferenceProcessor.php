<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetProductReferenceProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert reference from product into xml
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
        $ref = $product->reference;

        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if ($idDeclension
            && isset($product->combinations[$idDeclension])
            && !empty($product->combinations[$idDeclension]->reference)
        ) {
            $ref = $product->combinations[$idDeclension]->reference;
        }

        $this->addDOMElement($field, $ref, $xml);
    }
}

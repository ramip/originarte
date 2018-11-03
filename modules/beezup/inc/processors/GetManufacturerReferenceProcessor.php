<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetManufacturerReferenceProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert manufacturer reference from product into xml
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
        if ($product->id_supplier) {
            $idSupply = ProductSupplier::getIdByProductAndSupplier(
                $product->id,
                (int)$idDeclension,
                (int)$product->id_supplier
            );
            $supply = new ProductSupplier($idSupply);
        } else {
            $supply = new ProductSupplier();
        }

        $this->addDOMElement($field, $supply->product_supplier_reference, $xml);
    }
}

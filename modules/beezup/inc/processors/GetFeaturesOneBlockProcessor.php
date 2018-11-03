<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetFeaturesOneBlockProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert features or attributes information from product into xml
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
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (isset($product->features)) {
            $retorno = "";
            $inc = 0;
            foreach ($product->features as $feat) {
                if (!empty($feat['value']) && !empty($feat['name'])) {
                    $retorno .= $feat['name'].": ".$feat['value']."</br>";
                    $inc++;
                }
            }
            if ($inc > 0) {
                $retorno = Tools::substr($retorno, 0, -5);
            }
            $this->addDOMElement($field, $retorno, $xml);
        }
    }
}

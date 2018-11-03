<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetFeaturesProcessor extends BeezupProcessorAbstract
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
        $field->balise = "all_features";
        $xmlFeatures = new DOMElement($field->balise);
        $xml->appendChild($xmlFeatures);
        $duplicates = array();
        foreach ($product->features as $feat) {
            if (!empty($feat['value']) && !empty($feat['name'])) {
                $tmpName = Tools::str2url(
                    Tools::strtolower(
                        str_replace(
                            " ",
                            "_",
                            $feat['name']
                        )
                    )
                );
                if (is_numeric($tmpName) && $tmpName > 0) {
                    $tmpName = "x_".$tmpName;
                }
                $balise = $tmpName;

                //we are not using array_key_exists because it affects performance
                //of the product feed generation
                if (isset($duplicates[$tmpName])) {
                    $inc = count($duplicates[$tmpName]) + 1;
                    $balise = $balise."_".$inc;
                }

                $field->balise = $balise;
                $this->addDOMElement(
                    $field,
                    $feat['value'],
                    $xmlFeatures,
                    array("name" => $feat['name'])
                );
                $duplicates[$tmpName][] = 1;
            }
        }
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetVariationthemeProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert meta title from product into xml
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
        $attNames = array();
        $values = array();
        foreach ($product->beezup_combinations as $combination) {
            foreach ($combination as $att) {
                $attribute = $att->toArray();
                if (!empty($attribute['group_name'])
                    && !isset($values[$attribute['group_name']])
                ) {
                    $attNames[] = Tools::strtolower($attribute['group_name']);
                    $values[$attribute['group_name']] = 1;
                }
            }
        }

        $variationTheme = implode("-", array_unique($attNames));
        $this->addDOMElement($field, $variationTheme, $xml);
    }
}

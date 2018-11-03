<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetGroupNameAttributesProcessor extends BeezupProcessorAbstract
{
    /**
     * Extract and insert meta title from product into xml.
     *
     * @param Product             $product
     * @param BeezupField         $field
     * @param DOMElement          $xml
     * @param BeezupConfiguration $config
     * @param int                 $idDeclension
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
        $title = $product->name;
        if ($idDeclension > 0) {
            $combinations = $product->beezup_combinations[$idDeclension];

            $tmpTitle = '';
            $inc = 0;
            foreach ($combinations as $combination) {
                $att = $combination->toArray();
                ++$inc;
                $tmpTitle .= $att['group_name'].': '.$att['attribute_name']
                    .' - ';
            }

            if ($inc > 0) {
                $tmpTitle = Tools::substr($tmpTitle, 0, -3);
            }

            $title = $product->name.' - '.$tmpTitle;
        }

        $this->addDOMElement($field, $title, $xml);
    }
}

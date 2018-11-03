<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetAttributesProcessor extends BeezupProcessorAbstract
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
        $duplicates = array();
        $field->balise = "all_attributes";
        $xmlAttributes = new DOMElement($field->balise);
        $xml->appendChild($xmlAttributes);
        if ($productType == BeezupProduct::PRODUCT_TYPE_PARENT) {
            $tmpAtts = array();
            $values = array();

            foreach ($product->beezup_combinations as $combination) {
                foreach ($combination as $att) {
                    $attribute = $att->toArray();
                    if (!empty($attribute['group_name'])
                        && !empty($attribute['attribute_name'])
                        && !$this->inArrayR(
                            $tmpAtts,
                            $attribute['group_name'],
                            'group_name',
                            $attribute['attribute_name'],
                            "attribute_name"
                        )
                    ) {
                        $tmpAtts[] = $attribute;
                        $values[$attribute['group_name']][]
                            = $attribute['attribute_name'];
                    }
                }
            }

            foreach ($values as $key => $value) {
                $tmpName = Tools::str2url(
                    Tools::strtolower(
                        str_replace(
                            " ",
                            "_",
                            $key
                        )
                    )
                );
                $atts = implode(",", $value);

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
                if (empty($tmpName)) {
                    continue;
                }

                $field->balise = $tmpName;
                $this->addDOMElement(
                    $field,
                    $atts,
                    $xmlAttributes,
                    array("name" => $key)
                );
            }
        } elseif ($productType == BeezupProduct::PRODUCT_TYPE_CHILD
            && $idDeclension > 0
        ) {
            $combinations = $product->beezup_combinations[$idDeclension];

            foreach ($combinations as $combination) {
                $att = $combination->toArray();
                $tmpName = Tools::str2url(
                    Tools::strtolower(
                        str_replace(
                            " ",
                            "_",
                            $att['group_name']
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
                if (empty($tmpName)) {
                    continue;
                }
                $field->balise = $tmpName;
                $this->addDOMElement(
                    $field,
                    $att['attribute_name'],
                    $xmlAttributes,
                    array("name" => $att['group_name'])
                );
            }
        }
    }

    public function inArrayR($array, $value, $key, $att_value, $att_key)
    {
        foreach ($array as $val) {
            if ($val[$key] == $value && $att_value == $val[$att_key]) {
                return true;
            }
        }

        return false;
    }
}

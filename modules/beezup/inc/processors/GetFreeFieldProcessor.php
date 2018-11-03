<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetFreeFieldProcessor extends BeezupProcessorAbstract
{
    protected static $groups_cache = array();

    /**
     * Extract and insert free field into xml
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
        $val = null;
        if ($field->id_feature) {
            foreach ($product->features as $feat) {
                if ($field->id_feature == $feat['id_feature']) {
                    $val = $feat['value'];
                    break;
                }
            }
        } elseif ($field->id_attribute_group) {
            $val = array();
            $cache_key_group = sprintf('gs%d', $product->id);
            if (!isset(self::$groups_cache[$cache_key_group])) {
                self::$groups_cache[$cache_key_group]
                    = $product->getAttributesGroups($config->id_default_lang);
            }

            foreach (self::$groups_cache[$cache_key_group] as $group) {
                if ($group['id_attribute_group']
                    != $field->id_attribute_group
                ) {
                    continue;
                }

                if ($idDeclension
                    && $group['id_product_attribute'] == $idDeclension
                ) {
                    $val[] = $group['attribute_name'];
                } elseif ($productType == BeezupProduct::PRODUCT_TYPE_PARENT) {
                    foreach ($product->combinations as $id => $combination) {
                        if ($combination->quantity == 0
                            && $config->disable_oos_product
                        ) {
                            continue;
                        } elseif ($group['id_product_attribute'] == $id) {
                            $val[] = $group['attribute_name'];
                        }
                    }
                }
            }
            $val = array_unique($val);
        }

        $this->addDOMElement($field, $this->filterValue($field, $val), $xml);
    }

    /**
     * Filter given value(s) with Field configuration
     *
     * @param BeezupField $field
     * @param mixed       $value
     *
     * @return string
     */
    protected function filterValue(BeezupField $field, $value)
    {
        $res = array();
        if (empty($field->values_list)) {
            if (is_array($value)) {
                $res = $value;
            } else {
                $res[] = $value;
            }
        } else {
            $list = explode('|', $field->values_list);
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (in_array($val, $list)) {
                        $res[] = $val;
                    }
                }
            } elseif (!in_array($value, $list)) {
                $res[] = null;
            }
        }
        return implode(',', $res);
    }
}

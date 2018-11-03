<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

abstract class BeezupProcessorAbstract implements BeezupProcessorInterface
{
    /**
     * Add DOM element to XML
     *
     * @param BeezupConfig $config
     * @param mixed        $val
     * @param DOMElement   $xml
     * @param array        $attributs
     *
     * @return void
     */
    protected function addDOMElement(
        BeezupField $config,
        $val,
        DOMElement $xml,
        $attributs = array()
    ) {
        if (empty($val) && !empty($config->default)) {
            $val = $config->default;
        }

        if (!empty($val) && is_string($val)) {
            $DomElement = new DOMElement($config->balise);
            $xml->appendChild($DomElement);
            $DomElement->appendChild(new DOMCdataSection($val));
        } elseif (is_float($val)) {
            $DomElement = new DOMElement(
                $config->balise,
                number_format($val, 2, ',', '')
            );
            $xml->appendChild($DomElement);
        } else {
            $DomElement = new DOMElement($config->balise, $val);
            $xml->appendChild($DomElement);
        }

        // Attributs
        foreach ((array)$attributs as $name => $value) {
            $DomElement->setAttribute($name, $value);
        }
    }

    /**
     * Extract and insert data from product into xml
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
    }
}

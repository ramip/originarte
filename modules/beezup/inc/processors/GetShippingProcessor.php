<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetShippingProcessor extends BeezupProcessorAbstract
{
    protected static $cart = null;

    /**
     * Extract and insert shipping fees from product into xml
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
        $field->balise = "frais_de_port";
        if (self::$cart === null) {
            self::$cart = BeezupStaticProcessor::getInstance()
                ->getNewCart((int)BeezupProduct::getCurrency()->id);
        }

        $price = self::$cart->getPackageShippingCost(
            (int)$config->id_carrier,
            true,
            Context::getContext()->country,
            array(
                array(
                    'id_product'               => (int)$product->id,
                    'id_product_attribute'     => (int)$idDeclension,
                    'id_address_delivery'      => 0,
                    'cart_quantity'            => 1,
                    'is_virtual'               => $product->is_virtual,
                    'id_shop'                  => $product->id_shop_default,
                    'weight'                   => $product->weight
                        + ($idDeclension
                        && isset(
                            $product->combinations[$idDeclension]
                        )
                            ? (float)$product->combinations[$idDeclension]->weight
                            : 0),
                    'additional_shipping_cost' => $product->additional_shipping_cost,
                    'id_customization'         => (isset(
                        $product->id_customization
                    )
                        && $product->id_customization > 0)
                        ? $product->id_customization : 0,
                ),
            )
        );

        $this->addDOMElement(
            $field,
            (float)$price,
            $xml,
            array('currency' => BeezupProduct::getCurrency()->iso_code)
        );

        if (!empty($product->carriers)) {
            $field->balise = "carriers";
            $xmlCarriers = new DOMElement($field->balise);
            $xml->appendChild($xmlCarriers);
            //
            $tmpCarriers = $product->getCarriers();
            $pCarriers = array();
            foreach ($tmpCarriers as $c) {
                $pCarriers[] = $c['id_carrier'];
            }

            foreach ($product->carriers as $f_carrier) {
                $tmpName = Tools::str2url(
                    Tools::strtolower(
                        str_replace(
                            " ",
                            "_",
                            $f_carrier['name']
                        )
                    )
                );
                $field->balise = $tmpName;
                if (!in_array($f_carrier['object']->id, $pCarriers)) {
                    $this->addDOMElement($field, 0, $xmlCarriers);
                } elseif (!empty($f_carrier['value'])
                    && $f_carrier['value'] > 0
                ) {
                    $this->addDOMElement(
                        $field,
                        $f_carrier['value'],
                        $xmlCarriers
                    );
                } else {
                    $precio
                        = $f_carrier['object']->getDeliveryPriceByPrice(
                            $product->price,
                            $product->beezup_id_zone,
                            BeezupProduct::getCurrency()->id
                        );
                    if (empty($precio)) {
                        $precio = 0;
                    }
                    if ($precio == 0) {
                        $precio
                            = $f_carrier['object']->getDeliveryPriceByWeight(
                                $product->weight
                                + ($idDeclension
                                && isset(
                                    $product->combinations[$idDeclension]
                                )
                                    ? (float)$product->combinations[$idDeclension]->weight
                                    : 0),
                                $product->beezup_id_zone
                            );
                    }
                    if (empty($precio)) {
                        $precio = 0;
                    }
                    $this->addDOMElement($field, $precio, $xmlCarriers);
                }
            }
        }
    }
}

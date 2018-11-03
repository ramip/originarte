<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetPrixBarreProcessor extends BeezupProcessorAbstract
{
    protected static $currency_context = null;

    /**
     * Extract and insert priing information from product into xml
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
        $specificPrice = null;


        $price = BeezupStaticProcessor::getInstance()
            ->getPrice($this->getContext());
        $specificPrice = BeezupStaticProcessor::getInstance()
            ->getSpecificPrice();
        $reduction = 0;
        if (isset($specificPrice['reduction'])) {
            $reduction = $specificPrice['reduction'];
        }
        if (isset($specificPrice['reduction_type']) && $specificPrice['reduction_type'] == 'percentage') {
            if ($specificPrice['reduction'] > 0) {
                $percent = $specificPrice['reduction'] * 100;
                $percent = (100 - $percent) / 100;
                $reduction = ($price/$percent) - $price;
            }
        }
        if ($reduction > 0) {
            $priceNoReduc = $price + $reduction;
            $priceNoReduc = round($priceNoReduc, 2);
            $this->addDOMElement(
                $field,
                $priceNoReduc,
                $xml,
                array('currency' => BeezupProduct::getCurrency()->iso_code)
            );
        } else {
            $this->addDOMElement(
                $field,
                null,
                $xml,
                array('currency' => BeezupProduct::getCurrency()->iso_code)
            );
        }
    }


    protected function getContext()
    {
        if (self::$currency_context === null) {
            self::$currency_context = clone Context::getContext();

            self::$currency_context->currency = BeezupProduct::getCurrency();
        }

        return self::$currency_context;
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetURLProductProcessor extends BeezupProcessorAbstract
{
    /** @var Link Link object cache */
    protected static $link;

    /**
     * Extract and insert product url from product into xml
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
        if (self::$link === null) {
            self::$link = Context::getContext()->link;
        }
        if (version_compare(_PS_VERSION_, '1.6.1.1', '<')) {
            $url = self::$link->getProductLink(
                $product,
                null, //alias
                Category::getLinkRewrite(
                    (int)$product->id_category_default,
                    (int)$config->id_default_lang
                ), //category
                null, //ean13
                null, //id_lang
                BeezupRegistry::get('BEEZUP_ALL_SHOPS')
                    ? $product->id_shop_default : null, // id_shop
                (int)$idDeclension,
                false // force_route
            );
        } else {
            $url = self::$link->getProductLink(
                $product,
                null, //alias
                Category::getLinkRewrite(
                    (int)$product->id_category_default,
                    (int)$config->id_default_lang
                ), //category
                null, //ean13
                null, //id_lang
                BeezupRegistry::get('BEEZUP_ALL_SHOPS')
                    ? $product->id_shop_default : null, // id_shop
                (int)$idDeclension,
                false, // force_route
                false, //relative_protocol
                true //add_anchor
            );
        }
        $this->addDOMElement($field, $url, $xml);
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class GetAttachementsProcessor extends BeezupProcessorAbstract
{
    protected static $link;

    /**
     * Extract and insert short description from product into xml
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
        if ($product->cache_has_attachments) {
            $attachements = $product->getAttachments($config->id_default_lang);
            if (self::$link === null) {
                self::$link = Context::getContext()->link;
            }
            foreach ($attachements as $key => $attachement) {
                $f = clone $field;
                $url = self::$link->getPageLink('attachment.php', true)
                    .'?id_attachment='.$attachement['id_attachment'];
                $f->balise = sprintf('URLDocument%d', $key + 1);
                $this->addDOMElement($f, $url, $xml);
            }
        }
    }
}

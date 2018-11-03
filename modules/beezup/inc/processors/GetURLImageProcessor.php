<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).'/../URI.php';

/**
 * Image url field processor
 *
 *
 * @package Prestashop/Modules/Beezup/Processor/SubProcessors
 */
class GetURLImageProcessor extends BeezupProcessorAbstract
{

    /**
     *
     * @var integer # maximal des images
     */
    const MAX_IMAGES = 5;

    /** @var Link Link object cache */
    protected $link;

    /** @var Images cache */
    protected static $aCombinationsImagesCache = array();
    protected static $aProductsImagesCache = array();
    protected static $aUrlsCache = array();


    /**
     * Extract and insert image url from product into xml
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
        $nIdDeclination = null,
        $productType = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (null === $this->link) {
            $this->link = Context::getContext()->link;
        }

        $aImages = $this->getImages(
            $product,
            $config,
            $nIdDeclination,
            $config->id_default_lang
        );

        if ($nIdDeclination
            && $this->getCombinationImages($product, $config, $nIdDeclination)
        ) {
            $nIdCover = null;
        } else {
            $nIdCover = $this->getCoverImageId($product);
        } // if

        $aImagesIds = $this->getImagesIds($aImages, $nIdCover);

        $aTags = $this->getTagsList($aImagesIds);

        $this->generateTags($aTags, $product, $field, $xml, $config);
    }

    protected function getImagesIds($aImages, $nIdCover = null)
    {
        $aImagesIds = array();

        if ($nIdCover) {
            $aImagesIds[] = $nIdCover;
        } // if

        # all other images

        if (is_array($aImages)) {
            foreach ($aImages as $aImage) {
                $nImageId = (int)$aImage['id_image'];

                if ($nImageId && $nImageId != $nIdCover) {
                    $aImagesIds[] = $nImageId;
                } // if
            } // foreach
        }

        return array_unique($aImagesIds);
    }

    protected function getImages($oProduct, $oConfig, $nIdDeclination, $nIdLang)
    {

        # img search
        if ($nIdDeclination) {
            $aResult = $this->getCombinationImages(
                $oProduct,
                $oConfig,
                $nIdDeclination
            );
        } else {
            $aResult = $this->getProductImages($oProduct, $oConfig);
        } // if

        return $aResult;
    }


    protected function getCombinationImages(
        $oProduct,
        $oConfig,
        $nIdDeclination
    ) {
        $sCacheKey = sprintf(
            '%d-%d',
            $oProduct->id,
            (int)Shop::getContextShopID()
        );

        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset(self::$aCombinationsImagesCache[$sCacheKey])
        ) {
            self::$aCombinationsImagesCache[$sCacheKey]
                = $oProduct->getCombinationImages($oConfig->id_default_lang);
        }

        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (is_array(self::$aCombinationsImagesCache[$sCacheKey])
            && isset(
                self::$aCombinationsImagesCache[$sCacheKey][$nIdDeclination]
            )
            && is_array(
                self::$aCombinationsImagesCache[$sCacheKey][$nIdDeclination]
            )
        ) {
            return self::$aCombinationsImagesCache[$sCacheKey][$nIdDeclination];
        }

        return array();
    }

    protected function getProductImages($oProduct, $oConfig)
    {
        $sCacheKey = sprintf(
            '%d-%d',
            $oProduct->id,
            (int)Shop::getContextShopID()
        );

        if (!isset(self::$aProductsImagesCache[$sCacheKey])) {
            self::$aProductsImagesCache[$sCacheKey]
                = $oProduct->getImages($oConfig->id_default_lang);
        }

        return self::$aProductsImagesCache[$sCacheKey];
    }

    protected function getCoverImageId($oProduct)
    {
        $aCover = Product::getCover($oProduct->id);

        return ($aCover ? (int)$aCover['id_image'] : 0);
    }

    protected function getTagsList($aImagesIds)
    {
        $aTags = array();

        $nMaxImg = min(max(BeezupProduct::getMaxImg(), 1), self::MAX_IMAGES);

        foreach (range(1, $nMaxImg) as $nTagId) {
            //we are not using array_key_exists because it affects performance
            //of the product feed generation
            $aTags[$nTagId] = isset($aImagesIds[$nTagId - 1])
                ? $aImagesIds[$nTagId - 1] : null;
        } // foreach

        return $aTags;
    }

    protected function generateTags($aTags, $product, $field, $xml, $config)
    {
        $aMediaServers = array_filter(
            array(
                _MEDIA_SERVER_1_,
                _MEDIA_SERVER_2_,
                _MEDIA_SERVER_3_,
            )
        );

        $oUri = new URI();


        foreach ($aTags as $nKey => $mImageId) {
            $sImageId = Configuration::get('PS_LEGACY_IMAGES') ? $product->id
                .'-'.$mImageId : $mImageId;

            $mUrl = ($mImageId
                ? $this->link->getImageLink(
                    $product->link_rewrite,
                    $sImageId,
                    $config->image_type
                ) : null);

            if ($mUrl && BeezupRegistry::get('BEEZUP_ALL_SHOPS')) {
                $sShopDomain
                    = $this->getShopMainDomain($product->id_shop_default);

                $sImageDomain = $oUri->fromString($mUrl)->getHost();

                if (!in_array($sImageDomain, $aMediaServers)
                    && $sShopDomain != $sImageDomain
                ) {
                    $mUrl = $mUrl.' '.str_replace(
                        $sImageDomain,
                        $sShopDomain,
                        $mUrl
                    ).' '.$sImageDomain;
                } // if
            }

            $field->balise = 'url_image'.($nKey == 1 ? '' : '_'.$nKey);

            $this->addDOMElement($field, $mUrl, $xml);
        } // foreach
    }

    protected function getShopMainDomain($nIdShop)
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset(self::$aUrlsCache[$nIdShop])
        ) {
            self::$aUrlsCache[$nIdShop] = Db::getInstance()
                ->getValue(
                    'SELECT domain FROM '._DB_PREFIX_
                    .'shop_url WHERE main=1 AND id_shop = '.(int)$nIdShop
                );
        } // if

        return self::$aUrlsCache[$nIdShop];
    }
}

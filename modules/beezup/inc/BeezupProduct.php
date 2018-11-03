<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupProduct
{
    /** @var string Simple product (no children or parent) */
    const PRODUCT_TYPE_SIMPLE = 'simple';
    /** @var string Parent product */
    const PRODUCT_TYPE_PARENT = 'parent';
    /** @var string Child product */
    const PRODUCT_TYPE_CHILD = 'child';

    /** @var int PS id multiplicator for Beezup IDs */
    private static $__mutiplicator = null;

    /**
     * Specific price cache
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     * @var array
     */
    private static $__specificPrice = array();

    /**
     * Parent added products ids list
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     * @var array
     */
    private static $__parentProductsAdded = array();


    private static $__nbImg = null;

    private static $currency = null;

    /**
     * Initialize multiplicator
     *
     * @return void
     */
    private static function __initMutiplicator()
    {
        if (!self::$__mutiplicator) {
            $sql = 'SELECT MAX(`id_product_attribute`) AS max FROM `'
                ._DB_PREFIX_.'product_attribute`';
            $aMax = Db::getInstance()->getRow($sql);

            if (!$aMax) {
                return false;
            }

            if (!isset($aMax['max'])) {
                $max = 0;
            } else {
                $max = (int)$aMax['max'];
            }

            if ($max == 0) {
                $mult = 1;
            } else {
                $mult = 1;
                while ($mult < $max) {
                    $mult *= 10;
                }
            }
            self::$__mutiplicator = $mult;
        }
    }


    public static function getMaxImg()
    {
        if (!self::$__nbImg) {
            $sQuery = 'SELECT COUNT(*) as nb FROM `'._DB_PREFIX_
                .'image` GROUP BY `id_product`  '.
                'ORDER BY nb DESC LIMIT 1';

            $aNbImg = Db::getInstance()->ExecuteS($sQuery);

            self::$__nbImg = (int)$aNbImg[0]['nb'];
        }

        return self::$__nbImg;
    }

    /**
     * Get all products
     *
     * @param integer $id_lang
     *
     * @return array
     * @todo delete if not used
     */
    public static function getProducts($id_lang)
    {
        return Product::getProducts(
            $id_lang,
            0,
            0,
            'id_product',
            'ASC',
            false,
            false,
            Context::getContext()
        );
    }

    /**
     * Get features for a given product.
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array   $product
     * @param integer $id_lang
     *
     * @return boolean
     */
    public static function getProductFeatures(&$product, $id_lang)
    {
        $sql
            = '
			SELECT
				fp.`id_feature`,
				fvl.`value`
			FROM `'._DB_PREFIX_.'feature_product` fp
			LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl
				ON fp.`id_feature_value` = fvl.`id_feature_value`
				AND fvl.`id_lang` = '.(int)$id_lang.'
			WHERE fp.`id_product` = '.(int)$product['id_product'];

        $features = Db::getInstance()->ExecuteS($sql);

        if (!$features) {
            return false;
        }

        foreach ($features as &$feature) {
            $product['feat_'.(int)$feature['id_feature']] = $feature['value'];
        }

        return true;
    }

    /**
     * Get attributes for a given product.
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array   $product
     * @param integer $id_lang
     *
     * @return boolean
     */
    public static function getProductAttributes(&$product, $id_lang)
    {
        $sql
            = 'SELECT
				pa.`id_product_attribute`,
				pa.`reference`,
				pa.`supplier_reference`,
				pa.`location`,
				pa.`ean13`,
				pa.`wholesale_price`,
				pa.`price`,
				pa.`ecotax`,
				pa.`quantity`,
				pa.`weight`,
				pa.`default_on`,
				'.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'pai.`id_image`' : 'pa.`id_image`').',
				il.`legend`,
				a.`id_attribute_group`,
				al.`name`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON pa.`id_product_attribute` = pac.`id_product_attribute`
			'.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` paiON pa.`id_product_attribute` = pai.`id_product_attribute`' : '').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON '.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'pai' : 'pa').'.`id_image` = il.`id_image`
				AND il.`id_lang` = '.(int)$id_lang.'
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON pac.`id_attribute` = a.`id_attribute`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON a.`id_attribute` = al.`id_attribute`
				AND al.`id_lang` = '.(int)$id_lang.'
			WHERE pa.`id_product` = '.(int)$product['id_product'];

        $attributes = Db::getInstance()->ExecuteS($sql);

        if (!$attributes) {
            return false;
        }

        foreach ($attributes as &$attr) {
            $id_pa = (int)$attr['id_product_attribute'];
            $id_ag = (int)$attr['id_attribute_group'];

            if (!isset($product['attr_'.$id_ag])
                || !in_array($attr['name'], $product['attr_'.$id_ag])
            ) {
                $product['attr_'.$id_ag][] = $attr['name'];
            }

            if ($attr['default_on']) {
                $product['id_product_attribute'] = (int)$id_pa;

                if (!empty($attr['reference'])) {
                    $product['reference'] = $attr['reference'];
                }

                if (!empty($attr['supplier_reference'])) {
                    $product['supplier_reference']
                        = $attr['supplier_reference'];
                }

                if (!empty($attr['location'])) {
                    $product['location'] = $attr['location'];
                }

                if (!empty($attr['ean13'])) {
                    $product['ean13'] = $attr['ean13'];
                }

                if (!empty($attr['wholesale_price'])
                    && $attr['wholesale_price'] > 0
                ) {
                    $product['wholesale_price'] = $attr['wholesale_price'];
                }

                if (!empty($attr['price']) && $attr['price'] > 0) {
                    $product['default_attribute_price'] = $attr['price'];
                }

                if (!empty($attr['ecotax']) && $attr['ecotax'] > 0) {
                    $product['ecotax'] = $attr['ecotax'];
                }

                $product['quantity'] = $attr['quantity'];

                if (!empty($attr['weight']) && $attr['weight'] > 0) {
                    $product['attr_weight'] = (float)$attr['weight'];
                }

                if (!empty($attr['id_image']) && $attr['id_image'] > 0) {
                    $product['id_image'] = $attr['id_image'];
                }

                if (!empty($attr['legend']) && $attr['legend'] > 0) {
                    $product['image_legend'] = $attr['legend'];
                }
            }
        }

        return true;
    }

    /**
     * Get product attributes as new product
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array   $product
     * @param integer $id_lang
     *
     * @return boolean
     */
    public static function getAttributesAsProduct(&$product, $id_lang)
    {
        $sql
            = 'SELECT
				pa.`id_product_attribute`,
				pa.`reference`,
				pa.`supplier_reference`,
				pa.`location`,
				pa.`ean13`,
				pa.`wholesale_price`,
				pa.`price`,
				pa.`ecotax`,
				pa.`quantity`,
				pa.`weight`,
				pa.`default_on`,
				'.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'pai.`id_image`' : 'pa.`id_image`').',
				il.`legend`,
				a.`id_attribute_group`,
				al.`name`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON pa.`id_product_attribute` = pac.`id_product_attribute`
			'.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_image` pai	ON pa.`id_product_attribute` = pai.`id_product_attribute`' : '').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON '.(version_compare(_PS_VERSION_, '1.2', '>=') ? 'pai' : 'pa').'.`id_image` = il.`id_image`
				AND il.`id_lang` = '.(int)$id_lang.'
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON pac.`id_attribute` = a.`id_attribute`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON a.`id_attribute` = al.`id_attribute`
				AND al.`id_lang` = '.(int)$id_lang.'
			WHERE pa.`id_product` = '.(int)$product['id_product'];

        $attributes = Db::getInstance()->ExecuteS($sql);

        if (!$attributes) {
            return false;
        }

        foreach ($attributes as &$attr) {
            $id_pa = (int)$attr['id_product_attribute'];
            $id_ag = (int)$attr['id_attribute_group'];

            $product['declension'][$id_pa]['id_product_attribute']
                = $attr['id_product_attribute'];
            $product['declension'][$id_pa]['reference'] = $attr['reference'];
            $product['declension'][$id_pa]['supplier_reference']
                = $attr['supplier_reference'];
            $product['declension'][$id_pa]['location'] = $attr['location'];
            $product['declension'][$id_pa]['ean13'] = $attr['ean13'];
            $product['declension'][$id_pa]['wholesale_price']
                = $attr['wholesale_price'];
            $product['declension'][$id_pa]['price'] = $attr['price'];
            $product['declension'][$id_pa]['ecotax'] = $attr['ecotax'];
            $product['declension'][$id_pa]['quantity'] = $attr['quantity'];
            $product['declension'][$id_pa]['weight'] = $attr['weight'];
            $product['declension'][$id_pa]['default_on'] = $attr['default_on'];
            $product['declension'][$id_pa]['id_image'] = $attr['id_image'];
            $product['declension'][$id_pa]['image_legend'] = $attr['legend'];

            if (!isset($product['declension'][$id_pa]['attr_'.$id_ag])
                || !in_array(
                    $attr['name'],
                    $product['declension'][$id_pa]['attr_'.$id_ag]
                )
            ) {
                $product['declension'][$id_pa]['attr_'.$id_ag][]
                    = $attr['name'];
            }
        }

        return true;
    }

    /**
     * Calculate product reduction price.
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param float   $reduction_price
     * @param float   $reduction_percent
     * @param string  $date_from
     * @param string  $date_to
     * @param float   $product_price
     * @param boolean $usetax
     * @param float   $taxrate
     *
     * @return float
     */
    public static function getReductionValue(
        $reduction_price,
        $reduction_percent,
        $date_from,
        $date_to,
        $product_price,
        $usetax,
        $taxrate
    ) {
        // Avoid an error with 1970-01-01
        if ((!Validate::isDate($date_from) or !Validate::isDate($date_to))
            && !($date_from == '0000-00-00 00:00:00'
                && $date_to == '0000-00-00 00:00:00')
        ) {
            return 0;
        }

        $date_from = strtotime($date_from);
        $date_to = strtotime($date_to);
        $currentDate = time();


        if ($date_from != $date_to and ($currentDate > $date_to or $currentDate
                < $date_from)
        ) {
            return 0;
        }

        // reduction values
        if (!$usetax) {
            $reduction_price /= (1 + ($taxrate / 100));
        }

        // make the reduction
        if ($reduction_price and $reduction_price > 0) {
            if ($reduction_price >= $product_price) {
                $ret = $product_price;
            } else {
                $ret = $reduction_price;
            }
        } elseif ($reduction_percent and $reduction_percent > 0) {
            if ($reduction_percent >= 100) {
                $ret = $product_price;
            } else {
                $ret = $product_price * $reduction_percent / 100;
            }
        }

        return isset($ret) ? $ret : 0;
    }

    /**
     * Calculate product price
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array               $product
     * @param BeezupConfiguration $config
     * @param integer             $id_declension
     * @param boolean             $use_reduc
     *
     * @return float
     */
    public static function getPrice(
        &$product,
        &$config,
        $id_declension = null,
        $use_reduc = true
    ) {
        $specificPrice = null;

        $price = Product::priceCalculation(
            0,
            (int)$product['id_product'],
            (int)$id_declension,
            (int)Configuration::get('BEEZUP_COUNTRY'),
            0,
            0,
            (int)BeezupProduct::getCurrency()->id,
            1,
            1,
            true,
            2,
            false,
            $use_reduc,
            true,
            $specificPrice,
            true
        );

        self::$__specificPrice[(int)$product['id_product']] = $specificPrice;

        return $price;
    }

    /**
     * Get product specific price
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array $product
     *
     * @return array
     */
    public static function getSpecificPrice(&$product)
    {
        if (!isset(self::$__specificPrice[(int)$product['id_product']])) {
            self::$__specificPrice[(int)$product['id_product']]
                = SpecificPrice::getSpecificPrice(
                    (int)$product['id_product'],
                    0,
                    (int)BeezupProduct::getCurrency()->id,
                    (int)Configuration::get('BEEZUP_COUNTRY'),
                    1,
                    1
                );
        }

        return self::$__specificPrice[(int)$product['id_product']];
    }

    /**
     * Calculate product weight
     *
     * @deprecated since version 2.3.0
     * @todo       check and delete if no more used
     *
     * @param array   $product
     * @param integer $id_declension
     *
     * @return float
     */
    public static function getWeight(&$product, $id_declension = null)
    {
        $weight = (float)$product['weight'];

        if ($id_declension
            && isset($product['declension'])
            && !empty($product['declension'][$id_declension]['weight'])
        ) {
            $weight += (float)$product['declension'][$id_declension]['weight'];
        } elseif (isset($product['attr_weight'])) {
            $weight += (float)$product['attr_weight'];
        }

        return $weight;
    }

    /**
     * Return a Beezup generated Id for a product and its declension
     *
     * @param integer $id_product
     * @param integer $id_product_attribute
     *
     * @return integer
     */
    public static function getIdProductAndAttribute(
        $id_product,
        $id_product_attribute
    ) {
        if (BeezupRegistry::get('BEEZUP_NEW_PRODUCT_ID_SYSTEM')) {
            return self::getNewIdProductAndAttribute(
                $id_product,
                $id_product_attribute
            );
        }

        return self::getOldIdProductAndAttribute(
            $id_product,
            $id_product_attribute
        );
    }

    protected static function getNewIdProductAndAttribute(
        $id_product,
        $id_product_attribute
    ) {
        return (int)$id_product_attribute > 0 ? sprintf(
            '%d_%d',
            (int)$id_product,
            (int)$id_product_attribute
        ) : (int)$id_product;
    }

    public static function getOldIdProductAndAttribute(
        $id_product,
        $id_product_attribute
    ) {
        self::__initMutiplicator();

        return ((int)$id_product * (int)self::$__mutiplicator)
            + (int)$id_product_attribute;
    }

    public static function setCurrency(Currency $currency)
    {
        if ($currency->id) {
            self::$currency = $currency;
        } // if
    }

    /**
     *
     * @return Currency
     */
    public static function getCurrency()
    {
        if (self::$currency === null) {
            self::$currency = Context::getContext()->currency;
        } // if

        return self::$currency;
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupFieldProcess
{
    /** @var array Taxes cache */
    public static $__taxes = null;

    /** @var array Taxes zones cache */
    public static $__zoneTax = null;

    /** @var int Default lang cache */
    public static $__id_lang_default = null;

    /** @var array shipping range prices by carrier cache */
    public static $__zoneCarrierRangePrice = null;

    /** @var array carriers cache */
    public static $__carriers = null;

    /** @var array Categories cache */
    public static $__categories = null;

    public static $__link = null;

    /**
     * Intialize caches
     *
     * @access public
     * @static
     * @return boolean
     */
    public static function init()
    {
        $ret = self::__initTaxes();
        $ret &= self::__initZoneTax();
        $ret &= self::__initLangDefault();
        $ret &= self::__initZoneCarrierRangePrice();
        $ret &= self::__initCarriers();
        $ret &= self::__initCategories();

        self::$__link = new Link();

        return $ret;
    }

    /**
     * Initialize taxes cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initTaxes()
    {
        self::$__taxes = array();

        $results = Db::getInstance()->ExecuteS(
            'SELECT * FROM `'._DB_PREFIX_
            .'tax`'
        );
        if (!$results) {
            return false;
        }

        foreach ($results as &$res) {
            self::$__taxes[(int)$res['id_tax']] = $res;
        }

        return true;
    }

    /**
     * Initialize Taxes zone cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initZoneTax()
    {
        self::$__zoneTax
            = TaxRulesGroup::getAssociatedTaxRatesByIdCountry(
                Configuration::get('BEEZUP_COUNTRY')
            );

        return true;
    }

    /**
     * Initialize default language cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initLangDefault()
    {
        self::$__id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');

        return true;
    }

    /**
     * Initialize shipping range prices by carrier cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initZoneCarrierRangePrice()
    {
        self::$__zoneCarrierRangePrice = array();

        $type = Configuration::get('PS_SHIPPING_METHOD') ? 'weight' : 'price';
        $sql
            = 'SELECT
		cz.`id_zone`,
		cz.`id_carrier`,
		r.`id_range_'.$type.'`,
		r.`delimiter1`,
		r.`delimiter2`,
		d.`price`
		FROM `'._DB_PREFIX_.'carrier_zone` cz
		LEFT JOIN `'._DB_PREFIX_.'range_'.$type.'` r
			ON r.`id_carrier` = cz.`id_carrier`
		LEFT JOIN `'._DB_PREFIX_.'delivery` d
			ON d.`id_carrier` = cz.`id_carrier`
			AND d.`id_zone` = cz.`id_zone`
			AND d.`id_range_'.$type.'` = r.`id_range_'.$type.'`
		LEFT JOIN `'._DB_PREFIX_.'carrier` c
			ON c.`id_carrier` = cz.`id_carrier`
		WHERE r.`delimiter1` IS NOT NULL
		AND r.`delimiter2` IS NOT NULL
		AND d.`price` IS NOT NULL
		AND r.`id_range_'.$type.'` IS NOT NULL
		AND c.`deleted` = 0
		ORDER BY r.`delimiter1` ASC, r.`delimiter2` ASC';

        $results = Db::getInstance()->ExecuteS($sql);

        if (!$results) {
            return false;
        }

        foreach ($results as $res) {
            self::$__zoneCarrierRangePrice[(int)$res['id_zone']][(int)$res['id_carrier']][]
                = array(
                'min'   => (float)$res['delimiter1'],
                'max'   => (float)$res['delimiter2'],
                'price' => (float)$res['price'],
            );
        }

        return true;
    }

    /**
     * Initialize carriers cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initCarriers()
    {
        self::$__carriers = array();

        $sql
            = 'SELECT *
		FROM `'._DB_PREFIX_.'carrier` c
		LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl
			ON c.`id_carrier` = cl.`id_carrier`
		WHERE c.`deleted` = 0';

        $results = Db::getInstance()->ExecuteS($sql);

        if (!$results) {
            return false;
        }

        foreach ($results as $res) {
            self::$__carriers[(int)$res['id_carrier']]['id_carrier']
                = ( int)$res['id_carrier'];
            self::$__carriers[(int)$res['id_carrier']]['id_tax_rules_group']
                = (int)$res['id_tax_rules_group'];
            self::$__carriers[(int)$res['id_carrier']]['name'] = $res['name'];
            self::$__carriers[(int)$res['id_carrier']]['url'] = $res['url'];
            self::$__carriers[(int)$res['id_carrier']]['active']
                = (int)$res['active'];
            self::$__carriers[(int)$res['id_carrier']]['shipping_handling']
                = (int)$res['shipping_handling'];
            self::$__carriers[(int)$res['id_carrier']]['range_behavior']
                = (int)$res['range_behavior'];
            self::$__carriers[(int)$res['id_carrier']]['delay'][(int)$res['id_lang']]
                = $res['delay'];
        }

        return true;
    }

    /**
     * Initialize categories cache
     *
     * @access private
     * @static
     * @return boolean
     */
    private static function __initCategories()
    {
        $sql
            = 'SELECT *
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON cl.`id_category` = c.`id_category`';

        $results = Db::getInstance()->ExecuteS($sql);

        if (!$results) {
            return false;
        }

        self::$__categories = array();

        foreach ($results as &$res) {
            self::$__categories[(int)$res['id_category']]['id_category']
                = (int)$res['id_category'];
            self::$__categories[(int)$res['id_category']]['id_parent']
                = (int)$res['id_parent'];
            self::$__categories[(int)$res['id_category']]['level_depth']
                = (int)$res['level_depth'];
            self::$__categories[(int)$res['id_category']]['active']
                = (int)$res['active'];
            self::$__categories[(int)$res['id_category']]['name'][(int)$res['id_lang']]
                = $res['name'];
            self::$__categories[(int)$res['id_category']]['description'][(int)$res['id_lang']]
                = $res['description'];
            self::$__categories[(int)$res['id_category']]['link_rewrite'][(int)$res['id_lang']]
                = $res['link_rewrite'];
        }

        return true;
    }

    /**
     * Add DOM element to XML
     *
     * @access private
     * @static
     *
     * @param BeezupConfig $config
     * @param mixed        $val
     * @param DOMElement   $xml
     * @param array        $attributs
     *
     * @return void
     */
    private static function __addDOMElement(
        &$config,
        $val,
        &$xml,
        $attributs = array()
    ) {
        if (!empty($val) && is_string($val)) {
            $DomElement = new DOMElement($config['balise']);
            $xml->appendChild($DomElement);
            $DomElement->appendChild(new DOMCdataSection($val));
        } elseif (is_float($val)) {
            $DomElement = new DOMElement(
                $config['balise'],
                number_format($val, 2, ',', '')
            );
            $xml->appendChild($DomElement);
        } else {
            $DomElement = new DOMElement($config['balise'], $val);
            $xml->appendChild($DomElement);
        }

        // Attributs
        foreach ((array)$attributs as $name => $value) {
            $DomElement->setAttribute($name, $value);
        }
    }


    /**
     * Insert if product is parent or child
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     * @param bool         $product_type
     */
    public static function getParentOrChild(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (BeezupProduct::PRODUCT_TYPE_SIMPLE != $product_type) {
            self::__addDOMElement($field, $product_type, $xml);
        } else {
            self::__addDOMElement($field, null, $xml);
        }
    }

    /**
     * Insert product parent id
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     * @param bool         $product_type
     */
    public static function getParentId(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (BeezupProduct::PRODUCT_TYPE_CHILD == $product_type) {
            self::__addDOMElement(
                $field,
                BeezupProduct::getIdProductAndAttribute(
                    (int)$product['id_product'],
                    0
                ),
                $xml
            );
        } else {
            self::__addDOMElement($field, null, $xml);
        }
    }

    /**
     * Insert Product reference into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getReference(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($id_declension) {
            $id_product_attribute = (int)$id_declension;
        } else {
            $id_product_attribute
                = Tools::getIsset($product['id_product_attribute'])
                ? (int)$product['id_product_attribute'] : 0;
        }

        self::__addDOMElement(
            $field,
            BeezupProduct::getIdProductAndAttribute(
                (int)$product['id_product'],
                $id_product_attribute
            ),
            $xml
        );
    }

    /**
     * Insert Product reference into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getProductReference(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($id_declension
            && Tools::getIsset($product['declension'][(int)$id_declension])
            && !empty($product['declension'][(int)$id_declension]['reference'])
        ) {
            self::__addDOMElement(
                $field,
                $product['declension'][(int)$id_declension]['reference'],
                $xml
            );
        } else {
            self::__addDOMElement($field, $product['reference'], $xml);
        }
    }

    /**
     * Insert Product EAN13 into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getEan(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($id_declension
            && Tools::getIsset($product['declension'][(int)$id_declension])
            && !empty($product['declension'][(int)$id_declension]['ean13'])
        ) {
            self::__addDOMElement(
                $field,
                $product['declension'][(int)$id_declension]['ean13'],
                $xml
            );
        } else {
            self::__addDOMElement($field, $product['ean13'], $xml);
        }
    }

    /**
     * Insert Product manufacturer reference into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getManufacturerReference(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($id_declension
            && Tools::getIsset($product['declension'][(int)$id_declension])
            && !empty($product['declension'][(int)$id_declension]['supplier_reference'])
        ) {
            self::__addDOMElement(
                $field,
                $product['declension'][(int)$id_declension]['supplier_reference'],
                $xml
            );
        } else {
            self::__addDOMElement($field, $product['supplier_reference'], $xml);
        }
    }

    /**
     * Insert Product manufacturer name into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getManufacturer(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement($field, $product['manufacturer_name'], $xml);
    }

    /**
     * Insert Product name into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getName(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement($field, $product['name'], $xml);
    }

    /**
     * Insert Product short description into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getShortDescription(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement($field, $product['description_short'], $xml);
    }

    /**
     * Insert Product description into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getDescription(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement($field, $product['description'], $xml);
    }

    /**
     * Insert Product Taxes included price into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getPriceTTC(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement(
            $field,
            BeezupProduct::getPrice($product, $config, $id_declension),
            $xml
        );
    }

    /**
     * Insert Product Ecotax into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getEcoTax(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($id_declension
            && Tools::getIsset($product['declension'][(int)$id_declension])
            && !empty($product['declension'][(int)$id_declension]['ecotax'])
            && $product['declension'][(int)$id_declension]['ecotax'] > 0
        ) {
            self::__addDOMElement(
                $field,
                $product['declension'][(int)$id_declension]['ecotax'],
                $xml
            );
        } else {
            self::__addDOMElement($field, $product['ecotax'], $xml);
        }
    }

    /**
     * Insert Product state into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getCondition(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $condition = str_replace(
            array('new', 'used', 'refurbished'),
            array('neuf', 'occasion', 'reconditionne'),
            $product['condition']
        );

        self::__addDOMElement($field, $condition, $xml);
    }

    /**
     * Insert Product weight into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getWeight(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        self::__addDOMElement(
            $field,
            BeezupProduct::getWeight($product, $id_declension),
            $xml
        );
    }

    /**
     * Insert Product FO URL into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getURLproduct(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $url = Configuration::get('BEEZUP_SITE_ADDRESS');
        if (!Configuration::get('PS_REWRITING_SETTINGS')) {
            $url .= '/product.php?id_product='.(int)$product['id_product'];
            if (self::$__id_lang_default != $product['id_lang']) {
                $url .= '&id_lang='.(int)$product['id_lang'];
            }
        } else {
            // ean13
            if ($id_declension
                && !empty($product['declension'][$id_declension]['ean13'])
            ) {
                $ean = '-'.$product['declension'][$id_declension]['ean13'];
            } elseif ($product['ean13']) {
                $ean = '-'.$product['ean13'];
            } else {
                $ean = '';
            }


            if (Language::countActiveLanguages() > 1) {
                $url .= '/'.$product['lang_iso_code'];
            }

            if ($product['id_category_default'] != 1) {
                $url .= '/'.$product['category_link_rewrite'];
            }

            $url .= '/'.(int)$product['id_product'].'-'.$product['link_rewrite']
                .$ean.'.html';

            if (version_compare(_PS_VERSION_, '1.2', '<')
                && self::$__id_lang_default != $product['id_lang']
            ) {
                $url .= '&id_lang='.(int)$product['id_lang'];
            }
        }

        if ($id_declension) {
            $id_product_attribute = (int)$id_declension;
        } else {
            $id_product_attribute
                = Tools::getIsset($product['id_product_attribute'])
                ? (int)$product['id_product_attribute'] : 0;
        }

        if ($id_product_attribute) {
            if (!Configuration::get('PS_REWRITING_SETTINGS')) {
                $url .= '&id_product_attribute='.$id_product_attribute;
            } else {
                $url .= '?id_product_attribute='.$id_product_attribute;
            }
        }

        self::__addDOMElement($field, $url, $xml);
    }

    /**
     * Insert Product Image URL into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getURLimage(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $url = Configuration::get('BEEZUP_SITE_ADDRESS');

        $id_image = (int)$product['id_image'];
        if ($id_declension
            && Tools::getIsset(
                $product['declension'][$id_declension]['id_image']
            )
            && (int)$product['declension'][$id_declension]['id_image']
        ) {
            $id_image = (int)$product['declension'][$id_declension]['id_image'];
        }

        $link = self::$__link;
        if ((int)$id_image == 0) {
            $iso = Language::getIsoById((int)$config->id_default_lang);
            $url = $link->getImageLink(
                $product['link_rewrite'],
                $iso.'-default',
                $config->image_type
            );
        } else {
            $url = $link->getImageLink(
                $product['link_rewrite'],
                (int)$product['id_product'].'-'.$id_image,
                $config->image_type
            );
        }

        self::__addDOMElement($field, $url, $xml);
    }

    /**
     * Insert Product Shipping cost into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getShipping(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $product_price = BeezupProduct::getPrice(
            $product,
            $config,
            $id_declension
        );
        $product_weight = BeezupProduct::getWeight($product, $id_declension);

        if (Configuration::get('PS_SHIPPING_METHOD')) { #weight
            $toSearch = $product_weight;
        } else { #price
            $toSearch = $product_price;
        }

        $id_zone = $id_carrier = 0;

        #if Zone and Carrier
        if (Tools::getIsset(
            self::$__zoneCarrierRangePrice[(int)$config->id_zone][(int)$config->id_carrier]
        )
        ) {
            $id_zone = (int)$config->id_zone;
            $id_carrier = (int)$config->id_carrier;
        } #if Zone and Carrier default
        elseif (Tools::getIsset(
            self::$__zoneCarrierRangePrice[(int)$config->id_zone][(int)Configuration::get(
                'PS_CARRIER_DEFAULT'
            )]
        )
        ) {
            $id_zone = (int)$config->id_zone;
            $id_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');
        } #if Zone
        elseif (Tools::getIsset(
            self::$__zoneCarrierRangePrice[(int)$config->id_zone]
        )
            && sizeof(self::$__zoneCarrierRangePrice[(int)$config->id_zone])
        ) {
            $id_zone = (int)$config->id_zone;
            $carriers
                = array_keys(
                    self::$__zoneCarrierRangePrice[(int)$config->id_zone]
                );
            $id_carrier = (int)$carriers[0];
        } #else
        else {
            $keys = array_keys(self::$__zoneCarrierRangePrice);

            foreach ($keys as $key) {
                if (!empty(self::$__zoneCarrierRangePrice[(int)$key])) {
                    $id_zone = $key;
                    $carriers
                        = array_keys(
                            self::$__zoneCarrierRangePrice[(int)$config->id_zone]
                        );
                    $id_carrier = (int)$carriers[0];
                    break;
                }
            }
        }

        $price = 0;
        if ($id_zone && $id_carrier) {
            foreach (self::$__zoneCarrierRangePrice[$id_zone][$id_carrier] as &$range) {
                if (($range['min'] <= $toSearch && $range['max'] > $toSearch)
                    || (self::$__carriers[(int)$id_carrier]['range_behavior']
                        == 0
                        && $range
                        == end(
                            self::$__zoneCarrierRangePrice[$id_zone][$id_carrier]
                        ))
                ) {
                    $price = $range['price'];
                    break;
                }
            }
        }

        if (Tools::getIsset(
            self::$__carriers[(int)$id_carrier]['shipping_handling']
        )
            && self::$__carriers[(int)$id_carrier]['shipping_handling']
        ) {
            $price += (float)Configuration::get('PS_SHIPPING_HANDLING');
        }

        $price += (float)$product['additional_shipping_cost'];

        if (self::$__carriers[$id_carrier]['id_tax_rules_group']) {
            $idTaxRuleGroup
                = (int)self::$__carriers[$id_carrier]['id_tax_rules_group'];

            if (Tools::getIsset(self::$__zoneTax[$idTaxRuleGroup])) {
                $price *= 1 + ((float)self::$__zoneTax[$idTaxRuleGroup] / 100);
            }
        }

        if ((Configuration::get('PS_SHIPPING_FREE_PRICE') > 0
                && $product_price
                >= Configuration::get('PS_SHIPPING_FREE_PRICE'))
            || (Configuration::get('PS_SHIPPING_FREE_WEIGHT') > 0
                && $product_weight
                >= Configuration::get('PS_SHIPPING_FREE_WEIGHT'))
        ) {
            $price = 0.0;
        }

        self::__addDOMElement($field, $price, $xml);
    }

    /**
     * Insert Product Stock state into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getInStock(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $quantity = 0;
        if ($id_declension
            && Tools::getIsset(
                $product['declension'][(int)$id_declension]['quantity']
            )
        ) {
            $quantity
                = (int)$product['declension'][(int)$id_declension]['quantity'];
        } else {
            $quantity = (int)$product['quantity'];
        }

        if ($quantity) {
            $stock = 'En stock';
        } else {
            $stock = 'Hors stock';
        }

        self::__addDOMElement($field, $stock, $xml);
    }

    /**
     * Insert Product Sock quantity into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getStockQuantity(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $quantity = 0;
        if ($id_declension
            && Tools::getIsset(
                $product['declension'][(int)$id_declension]['quantity']
            )
        ) {
            $quantity
                = (int)$product['declension'][(int)$id_declension]['quantity'];
        } else {
            $quantity = (int)$product['quantity'];
        }
        self::__addDOMElement(
            array('balise' => 'test'),
            'test'.$quantity,
            $xml
        );
        self::__addDOMElement($field, $quantity, $xml);
    }

    /**
     * Insert Product Delivery infos into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getDeliveryInfo(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (Tools::getIsset(self::$__carriers[(int)$config->id_carrier])) {
            $info
                = self::$__carriers[(int)$config->id_carrier]['delay'][(int)$config->id_default_lang];
        } else {
            $info = '';
        }
        self::__addDOMElement($field, $info, $xml);
    }

    /**
     * Insert Product no-reduction price into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getPrixBarre(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $no_reduc_price = (float)BeezupProduct::getPrice(
            $product,
            $config,
            $id_declension,
            false
        );
        $reduc_price = (float)BeezupProduct::getPrice(
            $product,
            $config,
            $id_declension
        );


        if ($reduc_price < $no_reduc_price) {
            self::__addDOMElement($field, $no_reduc_price, $xml);
        } else {
            self::__addDOMElement($field, self::$defautValue, $xml);
        }
    }

    /**
     * Insert Product Promotion start date into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getPromoStart(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $specificPrice = BeezupProduct::getSpecificPrice($product);

        if (Tools::getIsset($specificPrice['from'])
            && Tools::getIsset($specificPrice['to'])
            && $specificPrice['from'] != $specificPrice['to']
        ) {
            self::__addDOMElement($field, $specificPrice['from'], $xml);
        } else {
            self::__addDOMElement($field, self::$defautValue, $xml);
        }
    }

    /**
     * Insert Product Promotion end date into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getPromoEnd(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $specificPrice = BeezupProduct::getSpecificPrice($product);

        if (Tools::getIsset($specificPrice['from'])
            && Tools::getIsset($specificPrice['to'])
            && $specificPrice['from'] != $specificPrice['to']
        ) {
            self::__addDOMElement($field, $specificPrice['to'], $xml);
        } else {
            self::__addDOMElement($field, self::$defautValue, $xml);
        }
    }

    /**
     * Insert Product Promotion type into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getPromoType(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $type = 'normal';
        $specificPrice = BeezupProduct::getSpecificPrice($product);

        if ($product['on_sale']) {
            $type = 'solde';
        } elseif ($specificPrice
            && $specificPrice['reduction'] > 0
            && (($specificPrice['from'] == $specificPrice['to'])
                || (strtotime($specificPrice['from']) <= time()
                    && strtotime($specificPrice['to']) >= time()))
        ) {
            $type = 'promo';
        } elseif (time($product['date_add']) >= strtotime(
            '-'
                .(int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT').' days'
        )
        ) {
            $type = 'nouveau';
        }

        self::__addDOMElement($field, $type, $xml);
    }

    /**
     * Insert if Product is a pack into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getBundle(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (version_compare(_PS_VERSION_, '1.2.0', '>=')
            && $product['is_pack']
        ) {
            self::__addDOMElement($field, 1, $xml);
        } else {
            self::__addDOMElement($field, self::$defautValue, $xml);
        }
    }

    /**
     * Insert Product category tree into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getCategory(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        $categoriesList = array();
        $categoriesList[]
            = self::$__categories[(int)$product['id_category_default']];

        $id_parent
            = (int)self::$__categories[(int)$product['id_category_default']]['id_parent'];

        while ($id_parent != 0 && $id_parent != 1) {
            $categoriesList[] = self::$__categories[$id_parent];
            $id_parent = self::$__categories[$id_parent]['id_parent'];
        }
        $categoriesList = array_reverse($categoriesList);

        foreach ($categoriesList as $k => &$category) {
            $tmp_field = $field;
            $tmp_field['balise'] .= '_'.($k + 1);

            self::__addDOMElement(
                $tmp_field,
                $category['name'][(int)$config->id_default_lang],
                $xml
            );
        }
    }

    /**
     * Insert Product Attributes or feature into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getFeatureAttrGroup(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($field['id_feature']) {
            self::getFeature(
                $product,
                $field,
                $xml,
                $config,
                $id_declension,
                $product_type
            );
        } elseif ($field['id_attribute_group']) {
            self::getAttributeGroup(
                $product,
                $field,
                $xml,
                $config,
                $id_declension,
                $product_type
            );
        } elseif (!empty($field['default'])) {
            self::__addDOMELement($field, $field['default'], $xml);
        } else {
            self::__addDOMElement($field, self::$defautValue, $xml);
        }
    }

    /**
     * Feature xml insertion submethod
     *
     * @access private
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    private static function getFeature(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (Tools::getIsset($product['feat_'.(int)$field['id_feature']])) {
            self::__addDOMElement(
                $field,
                $product['feat_'.(int)$field['id_feature']],
                $xml
            );
        }
    }

    /**
     * Attributes xml insertion submethod
     *
     * @access private
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    private static function getAttributeGroup(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if (BeezupProduct::PRODUCT_TYPE_PARENT == $product_type
            && Tools::getIsset($product['declension'])
            && !empty($product['declension'])
        ) {
            $attributes = array();

            foreach ($product['declension'] as $declension) {
                if (Tools::getIsset(
                    $declension['attr_'
                        .(int)$field['id_attribute_group']]
                )
                    && $declension['quantity'] > 0
                ) {
                    foreach ($declension['attr_'.(int)$field['id_attribute_group']] as $attr) {
                        $attributes[] = $attr;
                    }
                }
            }

            self::__addDOMElement(
                $field,
                implode(', ', array_unique($attributes)),
                $xml
            );
        } elseif ($id_declension
            && Tools::getIsset(
                $product['declension'][(int)$id_declension]['attr_'
                .(int)$field['id_attribute_group']]
            )
        ) {
            $content = implode(
                ', ',
                $product['declension'][(int)$id_declension]['attr_'
                .(int)$field['id_attribute_group']]
            );
            self::__addDOMElement($field, $content, $xml);
        } elseif (Tools::getIsset(
            $product['attr_'
            .(int)$field['id_attribute_group']]
        )
        ) {
            $content = implode(
                ', ',
                $product['attr_'.(int)$field['id_attribute_group']]
            );
            self::__addDOMElement($field, $content, $xml);
        }
    }

    /**
     * Insert free field into xml
     *
     * @access public
     * @static
     *
     * @param array        $product
     * @param array        $field
     * @param DOMElement   $xml
     * @param BeezupConfig $config
     * @param int          $id_declension
     *
     * @return void
     */
    public static function getFreeField(
        &$product,
        &$field,
        &$xml,
        &$config,
        $id_declension = null,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE
    ) {
        if ($field['id_feature']) {
            self::getFeature(
                $product,
                $field,
                $xml,
                $config,
                $id_declension,
                $product_type
            );
        } elseif ($field['id_attribute_group']) {
            self::getAttributeGroup(
                $product,
                $field,
                $xml,
                $config,
                $id_declension,
                $product_type
            );
        } elseif (!empty($field['default'])) {
            self::__addDOMELement($field, $field['default'], $xml);
        } else {
            self::__addDOMELement($field, self::$defautValue, $xml);
        }
    }
}

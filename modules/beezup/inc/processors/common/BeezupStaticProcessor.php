<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupStaticProcessor
{
    public static $instance = false;

    private $product;
    private $idDeclension;

    private $key;


    private $values = array();


    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new BeezupStaticProcessor();
        }

        return self::$instance;
    }


    public function setProduct($product, $idDeclension)
    {
        $this->product = $product;
        $this->idDeclension = $idDeclension;
        $this->key = $product->id."_".$idDeclension;
        $this->values[$this->key] = array();
    }


    public function getPrice($context)
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset($this->values[$this->key]['price'])) {
            $specificPrice = null;
            $this->values[$this->key]['price'] = Product::getPriceStatic(
                $this->product->id,          // id_product
                true,                  // usetax
                (int)$this->idDeclension,    // id_product_attribute
                2,                     // decimals
                null,                  // divisor
                false,                 // only_reduc
                true,                  // usereduc
                1,                     // quantity
                false,                 // force_associated_tax
                null,                  // id_customer
                null,                  // id_cart
                null,                  // id_address
                $specificPrice,        // specific_price_output
                true,                  // with_ecotax
                true,                  // use_group_reduction
                $context, // context
                false                   // use_customer_price
            );
            $this->values[$this->key]['specificPrice'] = $specificPrice;
        }

        return $this->values[$this->key]['price'];
    }

    public function getSpecificPrice()
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (isset($this->values[$this->key]['specificPrice'])) {
            return $this->values[$this->key]['specificPrice'];
        }

        return false;
    }


    public function getQuantity()
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset($this->values[$this->key]['qty'])) {
            $this->values[$this->key]['qty']
                = Product::getQuantity(
                    (int)$this->product->id,
                    (int)$this->idDeclension
                );
        }

        return $this->values[$this->key]['qty'];
    }


    public function getAttributesName($idLang)
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset($this->values[$this->key]['attributesName'])) {
            $this->values[$this->key]['attributesName']
                = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                    '
    			SELECT al.*, ag.public_name as group_name
    			FROM '._DB_PREFIX_.'product_attribute_combination pac
                join '._DB_PREFIX_.'attribute as a on a.id_attribute = pac.id_attribute
    			JOIN '._DB_PREFIX_
                    .'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang='
                    .(int)$idLang.')
                join '._DB_PREFIX_
                    .'attribute_group_lang as ag on ag.id_attribute_group = a.id_attribute_group and ag.id_Lang = '
                    .(int)$idLang.'
                WHERE pac.id_product_attribute='.(int)$this->idDeclension
                );
        }

        return $this->values[$this->key]['attributesName'];
    }


    public function getNewCart($currencyId)
    {
        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (!isset($this->values[$this->key]['cart'])) {
            $this->values[$this->key]['cart'] = new Cart();
            $this->values[$this->key]['cart']->id = -1;
            $this->values[$this->key]['cart']->id_currency = $currencyId;
        }

        return $this->values[$this->key]['cart'];
    }
}

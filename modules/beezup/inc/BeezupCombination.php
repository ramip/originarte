<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupCombination
{
    public $id;
    public $id_product_attribute;
    public $reference;
    public $supplier_reference;
    public $location;
    public $ean13;
    public $upc;
    public $wholesale_price;
    public $price;
    public $ecotax;
    public $quantity;
    public $weight;
    public $unit_price_impact;
    public $default_on;
    public $minimal_quantity;
    public $available_date;
    public $id_shop;
    public $id_attribute_group;
    public $is_color_group;
    public $group_name;
    public $attribute_name;
    public $id_attribute;

    public static $_keys
        = array(
            'id_product_attribute',
            'id_product',
            'reference',
            'supplier_reference',
            'location',
            'ean13',
            'upc',
            'wholesale_price',
            'price',
            'ecotax',
            'quantity',
            'weight',
            'unit_price_impact',
            'default_on',
            'minimal_quantity',
            'available_date',
            'id_shop',
            'id_attribute_group',
            'is_color_group',
            'group_name',
            'attribute_name',
            'id_attribute',

        );

    public function populate($row)
    {
        foreach (self::$_keys as $key) {
            if (isset($row[$key])) {
                $this->$key = $row[$key];
            }
        }
    }

    public function toArray()
    {
        $result = array();
        foreach (self::$_keys as $key) {
            $result[$key] = $this->$key;
        }

        return $result;
    }
}

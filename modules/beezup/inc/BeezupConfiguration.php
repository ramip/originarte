<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupConfiguration extends ObjectModel
{

    /** @var string configuration name */
    public $name;

    /** @var integer shop * */
    public $id_shop;

    /** @var integer shop group * */
    public $id_shop_group;

    /** @var boolean disable product when it is disabled */
    public $disable_disabled_product;

    /** @var boolean disable product when it is out of stock */
    public $disable_oos_product;

    /** @var boolean disable product when it is not available (available_for_order) */
    public $disable_not_available = true;

    /** @var integer default carrier * */
    public $id_carrier;

    /** @var integer default zone * */
    public $id_zone;

    /** @var string image type name * */
    public $image_type;

    /** @var integer default lang * */
    public $id_default_lang;

    /** @var boolean use product tax * */
    public $force_product_tax;

    /** @var boolean create a product for each declension * */
    public $set_attributes_as_product;

    /** @var array fields list * */
    public $fields = array();

    /** @var array DB Table */
    protected $table = 'beezup_configuration';

    /** @var string DB Primary Key */
    protected $identifier = 'id_configuration';

    /** @var array Required fields */
    protected $fieldsRequired
        = array(
            'disable_disabled_product',
            'disable_oos_product',
            'name',
            'id_shop',
            'id_shop_group',
            'id_carrier',
            'id_zone',
            'image_type',
            'id_default_lang',
            'force_product_tax',
            'set_attributes_as_product',
            'disable_not_available',
        );

    /** @var array Fields validation functions */
    protected $fieldsValidate
        = array(
            'id_shop_group'             => 'isUnsignedId',
            'id_shop'                   => 'isUnsignedId',
            'name'                      => 'isTableOrIdentifier',
            'disable_disabled_product'  => 'isBool',
            'disable_not_available'     => 'isBool',
            'disable_oos_product'       => 'isBool',
            'id_carrier'                => 'isUnsignedId',
            'id_zone'                   => 'isUnsignedId',
            'image_type'                => 'isTableOrIdentifier',
            'id_default_lang'           => 'isUnsignedId',
            'force_product_tax'         => 'isBool',
            'set_attributes_as_product' => 'isBool',
        );

    /** @var array Fields max length */
    protected $fieldsSize
        = array(
            'name'       => 75,
            'image_type' => 75,
        );

    /**
     * Return class fields as array
     *
     * @return array
     */
    public function getFields()
    {
        $fields = array();

        if ($this->id) {
            $fields['id_configuration'] = (int)$this->id;
        }
        $fields['id_shop'] = (int)$this->id_shop;
        $fields['id_shop_group'] = (int)$this->id_shop_group;
        $fields['name'] = pSQL($this->name);

        $fields['disable_disabled_product']
            = (int)$this->disable_disabled_product;
        $fields['disable_not_available'] = (int)$this->disable_not_available;
        $fields['disable_oos_product'] = (int)$this->disable_oos_product;
        $fields['id_carrier'] = (int)$this->id_carrier;
        $fields['id_zone'] = (int)$this->id_zone;
        $fields['image_type'] = pSQL($this->image_type);
        $fields['id_default_lang'] = (int)$this->id_default_lang;
        $fields['force_product_tax'] = (int)$this->force_product_tax;
        $fields['set_attributes_as_product']
            = (int)$this->set_attributes_as_product;

        return $fields;
    }

    /**
     * Object Constructor
     *
     * @param integer $id
     * @param integer $id_lang
     *
     * @return void
     */
    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id);
        if ($this->id) {
            $this->fields = BeezupField::getFieldsByIdConfiguration($this->id);
        }
    }

    public function copyFields($source_id)
    {
        $result = array();
        if ($this->id) {
            foreach (BeezupField::getFieldsByIdConfiguration($source_id) as $source_field) {
                $field = new BeezupField($source_field['id_field']);
                $old_id = $field->id;
                $field->id = null;
                $field->id_configuration = $this->id;
                $field->save();
                $result[(int)$field->id] = (int)$old_id;
            }
            $this->fields = BeezupField::getFieldsByIdConfiguration($this->id);
        }

        return $result;
    }

    /**
     * Delete properly configuration
     *
     * @return boolean
     */
    public function delete()
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_
            .'beezup_field` WHERE `id_configuration`='.(int)$this->id;

        return (Db::getInstance()->Execute($sql) && parent::delete());
    }

    public static function get($name, $id_shop = null, $id_shop_group = null)
    {
        if ($id_shop === null || !Shop::isFeatureActive()) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null || !Shop::isFeatureActive()) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        $configurations = self::getConfigurationsList($name);
        $combinations = array(
            array($id_shop, $id_shop_group),
            array($id_shop, null),
            array(null, $id_shop_group),
            array(null, null),
        );
        foreach ($combinations as $combination) {
            $key = self::getConfigurationKey(
                $name,
                $combination[0],
                $combination[1]
            );
            if (array_key_exists($key, $configurations)) {
                return new BeezupConfiguration(
                    $configurations[$key]['id_configuration']
                );
            }
        }

        return null;
    }

    public static function getConfigurationKey($name, $id_shop, $id_shop_group)
    {
        return sprintf('%s*%d*%d', $name, (int)$id_shop, (int)$id_shop_group);
    }

    public static function getConfigurationsList($name = null)
    {
        $result = array();
        foreach (Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'beezup_configuration`'.($name ? ' WHERE name="'.pSQL($name).'"' : '')) as $configuration) {
            $key = self::getConfigurationKey(
                $configuration['name'],
                $configuration['id_shop'],
                $configuration['id_shop_group']
            );
            $result[$key] = $configuration;
        }

        return $result;
    }
}

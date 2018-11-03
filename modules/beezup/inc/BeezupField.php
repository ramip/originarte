<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupField extends ObjectModel
{

    /** @var boolean Is field active */
    public $active;

    /** @var integer Id BeezupConfiguration */
    public $id_configuration;

    /** @var boolean Forced field */
    public $forced;

    /** @var string XML tag name */
    public $balise;

    /** @var boolean Is field customizable */
    public $editable;

    /** @var boolean Is field free */
    public $free_field;

    /** @var string Default value */
    public $default;

    /** @var integer Id feature */
    public $id_feature;

    /** @var integer Id attribute group */
    public $id_attribute_group;

    /** @var string Default values list */
    public $values_list;

    /** @var string Function used to set XML */
    public $function;

    /** @var string Group fields */
    public $fields_group;

    /** @var string DB Table */
    protected $table = 'beezup_field';

    /** @var string DB Primary Key */
    protected $identifier = 'id_field';

    /** @var array Fields required */
    protected $fieldsRequired
        = array(
            'active',
            'id_configuration',
            'forced',
            'balise',
            'editable',
            'default',
            'id_feature',
            'id_attribute_group',
            'values_list',
            'function',
            'fields_group',
        );

    /** @var array Fields validation method */
    protected $fieldsValidate
        = array(
            'active'             => 'isBool',
            'id_configuration'   => 'isUnsignedId',
            'forced'             => 'isBool',
            'balise'             => 'isTableOrIdentifier',
            'editable'           => 'isBool',
            'default'            => 'isGenericName',
            'id_feature'         => 'isUnsignedId',
            'id_attribute_group' => 'isUnsignedId',
            'function'           => 'isTableOrIdentifier',
            'fields_group'       => 'isGenericName',
        );

    /** @var array Max field length */
    protected $fieldsSize
        = array(
            'balise'       => 75,
            'default'      => 255,
            'function'     => 75,
            'fields_group' => 75,
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
            $fields['id_field'] = (int)$this->id;
        }

        $fields['active'] = (int)$this->active;
        $fields['id_configuration'] = (int)$this->id_configuration;
        $fields['forced'] = (int)$this->forced;
        $fields['free_field'] = (int)$this->free_field;
        $fields['balise'] = pSQL($this->balise);
        $fields['editable'] = (int)$this->editable;
        $fields['default'] = pSQL($this->default);
        $fields['id_feature'] = (int)$this->id_feature;
        $fields['id_attribute_group'] = (int)$this->id_attribute_group;
        $fields['values_list'] = pSQL($this->values_list);
        $fields['function'] = pSQL($this->function);
        $fields['fields_group'] = pSQL($this->fields_group);

        return $fields;
    }

    /**
     * Return Configuration fields
     *
     * @param integer $id_configuration
     *
     * @return mixed
     */
    public static function getFieldsByIdConfiguration($id_configuration)
    {
        return Db::getInstance()->ExecuteS(
            '
			SELECT *
			FROM `'._DB_PREFIX_.'beezup_field`
			WHERE `id_configuration` = '.(int)$id_configuration.'
			ORDER BY `fields_group` ASC, `id_field` ASC'
        );
    }
}

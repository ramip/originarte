<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOrderStatus extends ObjectModel
{
    public $id_beezup_order_status;
    public $id_order;
    public $id_order_status;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition
        = array(
            'table'   => 'beezup_order_status',
            'primary' => 'id_beezup_order_status',
            'fields'  => array(
                'id_order'        => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),
                'id_order_status' => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),

            ),
        );

    public static function statusExists($idOrder)
    {
        $check = Db::getInstance()->getRow(
            "select id_beezup_order_status from "
            ._DB_PREFIX_."beezup_order_status WHERE id_order = '".(int)$idOrder
            ."'"
        );
        if (!empty($check)) {
            return $check['id_beezup_order_status'];
        }

        return false;
    }
}

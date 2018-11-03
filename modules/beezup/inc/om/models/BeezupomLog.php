<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupomLog extends ObjectModel
{
    public $id;
    public $beezup_order_id;
    public $message_type;
    public $message;
    public $date;


    public static $definition
        = array(
            'table'   => 'beezupom_log',
            'primary' => 'id_beezupom_log',
            'fields'  => array(
                'beezup_order_id' => array('type' => self::TYPE_STRING),
                'message_type'    => array('type' => self::TYPE_STRING),
                'message'         => array('type' => self::TYPE_STRING),
                'date_upd'        => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isDateFormat',
                ),

            ),
        );


    public static function addLog($type, $message, $beezupOrderId = '')
    {
        return Db::getInstance()->insert(
            'beezupom_log',
            array(
                'beezup_order_id' => pSQL($beezupOrderId),
                'message_type'    => pSQL($type),
                'message'         => pSQL($message),
                'date'            => date('Y-m-d H:i:s'),
            )
        );
    }
}

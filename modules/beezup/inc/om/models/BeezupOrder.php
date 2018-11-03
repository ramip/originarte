<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOrder extends ObjectModel
{
    public $id_beezup_order;
    public $account_id;
    public $marketplace_technical_code;
    public $beezup_order_uuid;
    public $id_order;
    public $etag;
    public $order_json;
    public $infos_json;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition
        = array(
            'table'   => 'beezup_order',
            'primary' => 'id_beezup_order',
            'fields'  => array(
                'account_id'                 => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'marketplace_technical_code' => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'beezup_order_uuid'          => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'id_order'                   => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),
                'etag'                       => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                    'size'     => 128,
                ),
                'order_json'                 => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                ),
                'infos_json'                 => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                ),
                'date_add'                   => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isDateFormat',
                ),
                'date_upd'                   => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isDateFormat',
                ),

            ),
        );

    public static function getByBeezupOrderId(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE account_id="'.pSql($oOrderIdentifier->getAccountId())
            .'" AND marketplace_technical_code="'
            .pSql($oOrderIdentifier->getMarketplaceTechnicalCode())
            .'" AND	beezup_order_uuid="'
            .pSql($oOrderIdentifier->getBeezupOrderUUID()).'"';

        // $sQuery = 'SELECT * FROM ' . _DB_PREFIX_ . self::$definition['table'] . ' WHERE marketplace_technical_code="' . pSql($oOrderIdentifier->getMarketplaceTechnicalCode()). '" AND	beezup_order_uuid="' . pSql($oOrderIdentifier->getBeezupOrderUUID()). '"';
        return DB::getInstance()->executeS($sQuery);
    }

    public static function getByPrestashopOrderId($nOrderId)
    {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE id_order='.(int)$nOrderId.' ';

        return DB::getInstance()->executeS($sQuery);
    }

    public function getBeezupOrderId()
    {
        $oIdentifier = new BeezupOMOrderIdentifier();
        $oIdentifier
            ->setAccountId($this->account_id)
            ->setMarketplaceTechnicalCode($this->marketplace_technical_code)
            ->setBeezupOrderUUID($this->beezup_order_uuid);

        return $oIdentifier;
    }

    public function getPrestashopOrder()
    {
        return new Order($this->id_order);
    }

    // @todo see if we can secure use BeezupOMOrderIdentifier for type hinting
    public static function create(
        $nOrderId,
        BeezupOMOrderResponse $oBeezupOrderResponse
    ) {
        $oBeezupOrder = $oBeezupOrderResponse->getResult();
        $oIdentifier = BeezupOMOrderIdentifier::fromBeezupOrder($oBeezupOrder);
        if (!($oIdentifier instanceof BeezupOMOrderIdentifier)) {
            return null;
        }
        $oResult = new BeezupOrder();
        $oResult->account_id = $oIdentifier->getAccountId();
        $oResult->marketplace_technical_code
            = $oIdentifier->getMarketplaceTechnicalCode();
        $oResult->beezup_order_uuid = $oIdentifier->getBeezupOrderUUID();
        $oResult->id_order = (int)$nOrderId;
        $oResult->order_json = json_encode($oBeezupOrder->toArray());
        $oResult->infos_json = json_encode(
            $oBeezupOrderResponse->getInfo()
                ->toArray()
        );
        $oResult->etag = (string)$oBeezupOrderResponse->getEtag();
        $oResult->date_add = date('Y-m-d H:i:s');
        $oResult->date_upd = date('Y-m-d H:i:s');

        if ($oResult->save()) {
            return $oResult;
        }

        return null;
    }

    public static function fromBeezupOrderId(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $aOrders = self::getByBeezupOrderId($oOrderIdentifier);
        if ($aOrders && is_array($aOrders) && isset($aOrders[0])
            && is_array($aOrders[0])
            && isset($aOrders[0]['id_beezup_order'])
            && $aOrders[0]['id_beezup_order'] > 0
        ) {
            return new BeezupOrder((int)$aOrders[0]['id_beezup_order']);
        }

        return null;
    }

    public static function fromPrestashopOrderId($nOrderId)
    {
        $aOrders = self::getByPrestashopOrderId($nOrderId);
        if ($aOrders && is_array($aOrders) && isset($aOrders[0])
            && is_array($aOrders[0])
            && isset($aOrders[0]['id_beezup_order'])
            && $aOrders[0]['id_beezup_order'] > 0
        ) {
            return new BeezupOrder((int)$aOrders[0]['id_beezup_order']);
        }

        return null;
    }


    /**
     *
     * @return Ambigous BeezupOMOrderResult|null
     */
    /*	public function getBeezupOrderResult(){
            if ($this->order_json){
                $aData = json_decode($this->order_json, true);
                if (is_array($aData) && !empty($aData)){
                    return BeezupOMOrderResult::fromArray($aData);
                }
            }
            return null;
        }*/
    public function getBeezupOrderResult()
    {
        if ($this->order_json) {
            $aData = json_decode(trim($this->order_json), true);
            if (is_array($aData) && !empty($aData)) {
                return BeezupOMOrderResult::fromArray($aData);
            }
            if ($aData === null && json_last_error() === JSON_ERROR_SYNTAX) {
                $sPattern = '/\{"key":".*","value":""(.*)""\}/is';
                preg_match_all($sPattern, $this->order_json, $aMatches);
                if (isset($aMatches[1]) && is_array($aMatches[1])) {
                    $aReplaces = array();
                    foreach ($aMatches[1] as $sStringToEscape) {
                        $aReplaces['""'.$sStringToEscape.'""'] = '"\\"'
                            .$sStringToEscape.'\\""';
                    }
                    $this->order_json = str_replace(
                        array_keys($aReplaces),
                        array_values($aReplaces),
                        $this->order_json
                    );
                    $aData = json_decode(trim($this->order_json), true);
                    if (is_array($aData) && !empty($aData)) {
                        // $this->update();
                        return BeezupOMOrderResult::fromArray($aData);
                    }
                }
            }
        }

        return null;
    }


    public function getBeezupOrderInfos()
    {
        if ($this->infos_json) {
            $aData = json_decode($this->infos_json, true);
            if (is_array($aData) && !empty($aData)) {
                return BeezupOMInfoSummaries::fromArray($aData);
            }
        }

        return new BeezupOMInfoSummaries();
    }
}

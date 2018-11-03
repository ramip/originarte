<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupHarvestOrder extends ObjectModel
{
    public $id_harvest_order;
    public $execution_id;
    public $creation_utc_date;
    public $last_update_utc_date;
    public $error_message;
    public $processing_status;
    public $beezup_api_token;
    public $beezup_user_id;
    public $account_id;
    public $beezup_order_uuid;
    public $etag;
    public $marketplace_technical_code;
    public $http_status;
    public $order_detail_json;
    public $last_modification_utc_date;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition
        = array(
            'table'   => 'beezup_harvest_order',
            'primary' => 'id_harvest_order',
            'fields'  => array(
                'execution_id'               => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'creation_utc_date'          => array('type' => self::TYPE_DATE),
                'last_update_utc_date'       => array('type' => self::TYPE_DATE),
                'error_message'              => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                    'size'     => 4096,
                ),
                'processing_status'          => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'beezup_api_token'           => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'beezup_user_id'             => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'account_id'                 => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'beezup_order_uuid'          => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'etag'                       => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'marketplace_technical_code' => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'http_status'                => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'order_detail_json'          => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                    'size'     => 32000,
                ),
                'last_modification_utc_date' => array('type' => self::TYPE_DATE),
            ),
        );

    public static function getByExecutionId($sExecutionId)
    {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE execution_id="'.pSql($sExecutionId).'"';

        return DB::getInstance()->getRow($sQuery);
    }

    public static function getByBeezupOrderUUID($sBeezupOrderUUID)
    {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE beezup_order_uuid="'.pSql($sBeezupOrderUUID).'"';

        return DB::getInstance()->getExecuteS($sQuery);
    }

    public static function copyBeezupToPresta(
        BeezupOMHarvestOrderReporting $oSource,
        BeezupHarvestOrder $oTarget
    ) {
        $oTarget->execution_id = $oSource->getExecutionId();
        $oTarget->creation_utc_date = $oSource->getCreationUtcDate()
            ? $oSource->getCreationUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->last_update_utc_date = $oSource->getLastUpdateUtcDate()
            ? $oSource->getLastUpdateUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->error_message = $oSource->getErrorMessage();
        $oTarget->processing_status = $oSource->getProcessingStatus();
        $oTarget->beezup_api_token = $oSource->getBeezUPApiToken();
        $oTarget->beezup_user_id = $oSource->getBeezUPUserId();
        $oTarget->account_id = $oSource->getAccountId();
        $oTarget->beezup_order_uuid = $oSource->getBeezupOrderUUID();
        $oTarget->etag = $oSource->getEtag();
        $oTarget->marketplace_technical_code
            = $oSource->getMarketPlaceTechnicalCode();
        $oTarget->http_status = $oSource->getHttpStatus();
        $oTarget->order_detail_json = $oSource->getOrderDetailJson();
        $oTarget->last_modification_utc_date
            = $oSource->getLastModificationUtcDate()
            ? $oSource->getLastModificationUtcDate()->format('Y-m-d H:i:s')
            : null;

        return $oTarget;
    }

    public static function copyPrestaToBeezup(
        BeezupHarvestOrder $oSource,
        BeezupOMHarvestOrderReporting $oTarget
    ) {
        $oTarget->setExecutionId($oSource->execution_id);
        $oTarget->setCreationUtcDate(
            new DateTime(
                $oSource->creation_utc_date,
                new DateTimeZone('UTC')
            )
        );
        $oTarget->setLastUpdateUtcDate(
            new DateTime(
                $oSource->last_update_utc_date,
                new DateTimeZone('UTC')
            )
        );
        $oTarget->setErrorMessage($oSource->error_message);
        $oTarget->setProcessingStatus($oSource->processing_status);
        $oTarget->setBeezUPApiToken($oSource->beezup_api_token);
        $oTarget->setBeezUPUserId($oSource->beezup_user_id);
        $oTarget->setAccountId($oSource->account_id);
        $oTarget->setBeezupOrderUUID($oSource->beezup_order_uuid);
        $oTarget->setEtag($oSource->etag);
        $oTarget->setMarketPlaceTechnicalCode(
            $oSource->marketplace_technical_code
        );
        $oTarget->setHttpStatus($oSource->http_status);
        $oTarget->setOrderDetailJson($oSource->order_detail_json);
        $oTarget->setLastModificationUtcDate(
            new DateTime(
                $oSource->last_modification_utc_date,
                new DateTimeZone('UTC')
            )
        );

        return $oTarget;
    }

    public static function createNewFromOMObject(
        BeezupOMHarvestOrderReporting $oSource
    ) {
        $oResult = self::copyBeezupToPresta($oSource, new self());

        return $oResult->add() ? $oResult : null;
    }

    public static function createFromOMObject(
        BeezupOMHarvestOrderReporting $oSource
    ) {
        $aData = self::getByExecutionId($oSource->getExecutionId());
        $oResult = new self(
            $aData
            && isset($aData['id_harvest_order'])
                ? (int)$aData['id_harvest_order'] : null
        );
        $oResult = self::copyBeezupToPresta($oSource, $oResult);

        return $oResult->save() ? $oResult : null;
    }
}

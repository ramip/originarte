<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupHarvestClient extends ObjectModel
{
    public $id_harvest_client;
    public $execution_id;
    public $creation_utc_date;
    public $last_update_utc_date;
    public $error_message;
    public $total_order_count;
    public $processing_status;
    public $begin_period_utc_date;
    public $end_period_utc_date;
    public $entries_per_page;
    public $beezup_api_token;
    public $beezup_user_id;
    public $remaining_page_count;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition
        = array(
            'table'   => 'beezup_harvest_reporting',
            'primary' => 'id_harvest_client',
            'fields'  => array(
                'execution_id'          => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'creation_utc_date'     => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isGenericName',
                ),
                'last_update_utc_date'  => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isGenericName',
                ),
                'error_message'         => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isAnything',
                    'size'     => 4096,
                ),
                'total_order_count'     => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),
                'processing_status'     => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'entries_per_page'      => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),
                'begin_period_utc_date' => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isGenericName',
                ),
                'end_period_utc_date'   => array(
                    'type'     => self::TYPE_DATE,
                    'validate' => 'isGenericName',
                ),
                'beezup_api_token'      => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'beezup_user_id'        => array(
                    'type'     => self::TYPE_STRING,
                    'validate' => 'isGenericName',
                    'size'     => 64,
                ),
                'remaining_page_count'  => array(
                    'type'     => self::TYPE_INT,
                    'validate' => 'isUnsignedInt',
                ),

            ),
        );

    public static function getByExecutionId($sExecutionId)
    {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE execution_id="'.pSql($sExecutionId).'"';

        return DB::getInstance()->getRow($sQuery);
    }

    public static function getByProcessingStatus($sProcessingStatus)
    {
        $sQuery = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table']
            .' WHERE processing_status="'.pSql($sProcessingStatus).'"';

        return DB::getInstance()->executeS($sQuery);
    }

    public static function copyBeezupToPresta(
        BeezupOMHarvestClientReporting $oSource,
        BeezupHarvestClient $oTarget
    ) {
        $oTarget->execution_id = $oSource->getExecutionId();
        $oTarget->creation_utc_date = $oSource->getCreationUtcDate()
            ? $oSource->getCreationUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->last_update_utc_date = $oSource->getLastUpdateUtcDate()
            ? $oSource->getLastUpdateUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->error_message = $oSource->getErrorMessage();
        $oTarget->total_order_count = (int)$oSource->getTotalOrderCount();
        $oTarget->processing_status = $oSource->getProcessingStatus();
        $oTarget->begin_period_utc_date = $oSource->getBeginPeriodUtcDate()
            ? $oSource->getBeginPeriodUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->end_period_utc_date = $oSource->getEndPeriodUtcDate()
            ? $oSource->getEndPeriodUtcDate()->format('Y-m-d H:i:s') : null;
        $oTarget->entries_per_page = (int)$oSource->getEntriesPerPage();
        $oTarget->beezup_api_token = $oSource->getBeezUPApiToken();
        $oTarget->beezup_user_id = $oSource->getBeezUPUserId();
        $oTarget->remaining_page_count = (int)$oSource->getRemainingPageCount();

        return $oTarget;
    }

    public static function copyPrestaToBeezup(
        BeezupHarvestClient $oSource,
        BeezupOMHarvestClientReporting $oTarget
    ) {
        // @todo on peut utiliser $oTarget::fromArray
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
        $oTarget->setTotalOrderCount($oSource->total_order_count);
        $oTarget->setProcessingStatus($oSource->processing_status);
        $oTarget->setBeginPeriodUtcDate(
            new DateTime(
                $oSource->begin_period_utc_date,
                new DateTimeZone('UTC')
            )
        );
        $oTarget->setEndPeriodUtcDate(
            new DateTime(
                $oSource->end_period_utc_date,
                new DateTimeZone('UTC')
            )
        );
        $oTarget->setEntriesPerPage($oSource->entries_per_page);
        $oTarget->setBeezUPApiToken($oSource->beezup_api_token);
        $oTarget->setBeezUPUserId($oSource->beezup_user_id);
        $oTarget->getRemainingPageCount($oSource->remaining_page_count);

        return $oTarget;
    }

    public static function createNewFromOMObject(
        BeezupOMHarvestClientReporting $oSource
    ) {
        $oResult = self::copyBeezupToPresta($oSource, new self());

        return $oResult->add() ? $oResult : null;
    }

    public static function createFromOMObject(
        BeezupOMHarvestClientReporting $oSource
    ) {
        $aData = self::getByExecutionId($oSource->getExecutionId());
        $oResult = new self(
            $aData
            && isset($aData['id_harvest_client'])
                ? (int)$aData['id_harvest_client'] : null
        );
        $oResult = self::copyBeezupToPresta($oSource, $oResult);

        return $oResult->save() ? $oResult : null;
    }

    /**
     * Returns current (not finished) BeezupOMHarvestClientReporting
     *
     * @return BeezupOMHarvestClientReporting|null
     */
    public static function getCurrent($nTime = null)
    {
        $sQuery = 'SELECT id_harvest_client FROM '._DB_PREFIX_
            .self::$definition['table'].' WHERE processing_status="'
            .pSql(BeezupOMProcessingStatus::IN_PROGRESS).'" ';
        if ($nTime !== null && (int)$nTime > 0) {
            $oUtcTZ = new DateTime('now', new DateTimeZone('UTC'));
            $sServerTZ = new DateTime(
                'now',
                new DateTimeZone(Configuration::get('PS_TIMEZONE'))
            );
            $sQuery .= sprintf(
                ' AND DATE_ADD(last_update_utc_date,INTERVAL '
                .(int)$nTime.' second)> CONVERT_TZ(NOW(), "%s", "%s") ',
                $sServerTZ->format('P'),
                $oUtcTZ->format('P')
            );
        }

        $sQuery .= 'ORDER BY begin_period_utc_date DESC';
        $nHarvestClientId = DB::getInstance()->getValue($sQuery);
        if ($nHarvestClientId && (int)$nHarvestClientId > 0) {
            return new BeezupHarvestClient($nHarvestClientId);
        }

        return null;
    }

    /**
     * Returns current (not finished) BeezupOMHarvestClientReporting
     *
     * @return BeezupOMHarvestClientReporting|null
     */
    public static function setTimeout($nTime = null)
    {
        $sQuery = 'UPDATE '._DB_PREFIX_.self::$definition['table']
            .' SET processing_status="'.pSql(BeezupOMProcessingStatus::TIMEOUT)
            .'" WHERE processing_status="'
            .pSql(BeezupOMProcessingStatus::IN_PROGRESS).'" ';
        if ($nTime !== null && (int)$nTime > 0) {
            $sQuery .= ' AND DATE_ADD(last_update_utc_date,INTERVAL '
                .(int)$nTime.' second)< NOW() ';
        }

        return DB::getInstance()->execute($sQuery);
    }
}

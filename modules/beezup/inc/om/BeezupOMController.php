<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR
    .'BeezupHarvestClient.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR
    .'BeezupHarvestOrder.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR
    .'BeezupOrder.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR
    .'bootstrap.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BeezupOMRepository.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR
    .'BeezupOMProductIdentityMapperFactory.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR
    .'BeezupOMStoreMapperFactory.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BeezupOMValueExtractor.php';


class BeezupOMController
{
    const VERSION = '3.3.3';

    private $bConnectionTestCache = null;

    protected $aMailMethodCache = null;

    /**
     * @var BeezupOMOrderService
     */
    protected $oOrderService = null;

    protected $oCredential = null;

    /**
     *
     * @var array
     */
    protected $aBeezupOrderStatesListCache = null;

    protected $aBeezupStoresCache = null;

    protected $bDebugMode = false;

    protected $nTolerance = 0;

    /**
     * Mapping for extracting values
     *
     * @var array
     */
    protected $aFieldsValuesMapping
        = array(
            'Order_Shipping_CarrierName'           => array(
                'id_carrier',
                'name',
            ),
            'Order_Shipping_ShipperTrackingNumber' => array('shipping_number'),
        );

    /**
     * @var beezup
     */
    protected $oModule = null;

    # MAGIC METHODS

    /**
     */
    public function __construct(beezup $oModule)
    {
        // setDebugMode will trigger initialization of orderService
        $this->bDebugMode = (bool)(Configuration::get('BEEZUP_DEBUG_MODE')
            && Configuration::get('PS_MAINTENANCE_IP')
            && in_array(
                Tools::getRemoteAddr(),
                explode(',', Configuration::get('PS_MAINTENANCE_IP'))
            ));
        $this->nTolerance = (int)Configuration::get('BEEZUP_OM_TOLERANCE');

        $this->setModule($oModule);
        if ($this->bDebugMode) {
            $this->muteMail();
        }
    }

    # PUBLIC API

    /**
     * Executes CLI callback
     * Calling php <script_name.php> --callback=myFunction --arg1 =xxx --arg2=yyy
     * executes $this->myFunction($tArg1, $tArg2, $tArg3 = null) if in functions phpdoc is tag @callback
     *
     * @param string $sCallback
     *
     * @return NULL
     */
    public function executeShellCallback($sCallback = null)
    {
        $fStartTime = microtime(true);
        if ($sCallback === null && PHP_SAPI === 'cli') {
            $aOptions = getopt('', array('callback:'));
            if (array_key_exists('callback', $aOptions)
                && trim($aOptions['callback'])
            ) {
                $sCallback = trim($aOptions['callback']);
            } // if
        } // if
        if ($sCallback) {
            $oBeezup = Module::getInstanceByName('beezup');
            $bExecute = $oBeezup && $oBeezup->active;
            echo PHP_EOL.sprintf('Calling %s', $sCallback); // @todo translate
            $oSelfRef = new ReflectionClass($this);
            $aArgs = array();
            try {
                /**
                 * @var ReflectionMethod
                 */
                $oMethod = $oSelfRef->getMethod($sCallback);
                $sDocComment = $oMethod->getDocComment();
                if (empty($sDocComment)
                    || !stristr($sDocComment, '@callback')
                ) {
                    echo PHP_EOL.sprintf(
                        '%s is not a valid callback',
                        $sCallback
                    );  // @todo translate

                    return null;
                } // if
                foreach ($oMethod->getParameters() as $oParameter) {
                    $sName = $oParameter->getName();
                    $sShellName = Tools::strtolower(Tools::substr($sName, 1));
                    $aOption = getopt(
                        '',
                        array(
                            $sShellName.($oParameter->isOptional() ? '::'
                                : ':'),
                        )
                    );
                    if ($aOption && array_key_exists($sShellName, $aOption)) {
                        $aArgs[] = $oParameter->isArray()
                        && !is_array($aOption[$sShellName])
                            ? array($aOption[$sShellName])
                            : $aOption[$sShellName];
                    } else if (!$aOption && $oParameter->isOptional()) {
                            break;
                    } else {
                        echo PHP_EOL.sprintf(
                            'Argument %s has to be defined',
                            $sShellName
                        );  // @todo translate
                        $mResult = null;
                        $bExecute = false;
                    } // if
                } // foreach
                $cCallback = array($this, $sCallback);
                if ($bExecute && is_callable($cCallback)) {
                    $mResult = call_user_func_array($cCallback, $aArgs);
                    echo PHP_EOL.sprintf(
                        'Execution time for %s : %1.3f sec.',
                        $sCallback,
                        microtime(true) - $fStartTime
                    );  // @todo translate
                } else {
                    echo PHP_EOL.sprintf(
                        'Cannot execute %s',
                        $sCallback
                    );  // @todo translate
                    $mResult = null;
                } // if
            } catch (Exception $oException) {
                echo $oException->getMessage();
                $mResult = null;
            } // try
        } else {
            $mResult = null;
        } // if

        return $mResult;
    }

    /**
     * Changes all current syncs to timeout
     *
     * @callback
     */
    public function purgeSync()
    {
        $this->getOrderService()->purgeSync();
    }

    /**
     * Synchronize orders
     *
     * @callback
     */
    public function synchronizeOrders(
        $sStartTime = null,
        $sEndTime = null,
        array $aBeezupOrderStates = array(),
        array $aMarketPlaces = array()
    ) {
        if (PHP_SAPI === 'cli') {
            echo sprintf(
                'BeezupOM %s : synchronizeOrders, debug mode : %s',
                self::VERSION,
                $this->isDebugModeActivated() ? 'YES'
                    : 'NO'
            );  // @todo translate
        }
        $this->muteMail();

        $oRequest = ($sStartTime || $sEndTime || $aBeezupOrderStates
            || $aMarketPlaces)
            ?
            $this->createOrderListRequest(
                $sStartTime,
                $sEndTime,
                $aBeezupOrderStates,
                $aMarketPlaces
            )
            :
            null;

        try {
            $this->getOrderService()->synchronizeOrders($oRequest);
        } catch (Exception $oException) {
            echo PHP_EOL.$oException->getMessage();
        }

        $this->unmuteMail();
    }

    /**
     * Synchronizes order
     *
     * @callback
     */
    public function synchronizeOrder(
        $sMarketPlace,
        $sAccount,
        $sUUID,
        $oCachedOrder = null
    ) {
        if (PHP_SAPI === 'cli') {
            echo sprintf(
                'BeezupOM %s : synchronizeOrder, debug mode : %s',
                self::VERSION,
                $this->isDebugModeActivated() ? 'YES'
                    : 'NO'
            );  // @todo translate
        }
        $this->muteMail();

        $aData = array(
            'marketplacetechnicalcode' => $sMarketPlace,
            'accountid'                => $sAccount,
            'beezuporderuuid'          => $sUUID,
        );
        $oBeezupOrderId = BeezupOMOrderIdentifier::fromArray($aData);
        if ($oCachedOrder === null) {
            $oCachedOrder = BeezupOrder::fromBeezupOrderId($oBeezupOrderId);
            if ($oCachedOrder && $oCachedOrder->id_order
                && (int)$oCachedOrder->getBeezupOrderResult()
                    ->getOrderMerchantOrderId() === 0
            ) {
                $oBeezupOrder = $oCachedOrder->getBeezupOrderResult();
                $oBeezupOrder->setOrderMerchantOrderId($oCachedOrder->id_order);
                $oCachedOrder->order_json
                    = json_encode($oBeezupOrder->toArray());
                if (!$oCachedOrder->update()) {
                    return false;
                }
            } // if
        }
        $aResult = $this->getOrderService()->synchronizeOrder($oBeezupOrderId);
        $this->unmuteMail();

        return $aResult;
    }

    /**
     * Re-imports order
     *
     * @param unknown_type $aData
     * @param unknown_type $oPsOrder
     *
     * @return Ambigous <multitype:multitype: , string>
     */
    public function resyncOrder(Order $oPsOrder)
    {
        $aResult = array(
            'errors'    => array(),
            'warnings'  => array(),
            'infos'     => array(),
            'successes' => array(),
        );
        $oCachedOrder = BeezupOrder::fromPrestashopOrderId($oPsOrder->id);
        if ($oCachedOrder) {
            if ((int)$oCachedOrder->getBeezupOrderResult()
                    ->getOrderMerchantOrderId() === 0
            ) {
                $oBeezupOrder = $oCachedOrder->getBeezupOrderResult();
                $oBeezupOrder->setOrderMerchantOrderId($oPsOrder->id);
                $oCachedOrder->order_json
                    = json_encode($oBeezupOrder->toArray());
                if (!$oCachedOrder->update()) {
                    $aResult['errors'][]
                        = $this->l('Unable to update local cache');
                }
            }

            $oBeezupOrderId = $oCachedOrder->getBeezupOrderId();
            list(
                $bResult, $sOperation, $sError
                )
                = $this->synchronizeOrder(
                    $oBeezupOrderId->getMarketplaceTechnicalCode(),
                    $oBeezupOrderId->getAccountId(),
                    $oBeezupOrderId->getBeezupOrderUUID(),
                    $oCachedOrder
                );
            if ($bResult) {
                // @todo traduction
                if ($oCachedOrder) {
                    $oCachedOrder2
                        = BeezupOrder::fromPrestashopOrderId($oPsOrder->id);
                    if ($oCachedOrder2->etag === $oCachedOrder->etag) {
                        $aResult['successes'][]
                            = $this->l('Order has not been changed');
                    }
                    if ($oCachedOrder2->etag !== $oCachedOrder->etag) {
                        $aResult['successes'][] = $this->l('Order updated');
                    }
                    if ($oCachedOrder2->getBeezupOrderResult()
                        ->isPendingSynchronization()
                    ) {
                        $aResult['successes'][]
                            = $this->l('Synchronization is pending');
                    }
                }
            } else {
                $aResult['errors'][] = $sError ? $sError
                    : $this->l('Error updating order');
            }
        } else {
            $aResult['errors'][] = $this->l('No saved beezup order data');
        }

        return $aResult;
    }

    public function changeOrder(Order $oPsOrder, $aData)
    {
        $aResult = array(
            'errors'    => array(),
            'warnings'  => array(),
            'infos'     => array(),
            'successes' => array(),
        );
        if ($this->isDebugModeActivated()) {
            $aResult['infos'][] = var_export($aData, true);
        }

        if (!isset($aData['id_order']) || !is_numeric($aData['id_order'])) {
            $aResult['errors'][] = $this->l('Invalid order id');

            return $aResult;
        }

        $oPsOrder = new Order((int)$aData['id_order']);

        if (!Validate::isLoadedObject($oPsOrder)) {
            $aResult['errors'][] = $this->l('Unable load object');

            return $aResult;
        }

        $oBeezupOrderResponse = $this->getBeezupOrderFromPs($oPsOrder);
        $oBeezupOrder = $oBeezupOrderResponse->getResult();
        if (!$oBeezupOrder) {
            $aResult['errors'][] = $this->l('Unable load BeezUP order');

            return $aResult;
        }

        if (!isset($aData['action_id'])) {
            $aResult['errors'][] = $this->l('No action id');

            return $aResult;
        }

        $oLink = $oBeezupOrder->getTransitionLinkByRel($aData['action_id']);

        if (!$oLink) {
            $aResult['errors'][] = $this->l('Invalid action');
        }
        $aParams = array(
            'TestMode' => $this->isTestModeActivated() ? 1 : 0,
            'userName' => Context::getContext()->employee->firstname.' '
                .Context::getContext()->employee->lastname,
        );
        if (trim($aParams['userName']) == "") {
            $aParams['userName'] = "localhost";
        }
        if ($oLink) {
            list($bResult, $oResult) = $this->getOrderService()
                ->changeOrder($oLink, $aParams, $aData);
            if ($bResult) {
                $aResult['successes'][]
                    = $this->l('Order update well executed');
                $oCachedOrder
                    = BeezupOrder::fromPrestashopOrderId($oPsOrder->id);
                /**
                 * @var BeezupOMOrderResult
                 */
                $oBeezupOrderResult = $oCachedOrder->getBeezupOrderResult();

                $oBeezupOrderResult->setIsPendingSynchronization(true);
                $oCachedOrder->order_json
                    = json_encode($oBeezupOrderResult->toArray());
                if (!$oCachedOrder->update()) {
                    $aResult['errors'][]
                        = $this->l('Unable to update Local cache');
                }
            } else {// how to know what happened?
                if ($oResult && $oResult->getInfo()) {
                    foreach ($oResult->getInfo()->getErrors() as $oError) {
                        // ie we have 404 because of bad query params, we don't need to display those 404
                        if ($oError->getMessage() === 'HTTP Error' && !empty($aResult['errors'])) {
                            continue;
                        }
                        $aResult['errors'][] = $oError->getCode().' : '
                            .$oError->getMessage();
                    }
                } else {
                    $aResult['errors'][] = $this->l('Unable to update');
                }
            }
        }

        return $aResult;
    }

    /**
     * Creates order request
     *
     * @param string $sStartTime
     * @param string $sEndTime
     * @param array  $aBeezupOrderStates
     * @param array  $aMarketPlaces
     *
     * @return BeezupOMOrderListRequest
     */
    protected function createOrderListRequest(
        $sStartTime = null,
        $sEndTime = null,
        array $aBeezupOrderStates = array(),
        array $aMarketPlaces = array()
    ) {
        $oRequest = new BeezupOMOrderListRequest();
        $oBeginPeriodUtcDate = new DateTime('now', new DateTimeZone('UTC'));
        $nLastSynchro = ($sStartTime && strtotime($sStartTime))
            ? strtotime($sStartTime)
            : (int)Configuration::get('BEEZUP_OM_LAST_SYNCHRONIZATION');
        if ($nLastSynchro) {
            $oBeginPeriodUtcDate->setTimestamp(
                $nLastSynchro
            ); // @todo Datetime::getTimestamp if PHP >5.3.0
        }

        $oEndPeriodUtcDate = new DateTime('now', new DateTimeZone('UTC'));
        if ($sEndTime && strtotime($sEndTime)) {
            $oEndPeriodUtcDate->setTimestamp(strtotime($sEndTime));
        }

        $oRequest
            ->setBeginPeriodUtcDate($oBeginPeriodUtcDate)
            ->setEndPeriodUtcDate($oEndPeriodUtcDate);

        if (!empty($aMarketPlaces)) {
            $oRequest->setMarketPlaceTechnicalCodes($aMarketPlaces);
        }

        if (!empty($aBeezupOrderStates)) {
            $oRequest->setBeezupOrderStates($aBeezupOrderStates);
        }

        return $oRequest;
    }

    # MODULE

    public function setModule(beezup $oModule)
    {
        $this->oModule = $oModule;

        return $this;
    }

    public function getModule()
    {
        return $this->oModule;
    }


    # DEBUG

    /**
     * Sets debug mode
     *
     * @param boolean $bDebugMode
     *
     * @return BeezupOMController
     */
    public function setDebugMode($bDebugMode = true)
    {
        $this->bDebugMode = (bool)$bDebugMode;
        $this->getOrderService()->setDebugMode($this->bDebugMode);

        return $this;
    }

    /**
     *
     * @return boolean True if debug is activated
     */
    public function isDebugModeActivated()
    {
        return $this->bDebugMode;
    }

    public function isTestModeActivated()
    {
        return (bool)Configuration::get('BEEZUP_OM_TEST_MODE');
    }


    public function getTolerance()
    {
        return $this->nTolerance;
    }

    public function setTolerance($nTolerance)
    {
        $this->nTolerance = (int)$nTolerance;
        $this->getOrderService()->setTolerance($this->getTolerance());

        return $this;
    }

    # INSTALLATION

    /**
     * All instalation chores
     *
     * @todo add variables instalation
     * @todo add uninstall
     * @return boolean
     */
    public function install()
    {
        $bResult = $this->installSqlFile(
            implode(
                DIRECTORY_SEPARATOR,
                array(dirname(__FILE__), 'models', 'install.sql')
            )
        );

        return $bResult;
    }

    /**
     *
     * @param string $sFile
     *
     * @return boolean
     */
    protected function installSqlFile($sFile)
    {
        $aQueries = array_map('trim', explode(';', Tools::file_get_contents($sFile)));
        foreach ($aQueries as $sQuery) {
            if (!$sQuery) {
                continue;
            } // if
            $sQuery = str_replace('PREFIX', _DB_PREFIX_, $sQuery);
            if (!Db::getInstance()->Execute($sQuery)) {
                die($sQuery);

                return false;
            } // if
        } // foreach

        return true;
    }

    # SERVICES

    /**
     * @return BeezupOMRepository
     */
    protected function createRepository()
    {
        $oResult = new BeezupOMRepository();
        $oResult
            ->setDebugMode($this->isDebugModeActivated())
            ->setModule($this->getModule());

        return $oResult;
    }


    /**
     * @return BeezupOMOrderService
     */
    public function getOrderService()
    {
        if ($this->oOrderService === null) {
            $this->oOrderService = $this->createOrderService();
            $this->oOrderService->setDebugMode($this->isDebugModeActivated());
            $this->oOrderService->setTolerance($this->getTolerance());
        }

        return $this->oOrderService;
    }

    /**
     * @return BeezupOMOrderService
     */
    protected function createOrderService()
    {
        return new BeezupOMOrderService($this->createRepository());
    }

    /**
     * Returns list of Beezup order statuses
     *
     * @return array
     */
    public function getBeezupOrderStatesList()
    {
        $aResult = array();
        foreach ($this->getOrderService()->getLovValues('BeezUPOrderState', Context::getContext()->language->iso_code) as $sKey => $oValue) {
            $aResult[$sKey] = $oValue->getTranslationText();
        }

        return $aResult;
    }

    public function getMarketplaceCarriers($sMarketplaceTechnicalCode)
    {
        $sLOVListName = 'ShippingMethod_'.$sMarketplaceTechnicalCode;
        $aResult = array();
        foreach ($this->getOrderService()->getLovValues($sLOVListName, Context::getContext()->language->iso_code) as $sKey => $oValue) {
            $aResult[$oValue->getCodeIdentifier()]
                = $oValue->getTranslationText();
        }

        return $aResult;
    }


    public function getMarketplacCarriersUp()
    {
        $marketplaceResponse = $this->getOrderService()->getClientProxy()
            ->marketplaces();
        $result = $marketplaceResponse->getResult();
        $marketplaces = $result->getMarketplaces();
        $retorno = array();
        $tmpBusiness = array();
        foreach ($marketplaces as $market) {
            $code = $market->getMarketplaceTechnicalCode();
            $business_code = $market->getMarketplaceBusinessCode();
            $tmpCode = $code."CarrierName";
            if ($code == "Fnac" || $code == "PriceMinister" || $code == "Mirakl"
                || $code == "Bol"
                || $code == "RealDE"
            ) {
            } else {
                continue;
            }
            if ($code == "Mirakl" || $code == "Bol" || $code == "RealDE") {
                $tmpCode = Tools::ucfirst(Tools::strtolower($business_code))
                    ."CarrierCode";
            }
            $carrierResponse = $this->getOrderService()->getClientProxy()
                ->getMarketplace($tmpCode);
            $carResponse = $carrierResponse->getResult();
            $tmpvars = array();

            foreach ($carResponse->getValues() as $car) {
                $carCode = $car->getCodeIdentifier();
                $carName = $car->getTranslationText();
                $midx = md5(Tools::strtoupper($code.$carCode));
                $midx2 = md5(Tools::strtoupper($code.$carCode.$business_code));
                if (!in_array($carCode, $tmpvars)
                    && (!isset($tmpBusiness[$midx2]))
                ) {
                    $tmpvars[] = $carCode;
                    $tmpBusiness[$midx2] = 1;
                    $retorno[] = array(
                        'mc_idx'                     => $midx,
                        'marketplace_technical_code' => $code,
                        'marketplace_business_code'  => $business_code,
                        'code'                       => $carCode,
                        'name'                       => $carName,

                    );
                }
            }
        }

        return $retorno;
    }


    public function getClientTechnicalCodes()
    {
        $aResult = array();
        foreach ($this->getMarketplaces() as $oMarketplace) {
            $aResult[] = $oMarketplace->getMarketplaceTechnicalCode();
        }

        return array_unique($aResult);
    }

    private $beezupMarketplaces = array();

    public function getClientMarketplacesCarriers()
    {
        $aResult = array();
        foreach ($this->getMarketplaces() as $oMarketplace) {
            $sMarketplaceTechnicalCode
                = $oMarketplace->getMarketplaceTechnicalCode();
            $sMarketplaceBusinessCode
                = $oMarketplace->getMarketPlaceBusinessCode();
            $this->beezupMarketplaces[$sMarketplaceBusinessCode]
                = $sMarketplaceBusinessCode;
            // todo
            foreach ($this->getMarketplaceCarriers($sMarketplaceTechnicalCode) as $sCarrierCode => $sCarrierName) {
                $sCarrierCombination = $sMarketplaceTechnicalCode.$sCarrierCode;
                if (!isset($aResult[$sCarrierCombination])) {
                    $aResult[$sCarrierCombination] = array(
                        'mc_idx'                     => md5(
                            Tools::strtoupper(
                                $sMarketplaceTechnicalCode
                                .$sCarrierCode
                            )
                        ),
                        'marketplace_technical_code' => $sMarketplaceTechnicalCode,
                        'marketplace_business_code'  => array($sMarketplaceBusinessCode),
                        'code'                       => $sCarrierCode,
                        'name'                       => $sCarrierName,
                    );
                } else {
                    $aResult[$sCarrierCombination]['marketplace_business_code'][]
                        = $sMarketplaceBusinessCode;
                }
            }
        }
        foreach ($aResult as &$aRow) {
            $aRow['marketplace_business_code'] = implode(
                ', ',
                array_unique($aRow['marketplace_business_code'])
            );
        }

        return $aResult;
    }

    /**
     * Returns line to add to cron
     *
     * @return string Crontab compatible line
     */
    protected function getCronCall()
    {
        $sPhpBinary = defined('PHP_BINARY') ? PHP_BINARY : PHP_BINDIR;
        $sPhpExecutable = ($sPhpBinary ? rtrim($sPhpBinary, DIRECTORY_SEPARATOR)
                .DIRECTORY_SEPARATOR : '').'php'.(DIRECTORY_SEPARATOR == '\\'
                ? '.exe' : '');

        return sprintf(
            '*/10 * * * * %s %s',
            $sPhpExecutable,
            _PS_MODULE_DIR_.'/beezup/cron_om.php --callback=synchronizeOrders'
        );
    }

    /**
     * Returns line to add to cron
     *
     * @return string Crontab compatible line
     */
    protected function getSyncPurgeCall()
    {
        $sPhpBinary = defined('PHP_BINARY') ? PHP_BINARY : PHP_BINDIR;
        $sPhpExecutable = ($sPhpBinary ? rtrim($sPhpBinary, DIRECTORY_SEPARATOR)
                .DIRECTORY_SEPARATOR : '').'php'.(DIRECTORY_SEPARATOR == '\\'
                ? '.exe' : '');

        return sprintf(
            '%s %s',
            $sPhpExecutable,
            _PS_MODULE_DIR_.'/beezup/cron_om.php --callback=purgeSync'
        );
    }

    /**
     *
     * @return multitype:string NULL multitype:
     */

    public function getStores()
    {
        return $this->getOrderService()->getStores();
    }

    public function getSmartyVars()
    {
        $aPsIdentityFields
            = BeezupOMProductIdentityMapperFactory::create(
                $this->isDebugModeActivated()
            )
            ->getPsIdentityFields();

        $oLastSync = new DateTime('now', new DateTimeZone('UTC'));
        $oLastSync->setTimestamp(
            Configuration::get('BEEZUP_OM_LAST_SYNCHRONIZATION')
        );

        if (!class_exists('BeezupHarvestClient', false)) {
            require_once _PS_CLASS_DIR_.DIRECTORY_SEPARATOR
                .'BeezupHarvestClient.php';
        }

        $productTemplates = Db::getInstance()->ExecuteS(
            "select * from "
            ._DB_PREFIX_."beezupom_product_template"
        );

        return array(
            'om_debug_mode'        => $this->isDebugModeActivated(),
            'om_last_sync'         => $oLastSync->format('Y-m-d H:i:s'),
            'cron_call'            => $this->getCronCall(),
            'sync_purge_call'      => $this->getSyncPurgeCall(),
            'current_sync'         => BeezupHarvestClient::getCurrent(
                (int)Configuration::get('BEEZUP_OM_SYNC_TIMEOUT')
            ),
            'is_connection_ok'     => $this->isConnectionOk(),
            'om_statuses'          => $this->getBeezupOrderStatesList(),
            'om_stores'            => $this->getStores(),
            'om_ps_id_fields'      => $aPsIdentityFields,
            'om_ps_id_fields_json' => json_encode($aPsIdentityFields),
            'om_carriers'          => $this->getClientMarketplacesCarriers(),
            'ps_statuses'          => OrderState::getOrderStates(
                Context::getContext()->language->id
            ),
            'ps_stores'            => Shop::getShops(),
            'carriers'             => $this->getCarriers(),
            'payment_modules'      => $this->getPaymentModules(),
            'harvest_key'          => $this->getHarvestKey(),
            'timezone'             => new DateTimeZone(
                Configuration::get('PS_TIMEZONE')
            ),
            'test_mode'            => $this->isTestModeActivated(),
            'om_scarriers_up'      => $this->getMarketplacCarriersUp(),
            'productTemplates'     => $productTemplates,
            'beezup_marketplaces'  => $this->beezupMarketplaces,

        );
    }

    public function getMarketplaces()
    {
        return $this->getOrderService()->getMarketplaces();
    }

    public function getHarvestKey()
    {
        return md5(Configuration::get('BEEZUP_OM_USER_ID')._COOKIE_KEY_);
    }

    protected function getCarriers()
    {
        return Carrier::getCarriers(
            Context::getContext()->language->id,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );
    }

    /**
     * @see Module::getPaymentModules
     * @return Ambigous <multitype:, boolean, mixed, unknown, multitype:unknown >
     */
    protected function getPaymentModules()
    {
        $list = Shop::getContextListShopID();
        $hookPayment = 'Payment';
        if (Db::getInstance()->getValue(
            'SELECT `id_hook` FROM `'._DB_PREFIX_
            .'hook` WHERE `name` = \'displayPayment\''
        )
        ) {
            $hookPayment = 'displayPayment';
        }

        return Db::getInstance()
            ->executeS(
                'SELECT DISTINCT m.`id_module`, m.`name`	FROM `'
                ._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			WHERE h.`name` = \''.pSQL($hookPayment).'\'
			AND (SELECT COUNT(*) FROM '._DB_PREFIX_
                .'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('
                .implode(', ', $list).')) = '.count($list).'
			AND hm.id_shop IN('.implode(', ', $list).')
			GROUP BY hm.id_hook, hm.id_module
			ORDER BY hm.`position`, m.`name` DESC'
            );
    }

    /**
     * Tests connection status
     *
     * @return NULL
     * @todo ServiceOrder
     */
    protected function isConnectionOk()
    {
        if ($this->bConnectionTestCache === null) {
            if (!Configuration::get('BEEZUP_OM_USER_ID')
                || !Configuration::get('BEEZUP_OM_API_TOKEN')
            ) {
                $this->bConnectionTestCache = false;
            }
            try {
                $this->bConnectionTestCache = ((int)$this->getOrderService()
                    ->isCredentialValid());
            } catch (Exception $oException) {
                $this->bConnectionTestCache = false;
            }
        }

        return $this->bConnectionTestCache;
    }

    public function getBeezupOrderFromPs($oPsOrder)
    {
        $oCachedOrder = BeezupOrder::fromPrestashopOrderId($oPsOrder->id);
        if ($oCachedOrder) {
            $oOrderIdentifier = $oCachedOrder->getBeezupOrderId();
            $oBeezupOMOrderResponse = $this->getOrderService()
                ->getOrder($oOrderIdentifier);
            if ($oBeezupOMOrderResponse
                && $oBeezupOMOrderResponse->getResult()
            ) {
                return $oBeezupOMOrderResponse;
            }
        }

        return null;
    }

    /**
     * Returns disponible order actions
     *
     * @param unknown_type $oBeezupOrder
     * @param unknown_type $oPsOrder
     *
     * @return multitype:|multitype:multitype:string NULL unknown
     */
    public function getOrderActions($oBeezupOrder = null, Order $oPsOrder = null)
    {
        $aResult = array();
        if (!$oBeezupOrder || !($oBeezupOrder instanceof BeezupOMOrderResult)) {
            return $aResult;
        }

        $aLovValues = $this->getOrderService()
            ->getLovValues(
                'OrderChangeBusinessOperationType',
                Context::getContext()->language->iso_code
            );

        foreach ($oBeezupOrder->getTransitionLinks() as $oLink) {
            $aResult[] = array(
                'link'            => $oLink,
                'href'            => $oLink->getHref(),
                'id'              => $oLink->getRel(),
                'name'            => $oLink->getRel(),
                'translated_name' => isset($aLovValues[$oLink->getRel()]) ? $aLovValues[$oLink->getRel()]->getTranslationText() : $oLink->getRel(),
                'fields'          => json_encode($oLink->toArray()),
                'values'          => json_encode(
                    $this->getFieldsValues(
                        $oLink,
                        $oPsOrder
                    )
                ),
                'lovs'            => json_encode(
                    $this->getOrderService()
                        ->getLOVValuesForParams($oLink)
                ),
                'info'            => json_encode(
                    $this->getTransitionLinkInfo($oLink)
                ),
            );
        }

        return $aResult;
    }

    // @stub
    protected function getTransitionLinkInfo(BeezupOMLink $oLink)
    {
        return '';
        // if ($oLink->getInfo())
    }

    # TRANSLATION

    protected function l($sString)
    {
        return $this->getModule()
            ? Translate::getModuleTranslation(
                $this->getModule(),
                $sString,
                'beezup'
            ) : $sString;
    }

    # MAPPING

    /**
     * Extracts existing values using mapping, using order as root object
     *
     * @param BezupOMLink $oLink
     * @param Order       $oPsOrder
     *
     * @return array
     */
    protected function getFieldsValues(BeezupOMLink $oLink, Order $oPsOrder)
    {
        $aResult = array();
        foreach ($oLink->getParameters() as $oParam) {
            if (isset($this->aFieldsValuesMapping[$oParam->getName()])) {
                $aResult[$oParam->getName()] = BeezupOMValueExtractor::extract($this->aFieldsValuesMapping[$oParam->getName()], $oPsOrder);
            } // if
        } // foreach

        return $aResult;
    }




    # MAIL HANDLING

    /**
     * Desactivates mail sending
     */
    protected function muteMail()
    {
        return false;
        if ($this->aMailMethodCache === null) {
            $this->aMailMethodCache = array(
                'PS_MAIL_METHOD' => Configuration::get('PS_MAIL_METHOD'),
                'PS_MAIL_SERVER' => Configuration::get('PS_MAIL_SERVER'),
            );
        }
        if (version_compare(_PS_VERSION_, '1.5.4.0', 'lt')) {
            Configuration::updateValue('PS_MAIL_METHOD', 2);
            Configuration::updateValue('PS_MAIL_SERVER', 'smtp.beezup.com');
        } else {
            Configuration::updateValue('PS_MAIL_METHOD', 3);
        }
    }

    /**
     * Reactivates mail sending
     */
    protected function unmuteMail()
    {
        return false;
        if ($this->isDebugModeActivated()) {
            return false;
        }
        if (($this->aMailMethodCache !== null)) {
            foreach ($this->aMailMethodCache as $sKey => $mValue) {
                Configuration::updateValue($sKey, $mValue);
            }
        }
    }
}

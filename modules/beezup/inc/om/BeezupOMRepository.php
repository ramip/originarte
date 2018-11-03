<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR
    .'BeezupOrderStatus.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR
    .'BeezupomLog.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR
    .'BeezupOMProductIdentityMapperFactory.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR
    .'BeezupOMStoreMapperFactory.php';

class BeezupOMRepository implements BeezupOMRepositoryInterface
{
    /**
     * @var BeezupOMProductIdentityMapper
     */
    protected $oProductIdentityMapper = null;
    protected $aFirstNamesCache = null;
    protected $oCredential = null;
    protected $oModule = null;
    protected $aLog = array();
    protected $nTolerance = 0;
    protected $aMailMethodCache = null;
    private $beezupOrderId = "";


    /**
     * Desactivates mail sending
     */
    protected function muteMail()
    {
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
        if ($this->isDebugModeActivated()) {
            return false;
        }
        if (($this->aMailMethodCache !== null)) {
            foreach ($this->aMailMethodCache as $sKey => $mValue) {
                Configuration::updateValue($sKey, $mValue);
            }
        }
    }


    public function getLog()
    {
        return $this->aLog;
    }

    // INTERFACE IMPLEMENTATION

    /**
     * @param BeezupOMCredential $oCredential
     *
     * @return BeezupOMController
     */
    public function setCredential(BeezupOMCredential $oCredential)
    {
        $this->oCredential = $oCredential;

        return $this;
    }

    /**
     * @return BeezupOMCredential
     */
    public function getCredential()
    {
        if ($this->oCredential === null) {
            $this->setCredential($this->createCredential());
        }

        return $this->oCredential;
    }

    /**
     * @return BeezupOMCredential
     */
    protected function createCredential()
    {
        return new BeezupOMCredential(
            Configuration::get('BEEZUP_OM_USER_ID'),
            Configuration::get('BEEZUP_OM_API_TOKEN')
        );
    }

    /**
     * @stub
     * (non-PHPdoc)
     *
     * @see BeezupOMRepositoryInterface::isConfigurationOk()
     */
    public function isConfigurationOk()
    {
        return Configuration::get('BEEZUP_OM_USER_ID')
            && Configuration::get('BEEZUP_OM_API_TOKEN')
            && Configuration::get('BEEZUP_OM_SYNC_TIMEOUT')
            && Configuration::get('BEEZUP_OM_LAST_SYNCHRONIZATION')
            && Configuration::get('BEEZUP_OM_STATUS_MAPPING')
            && Configuration::get('BEEZUP_OM_STORES_MAPPING')
            && Configuration::get('BEEZUP_OM_ID_FIELD_MAPPING')
            && Configuration::get('BEEZUP_OM_DEFAULT_CARRIER_ID');
    }

    /**
     * (non-PHPdoc).
     *
     * @see  BeezupOMRepositoryInterface::getCurrentHarvestSynchronization()
     *
     * @todo Add BEEZUP_OM_SYNC_TIMEOUT
     */
    public function getCurrentHarvestSynchronization()
    {
        return BeezupHarvestClient::getCurrent(
            (int)Configuration::get('BEEZUP_OM_SYNC_TIMEOUT')
        );
    }

    /**
     * (non-PHPdoc).
     *
     * @see BeezupOMRepositoryInterface::updateLastSynchronizationDate()
     */
    public function updateLastSynchronizationDate(
        DateTime $oLastSynchronizationDate
    ) {
        Configuration::updateValue(
            'BEEZUP_OM_LAST_SYNCHRONIZATION',
            $oLastSynchronizationDate->getTimestamp(),
            false,
            0,
            0
        );

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see BeezupOMRepositoryInterface::getLastSynchronizationDate()
     */
    public function getLastSynchronizationDate()
    {
        $oResult = new DateTime('now', new DateTimeZone('UTC'));
        $nTime
            = (int)Configuration::getGlobalValue(
                'BEEZUP_OM_LAST_SYNCHRONIZATION'
            );
        if ($nTime) {
            $oResult->setTimestamp($nTime);
        }

        return $oResult;
    }

    /**
     * @param BeezupOMHarvestClientReporting $oSource
     *
     * @return bool
     */
    public function saveHarvestClientReporting(
        BeezupOMHarvestClientReporting $oSource,
        $sNewExecutionId = null
    ) {
        $oBeezupHarvestClient
            = BeezupHarvestClient::createFromOMObject($oSource);
        if ($oBeezupHarvestClient && is_string($sNewExecutionId)
            && $sNewExecutionId != $oBeezupHarvestClient->execution_id
        ) {
            $oBeezupHarvestClient->execution_id = $sNewExecutionId;
            $oBeezupHarvestClient->update();
        }

        return (bool)$oBeezupHarvestClient;
    }

    public function saveHarvestOrderReporting(
        BeezupOMHarvestOrderReporting $oSource,
        $sNewExecutionId = null
    ) {
        $oBeezupHarvestOrder = BeezupHarvestOrder::createFromOMObject($oSource);
        if ($oBeezupHarvestOrder && is_string($sNewExecutionId)
            && $sNewExecutionId != $oBeezupHarvestOrder->execution_id
        ) {
            $oBeezupHarvestOrder->execution_id = $sNewExecutionId;
            $oBeezupHarvestOrder->update();
        }

        return (bool)$oBeezupHarvestOrder;
    }

    public function getImportedOrderIdentifier($nMerchantOrderId)
    {
        if (!is_int($nMerchantOrderId) || $nMerchantOrderId <= 0) {
            return;
        }
        $oPsOrder = new Order((int)$nMerchantOrderId);
        if (!Validate::isLoadedObject($oPsOrder)) {
            return;
        }
        $oImportedOrder = BeezupOrder::fromPrestashopOrderId($oPsOrder->id);
        if (!$oImportedOrder || !Validate::isLoadedObject($oImportedOrder)) {
            return;
        }

        return $oImportedOrder->getBeezupOrderId();
    }

    public function wasIdentifierImported(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $oImportedOrder = BeezupOrder::fromBeezupOrderId($oOrderIdentifier);

        return $oImportedOrder && Validate::isLoadedObject($oImportedOrder);
    }

    public function updateOrder(BeezupOMOrderResponse $oBeezupOrderResponse)
    {
        /**
         * @var BeezupOMOrderResult
         */
        $oBeezupOrder = $oBeezupOrderResponse->getResult();
        $this->beezupOrderId = $oBeezupOrder->getBeezupOrderUUID();
        $oPsOrder = $this->getAssociatedPsOrder($oBeezupOrder);
        $bResult = true;
        $bStatusChanged = false;
        if (!$oBeezupOrder->isPendingSynchronization()) {
            $nStatusId
                = $this->getStatusMapping(
                    $oBeezupOrder->getOrderStatusBeezUPOrderStatus()
                );
            $this->addLog('INFO', $this->l('No sync pending, changing status'));
            $this->muteMail();
            $bStatusChanged = $this->addStatus($oPsOrder, $nStatusId);
            $this->unmuteMail();
            $this->updateOrderTotals($oPsOrder, $oBeezupOrder);
            $oPsOrder->update();
            $this->updatePaymentDetails($oPsOrder);
            $this->updateInvoiceDetails($oPsOrder);
            $this->updateCarrierDetails($oPsOrder);
            $oPsOrder->update();
            $sEtag = $oBeezupOrderResponse->getEtag();
        } else {
            $this->addLog($this->l('Synchronisation pending'));
            $sEtag = md5(uniqid().time());
        }
        if ($sEtag) {
            $oCachedOrder
                = BeezupOrder::fromBeezupOrderId(
                    BeezupOMOrderIdentifier::fromBeezupOrder($oBeezupOrder)
                );
            if ($oCachedOrder && Validate::isLoadedObject($oCachedOrder)
                && ($oCachedOrder->etag !== $sEtag)
            ) {
                $this->addLog('INFO', $this->l('Etag changed'));
                $oCachedOrder->etag = $sEtag;
                $oCachedOrder->order_json
                    = json_encode($oBeezupOrder->toArray());
                $oCachedOrder->infos_json
                    = json_encode($oBeezupOrderResponse->getInfo()->toArray());
                $oCachedOrder->date_upd = date('Y-m-d H:i:s');
                $oCachedOrder->update();
            }
        }
        // @todo see if we need block this functionality (ie. ME a changed
        // adress in BO)
        $bOrderNeedsUpdate = false;
        $oCustomer = new Customer($oPsOrder->id_customer);
        $this->addLog('INFO', 'COMPARING ADRESSES');
        // invoice address for changed ?
        $oInvoiceAddres = $this->createInvoiceAddress(
            $oCustomer,
            $oBeezupOrder
        );
        if (!$this->areAddressSame(
            $oInvoiceAddres,
            new Address($oPsOrder->id_address_invoice)
        )
        ) {
            $oInvoiceAddres = $this->processAddress(
                $oCustomer,
                $oInvoiceAddres
            );
            $this->addLog('INFO', 'PROCESING ADRESSES');
            if ($oInvoiceAddres && Validate::isLoadedObject($oInvoiceAddres)) {
                $oPsOrder->id_address_invoice = $oInvoiceAddres->id;
                $bOrderNeedsUpdate = true;
            } // if
        } // if
        // delivery address for changed ?
        $oDeliveryAddres = $this->createDeliveryAddress(
            $oCustomer,
            $oBeezupOrder
        );
        if (!$this->areAddressSame(
            $oDeliveryAddres,
            new Address($oPsOrder->id_address_delivery)
        )
        ) {
            $oDeliveryAddres = $this->processAddress(
                $oCustomer,
                $oDeliveryAddres
            );
            if ($oDeliveryAddres
                && Validate::isLoadedObject($oDeliveryAddres)
            ) {
                $oPsOrder->id_address_delivery = $oDeliveryAddres->id;
                $bOrderNeedsUpdate = true;
            } // if
        } // if
        $bUpdateCustomer = false;
        if ($oCustomer->lastname === '---' && $oInvoiceAddres
            && $oInvoiceAddres->lastname !== $oCustomer->lastname
        ) {
            $oCustomer->lastname = $oInvoiceAddres->lastname;
            $bUpdateCustomer = true;
        }
        if ($oCustomer->firstname === '---' && $oInvoiceAddres
            && $oInvoiceAddres->firstname !== $oCustomer->firstname
        ) {
            $oCustomer->firstname = $oInvoiceAddres->firstname;
            $bUpdateCustomer = true;
        }
        if ($bUpdateCustomer) {
            $this->addLog('INFO', $this->l('Updating customer'));
            $oCustomer->update();
        }
        if ($bOrderNeedsUpdate) {
            $this->addLog('INFO', $this->l('Updating order'));
            $bResult = $oPsOrder->update();
        }
        $oResult = new BeezupOMSetOrderIdValues();
        $oResult->setOrderMerchantOrderId($oPsOrder->id)
            ->setOrderMerchantECommerceSoftwareName('Prestashop')
            ->setOrderMerchantECommerceSoftwareVersion(_PS_VERSION_);

        return $oResult;
        // return $bResult;
    }

    private $stockReset = array();

    public function createOrder(BeezupOMOrderResponse $oBeezupOrderResponse)
    {
        $oBeezupOrder = $oBeezupOrderResponse->getResult();
        $this->beezupOrderId = $oBeezupOrder->getBeezupOrderUUID();
        if ($this->shouldSkipImport($oBeezupOrder)) {
            $this->addLog(
                'ERROR',
                sprintf(
                    $this->l('BeezUP: Order %s will be not imported'),
                    $oBeezupOrder->getBeezupOrderUUID()
                )
            );

            return false;
        }
        // @todo switch context shop
        $nOrderId = null;
        try {
            $bIsPrescanOk = $this->prescanOrder($oBeezupOrder);
            $oCart = null;
            if ($bIsPrescanOk) {
                // @todo loop for every shop
                $oCart = new Cart();
                $nCurrencyId
                    = Currency::getIdByIsoCode(
                        $oBeezupOrder->getOrderCurrencyCode()
                    );
                if (!$nCurrencyId
                    || !Validate::isLoadedObject(new Currency($nCurrencyId))
                ) {
                    $this->addLog(
                        'ERROR',
                        $this->l('BeezUP error: Unable to use currency').' '
                        .$oBeezupOrder->getOrderCurrencyCode().' '.$nCurrencyId
                        .var_export($oBeezupOrder->toArray(), true)
                    );

                    return false;
                }

                $marketChannelFilters
                    = Configuration::get("BEEZUP_MARKETCHANNEL_FILTERS");
                $marketChannelFilters = explode(",", $marketChannelFilters);
                $marketplaceChannel
                    = $oBeezupOrder->getOrderMarketplaceChannel();
                $blnResetStock = false;
                foreach ($marketChannelFilters as $filter) {
                    if ((string)$filter == (string)$marketplaceChannel
                        && (!empty($filter) || trim($filter) != "")
                    ) {
                        $blnResetStock = true;
                        break;
                    } elseif (is_numeric($filter)) {
                        if ((int)$filter == 1) {
                            $blnResetStock = true;
                            break;
                        }
                    }
                }

                $oCustomer = $this->getCustomer($oBeezupOrder);
                if (!$oCustomer) {
                    $this->addLog(
                        'ERROR',
                        $this->l('BeezUP error: Unable to create customer')
                    );

                    return false;
                }
                if ($this->needsTemporaryAddress($oBeezupOrderResponse)) {
                    $this->addLog(
                        'ERROR',
                        $this->l(
                            'BeezUP: This order needs temporary address before acceptance'
                        )
                    );
                    $oInvoiceAddress = $this->createTemporaryAddress(
                        $oCustomer,
                        $oBeezupOrder
                    );
                    $oDeliveryAddress = clone $oInvoiceAddress;
                } else {
                    $oDeliveryAddress = $this->createDeliveryAddress(
                        $oCustomer,
                        $oBeezupOrder
                    );
                    $oInvoiceAddress = $this->createInvoiceAddress(
                        $oCustomer,
                        $oBeezupOrder
                    );
                }
                $oDeliveryAddress = $this->processAddress(
                    $oCustomer,
                    $oDeliveryAddress
                );
                $oInvoiceAddress = $this->processAddress(
                    $oCustomer,
                    $oInvoiceAddress
                );
                if (!$oDeliveryAddress) {
                    $this->addLog(
                        'ERROR',
                        $this->l(
                            'BeezUP error: Unable to create delivery address'
                        )
                    );
                }
                if (!$oInvoiceAddress) {
                    $this->addLog(
                        'ERROR',
                        $this->l(
                            'BeezUP error: Unable to create invoice address'
                        )
                    );
                }
                if (!$oInvoiceAddress || !$oDeliveryAddress) {
                    if (count(Cart::getCustomerCarts($oCustomer->id, false))
                        == 0
                    ) {
                        // @todo delete adresses if customer exists but adresses
                        // were created, but not used
                        $oCustomer->delete();
                    }
                    if ($oInvoiceAddress && !$oInvoiceAddress->isUsed()) {
                        // @todo remove address from db
                        $oInvoiceAddress->delete();
                    }
                    if ($oDeliveryAddress && !$oDeliveryAddress->isUsed()) {
                        $oDeliveryAddress->delete();
                    }

                    return false;
                }
                $oCart->id_address_delivery = $oDeliveryAddress->id;
                $oCart->id_address_invoice = $oInvoiceAddress->id;
                $oCart->id_customer = $oCustomer->id;
                $oCart->id_currency
                    = Currency::getIdByIsoCode(
                        $oBeezupOrder->getOrderCurrencyCode()
                    );
                $oCart->id_carrier
                    = (int)$this->getCarrierId(
                        $oBeezupOrder->getMarketplaceTechnicalCode(),
                        $oBeezupOrder->getOrderShippingMethod()
                    );
                if (!$oCart->save()) {
                    $this->addLog(
                        'ERROR',
                        $this->l('BeezUP error: Unable to save cart')
                    );

                    return false;
                }
                Context::getContext()->cart = $oCart;
                Context::getContext()->currency
                    = new Currency($oCart->id_currency, null, $oCart->id_shop);
                Context::getContext()->customer
                    = new Customer($oCart->id_customer);
                Context::getContext()->language = new Language($oCart->id_lang);
                Context::getContext()->shop = new Shop($oCart->id_shop);
                Context::getContext()->country
                    = new Country($oDeliveryAddress->id_country);

                // if not employee, we are picking the first superadmin or, if no superadminn the last active found
                if (!isset(Context::getContext()->employee)
                    || !Context::getContext()->employee
                ) {
                    $oEmployee = null;
                    foreach (Employee::getEmployees() as $aEmployeeData) {
                        $oEmployee
                            = new Employee((int)$aEmployeeData['id_employee']);
                        if ($oEmployee && $oEmployee->isSuperAdmin()) {
                            break;
                        }
                    }
                    Context::getContext()->employee = $oEmployee;
                }

                // s'il ya un seul produit qui passe pas, on fait pas d'import
                // dans ce cas la, bien supprimer le panier et client
                $aProductsAdded = false;
                try {
                    $aProductsAdded = $this->copyProductsToCart(
                        $oBeezupOrder,
                        $oCart,
                        $blnResetStock
                    );
                } catch (Exception $ex) {
                    $this->addLog('ERROR', $ex->getMessage());
                }

                if ($aProductsAdded === false || count($aProductsAdded) === 0) {
                    $this->addLog(
                        'ERROR',
                        $this->l(
                            'BeezUP error: impossible identify or add all products to panier, aborting import of order'
                        )
                        .' '.$oBeezupOrder->getBeezupOrderUUID()
                    );
                    $oCart->delete();
                    if (count(Cart::getCustomerCarts($oCustomer->id, false))
                        == 0
                    ) {
                        // @todo delete adresses if customer exists but adresses
                        // were created, but not used
                        $oCustomer->delete();
                    }
                    if ($oInvoiceAddress && !$oInvoiceAddress->isUsed()) {
                        $oInvoiceAddress->delete();
                    }
                    if ($oDeliveryAddress && !$oDeliveryAddress->isUsed()) {
                        $oDeliveryAddress->delete();
                    }

                    return false;
                }

                $oCart->getDeliveryOptionList(null, true);
                $oCart->getDeliveryOption(null, false, false);
                $aPackages = $oCart->getPackageList(true);
                /*
                 * $oCart->id_carrier = $this->getCarrierId(); $oCart->update();
                 */
                if ($oBeezupOrder->getOrderStatusBeezUPOrderStatus()
                    !== 'New'
                ) {
                    $nNextStatusId
                        = $this->getStatusMapping(
                            $oBeezupOrder->getOrderStatusBeezUPOrderStatus()
                        );
                } else {
                    $nNextStatusId = null;
                }
                $nNewStatusId = $this->getStatusMapping('New');

                $sPaymentName
                    = Tools::ucfirst(
                        Tools::strtolower(
                            $oBeezupOrder->getMarketplaceBusinessCode()
                        )
                    )
                    .' (BeezUP)';

                if ($oBeezupOrder->getOrderMarketplaceChannel() == "AFN") {
                    $sPaymentName = "Amazon FBA (BeezUP)";
                } elseif ($oBeezupOrder->getOrderMarketplaceChannel()
                    == "Cdiscount Fulfilment"
                ) {
                    $sPaymentName = "Cdiscount Fulfilment (BeezUP)";
                }
                // we couldn not rely on result of validateOrder; some modules (hello, paypal!) do not return true
                // now we are using only our payment module wrapper, but we cannot be sure
                // if some evil master didn't made crappy override of PaymentModuleCore::validateOrder
                // default validateOrder returns true always and only if currentOrder is set, so technially it's the same
                $oPaymentModule = $this->getPaymentModule();
                $oPaymentModule->currentOrder = null;
                $this->muteMail();
                try {
                    $statusId = $nNewStatusId ? $nNewStatusId
                        : Configuration::get('PS_OS_PAYMENT');
                    $_payment = $oPaymentModule->validateOrder(
                        $oCart->id,
                        $statusId,
                        $oCart->getOrderTotal(),
                        $sPaymentName
                    );
                } catch (Exception $ex) {
                    $this->addLog('ERROR', $ex->getMessage());
                    $this->unmuteMail();

                    return false;
                }
                $this->unmuteMail();
                if ($oPaymentModule->currentOrder) {
                    $nOrderId = $oPaymentModule->currentOrder;
                    $oOrder = new Order($nOrderId);
                    if (!Validate::isLoadedObject($oOrder)) {
                        $this->resetStock();
                        $this->addLog(
                            'ERROR',
                            $this->l('BeezUP error: Unable to reload order')
                        );

                        return false;
                    }

                    $bOrderStatus = new BeezupOrderStatus();
                    $bOrderStatus->id_order = $nOrderId;
                    $bOrderStatus->id_order_status = $statusId;
                    $bOrderStatus->add();

                    $this->addMessage($oOrder, $this->l('BeezUP: Order added'));
                    // creating link+ cache
                    if (!BeezupOrder::create(
                        $nOrderId,
                        $oBeezupOrderResponse
                    )
                    ) {
                        $sMessage
                            = sprintf(
                                $this->l(
                                    'BeezUP: Unable to create link between ps order %d and %s'
                                ),
                                $nOrderId,
                                $oBeezupOrder->getBeezupOrderUUID()
                            );
                        $this->addLog(
                            'ERROR',
                            $this->l('BeezUP error').' : '.$sMessage
                        );
                        $this->addMessage($oOrder, $sMessage);
                    }
                    // @todo order::id_carrier can be 0 if carrier cannot ship
                    // ...
                    if (!$oOrder->id_carrier
                        || $oCart->id_carrier != $oOrder->id_carrier
                    ) {
                        $sMessage = $this->l('BeezUP: Carrier id forced to').' '
                            .$oCart->id_carrier;
                        $this->addMessage($oOrder, $sMessage);
                        $oOrder->id_carrier = $oCart->id_carrier;
                        $oOrder->update();
                    }

                    // @todo order update inside this function, can we avoid
                    // that?
                    $this->updateOrderDetails($oOrder, $aProductsAdded);
                    $this->updateOrderTotals($oOrder, $oBeezupOrder);
                    $oOrder->update();

                    $this->updatePaymentDetails($oOrder);
                    $this->updateCarrierDetails($oOrder);
                    $this->updateInvoiceDetails($oOrder);
                    $oOrder->update();
                    if ($nNextStatusId) {
                        $this->addLog('INFO', $this->l('Adding next status'));
                        $this->muteMail();
                        $this->addStatus($oOrder, $nNextStatusId);
                        $this->unmuteMail();
                        // orderhistory wants to make payments
                    }
                    $this->updateOrderTotals($oOrder, $oBeezupOrder);
                    $this->updatePaymentDetails($oOrder);
                    $this->updateInvoiceDetails($oOrder);
                } else {
                    $this->resetStock();
                    $this->addLog(
                        'ERROR',
                        $this->l('BeezUP error: Unable to validate order')
                    );

                    return false;
                }
                // @todo readjust all prices
            }
        } catch (Exception $oException) {
            $this->resetStock();
            $this->addLog(
                'ERROR',
                $this->l('BeezUP error').' : '.$oException->getMessage()
            );
        }
        // @todo readjust prices etc
        // @todo save errors and warnings
        if ($nOrderId) {
            $checkCart = Db::getInstance()->getRow(
                'select id_order from '
                ._DB_PREFIX_."orders where id_cart = '".(int)$oCart->id."'"
            );
            if ($checkCart) {
                if ($checkCart['id_order'] == $nOrderId) {
                    $oResult = new BeezupOMSetOrderIdValues();
                    $oResult->setOrderMerchantOrderId($nOrderId)
                        ->setOrderMerchantECommerceSoftwareName('Prestashop')
                        ->setOrderMerchantECommerceSoftwareVersion(
                            _PS_VERSION_
                        );

                    return $oResult;
                }
            }
        }

        return false;
    }


    public function resetStock()
    {
        foreach ($this->stockReset as $product) {
            StockAvailable::setQuantity(
                $product['id_product'],
                $product['id_product_attribute'],
                $product['stock'],
                $product['id_shop']
            );
        }
    }


    public function purgeSync()
    {
        foreach (BeezupHarvestClient::getByProcessingStatus(BeezupOMProcessingStatus::IN_PROGRESS) as $aHarvest) {
            $oHarvestClient
                = new BeezupHarvestClient((int)$aHarvest['id_harvest_client']);
            $oHarvestClient->processing_status
                = BeezupOMProcessingStatus::TIMEOUT;
            $oHarvestClient->update();
        }
    }

    // MODULE UPSTREAM (FOR TRANSLATIONS)
    public function setModule(beezup $oModule)
    {
        $this->oModule = $oModule;

        return $this;
    }

    public function getModule()
    {
        return $this->oModule;
    }

    public function l($sString)
    {
        return $this->getModule() ? $this->getModule()->l($sString) : $sString;
    }

    // CUSTOMER HANDLING
    public function isDebugModeActivated()
    {
        return $this->bDebugMode;
    }

    public function setDebugMode($bDebugMode)
    {
        $this->bDebugMode = $bDebugMode;

        return $this;
    }

    protected function getCustomer(BeezupOMOrderResult $oBeezupOrder)
    {
        $nCustomerId = $this->findCustomer($oBeezupOrder);
        if ($nCustomerId > 0) {
            return new Customer($nCustomerId);
        } else {
            return $this->createCustomer($oBeezupOrder);
        }
    }

    protected function convertEmail($sEmail)
    {
        return $sEmail;
        // return $this->isDebugModeActivated() ? str_replace('@','.', $sEmail)
        // . '@studio-kiwik.fr' : $sEmail;
    }

    protected function findCustomer(BeezupOMOrderResult $oBeezupOrder)
    {
        $sEmail = $this->convertEmail($oBeezupOrder->getOrderBuyerEmail());
        $aCustomers = Customer::getCustomersByEmail($sEmail);
        if (empty($aCustomers)) {
            return 0;
        } elseif (count($aCustomers) > 1) {
            // @todo add logic to detect / choose one
            return 0 - count($aCustomers);
        } else {
            $aCustomer = $aCustomers[0];
            return (int)$aCustomer['id_customer'];
        }
    }

    protected function generateEmail(BeezupOMOrderResult $oBeezupOrder)
    {
        $sRawValue = $oBeezupOrder->getBeezupOrderUUID();
        $sFakeDomain = preg_replace(
            '/\W/',
            '',
            $oBeezupOrder->getMarketPlaceTechnicalCode()
        ).'.com';

        return 'fakeemail'.md5($sFakeDomain.$sRawValue).'@'
            .Tools::strtolower($sFakeDomain);
    }

    // @todo force valid values
    protected function createCustomer(BeezupOMOrderResult $oBeezupOrder)
    {
        $oCustomer = new Customer();
        $sEmail = $this->convertEmail($oBeezupOrder->getOrderBuyerEmail());
        $aName = $this->getFirstAndLastName($oBeezupOrder->getOrderBuyerName());
        $oCustomer->note = $this->l('BeezUP import').' '.date('Y-m-d H:i:s');
        $oCustomer->id_gender = 0;
        $oCustomer->firstname = $aName[0];
        $oCustomer->lastname = $aName[1];
        $oCustomer->email = ($sEmail && Validate::isEmail($sEmail) ? $sEmail
            : $this->generateEmail($oBeezupOrder));
        $oCustomer->company = Tools::substr(
            preg_replace(
                '/[<>={}]+/',
                ' ',
                $oBeezupOrder->getOrderBuyerCompanyName()
            ),
            0,
            64
        );
        $oCustomer->passwd = Tools::passwdGen(8);
        $oCustomer->active = true;
        $oCustomer->newsletter = false;
        $oCustomer->id_shop = (int)Context::getContext()->shop->id;
        $mValidation = $oCustomer->validateFields(false, true);
        if ($mValidation === true) {
            if ($oCustomer->save()) {
                return $oCustomer;
            } else {
                $this->addLog(
                    'ERROR',
                    $this->l('BeezUP error: Unable to save customer').' '
                    .$oBeezupOrder->getOrderBuyerName()
                );
            }
        } else {
            $this->addLog('ERROR', $this->l('BeezUP error').' : '.$mValidation);
        }

        return;
    }

    // ADDRESS HANDLING
    protected function areAddressSame($oCurrentAddress, $oNewAddress)
    {
        return $this->getAddressHash($oCurrentAddress)
            === $this->getAddressHash($oNewAddress);
    }

    /**
     * For CDiscount, we do not have client address before acceptance
     * It's marked via BuyerInfoNotTransmitedUntilAcceptance code in
     * Info->Informations.
     *
     * @param BeezupOMOrderResponse $oBeezupOrderResponse
     */
    protected function needsTemporaryAddress(
        BeezupOMOrderResponse $oBeezupOrderResponse
    ) {
        foreach ($oBeezupOrderResponse->getInfo()->getInformations() as $oInformation) {
            if ($oInformation->getCode()
                === 'BuyerInfoNotTransmitedUntilAcceptance'
            ) {
                return true;
            } // if
        }
        $json = json_decode($oBeezupOrderResponse->getJson());
        if (!isset($json->info->informations)) {
            return false;
        }
        foreach ($json->info->informations as $info) {
            if ($info->informationCode
                === 'BuyerInfoNotTransmitedUntilAcceptance'
            ) {
                return true;
            } // if
        }

        return false;
    }

    protected function getAddressHash(Address $oAddress)
    {
        $aFields = array(
            'firstname',
            'lastname',
            'address1',
            'address2',
            'city',
            'postcode',
            'id_country',
            'id_customer',
            'id_state',
            'company',
            'phone',
            'phone_mobile',
        );
        $aData = array();
        foreach ($aFields as $sField) {
            if (!empty($oAddress->{$sField})) {
                $aData[$sField] = Tools::strtolower(trim($oAddress->{$sField}));
            }
        }
        ksort($aData);
        $sId = implode('*', $aData);

        return md5($sId);
    }

    protected function findExistingAddress(Customer $oCustomer, $oAddress)
    {
        $aAdresses
            = $oCustomer->getAddresses(
                isset($oCustomer->id_lang)
                ? $oCustomer->id_lang : Context::getContext()->language->id
            );
        foreach ($aAdresses as $aAddress) {
            $oTargetAddress = new Address($aAddress['id_address']);
            if (($oAddress->id && $oAddress->id == $oTargetAddress->id)
                || $this->getAddressHash($oAddress)
                == $this->getAddressHash($oTargetAddress)
            ) {
                return $oTargetAddress;
            }
        }

        return;
    }

    protected function getCountryId($sIsoCode, $sFullName)
    {
        // searching by iso code
        $nIsoCode = Validate::isLanguageIsoCode($sIsoCode)
            ? Country::getByIso($sIsoCode) : null;
        if ($nIsoCode) {
            return (int)$nIsoCode;
        }
        // searching by name
        foreach (array_unique(array(Context::getContext()->language->id, (int)Configuration::get('PS_LANG_DEFAULT'), null,)) as $mLanguageId) {
            $nIsoCode = (int)Country::getIdByName($mLanguageId, $sFullName);
            if ($nIsoCode) {
                return $nIsoCode;
            }
        }
        // searching by name elements - ie "France Metropolitaine"
        if (stristr($sFullName, ' ')) {
            foreach (array_unique(array(Context::getContext()->language->id, (int)Configuration::get('PS_LANG_DEFAULT'), null,)) as $mLanguageId) {
                foreach (explode(' ', $sFullName) as $sName) {
                    $nIsoCode = (int)Country::getIdByName(
                        $mLanguageId,
                        trim($sName)
                    );
                    if ($nIsoCode) {
                        return $nIsoCode;
                    }
                }
            }
        }
        // FX means France Metropolitaine, if nothing found we can try with "FR"
        if (Tools::strtoupper($sIsoCode) === 'FX') {
            return $this->getCountryId('FR', 'France');
        }

        return;
    }

    protected function createTemporaryAddress($oCustomer, $oBeezupOrder)
    {
        $oAddress = new Address();
        $oAddress->firstname = 'Customer firstname';
        $oAddress->lastname = 'Customer lastname';
        $oAddress->address1 = 'Temporary address';
        $oAddress->city = 'Temporary city';
        $oAddress->postcode = '00000';
        $oAddress->id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        $oAddress->id_customer = $oCustomer->id;
        $oAddress->phone = '00000000';
        $oAddress->phone_mobile = '00000000';
        $oAddress->alias = md5(
            'TEMPORARY '.$oBeezupOrder->getBeezupOrderUUID()
            .$this->getAddressHash($oAddress)
        );

        return $oAddress;
    }

    protected function createInvoiceAddress(
        Customer $oCustomer,
        BeezupOMOrderResult $oBeezupOrder
    ) {
        $aLines = array_filter(
            array_map(
                'trim',
                array(
                    $oBeezupOrder->getOrderBuyerAddressLine1(),
                    $oBeezupOrder->getOrderBuyerAddressLine2().' '
                    .$oBeezupOrder->getOrderBuyerAddressLine3(),
                )
            )
        );
        $oAddress = new Address();
        $aName = $this->getFirstAndLastName($oBeezupOrder->getOrderBuyerName());
        $oAddress->firstname = $aName[0];
        $oAddress->lastname = $aName[1];
        $oAddress->address1 = isset($aLines[0])
            ? Tools::substr(
                preg_replace('/[!<>?=+@{}_$%]+/', '', $aLines[0]),
                0,
                128
            ) : '';
        $oAddress->address2 = isset($aLines[1])
            ? Tools::substr(
                preg_replace('/[!<>?=+@{}_$%]+/', '', $aLines[1]),
                0,
                128
            ) : '';
        if ($oAddress->address2 && empty($oAddress->address1)) {
            $oAddress->address1 = $oAddress->address2;
            $oAddress->address2 = '';
        }
        $oAddress->city = Tools::substr(
            preg_replace(
                '/[!<>;?=+@#"°{}_$%]+/',
                '',
                $oBeezupOrder->getOrderBuyerAddressCity()
            ),
            0,
            64
        );
        $oAddress->postcode = Tools::substr(
            preg_replace(
                '/[^a-zA-Z 0-9-]+/',
                '',
                $oBeezupOrder->getOrderBuyerAddressPostalCode()
            ),
            0,
            12
        );
        $oAddress->id_country
            = $this->getCountryId(
                $oBeezupOrder->getOrderBuyerAddressCountryIsoCodeAlpha2(),
                $oBeezupOrder->getOrderBuyerAddressCountryName()
            );
        $oAddress->id_customer = $oCustomer->id;
        $oAddress->id_state
            = State::getIdByIso(
                $oBeezupOrder->getOrderBuyerStateOrRegion(),
                $oAddress->id_country
            );
        $oAddress->company = Tools::substr(
            preg_replace(
                '/[<>={}]+/',
                ' ',
                $oBeezupOrder->getOrderBuyerCompanyName()
            ),
            0,
            64
        );
        $oAddress->phone = Tools::substr(
            preg_replace(
                '/[^+0-9. ()-]+/',
                ' ',
                $oBeezupOrder->getOrderBuyerPhone()
            ),
            0,
            32
        );
        $oAddress->phone_mobile = Tools::substr(
            preg_replace(
                '/[^+0-9. ()-]+/',
                ' ',
                $oBeezupOrder->getOrderBuyerMobilePhone()
            ),
            0,
            32
        );
        $oAddress->alias = md5(
            'INVOICE '.$oBeezupOrder->getBeezupOrderUUID()
            .$this->getAddressHash($oAddress)
        );

        return $oAddress;
    }

    protected function createDeliveryAddress(
        Customer $oCustomer,
        BeezupOMOrderResult $oBeezupOrder
    ) {
        $aLines = array_filter(
            array_map(
                'trim',
                array(
                    $oBeezupOrder->getOrderShippingAddressLine1(),
                    $oBeezupOrder->getOrderShippingAddressLine2().' '
                    .$oBeezupOrder->getOrderShippingAddressLine3(),
                )
            )
        );
        $oAddress = new Address();
        $aName
            = $this->getFirstAndLastName(
                $oBeezupOrder->getOrderShippingAddressName()
            );
        $oAddress->firstname = $aName[0];
        $oAddress->lastname = $aName[1];
        $oAddress->address1 = isset($aLines[0])
            ? Tools::substr(
                preg_replace('/[!<>?=+@{}_$%]+/', '', $aLines[0]),
                0,
                128
            ) : '';
        $oAddress->address2 = isset($aLines[1])
            ? Tools::substr(
                preg_replace('/[!<>?=+@{}_$%]+/', '', $aLines[1]),
                0,
                128
            ) : '';
        if ($oAddress->address2 && empty($oAddress->address1)) {
            $oAddress->address1 = $oAddress->address2;
            $oAddress->address2 = '';
        }
        $oAddress->city = Tools::substr(
            preg_replace(
                '/[!<>;?=+@#"°{}_$%]+/',
                '',
                $oBeezupOrder->getOrderShippingAddressCity()
            ),
            0,
            64
        );
        $oAddress->postcode = Tools::substr(
            preg_replace(
                '/[^a-zA-Z 0-9-]+/',
                '',
                $oBeezupOrder->getOrderShippingAddressPostalCode()
            ),
            0,
            12
        );
        $oAddress->id_country
            = $this->getCountryId(
                $oBeezupOrder->getOrderShippingAddressCountryIsoCodeAlpha2(),
                $oBeezupOrder->getOrderShippingAddressCountryName()
            );
        $oAddress->id_customer = $oCustomer->id;
        $oAddress->id_state
            = State::getIdByIso(
                $oBeezupOrder->getOrderShippingAddressStateOrRegion(),
                $oAddress->id_country
            );
        $oAddress->company = Tools::substr(
            preg_replace(
                '/[<>={}]+/',
                ' ',
                $oBeezupOrder->getOrderShippingCompanyName()
            ),
            0,
            64
        );
        $oAddress->phone = Tools::substr(
            preg_replace(
                '/[^+0-9. ()-]+/',
                ' ',
                $oBeezupOrder->getOrderShippingPhone()
            ),
            0,
            32
        );
        $oAddress->phone_mobile = Tools::substr(
            preg_replace(
                '/[^+0-9. ()-]+/',
                ' ',
                $oBeezupOrder->getOrderShippingMobilePhone()
            ),
            0,
            32
        );
        $oAddress->alias = md5(
            'DELIVERY '.$oBeezupOrder->getBeezupOrderUUID()
            .$this->getAddressHash($oAddress)
        );

        return $oAddress;
    }

    /**
     * Process address.
     * If exists adress with the same data, it will use it, otherwise it will
     * create a new address.
     *
     * @param Customer $oCustomer
     * @param Address  $oAddress
     *
     * @return multitype:multitype: Ambigous <NULL, Address>
     *                              |multitype:multitype: Address |multitype:NULL string
     */
    protected function processAddress(Customer $oCustomer, Address $oAddress)
    {
        $mExistingAddress = $this->findExistingAddress($oCustomer, $oAddress);
        if ($mExistingAddress) {
            return $mExistingAddress;
        }
        $mValidation = $oAddress->validateFields(false, true);
        if ($mValidation === true && $oAddress->save()) {
            return $oAddress;
        }
        if ($mValidation === true) {
            $this->addLog(
                'ERROR',
                $this->l('BeezUP error: Unable to save address')
            );
        } else {
            $this->addLog('ERROR', $this->l('BeezUP error').': '.$mValidation);
        }

        return;
    }

    protected function loadFirstNamesList()
    {
        if ($this->aFirstNamesCache === null) {
            $this->aFirstNamesCache = array_flip(
                array_map(
                    'trim',
                    (array)file(
                        dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'
                        .DIRECTORY_SEPARATOR.'firstnames.txt'
                    )
                )
            );
        }
    }

    /**
     * @todo add dictionary matching or positioning by marketplace
     *
     * @param unknown_type $sName
     *
     * @return Ambigous <string, multitype:>
     */
    protected function getFirstAndLastName($sName)
    {
        if ($this->aFirstNamesCache === null) {
            $this->loadFirstNamesList();
        }
        $aName = explode(' ', $sName);
        $aNameResult = array();
        $aFirstNameResult = array();
        $nLast = 0;
        foreach (array_filter(array_map('trim', $aName)) as $sElement) {
            $sCmp = Tools::strtolower($sElement);
            if ((isset($this->aFirstNamesCache[$sCmp])
                    && ($nLast != 2
                        || ($nLast == 2 && empty($aFirstNameResult))))
                || ($nLast == 1 && !empty($aNameResult))
            ) {
                $aFirstNameResult[] = $sElement;
                $nLast = 1;
            } else {
                $aNameResult[] = $sElement;
                $nLast = 2;
            }
        }
        if (!empty($aFirstNameResult) && !empty($aNameResult)) {
            $aName = array(
                0 => implode(' ', $aFirstNameResult),
                1 => implode(' ', $aNameResult),
            );
        } else {
            $aName = explode(' ', $sName, 2);
            if (count($aName) === 1 || empty($aName[1])) {
                $aName[1] = $aName[0];
                $aName[0] = '---';
            }
            if (empty($aName[1])) {
                $aName[1] = '---';
            }
        }

        $sPattern = (defined('PREG_BAD_UTF8_OFFSET')
            ? '/[0-9!<>,;?=+()@#"°{}_$%:]+/u'
            : preg_replace(
                '/\\\[px]\{[a-z]{1,2}\}|(\/[a-z]*)u([a-z]*)$/i',
                '$1$2',
                '/[0-9!<>,;?=+()@#"°{}_$%:]+/u'
            ));

        foreach ($aName as $nKey => $sValue) {
            $aName[$nKey] = Tools::substr(
                preg_replace(
                    $sPattern,
                    '',
                    Tools::stripslashes($sValue)
                ),
                0,
                32
            );
        }

        return $aName;
    }

    // BEEZUP / PRESTA HELPERS
    protected function getAssociatedPsOrder(BeezupOMOrderResult $oBeezupOrder)
    {
        $beezup_order_id = $oBeezupOrder->getBeezupOrderUUID();
        $BeezupDbOrder = Db::getInstance()->getRow(
            "select * from "._DB_PREFIX_
            ."beezup_order where beezup_order_uuid = '".pSQL($beezup_order_id)."'"
        );
        if (empty($BeezupDbOrder)) {
            throw new Exception(
                sprintf(
                    $this->l(
                        'BeezUP: Invalid Prestashop beezup order id %d for BeezUP order %s'
                    ),
                    $oBeezupOrder->getOrderMerchantOrderId(),
                    $oBeezupOrder->getBeezupOrderUUID()
                )
            );
        }
        $orderId = (int)$BeezupDbOrder['id_order'];
        $nPsOrderId = (int)$oBeezupOrder->getOrderMerchantOrderId();
        if ($nPsOrderId <= 0) {
            throw new Exception(
                sprintf(
                    $this->l(
                        'BeezUP: Invalid Prestashop order id %d for BeezUP order %s'
                    ),
                    $oBeezupOrder->getOrderMerchantOrderId(),
                    $oBeezupOrder->getBeezupOrderUUID()
                )
            );
        }
        if ($orderId != $nPsOrderId) {
            $nPsOrderId = $orderId;
        }
        $oPsOrder = new Order($nPsOrderId);
        if (!Validate::isLoadedObject($oPsOrder)) {
            throw new Exception(
                sprintf(
                    $this->l(
                        'BeezUP: Unable to load Prestashop order id %d for BeezUP order %s'
                    ),
                    $oBeezupOrder->getOrderMerchantOrderId(),
                    $oBeezupOrder->getBeezupOrderUUID()
                )
            );
        }

        return $oPsOrder;
    }

    protected function shouldSkipImport(BeezupOMOrderResult $oBeezupOrder)
    {
        if (!$oBeezupOrder) {
            return true;
        }
        $blnDays = Configuration::get("BEEZUP_OM_IMPORT_FILTER_DAYS_ON");
        $days = Configuration::get("BEEZUP_OM_IMPORT_FILTER_DAYS");
        $status = Configuration::get("BEEZUP_OM_IMPORT_FILTER_STATUS");
        $status = explode(",", $status);
        $oStatus = $oBeezupOrder->getOrderStatusBeezUPOrderStatus();
        if (!in_array($oStatus, $status)) {
            return true;
        }
        if ($blnDays == 1) {
            $date1 = $oBeezupOrder->getOrderPurchaseUtcDate();
            $date2 = new DateTime("now", new DateTimeZone('UTC'));
            $diff = $date2->diff($date1);
            if ((int)$diff->d >= $days) {
                return true;
            }
        }
        $importFba = Configuration::get("BEEZUP_OM_IMPORT_FBA");
        $importCdiscount = Configuration::get("BEEZUP_OM_IMPORT_CDISCOUNT");
        if ($oBeezupOrder->getOrderMarketplaceChannel() == "AFN" && $importFba == 1) {
            return true;
        } elseif ($oBeezupOrder->getOrderMarketplaceChannel() == "Cdiscount Fulfilment" && $importCdiscount == 1) {
            return true;
        }
        return false;
    }

    protected function updateOrderTotals(
        Order $oOrder,
        BeezupOMOrderResult $oBeezupOrder
    ) {
        $oOrder->total_shipping = (float)$oBeezupOrder->getOrderShippingPrice();
        $oOrder->total_shipping_tax_incl = $oOrder->total_shipping;
        // @todo use presta tax rate
        $fCarrierTaxRate = $this->getCarrierTaxRate($oOrder, $oBeezupOrder);
        $oOrder->total_shipping_tax_excl = round(
            (100
                * $oOrder->total_shipping_tax_incl / (100 + $fCarrierTaxRate)),
            6
        );
        $oOrder->total_wrapping = $this->getOrderProcessingFee($oBeezupOrder);
        $oOrder->total_wrapping_tax_incl = $oOrder->total_wrapping;
        $oOrder->total_wrapping_tax_excl = round(
            (100
                * $oOrder->total_wrapping_tax_incl / (100 + $fCarrierTaxRate)),
            6
        );
        $oOrder->total_paid = round(
            $oOrder->total_shipping
            + $oOrder->total_products_wt + $oOrder->total_wrapping,
            6
        );
        $oOrder->total_paid_tax_incl = round(
            $oOrder->total_shipping_tax_incl
            + $oOrder->total_products_wt + $oOrder->total_wrapping_tax_incl,
            6
        );
        $oOrder->total_paid_tax_excl = round(
            $oOrder->total_shipping_tax_excl
            + $oOrder->total_products + $oOrder->total_wrapping_tax_excl,
            6
        );
        $oOrder->total_paid_real = $oOrder->total_paid_tax_incl;
    }

    protected function getCarrierTaxRate(
        Order $oOrder,
        BeezupOMOrderResult $oBeezupOrder
    ) {
        $fResult = (float)$oBeezupOrder->getOrderShippingShippingTax();
        if (Validate::isLoadedObject($oOrder)) {
            $oCarrier = new Carrier($oOrder->id_carrier);
            $oAddress = new Address($oOrder->id_address_delivery);
            if (Validate::isLoadedObject($oCarrier)
                && Validate::isLoadedObject($oAddress)
            ) {
                $fResult = (float)$oCarrier->getTaxesRate($oAddress);
            }
        }

        return $fResult;
    }

    protected function updateOrderDetails(Order $oOrder, $aProductsAdded)
    {
        // readjusting price
        $fTotalProducts = 0;
        $fTotalProductsWithTax = 0;
        $oDeliveryAddress = new Address($oOrder->id_address_delivery);
        foreach ($oOrder->getProductsDetail() as $aProductDetail) {
            foreach ($aProductsAdded as $aProduct) {
                if ((int)$aProductDetail['product_id']
                    === (int)$aProduct['ids'][0]
                    && (int)$aProductDetail['product_attribute_id']
                    === (int)$aProduct['ids'][1]
                ) {
                    $oDetail
                        = new OrderDetail($aProductDetail['id_order_detail']);
                    $oItem = $aProduct['item'];
                    $oProduct = new Product($aProduct['ids'][0]);
                    $fTaxRate = $oProduct->getTaxesRate($oDeliveryAddress);
                    $oDetail->product_quantity = $oItem->getOrderItemQuantity();
                    $oDetail->product_price = $oItem->getOrderItemItemPrice();
                    $oDetail->total_price_tax_incl
                        = $oItem->getOrderItemTotalPrice();
                    $oDetail->total_price_tax_excl = round(
                        (100
                            * $oDetail->total_price_tax_incl) / (100
                            + $fTaxRate),
                        6
                    );
                    $oDetail->unit_price_tax_incl = $oDetail->product_price;
                    $oDetail->unit_price_tax_excl = round(
                        (100
                            * $oDetail->product_price / (100 + $fTaxRate)),
                        6
                    ); // Validator::isPrice
                    $fTotalProducts += $oDetail->total_price_tax_excl;
                    $fTotalProductsWithTax += $oDetail->total_price_tax_incl;
                    $oDetail->update();
                }
            }
        }
        $oOrder->total_products_wt = $fTotalProductsWithTax;
        $oOrder->total_products = $fTotalProducts;
    }

    // PAYMENTS
    protected function updateInvoiceDetails(Order $oOrder)
    {
        $aInvoices = ObjectModel::hydrateCollection(
            'OrderInvoice',
            Db::getInstance()->executeS(
                'SELECT oi.*	FROM `'._DB_PREFIX_
                .'order_invoice` oi WHERE oi.id_order = '.(int)$oOrder->id
            )
        );
        foreach ($aInvoices as $nIndex => $oInvoice) {
            if ($nIndex === 0) {
                $oInvoice->total_paid_tax_excl = $oOrder->total_paid_tax_excl;
                $oInvoice->total_paid_tax_incl = $oOrder->total_paid_tax_incl;
                $oInvoice->total_products = $oOrder->total_products;
                $oInvoice->total_products_wt = $oOrder->total_products_wt;
                $oInvoice->total_shipping_tax_excl
                    = $oOrder->total_shipping_tax_excl;
                $oInvoice->total_shipping_tax_incl
                    = $oOrder->total_shipping_tax_incl;
                $oInvoice->total_wrapping_tax_excl
                    = $oOrder->total_wrapping_tax_excl;
                $oInvoice->total_wrapping_tax_incl
                    = $oOrder->total_wrapping_tax_incl;
                $oInvoice->update();
            } else {
                $oInvoice->delete();
            }
        }
    }

    protected function updatePaymentDetails(Order $oOrder)
    {
        // @todo verify since which version OrderPayment::getByOrderReference
        // returns collection
        $aPayments = OrderPayment::getByOrderReference($oOrder->reference);
        if ($aPayments) {
            $this->addLog(
                'INFO',
                sprintf(
                    'BeezUP: processing payments, %d payments found for order %d',
                    count($aPayments),
                    $oOrder->id
                )
            );
            foreach (array_values($aPayments) as $nIndex => $oOrderPayment) {
                if ($nIndex === 0) {
                    if ($oOrderPayment->amount !== $oOrder->total_paid_real
                        || $oOrderPayment->payment_method != $oOrder->payment
                    ) {
                        // $this->addLog ( sprintf ( 'BeezUP: processing
                        // payments, updating first payment %d with %f (was: %f)
                        // for order %d', $oOrderPayment->id,
                        // $oOrder->total_paid_real, $oOrderPayment->amount,
                        // $oOrder->id) );
                        $oOrderPayment->amount = $oOrder->total_paid_real;
                        $oOrderPayment->payment_method = $oOrder->payment;
                        $oOrderPayment->id_currency = $oOrder->id_currency;
                        $bUpdateResult = $oOrderPayment->update();
                        // $this->addLog ( $bUpdateResult ? 'UPDATE OK' :
                        // 'UPDATE FAILED' );
                    } else {// $this->addLog ( sprintf ( 'BeezUP: processing
                        // payments, first payment %d with %f for order %d not
                        // changed', $oOrderPayment->id, $oOrderPayment->amount,
                        // $oOrder->id) );
                    }
                } else {
                    // @todo see if necessary
                    // $this->addLog ( sprintf ( 'BeezUP: processing payments,
                    // deleting payment %d for order %d', $oOrderPayment->id,
                    // $oOrder->id ) );
                    $oOrderPayment->delete();
                    $sQuery
                        = sprintf(
                            'DELETE FROM %sorder_invoice_payment WHERE id_order=%d AND id_order_payment = %d',
                            _DB_PREFIX_,
                            (int)$oOrder->id,
                            (int)$oOrderPayment->id
                        );
                    Db::getInstance()->execute($sQuery);
                }
            }
        } else {
            $this->addLog(
                'INFO',
                sprintf(
                    'BeezUP: processing payments, creating new payment for montant %f for order %d',
                    $oOrder->total_paid_real,
                    $oOrder->id
                )
            );
            $oOrderPayment = new OrderPayment();
            $oOrderPayment->id_order = $oOrder->id;
            $oOrderPayment->amount = $oOrder->total_paid_real;
            $oOrderPayment->payment_method = $oOrder->payment;
            $oOrderPayment->id_currency = $oOrder->id_currency;
            $oOrderPayment->add();
        }
    }

    // CARRIER
    protected function updateCarrierDetails(Order $oOrder)
    {
        $aCarriers = Db::getInstance()->executeS(
            '
			SELECT `id_order_carrier`, `id_carrier`
			FROM `'._DB_PREFIX_.'order_carrier`
			WHERE `id_order` = '.(int)$oOrder->id
        );
        $bCarrierUpdated = false;
        if ($aCarriers) {
            foreach ($aCarriers as $aCarrier) {
                if (!$bCarrierUpdated
                    && (int)$aCarrier['id_carrier'] === (int)$oOrder->id_carrier
                ) {
                    $oOrderCarrier
                        = new OrderCarrier((int)$aCarrier['id_order_carrier']);
                    $oOrderCarrier->shipping_cost_tax_excl
                        = $oOrder->total_shipping_tax_excl;
                    $oOrderCarrier->shipping_cost_tax_incl
                        = $oOrder->total_shipping_tax_incl;
                    $bCarrierUpdated = $oOrderCarrier->update();
                    if (!$bCarrierUpdated) {
                        $oOrderCarrier->delete();
                    }
                } else {
                    $oOrderCarrier
                        = new OrderCarrier((int)$aCarrier['id_order_carrier']);
                    $oOrderCarrier->delete();
                } // if
            } // foreach
        } // if
        if (!$bCarrierUpdated) {
            $this->addOrderCarrier($oOrder);
        }
    }

    protected function addOrderCarrier(Order $oOrder)
    {
        $oOrderCarrier = new OrderCarrier();
        $oOrderCarrier->id_order = (int)$oOrder->id;
        $oOrderCarrier->id_carrier = (int)$oOrder->id_carrier;
        $oOrderCarrier->shipping_cost_tax_excl
            = $oOrder->total_shipping_tax_excl;
        $oOrderCarrier->shipping_cost_tax_incl
            = $oOrder->total_shipping_tax_incl;

        return $oOrderCarrier->add();
    }

    protected function getCarrierId(
        $sMarketplaceTechnicalCode = null,
        $sCarrierCode = null
    ) {
        if ($sMarketplaceTechnicalCode && $sCarrierCode) {
            $aCarriersMapping = $this->getCarriersMapping();
            $sMCIdx = md5(
                Tools::strtoupper(
                    $sMarketplaceTechnicalCode
                    .$sCarrierCode
                )
            );
            if (is_array($aCarriersMapping)
                && isset($aCarriersMapping[$sMCIdx])
                && (int)$aCarriersMapping[$sMCIdx] > 0
            ) {
                $oCarrier
                    = Carrier::getCarrierByReference(
                        (int)$aCarriersMapping[$sMCIdx]
                    );
                if ($oCarrier && $oCarrier->id) {
                    return (int)$oCarrier->id;
                }
            }
        }

        return (int)Configuration::get('BEEZUP_OM_DEFAULT_CARRIER_ID')
            ? (int)Configuration::get('BEEZUP_OM_DEFAULT_CARRIER_ID')
            : (int)Configuration::get('PS_CARRIER_DEFAULT');
    }

    protected function getPaymentModule()
    {
        require_once 'BeezupOMPayment.php';
        $oPaymentModule = new BeezupOMPayment();

        return $oPaymentModule;
    }

    protected function getOrderProcessingFee(BeezupOMOrderResult $oBeezupOrder)
    {
        $fResult = 0;
        foreach ($oBeezupOrder->getOrderItems() as $oItem) {
            if ($oItem->getOrderItemOrderItemType() !== 'Product') {
                $fResult += (float)$oItem->getOrderItemTotalPrice();
            }
        }

        return $fResult;
    }

    /**
     * Tests.
     *
     * @param BeezupOMOrderResult $oBeezupOrder
     */
    protected function prescanOrder(BeezupOMOrderResult $oBeezupOrder)
    {
        $aShops = array();
        $aErrors = array();
        foreach ($oBeezupOrder->getOrderItems() as $oItem) {
            if ($oItem->getOrderItemOrderItemType() !== 'Product') {
                continue;
            }
            $nShopId = BeezupOMStoreMapperFactory::create()
                ->getStoreMappingForStore($oItem->getOrderItemBeezUPStoreId());
            if (!$nShopId) {
                $aErrors[]
                    = sprintf(
                        $this->l('BeezUP: No mapping for store %s'),
                        $oItem->getOrderItemBeezUPStoreId()
                    );
                continue;
            }
            $aShops[] = $nShopId;
        }
        $aShops = array_unique(array_filter($aShops), SORT_REGULAR);
        if (count($aShops) !== 1 && count($aShops) > 0) {
            $nFirstShopId = reset($aShops);
            if (!(is_integer($nFirstShopId) || is_string($nFirstShopId))) {
                $this->addLog(
                    'INFO',
                    sprintf('shop id is of type %s', gettype($nFirstShopId))
                );
            }
            $aSharedOrdersShops = Shop::getSharedShops(
                $nFirstShopId,
                Shop::SHARE_ORDER
            );
            $aSharedCustomerShops = Shop::getSharedShops(
                $nFirstShopId,
                Shop::SHARE_CUSTOMER
            );
            $aSharedStockShops = Shop::getSharedShops(
                $nFirstShopId,
                Shop::SHARE_STOCK
            );
            foreach ($aShops as $nShopId) {
                if (!in_array($nShopId, $aSharedOrdersShops)) {
                    $aErrors[]
                        = sprintf(
                            $this->l(
                                'BeezUP: Shop %d is not sharing orders with %d'
                            ),
                            $nShopId,
                            $nFirstShopId
                        );
                }
                if (!in_array($nShopId, $aSharedCustomerShops)) {
                    $aErrors[]
                        = sprintf(
                            $this->l(
                                'BeezUP: Shop %d is not sharing customers with %d'
                            ),
                            $nShopId,
                            $nFirstShopId
                        );
                }
                if (!in_array($nShopId, $aSharedStockShops)) {
                    $aErrors[]
                        = sprintf(
                            $this->l(
                                'BeezUP: Shop %d is not sharing stock with %d'
                            ),
                            $nShopId,
                            $nFirstShopId
                        );
                }
            }
            // @todo what if not
        }
        $aErrors = array_unique($aErrors, SORT_REGULAR);
        foreach ($aErrors as $sMessage) {
            $this->addLog('ERROR', $this->l('BeezUP error').' : '.$sMessage);
        }

        if (count($aErrors) === 0) {
            $shop = new Shop((int)$aShops[0]);
            Context::getContext()->shop = $shop;
            Shop::setContext(Shop::CONTEXT_SHOP, $shop->id);
        }

        return count($aErrors) === 0;
    }
    // PRESTASHOP ORDER MANAGEMENT

    /**
     * Adds Prestashop order status.
     *
     * @param Order $oPsOrder
     *                         Prestashop order
     * @param int   $nStatusId
     *                         New status ID
     *
     * @return bool True if new status added, false on error or
     */
    protected function addStatus(Order $oPsOrder, $nStatusId)
    {
        if ($nStatusId != $oPsOrder->current_state) {
            $bOrderStatusId = BeezupOrderStatus::statusExists($oPsOrder->id);
            if ($bOrderStatusId) {
                $bOrderStatus = new BeezupOrderStatus($bOrderStatusId);
                $orderStatusFilter
                    = Configuration::get("BEEZUP_ORDER_STATUS_FILTER");
                if ($bOrderStatus->id_order_status != $oPsOrder->current_state
                    && $bOrderStatus->id_order_status == $nStatusId
                    && $orderStatusFilter == 1
                ) {
                    return true;
                }
                $bOrderStatus->id_order_status = $nStatusId;
                $bOrderStatus->update();
            } else {
                $bOrderStatus = new BeezupOrderStatus();
                $bOrderStatus->id_order = $oPsOrder->id;
                $bOrderStatus->id_order_status = $nStatusId;
                $bOrderStatus->add();
            }
            $oHistory = new OrderHistory();
            $oHistory->id_order = $oPsOrder->id;
            $oHistory->changeIdOrderState($nStatusId, $oPsOrder, true);
            $oHistory->addWithemail(true, array());
            $oPsOrder->current_state = $nStatusId;
            if ($oHistory->save() && $oPsOrder->update()) {
                $this->addMessage($oPsOrder, 'BeezUP : Order state changed');

                return true;
            } // if
        } // if

        return false;
    }

    /**
     * Adds prestashop order message.
     *
     * @param Order  $oPrestaOrder
     * @param string $sMessage
     *
     * @return bool True on success
     */
    protected function addMessage(Order $oPsOrder, $sMessage)
    {
        if (!Validate::isCleanHtml($sMessage)) {
            return false;
        }
        $oMessage = new Message();
        $oMessage->id_order = $oPsOrder->id;
        $oMessage->private = true;
        $oMessage->message = $sMessage;

        return $oMessage->add();
    }

    // CACHED ORDER MANAGEMENT
    public function getCachedBeezupOrderResponse(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $oCachedOrder = BeezupOrder::fromBeezupOrderId($oOrderIdentifier);
        if ($oCachedOrder && Validate::isLoadedObject($oCachedOrder)
            && $oCachedOrder->getBeezupOrderResult()
        ) {
            $oBeezupResponse = new BeezupOMOrderResponse();
            $oBeezupResponse->setResult($oCachedOrder->getBeezupOrderResult())
                ->setInfo($oCachedOrder->getBeezupOrderInfos());
            if ($oCachedOrder->etag) {
                $oBeezupResponse->setEtag($oCachedOrder->etag);
            }

            return $oBeezupResponse;
        }

        return;
    }

    public function getCachedBeezupOrderResult(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $oCachedOrder = BeezupOrder::fromBeezupOrderId($oOrderIdentifier);
        if ($oCachedOrder && Validate::isLoadedObject($oCachedOrder)) {
            return $oCachedOrder->getBeezupOrderResult();
        }

        return;
    }

    public function getCachedBeezupOrderInfo(
        BeezupOMOrderIdentifier $oOrderIdentifier
    ) {
        $oCachedOrder = BeezupOrder::fromBeezupOrderId($oOrderIdentifier);
        if ($oCachedOrder && Validate::isLoadedObject($oCachedOrder)) {
            return $oCachedOrder->getBeezupOrderInfos();
        }

        return;
    }

    /**
     * PriceMinister sends one line per product, even if it's the same product
     * To deal with it, we need to reuse.
     *
     * @param BeezupOMOrderResult $oBeezupOrder
     *
     * @return array of BeezupOMOrderItem
     */
    protected function getOrderItemsCollection(
        BeezupOMOrderResult $oBeezupOrder
    ) {
        $aResult = array();

        foreach ($oBeezupOrder->getOrderItems() as $oOrderItem) {
            if ($oOrderItem->getOrderItemQuantity() < 1) {
                continue;
            }

            $sProductId = $oOrderItem->getOrderItemMerchantProductId();
            // just in case if OrderItem_MerchantProductId is empty
            if (empty($sProductId)) {
                $aResult[] = clone $oOrderItem;
                continue;
            } // if
            if (!isset($aResult[$sProductId])) {
                $aResult[$sProductId] = clone $oOrderItem;
            } else {// if price is not the same, we panic
                if ($aResult[$sProductId]->getOrderItemItemPrice()
                    != $oOrderItem->getOrderItemItemPrice()
                ) {
                    throw new Exception(
                        sprintf(
                            'Unable to merge items %s and %s, item price do not match (%s, %s)',
                            $aResult[$sProductId]->getBeezUPOrderItemId(),
                            $oOrderItem->getBeezUPOrderItemId(),
                            $aResult[$sProductId]->getOrderItemItemPrice(),
                            $oOrderItem->getOrderItemItemPrice()
                        )
                    );
                }
                $nCurrentQuantity
                    = $aResult[$sProductId]->getOrderItemQuantity();
                $fCurrentTotalPrice
                    = $aResult[$sProductId]->getOrderItemTotalPrice();
                $aResult[$sProductId]->setOrderItemQuantity(
                    $nCurrentQuantity
                    + $oOrderItem->getOrderItemQuantity()
                );
                $aResult[$sProductId]->setOrderItemTotalPrice(
                    $fCurrentTotalPrice
                    + $oOrderItem->getOrderItemTotalPrice()
                );
            } // if
        } // foreach

        return $aResult;
    }

    protected function isMultipleProductStockFilterActive(BeezupOMOrderResult $oBeezupOrder)
    {
        $multipleStockFilter = Configuration::get('BEEZUP_OM_MULTIPLE_STOCK_FILTER');
        if ($multipleStockFilter == 0) {
            return false;
        }
        $blnNoStock = false;
        $blnStock = false;
        $aOrderItems = $this->getOrderItemsCollection($oBeezupOrder);
        foreach ($aOrderItems as $oItem) {
            $qty = (int)$oItem->getOrderItemQuantity();
            if ($qty < 1) {
                continue;
            }
            if ($oItem && $this->isItemMatchingContext($oItem)
                && $oItem->getOrderItemOrderItemType() == 'Product'
            ) {
                $aProducts = $this->findProduct($oItem, $oBeezupOrder->getMarketplaceBusinessCode());
            }
            $nFound = count($aProducts);
            if ($nFound == 0) {
                return false;
            }
            list(
                $nProductId, $nProductDeclinationId
                )
                = reset($aProducts);
            if ($nFound > 1) {
                return false;
            }
            $idProduct = $nProductId;
            $idProductAttribute = $nProductDeclinationId;
            $id_shop = (int)Context::getContext()->shop->id;
            $currentStock = StockAvailable::getQuantityAvailableByProduct($idProduct, $idProductAttribute, $id_shop);
            if ($currentStock == 0) {
                $blnNoStock = true;
            } else {
                $blnStock = true;
            }
        }
        return $blnNoStock && $blnStock;
    }

    protected function copyProductsToCart(
        BeezupOMOrderResult $oBeezupOrder,
        Cart $oCart,
        $blnResetStock = false
    ) {
        $aInsertedProducts = array();
        $multipleStockFilter = $this->isMultipleProductStockFilterActive($oBeezupOrder);
        $aOrderItems = $this->getOrderItemsCollection($oBeezupOrder);
        foreach ($aOrderItems as $oItem) {
            $qty = (int)$oItem->getOrderItemQuantity();
            if ($qty < 1) {
                continue;
            }
            try {
                if ($oItem && $this->isItemMatchingContext($oItem)
                    && $oItem->getOrderItemOrderItemType() == 'Product'
                ) {
                    $aProducts = $this->findProduct(
                        $oItem,
                        $oBeezupOrder->getMarketplaceBusinessCode()
                    );
                    $nFound = count($aProducts);
                    if ($nFound == 0) {
                        $this->addLog(
                            'ERROR',
                            sprintf(
                                $this->l(
                                    'BeezUP error: Unable to find matching for product %s %s'
                                ),
                                $oItem->getBeezUPOrderItemId(),
                                $oItem->getOrderItemMerchantImportedProductId()
                            )
                        );

                        return false;
                    }
                    list(
                        $nProductId, $nProductDeclinationId
                        )
                        = reset($aProducts);
                    if ($nFound > 1) {
                        $this->addLog(
                            'ERROR',
                            sprintf(
                                $this->l(
                                    'BeezUP: %d possible matches found for product %s, choosed %d:%d'
                                ),
                                $nFound,
                                $oItem->getBeezUPOrderItemId(),
                                $nProductId,
                                $nProductDeclinationId
                            )
                        );
                    }

                    if ($blnResetStock) {
                        $qty = (int)$oItem->getOrderItemQuantity();
                        if ($qty < 1) {
                            continue;
                        }
                        $idProduct = $nProductId;
                        $idProductAttribute = $nProductDeclinationId;
                        $id_shop = (int)Context::getContext()->shop->id;
                        $currentStock
                            = StockAvailable::getQuantityAvailableByProduct(
                                $idProduct,
                                $idProductAttribute,
                                $id_shop
                            );
                        $stock = (float)$currentStock + (float)$qty;
                        StockAvailable::setQuantity(
                            $idProduct,
                            $idProductAttribute,
                            $stock,
                            $id_shop
                        );
                        $this->stockReset[] = array(
                            "id_product"           => $idProduct,
                            "id_product_attribute" => $idProductAttribute,
                            "id_shop"              => $id_shop,
                            "stock"                => (float)$currentStock,
                        );
                    }


                    if ($this->addProductToCart(
                        $oCart,
                        $oItem,
                        $nProductId,
                        $nProductDeclinationId,
                        $multipleStockFilter
                    )
                    ) {
                        $aInsertedProducts[] = array(
                            'item' => $oItem,
                            'ids'  => array(
                                $nProductId,
                                $nProductDeclinationId,
                            ),
                        );
                    } else {
                        $this->addLog(
                            'ERROR',
                            sprintf(
                                $this->l(
                                    'BeezUP error: Unable to add product %s (%d %d) to cart - %s'
                                ),
                                $oItem->getBeezUPOrderItemId(),
                                $nProductId,
                                $nProductDeclinationId,
                                'addProductToCart'
                            )
                        );

                        return false;
                    }
                } // if
            } catch (Exception $oException) {
                $this->addLog(
                    'ERROR',
                    $this->l('BeezUP error').' : '.$oException->getMessage()
                );
            }
        }

        return $aInsertedProducts;
    }

    protected function getProductIdentityMapper()
    {
        if ($this->oProductIdentityMapper === null) {
            $this->oProductIdentityMapper
                = BeezupOMProductIdentityMapperFactory::create(
                    $this->isDebugModeActivated()
                );
        }

        return $this->oProductIdentityMapper;
    }

    /**
     * @param BeezupOMOrderItem $oItem
     *
     * @return array
     */
    protected function findProduct(
        BeezupOMOrderItem $oItem,
        $marketplaceBusinessCode
    ) {
        $productTemplates = Db::getInstance()->ExecuteS(
            "select * from "
            ._DB_PREFIX_."beezupom_product_template
         where marketplace = '".pSQL($marketplaceBusinessCode)."'"
        );
        $aResult = array();
        $aMappingCallbacks = $this->getProductIdentityMapper()
            ->getMappingCallbacks($oItem->getOrderItemBeezUPStoreId());
        $aSearch = array_unique(
            array_filter(
                array(
                    $oItem->getOrderItemMerchantImportedProductId(),
                    $oItem->getOrderItemMerchantProductId(),
                )
            )
        );
        foreach ($aMappingCallbacks as $oMappingCallback) {
            /* finally, we probably don't need find */
            $aFound = array();
            foreach ($aSearch as $sTerm) {
                foreach ($productTemplates as $productTemplate) {
                    if ($oMappingCallback->mappingIdentifier
                        == $productTemplate['field_type']
                    ) {
                        $sTerm = str_replace(
                            $productTemplate['search_value'],
                            $productTemplate['replace_value'],
                            $sTerm
                        );
                    }
                }
                $aFound = array_merge(
                    $aFound,
                    $oMappingCallback->findAll($sTerm)
                );
            }

            $aResult = array_merge(
                $aResult,
                array_unique($aFound, SORT_REGULAR)
            );
        }
        $aResult = array_unique($aResult, SORT_REGULAR);
        foreach ($aResult as $nKey => $aRow) {
            if ($aRow[0] == 0) {
                unset($aResult[$nKey]);
            }
        }

        return $aResult;
    }
    // tests whether item matches given context (for multiple orders)
    // @todo
    protected function isItemMatchingContext($oItem)
    {
        return true;
    }

    protected function addProductToCart(
        Cart $oCart,
        BeezupOMOrderItem $oItem,
        $nProductId,
        $nProductDeclinationId,
        $multipleStockFilter
    ) {
        // @todo Handle case of double insertion
        // @todo Handle out-of-stock
        $bResult = $oCart->updateQty(
            $oItem->getOrderItemQuantity(),
            $nProductId,
            $nProductDeclinationId,
            false,
            'up',
            0,
            null,
            false
        );
        // forcing for products out of stock
        if (!$bResult && Configuration::get('BEEZUP_OM_FORCE_CART_ADD') || $multipleStockFilter) {
            if ($oCart->containsProduct($nProductId, $nProductDeclinationId)) {
                // @todo multidelivery?
                $sQuery
                    = sprintf(
                        'UPDATE %scart_product SET quantity = %d, date_add = NOW() WHERE id_cart = %d AND id_product = %d ',
                        _DB_PREFIX_,
                        (int)$oItem->getOrderItemQuantity(),
                        (int)$nProductId,
                        (int)$oCart->id
                    );
                if ($nProductDeclinationId > 0) {
                    $sQuery .= sprintf(
                        ' AND id_product_attribute = %d',
                        (int)$nProductDeclinationId
                    );
                }
                $bResult = Db::getInstance()->execute($sQuery);
            } else {
                $bResult = Db::getInstance()->insert(
                    'cart_product',
                    array(
                        'id_product'           => (int)$nProductId,
                        'id_product_attribute' => (int)$nProductDeclinationId,
                        'id_cart'              => (int)$oCart->id,
                        'id_address_delivery'  => (int)$oCart->id_address_delivery,
                        'id_shop'              => $oCart->id_shop,
                        'quantity'             => (int)$oItem->getOrderItemQuantity(
                        ),
                        'date_add'             => date('Y-m-d H:i:s'),
                    )
                );
            } // if
        }
        $aContains = $oCart->containsProduct(
            $nProductId,
            $nProductDeclinationId
        );

        return $bResult && $aContains && is_array($aContains)
            && isset($aContains['quantity'])
            && (int)$aContains['quantity']
            >= (int)$oItem->getOrderItemQuantity();
    }

    // ORDER STATE MAPPING
    protected function getStatusMapping($sBeezupStatus)
    {
        $aStatuses = $this->getPrestaStatusMapping();

        return isset($aStatuses[$sBeezupStatus])
            ? $aStatuses[$sBeezupStatus] : null;
    }

    protected function getPrestaStatusMapping()
    {
        return json_decode(
            Configuration::get('BEEZUP_OM_STATUS_MAPPING'),
            true
        );
    }

    protected function getCarriersMapping()
    {
        return json_decode(
            Configuration::get('BEEZUP_OM_CARRIERS_MAPPING'),
            true
        );
    }

    protected function addLog($type, $message)
    {
        BeezupomLog::addLog($type, $message, $this->beezupOrderId);
    }
}

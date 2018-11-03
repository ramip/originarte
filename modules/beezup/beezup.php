<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once(_PS_MODULE_DIR_.'/beezup/inc/BeezupConfiguration.php');
require_once(_PS_MODULE_DIR_.'/beezup/inc/BeezupField.php');

class Beezup extends Module
{

    /**
     * @var int Batch size for getproductsLight
     */
    const PRODUCT_BATCH_SIZE = 1000;

    /** @var string Memory limit allowed to module */
    const MEMORY_LIMIT = '1024M';
    /** @var integer Time limit allowed to module */
    const TIME_LIMIT = 900;

    /** @var BeezupTrackerAbstract Used tracker implementation */
    protected $_tracker;

    protected $marketChannelFilters
        = array(
            array("value" => 1, "name" => "All Orders"),
            array("value" => "AFN", "name" => "Amazon FBA"),
            array(
                "value" => "Cdiscount Fulfilment",
                "name"  => "Cdiscount Fulfilment",
            ),
        );
    /** @var array Configuration keys */
    protected $_confKeys
        = array(
            'BEEZUP_TRACKER_ACTIVE',
            'BEEZUP_TRACKER_URL',
            'BEEZUP_TRACKER_STORE_IDS',
            'BEEZUP_TRACKER_VALIDATE_STATE', // 0:New Order / 1:Delivered
            'BEEZUP_TRACKER_PRICE',
            'BEEZUP_COUNTRY',
            'BEEZUP_SITE_ADDRESS',
            'BEEZUP_DEBUG_MODE',
            'BEEZUP_USE_CACHE',
            'BEEZUP_CACHE_VALIDITY_DAYS',
            'BEEZUP_CACHE_VALIDITY_HOURS',
            'BEEZUP_CACHE_VALIDITY_MINUTES',
            'BEEZUP_USE_CRON',
            'BEEZUP_CRON_HOURS',
            'BEEZUP_CRON_MINUTES',
            'BEEZUP_ALL_SHOPS',
            'BEEZUP_NEW_PRODUCT_ID_SYSTEM',
            'BEEZUP_CATEGORY_DEEPEST',
            'BEEZUP_MEMORY_LIMIT',
            'BEEZUP_TIME_LIMIT',
            'BEEZUP_BATCH_SIZE',
            'BEEZUP_OM_USER_ID',
            'BEEZUP_OM_API_TOKEN',
            'BEEZUP_OM_SYNC_TIMEOUT',
            'BEEZUP_OM_LAST_SYNCHRONIZATION',
            'BEEZUP_OM_STATUS_MAPPING',
            'BEEZUP_OM_STORES_MAPPING',
            'BEEZUP_OM_ID_FIELD_MAPPING',
            'BEEZUP_OM_CARRIERS_MAPPING',
            'BEEZUP_OM_DEFAULT_CARRIER_ID',
            'BEEZUP_OM_FORCE_CART_ADD',
            'BEEZUP_OM_TEST_MODE',
            'BEEZUP_OM_TOLERANCE',
            'BEEZUP_OM_UPDATE_ACTIVE',
            'PS_BEEZUP_CARRIER_MAP_UP',
            'PS_BEEZUP_ENABLE_CATEGORY_FILTER',
            'PS_BEEZUP_SELECTED_CATEGORIES',
            'PS_BEEZUP_CARRIERS_FEED',
            'PS_BEEZUP_FEED_CONCURRENT_CALL',
            'BEEZUP_MARKETCHANNEL_FILTERS',
            'BEEZUP_OM_IMPORT_FILTER_DAYS',
            'BEEZUP_OM_IMPORT_FILTER_STATUS',
            'BEEZUP_OM_IMPORT_FILTER_DAYS_ON',
            'BEEZUP_ORDER_STATUS_FILTER',
            'BEEZUP_OM_IMPORT_FBA',
            'BEEZUP_OM_IMPORT_CDISCOUNT',
            'BEEZUP_OM_MULTIPLE_STOCK_FILTER',
            'BEEZUP_OM_CLEAN_LOG_DAYS',
            'BEEZUP_OM_DEBUG_LOGS'
        );

    /**
     * Utility class for everything concerning Order Management
     *
     * @var BeezupOMController
     */
    protected $om_controller = null;

    /** @var array Configuration values */
    public $_conf = array();

    /** @var integer number of products inserted in xml (combinations included) */
    protected $_nbProducts;
    /** @var integer number of products inserted in xml (combinations excluded) */
    protected $_nbRealProducts;

    /** @var json var used for update OM in hook updateOrderStatus to get the carrier exceptions */
    private $_jsonCarrierMap;

    protected $_log_id = null;

    public $bootstrap = false;

    /**
     * Module constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = 'beezup';
        $this->tab = 'smart_shopping';
        $this->page = 'beezup';
        $this->version = '3.6.1';
        $this->author = 'BeezUp';
        $this->module_key = "9b540b53b7c345218605008a6d6b3477";
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
        $this->_min_ps_version = '1.5.0';
        $this->bootstrap = $this->isPresta16x();
        parent::__construct();
        $cookie = Context::getContext()->cookie;

        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'BeezupGlobals.php';
        $this->initAutoInstaller();

        if (!$this->_isCompatibleVersion()) {
            $this->warning = sprintf(
                $this->__lang[Language::getIsoById($cookie->id_lang)],
                htmlentities($this->name, ENT_COMPAT, 'UTF-8'),
                htmlentities($this->_min_ps_version, ENT_COMPAT, 'UTF-8')
            );
        }

        $this->displayName = $this->l('BeezUP');
        $this->description = $this->l('Beezup export module for Prestashop');

        if ($this->active) {
            $this->loadBeezupConf();

            // Require autoloader if module is active
            require_once _PS_MODULE_DIR_.'/beezup/inc/BeezupAutoloader.php';
            BeezupAutoloader::register();
        }

        $this->defineUserIdLang();
    }

    public function cleanOMLog()
    {
        $removeLogDays = Configuration::get('BEEZUP_OM_MULTIPLE_STOCK_FILTER');
        if ((int)$removeLogDays > 0) {
            $remove = Db::getInstance()->execute("DELETE FROM `"._DB_PREFIX_."beezupom_log` where `date` < DATE_SUB(NOW(),INTERVAL " . (int)$removeLogDays . " DAY)");
        }
    }

    protected function defineUserIdLang()
    {
        if (!defined('_USER_ID_LANG_')) {
            $cookie = Context::getContext()->cookie;

            $id_lang = ($cookie instanceof Cookie
                && Tools::getIsset($cookie->id_lang)
                && $cookie->id_lang > 0) ? $cookie->id_lang
                : Configuration::get('PS_LANG_DEFAULT');

            $aLanguage = Language::getLanguage($id_lang);

            if (!$aLanguage['active']) {
                $id_lang = Configuration::get('PS_LANG_DEFAULT');
            }

            define('_USER_ID_LANG_', (int)$id_lang);
        }
    }

    protected function isPresta16x()
    {
        return version_compare(_PS_VERSION_, '1.6', 'ge');
    }

    protected function loadBeezupConf()
    {
        $this->_conf = Configuration::getMultiple($this->_confKeys);
        $this->_conf['BEEZUP_TRACKER_STORE_IDS'] = array();
        foreach (Language::getLanguages(true) as $aLanguage) {
            $nLangId = (int)$aLanguage['id_lang'];
            $this->_conf['BEEZUP_TRACKER_STORE_IDS'][$nLangId]
                = Configuration::get('BEEZUP_TRACKER_STORE_IDS', $nLangId);
        }
        $this->_conf['BEEZUP_OM_STATUS_MAPPING']
            = json_decode($this->_conf['BEEZUP_OM_STATUS_MAPPING'], true);
        if (!is_array($this->_conf['BEEZUP_OM_STATUS_MAPPING'])
            || empty($this->_conf['BEEZUP_OM_STATUS_MAPPING'])
        ) {
            $this->_conf['BEEZUP_OM_STATUS_MAPPING'] = array(
                'New'        => Configuration::get('PS_OS_PAYMENT'),
                'InProgress' => Configuration::get('PS_OS_PREPARATION'),
                'Canceled'   => Configuration::get('PS_OS_CANCELED'),
                'Shipped'    => Configuration::get('PS_OS_SHIPPING'),
                'Closed'     => Configuration::get('PS_OS_DELIVERED'),
            );
        }
        $this->_conf['BEEZUP_OM_STORES_MAPPING']
            = json_decode($this->_conf['BEEZUP_OM_STORES_MAPPING'], true);
        if (!is_array($this->_conf['BEEZUP_OM_STORES_MAPPING'])
            || empty($this->_conf['BEEZUP_OM_STORES_MAPPING'])
        ) {
            $this->_conf['BEEZUP_OM_STORES_MAPPING'] = array();
            if (defined('_PS_ADMIN_DIR_')) {
                $aStores = $this->getBeezupOMController()->getStores();
                foreach ($aStores as $sStoreId => $_) {
                    $this->_conf['BEEZUP_OM_STORES_MAPPING'][$sStoreId]
                        = Context::getContext()->shop->id;
                    ;
                } // foreach
            }
        } // if

        $this->_conf['BEEZUP_OM_CARRIERS_MAPPING']
            = json_decode($this->_conf['BEEZUP_OM_CARRIERS_MAPPING'], true);

        if (!is_array($this->_conf['BEEZUP_OM_CARRIERS_MAPPING'])) {
            $this->_conf['BEEZUP_OM_CARRIERS_MAPPING'] = array();
        }

        $this->_conf['BEEZUP_OM_ID_FIELD_MAPPING']
            = json_decode($this->_conf['BEEZUP_OM_ID_FIELD_MAPPING'], true);
        if (!is_array($this->_conf['BEEZUP_OM_ID_FIELD_MAPPING'])) {
            $this->_conf['BEEZUP_OM_ID_FIELD_MAPPING'] = array();
            if (defined('_PS_ADMIN_DIR_')) {
                if (!$aStores) {
                    $aStores = $this->getBeezupOMController()->getStores();
                }
                foreach ($aStores as $sStoreId => $_) {
                    $this->_conf['BEEZUP_OM_ID_FIELD_MAPPING'][$sStoreId]
                        = array('id');
                } // foreach
            }
        } // if
    }

    /**
     * Display administration pannel
     *
     * @return string
     */
    public function getContent()
    {
        $smarty = Context::getContext()->smarty;
        $currentIndex = AdminController::$currentIndex;


        if (Tools::isSubmit('stopXmlGeneration')) {
            $token = Tools::getValue('token');
            Configuration::updateValue('BEEZUP_XML_GENERATION_STOP', 1);
            Tools::redirectAdmin(
                $currentIndex
                .'&configure=beezup&confirm=1&token='.$token
            );
        }

        if (Configuration::get('BEEZUP_DEBUG_MODE')) {
            $display_errors = ini_get('display_errors');
            ini_set('display_errors', true);
        }

        // Disable cache
        $smarty->caching = false;

        require_once(_PS_MODULE_DIR_.'/beezup/inc/smarty/array_to_options.php');
        require_once(_PS_MODULE_DIR_.'/beezup/inc/smarty/explode.php');

        if (Configuration::get('PS_FORCE_SMARTY_2')) {
            $smarty->register_function(
                'array_to_options',
                'smarty_function_array_to_options'
            );
            $smarty->register_function('explode', 'smarty_function_explode');
        } else {
            $smarty->registerPlugin(
                'function',
                'array_to_options',
                'smarty_function_array_to_options'
            );
            $smarty->registerPlugin(
                'function',
                'explode',
                'smarty_function_explode'
            );
        }

        // Disable output compression
        if (Configuration::get('PS_HTML_THEME_COMPRESSION')) {
            if (Configuration::get('PS_FORCE_SMARTY_2')) {
                $smarty->register_outputfilter('smartyMinifyHTML');
            } else {
                $smarty->unregisterFilter('output', 'smartyMinifyHTML');
            }
        }

        $this->postProcess();

        $smarty->assign(
            array(
                'memory_test'      => $this->testMemoryLimit(),
                'memory_limit'     => @ini_get('memory_limit'),
                'memory_limit_ext' => self::MEMORY_LIMIT,
                'time_test'        => $this->testTimeLimit(),
                'time_limit'       => @ini_get('max_execution_time'),
                'time_limit_ext'   => self::TIME_LIMIT,
                'cache_file_test'  => $this->_checkWritableFile(
                    dirname(__FILE__)
                    .'/views/cache'
                ),
                'log_file_test'    => $this->_checkWritableFile(
                    dirname(__FILE__)
                    .'/views/log/log.txt'
                ),
            )
        );

        if (Tools::getIsset('confirm')) {
            $smarty->assign('update_success', (int)Tools::getValue('confirm'));
        }

        $attr_groups = array();
        $tmp_attr = AttributeGroup::getAttributesGroups(_USER_ID_LANG_);
        foreach ($tmp_attr as &$attr_gp) {
            $attr_groups[] = array(
                'value' => 'attr_'.(int)$attr_gp['id_attribute_group'],
                'name'  => $attr_gp['public_name'],
                'title' => $attr_gp['name'],
            );
        }
        unset($tmp_attr);

        $features = array();
        $tmp_feat = Feature::getFeatures(_USER_ID_LANG_);
        foreach ($tmp_feat as &$feat) {
            $features[] = array(
                'value' => 'feat_'.(int)$feat['id_feature'],
                'name'  => $feat['name'],
                'title' => $feat['name'],
            );
        }
        $om_carriers_up = Configuration::get("PS_BEEZUP_CARRIER_MAP_UP");

        $feed_carriers = Carrier::getCarriers(_USER_ID_LANG_);
        $db_carriers
            = json_decode(Configuration::get("PS_BEEZUP_CARRIERS_FEED"), true);

        foreach ($feed_carriers as $key => $f_carrier) {
            $feed_carriers[$key]['in_feed'] = false;
            $feed_carriers[$key]['feed_value'] = '';
            if (!empty($db_carriers) && isset($db_carriers[$f_carrier['id_carrier']])) {
                $feed_carriers[$key]['in_feed'] = true;
                $feed_carriers[$key]['feed_value']
                    = $db_carriers[$f_carrier['id_carrier']]['value'];
            }
        }

        $marketChannel = Configuration::get("BEEZUP_MARKETCHANNEL_FILTERS");
        $marketChannel = explode(",", $marketChannel);

        $marketChannelFilters = array();
        foreach ($this->marketChannelFilters as $filter) {
            $tmpArr = array(
                "name"   => $filter['name'],
                "value"  => $filter['value'],
                "active" => 0,
            );
            foreach ($marketChannel as $channel) {
                if ($filter['value'] == $channel) {
                    $tmpArr['active'] = 1;
                    break;
                }
            }
            $marketChannelFilters[] = $tmpArr;
        }

        $filterStatus = Configuration::get("BEEZUP_OM_IMPORT_FILTER_STATUS");
        $filterStatus = explode(",", $filterStatus);

        $smarty->assign(
            array(
                'request_uri'            => $currentIndex
                    ."&configure=beezup&token="
                    .Tools::getValue('token'),
                'module_path'            => dirname(__FILE__)
                    .DIRECTORY_SEPARATOR,
                'templates_path'         => dirname(__FILE__)
                    .DIRECTORY_SEPARATOR
                    .$this->getRelativeTemplatesPath(),
                'configuration'          => $this->getBeezupConfiguration(),
                'carriers'               => Carrier::getCarriers(
                    _USER_ID_LANG_,
                    false,
                    false,
                    false,
                    null,
                    Carrier::ALL_CARRIERS
                ),
                'zones'                  => Zone::getZones(true),
                'image_types'            => ImageType::getImagesTypes(
                    'products'
                ),
                'languages'              => Language::getLanguages(true),
                'currencies'             => Currency::getCurrencies(),
                'id_default_lang'        => _USER_ID_LANG_,
                'attribute_groups'       => $attr_groups,
                'features'               => $features,
                'beezup_conf'            => $this->_conf,
                'cron_path'              => dirname(__FILE__)
                    .DIRECTORY_SEPARATOR
                    .'_cron.php',
                'cron_log'               => dirname(__FILE__)
                    .DIRECTORY_SEPARATOR
                    .'views'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR
                    .'cron.txt',
                'log'                    => array_slice(
                    array_reverse($this->getLog()),
                    1,
                    10
                ),
                'countries'              => Country::getCountries(
                    _USER_ID_LANG_
                ),
                'order_link'             => $this->context->link->getAdminLink(
                    'AdminOrders'
                )
                    .'&vieworder&id_order=',
                'om_carriers_up'         => $om_carriers_up,
                'category_tree'          => $this->renderCategoryForm(),
                'enable_category_filter' => Configuration::get(
                    "PS_BEEZUP_ENABLE_CATEGORY_FILTER"
                ),
                'available_carriers'     => $feed_carriers,
                'marketChannelFilters'   => $marketChannelFilters,
                'importFilterStatus'     => $filterStatus,
                'importFilterDaysEnable' => Configuration::get(
                    "BEEZUP_OM_IMPORT_FILTER_DAYS_ON"
                ),
                'importFilterDays'       => Configuration::get(
                    "BEEZUP_OM_IMPORT_FILTER_DAYS"
                ),
                'orderStatusFilter'      => Configuration::get(
                    "BEEZUP_ORDER_STATUS_FILTER"
                ),
            )
        );

        $smarty->assign($this->getBeezupOMController()->getSmartyVars());

        if (Tools::getValue('action') !== 'cleanup') {
            // otherwise processed in postProcess
            $smarty->assign('cleanup_messages', $this->getCleanupMessages());
        }

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $return = $this->display(
                __FILE__,
                $this->getRelativeTemplatesPath()
                .DIRECTORY_SEPARATOR.'admin.tpl'
            );
        } else {
            $page = 'home';
            if (Tools::isSubmit('tracking')) {
                $page = 'tracking';
                $smarty->assign(
                    array(
                        'currentPage' => $page,
                    )
                );
                $return = $this->display(
                    __FILE__,
                    $this->getRelativeTemplatesPath()
                    .DIRECTORY_SEPARATOR.'tracker.tpl'
                );
            } elseif (Tools::isSubmit('om')) {
                $page = 'om';
                $smarty->assign(
                    array(
                        'currentPage' => $page,
                    )
                );
                $return = $this->display(
                    __FILE__,
                    $this->getRelativeTemplatesPath()
                    .DIRECTORY_SEPARATOR.'om.tpl'
                );
            } else {
                $smarty->assign(
                    array(
                        'currentPage' => $page,
                    )
                );
                $return = $this->display(
                    __FILE__,
                    $this->getRelativeTemplatesPath()
                    .DIRECTORY_SEPARATOR.'configuration.tpl'
                );
            }
        }
        if (Configuration::get('BEEZUP_DEBUG_MODE')) {
            ini_set('display_errors', $display_errors);
        }

        return $return;
    }


    /*
            *Category form
         */
    public function renderCategoryForm()
    {
        $tree = "";
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $index = explode(
                ',',
                Configuration::get('PS_BEEZUP_SELECTED_CATEGORIES')
            );
            $indexedCategories
                = Tools::isSubmit('categoryFilter')
                ? Tools::getValue('categoryFilter') : $index;
            $helper = new Helper();
            $tree = '<div class="margin-form">'
                .$helper->renderCategoryTree(
                    null,
                    $indexedCategories,
                    "categoryFilter"
                ).'</div>';
        } else {
            $root_category = Category::getRootCategory();
            //	$root_category = array('id_category' => $root_category->id, 'name' => $root_category->name);
            if (Tools::getValue('categoryFilter')) {
                $selected_categories = Tools::getValue('categoryFilter');
            } else {
                $selected_categories = explode(
                    ',',
                    Configuration::get('PS_BEEZUP_SELECTED_CATEGORIES')
                );
            }
            $tree = new HelperTreeCategories(
                "categoryFilter",
                "Filter Categories from Tree"
            );
            $tree->setInputName("categoryFilter");
            $tree->setSelectedCategories($selected_categories);
            $tree->setRootCategory($root_category->id);
            $tree->setUseSearch(false);
            $tree->setUseCheckBox(true);
            $tree = $tree->render();
        }

        return $tree;
    }


    /*
            * Get products filtering by categories
         */

    public function getProductsCategoryFilter(
        $id_lang,
        $start,
        $limit,
        $order_by,
        $order_way,
        $categories = false,
        $only_active = false,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }
        $front = true;
        if (!in_array(
            $context->controller->controller_type,
            array('front', 'modulefront')
        )
        ) {
            $front = false;
        }
        if (!Validate::isOrderBy($order_by)
            || !Validate::isOrderWay($order_way)
        ) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price'
            || $order_by == 'date_add'
            || $order_by == 'date_upd'
        ) {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        if ($categories && is_array($categories)) {
            $categories = implode(",",  array_map('intval', $categories));
        }
        $sql
            = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_
            .'product_lang` pl ON (p.`id_product` = pl.`id_product` '
            .Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
					LEFT JOIN `'._DB_PREFIX_
            .'supplier` s ON (s.`id_supplier` = p.`id_supplier`)'.
            ($categories ? 'LEFT JOIN `'._DB_PREFIX_
                .'category_product` c ON (c.`id_product` = p.`id_product`)'
                : '').'
					WHERE pl.`id_lang` = '.(int)$id_lang.
            ($categories ? ' AND c.`id_category` in ('.$categories.')' : '').
            ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")'
                : '').
            ($only_active ? ' AND product_shop.`active` = 1' : '').'
						ORDER BY '.(isset($order_by_prefix)
                ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '
            .pSQL($order_way).
            ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return ($rq);
    }


    /**
     * Lightweight version of Product::getProducts. Gets products in batches
     */
    public function getProductsLight($nLangId)
    {
        $aResult = array();
        $nIndex = 0;
        $nBatchSize = (int)BeezupRegistry::get('BEEZUP_BATCH_SIZE') > 0
            ? (int)BeezupRegistry::get('BEEZUP_BATCH_SIZE')
            : self::PRODUCT_BATCH_SIZE;
        do {
            $aProducts = Product::getProducts(
                $nLangId,
                $nIndex * $nBatchSize,
                $nBatchSize,
                'p.id_category_default',
                'ASC'
            );
            $nIndex++;
            foreach ($aProducts as $aProduct) {
                $aResult[] = array(
                    'id_product'          => $aProduct['id_product'],
                    'id_category_default' => $aProduct['id_category_default'],
                    'name'                => $aProduct['name'],
                );
            } // foreach
        } while ($aProducts);

        return $aResult;
    }


    /**
     * Test memory capacities
     *
     * @return bool
     */
    public function testMemoryLimit()
    {
        $from = array('G', 'M', 'K');
        $to = array('000000000', '000000', '000');

        // Check if current memory allocation is greater than required
        $currentLimit = @ini_get('memory_limit');
        $currentLimitInt = (int)str_replace($from, $to, $currentLimit);
        $requiredLimitInt = (int)str_replace($from, $to, self::MEMORY_LIMIT);

        if ($currentLimitInt >= $requiredLimitInt) {
            return true;
        }


        // Check if current memory allocation can be overwrited
        @ini_set('memory_limit', self::MEMORY_LIMIT);
        $newLimit = @ini_get('memory_limit');
        $newLimitInt = (int)str_replace($from, $to, $newLimit);

        if ($newLimitInt != $requiredLimitInt) {
            return false;
        }

        // Restore memory allocation
        @ini_set('memory_limit', $currentLimit);

        return true;
    }

    /**
     * Test timemout capacities
     *
     * @return bool
     */
    public function testTimeLimit()
    {
        $currentTimeLimit = @ini_get('max_execution_time');
        // Check if current time allocation is greater than required
        if ((int)$currentTimeLimit >= (int)self::TIME_LIMIT) {
            return true;
        }

        // Check if current time allocation can be overwrited
        @ini_set('max_execution_time', self::TIME_LIMIT);
        if ((int)@ini_get('max_execution_time') != (int)self::TIME_LIMIT) {
            return false;
        }

        // Restore time allocation
        @ini_set('max_execution_time', $currentTimeLimit);

        return true;
    }

    /**
     * Initialize autoInstaller class
     *
     * @return void
     */
    protected function initAutoInstaller()
    {
        $this->_installDbFiles = array(
            dirname(__FILE__).'/install/create.sql',
            dirname(__FILE__).'/install/insert.sql',
        );

        $this->_uninstallDbFiles = array(
            dirname(__FILE__).'/install/drop.sql',
        );


        $this->_installTabs[] = array(
            'class'  => 'AdminBeezupLog',
            'name'   => 'BeezUP Orders Log',
            'parent' => 'AdminParentOrders',

        );


        $aOrderBOViewHooks = (version_compare(_PS_VERSION_, '1.6.0.9', 'ge')
            ? array(
                'displayAdminOrderTabOrder',
                'displayAdminOrderContentOrder',
            ) : array('adminOrder'));

        $this->_installHooks = array_merge(
            array(
                'header',
                'updateOrderStatus',
                'newOrder',
                'paymentTop',
                'orderConfirmation',
                'updateCarrier',
            ),
            $aOrderBOViewHooks
        );
    }

    /**
     * Returns default local config
     *
     * @return multitype:number string multitype:
     */
    protected function getDefaultLocalConfig()
    {
        return array(
            'BEEZUP_TRACKER_ACTIVE'            => 0,
            'BEEZUP_TRACKER_URL'               => 'tracker.beezup.com',
            'BEEZUP_TRACKER_STORE_IDS'         => array(),
            'BEEZUP_TRACKER_PRICE'             => 0,
            'BEEZUP_SITE_ADDRESS'              => 'http://'
                .rtrim($_SERVER['SERVER_NAME'].__PS_BASE_URI__, '\\/'),
            'BEEZUP_DEBUG_MODE'                => 0,
            'BEEZUP_ALL_SHOPS'                 => 0,
            'BEEZUP_USE_CACHE'                 => 0,
            'BEEZUP_COUNTRY'                   => 8,
            'BEEZUP_CACHE_VALIDITY_DAYS'       => 0,
            'BEEZUP_CACHE_VALIDITY_HOURS'      => 12,
            'BEEZUP_CACHE_VALIDITY_MINUTES'    => 0,
            'BEEZUP_USE_CRON'                  => 0,
            'BEEZUP_CRON_HOURS'                => 0,
            'BEEZUP_CRON_MINUTES'              => 0,
            'BEEZUP_NEW_PRODUCT_ID_SYSTEM'     => 1,
            'BEEZUP_CATEGORY_DEEPEST'          => 0,
            'BEEZUP_MEMORY_LIMIT'              => ini_get('memory_limit'),
            'BEEZUP_TIME_LIMIT'                => ini_get('max_execution_time'),
            'BEEZUP_BATCH_SIZE'                => self::PRODUCT_BATCH_SIZE,
            'BEEZUP_OM_DEFAULT_CARRIER_ID'     => Configuration::get(
                'PS_CARRIER_DEFAULT'
            ),
            // @todo assure that all values are properly picked for all shops
            'BEEZUP_OM_FORCE_CART_ADD'         => 0,
            // force add to cart,
            'BEEZUP_OM_TEST_MODE'              => 0,
            'BEEZUP_OM_TOLERANCE'              => 120,
            'BEEZUP_OM_UPDATE_ACTIVE'          => 0,
            'PS_BEEZUP_ENABLE_CATEGORY_FILTER' => 0,
            'PS_BEEZUP_SELECTED_CATEGORIES'    => '',
            'PS_BEEZUP_CARRIERS_FEED'          => '',
            'PS_BEEZUP_FEED_CONCURRENT_CALL'   => 0,
            'BEEZUP_OM_IMPORT_FBA' => 0,
            'BEEZUP_OM_IMPORT_CDISCOUNT' => 0,
            'BEEZUP_OM_MULTIPLE_STOCK_FILTER' => 0,
            'BEEZUP_OM_DEBUG_LOGS' => 0,
            'BEEZUP_OM_CLEAN_LOG_DAYS' => 30
        );
    }

    /**
     * Returns default global config (config for all stores)
     *
     * @return multitype:string number
     */
    protected function getDefaultGlobalConfig()
    {
        return array(
            'BEEZUP_OM_USER_ID'              => '',
            'BEEZUP_OM_API_TOKEN'            => '',
            'BEEZUP_OM_SYNC_TIMEOUT'         => 3600,
            'BEEZUP_OM_LAST_SYNCHRONIZATION' => (int)gmdate('U'),
            'BEEZUP_OM_STATUS_MAPPING'       => json_encode(array()),
            'BEEZUP_OM_STORES_MAPPING'       => json_encode(array()),
            'BEEZUP_OM_CARRIERS_MAPPING'     => json_encode(array()),
            'BEEZUP_OM_ID_FIELD_MAPPING'     => json_encode(array()),
        );
    }

    /**
     * Install module
     *
     * @return boolean
     */
    public function install()
    {
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', true);
        $start = microtime(true);
        $this->resetLog();

        $this->prepareOverrideFiles();

        $ret = $this->_install();


        Configuration::updateValue('BEEZUP_OM_UPDATE_ACTIVE', 0);
        $carriers = array(
            "PriceMinister",
            "Fnac",
            "DARTY",
            "BOULANGER",
            "LEQUIPE",
            "COMPTOIRSANTE",
            "RUEDUCOMMERCE",
            "BLEUBONHEUR",
            "Bol",
            "RealDE",
        );
        $retorno = array();
        foreach ($carriers as $data) {
            $retorno[$data] = "";
        }
        Configuration::updateValue(
            "PS_BEEZUP_CARRIER_MAP_UP",
            json_encode($retorno)
        );


        foreach ($this->getDefaultLocalConfig() as $sKey => $mValue) {
            $ret_cf = Configuration::updateValue($sKey, $mValue);
            $ret = $ret && $ret_cf;
        }

        foreach ($this->getDefaultGlobalConfig() as $sKey => $mValue) {
            $ret_cf = Configuration::updateValue($sKey, $mValue, false, 0, 0);
            $ret = $ret && $ret_cf;
        }

        $ret_om = $this->getBeezupOMController()->install();

        $ret = $ret_om && $ret;

        $stop = microtime(true);

        if ($ret) {
            $this->resetCache();
            $this->addLog(
                'Installation success',
                number_format($stop - $start, 2).'s',
                number_format(memory_get_peak_usage() / (1024 * 1024), 2).'Mo'
            );
            $this->addLog('OM Installation '.($ret_om ? 'success' : 'failed'));
        }

        if (sizeof($this->_errors)) {
            echo '<div class="warning error">'
                .'<ul>'
                .'<li>'
                .implode('</li><li>', $this->_errors)
                .'</li>'
                .'</ul>'
                .'</div>';
        }
        ini_set('display_errors', $display_errors);

        return $ret;
    }

    protected function prepareOverrideFiles()
    {
        return;
    }


    /**
     * Uninstall module
     *
     * @return boolean
     */
    public function uninstall()
    {
        $ret = $this->_uninstall();
        foreach ($this->_confKeys as $sName) {
            // just in case if PHP one day is capable to really optimize things
            $del = Configuration::deleteByName($sName);
            $ret = $del && $ret;
        }
        if ($ret) {
            $this->resetLog();
            $this->resetCache();
        }

        return $ret;
    }


    public function _updateOmHookStatusConfig()
    {
        $carriers = array(
            "PriceMinister",
            "Fnac",
            "DARTY",
            "BOULANGER",
            "LEQUIPE",
            "COMPTOIRSANTE",
            "RUEDUCOMMERCE",
            "OUTIZ",
            "BLEUBONHEUR",
            "Bol",
            "RealDE",
            "GOSPORT",
            "MIRAKL-GOSPORT",
        );
        $retorno = array();
        foreach ($carriers as $data) {
            $retorno[$data] = "";
            if (Tools::isSubmit("om_carrier_".$data)) {
                $maps = Tools::getValue("om_carrier_".$data);

                $last_value = "";
                foreach ($maps as $key => $map) {
                    if (array_key_exists('beezup', $map)) {
                        $last_value = $map['beezup'];
                    } else {
                        $retorno[$data][] = array(
                            "id_carrier"     => $map['ps'],
                            "beezup_carrier" => $last_value,
                        );
                    }
                }

                //$retorno[$data]  = Tools::getValue("om_carrier_".$data);
            }
        }

        Configuration::updateValue(
            "PS_BEEZUP_CARRIER_MAP_UP",
            json_encode($retorno)
        );

        if (Tools::isSubmit("BEEZUP_OM_UPDATE_ACTIVE")) {
            Configuration::updateValue(
                "BEEZUP_OM_UPDATE_ACTIVE",
                (int)Tools::getValue("BEEZUP_OM_UPDATE_ACTIVE")
            );
            $this->_conf['BEEZUP_OM_UPDATE_ACTIVE']
                = (int)Tools::getValue("BEEZUP_OM_UPDATE_ACTIVE");
        }
    }


    /**
     * Update module Configuration
     *
     * @return void
     */
    protected function postProcess()
    {
        $errors = $this->_errors;
        $smarty = Context::getContext()->smarty;
        $currentIndex = AdminController::$currentIndex;

        $token = Tools::getValue('token');
        $errors = array();
        if (Tools::getValue('action') === 'cleanup') {
            $smarty->assign('cleanup_messages', $this->doCleanup());
            Tools::redirectAdmin(
                $currentIndex
                .'&configure=beezup&confirm=1&token='.$token
            );
        } elseif (Tools::isSubmit('submitOMPurgeLocks')) {
            $this->getBeezupOMController()->purgeSync();
        } else {
            if (Tools::isSubmit('submitOMDebugConfiguration')) {
                $this->_conf['BEEZUP_OM_SYNC_TIMEOUT']
                    = max((int)Tools::getValue('BEEZUP_OM_SYNC_TIMEOUT'), 1);
                $this->_conf['BEEZUP_OM_TOLERANCE']
                    = (int)Tools::getValue('BEEZUP_OM_TOLERANCE');
                $this->_conf['BEEZUP_OM_IMPORT_FBA']
                    = (int)Tools::getValue('BEEZUP_OM_IMPORT_FBA');
                $this->_conf['BEEZUP_OM_IMPORT_CDISCOUNT']
                    = (int)Tools::getValue('BEEZUP_OM_IMPORT_CDISCOUNT');
                $this->_conf['BEEZUP_OM_MULTIPLE_STOCK_FILTER']
                    = (int)Tools::getValue('BEEZUP_OM_MULTIPLE_STOCK_FILTER');
                $this->_conf['BEEZUP_OM_DEBUG_LOGS']
                    = (int)Tools::getValue('BEEZUP_OM_DEBUG_LOGS');
                $this->_conf['BEEZUP_OM_CLEAN_LOG_DAYS']
                    = (int)Tools::getValue('BEEZUP_OM_CLEAN_LOG_DAYS');
                if (!preg_match("/^\d+$/", Tools::getValue('BEEZUP_OM_SYNC_TIMEOUT'))) {
                    $errors[] = $this->l("Field Sync Timeout has to be numeric");
                }
                $filterStatus = "";
                foreach (Tools::getValue("import_filter_status") as $status) {
                    $filterStatus .= $status.",";
                }
                if (Tools::strlen($filterStatus) > 0) {
                    $filterStatus = Tools::substr($filterStatus, 0, -1);
                }

                $this->_conf['BEEZUP_OM_IMPORT_FILTER_STATUS'] = $filterStatus;
                if (Tools::getValue("input_filter_days_enable") == 1) {
                    $this->_conf['BEEZUP_OM_IMPORT_FILTER_DAYS']
                        = (int)Tools::getValue("import_filter_days");
                    $this->_conf['BEEZUP_OM_IMPORT_FILTER_DAYS_ON'] = 1;
                } else {
                    $this->_conf['BEEZUP_OM_IMPORT_FILTER_DAYS'] = '';
                    $this->_conf['BEEZUP_OM_IMPORT_FILTER_DAYS_ON'] = 0;
                }

                $this->_conf['BEEZUP_ORDER_STATUS_FILTER'] = 0;
                if (Tools::getValue("input_filter_status_enable") == 1) {
                    $this->_conf['BEEZUP_ORDER_STATUS_FILTER'] = 1;
                }
                if ($this->updateConf() && empty($errors)) {
                    Tools::redirectAdmin(
                        $currentIndex
                        .'&configure=beezup&confirm=1&token='.$token.'&om'
                    );
                } else {
                    $smarty->assign('update_errors', $errors);
                }
            } elseif (Tools::isSubmit('submitProductTemplate')) {
                $type = Tools::getValue('type');
                $searchValue = Tools::getValue('search_value');
                $replaceValue = Tools::getValue('replace_value');
                $marketplace = Tools::getValue('marketplace');


                $exists = Db::getInstance()->getRow(
                    "select field_type from "
                    ._DB_PREFIX_."beezupom_product_template where
                      field_type = '".pSQL($type)."' and  search_value = '"
                    .pSQL($searchValue)."' 
                      and replace_value = '".pSQL($replaceValue)
                    ."' and marketplace = '".pSQL($marketplace)."' "
                );
                if (empty($exists)) {
                    Db::getInstance()->insert(
                        "beezupom_product_template",
                        array(
                            "field_type"    => pSQL($type),
                            "search_value"  => pSQL($searchValue),
                            "replace_value" => pSQL($replaceValue),
                            "marketplace"   => pSQL($marketplace),
                        )
                    );
                }
                Tools::redirectAdmin(
                    $currentIndex
                    .'&configure=beezup&confirm=1&token='.$token.'&om'
                );
            } elseif (Tools::isSubmit('removeProductTemplate')) {
                $id = Tools::getValue('id');
                if (is_numeric($id)) {
                    $id = (int)$id;
                    Db::getInstance()->execute(
                        "delete from "._DB_PREFIX_."beezupom_product_template 
                    where id_beezupom_product_template = '".$id."'"
                    );
                }
                Tools::redirectAdmin(
                    $currentIndex
                    .'&configure=beezup&confirm=1&token='.$token.'&om'
                );
            } elseif (Tools::isSubmit("submitOMStock")) {
                $this->_conf['BEEZUP_OM_FORCE_CART_ADD']
                    = Tools::getValue('BEEZUP_OM_FORCE_CART_ADD');
                $marketChannelFilter = Tools::getValue("marketChannelFilter");

                $marketChannelFilter = implode(",", $marketChannelFilter);
                $this->_conf['BEEZUP_MARKETCHANNEL_FILTERS']
                    = $marketChannelFilter;
                if ($this->updateConf()) {
                    Tools::redirectAdmin(
                        $currentIndex
                        .'&configure=beezup&confirm=1&token='.$token.'&om'
                    );
                } else {
                    $smarty->assign('update_errors', $errors);
                }
            } else {
                if (Tools::isSubmit('submitOMConfiguration')) {
                    $this->_conf['BEEZUP_OM_USER_ID']
                        = (string)Tools::getValue('BEEZUP_OM_USER_ID');
                    $this->_conf['BEEZUP_OM_API_TOKEN']
                        = (string)Tools::getValue('BEEZUP_OM_API_TOKEN');
                    $this->_conf['BEEZUP_OM_STATUS_MAPPING']
                        = Tools::getValue('BEEZUP_OM_STATUS_MAPPING');
                    $this->_conf['BEEZUP_OM_STORES_MAPPING']
                        = Tools::getValue('BEEZUP_OM_STORES_MAPPING');
                    $this->_conf['BEEZUP_OM_CARRIERS_MAPPING']
                        = Tools::getValue('BEEZUP_OM_CARRIERS_MAPPING');

                    $this->_conf['BEEZUP_OM_ID_FIELD_MAPPING']
                        = Tools::getValue('BEEZUP_OM_ID_FIELD_MAPPING');
                    $this->_conf['BEEZUP_OM_DEFAULT_CARRIER_ID']
                        = Tools::getValue('BEEZUP_OM_DEFAULT_CARRIER_ID');
                    if ($this->updateConf()) {
                        Tools::redirectAdmin(
                            $currentIndex
                            .'&configure=beezup&confirm=1&token='.$token.'&om'
                        );
                    } else {
                        $smarty->assign('update_errors', $errors);
                    }
                } else {
                    if (Tools::isSubmit('submitOMLastSynchro')) {
                        if (Tools::getValue('BEEZUP_OM_LAST_SYNCHRONIZATION')
                            && strtotime(
                                Tools::getValue(
                                    'BEEZUP_OM_LAST_SYNCHRONIZATION'
                                )
                            )
                        ) {
                            $oDateTime
                                = new DateTime(
                                    Tools::getValue(
                                        'BEEZUP_OM_LAST_SYNCHRONIZATION'
                                    ),
                                    new DateTimeZone('UTC')
                                );
                            if ($oDateTime->getTimestamp()) {
                                $this->_conf['BEEZUP_OM_LAST_SYNCHRONIZATION']
                                    = $oDateTime->getTimestamp();
                            } else {
                                $errors[] = $this->l('Invalid date');
                            }
                        }

                        if ($this->updateConf()) {
                            Tools::redirectAdmin(
                                $currentIndex
                                .'&configure=beezup&confirm=1&token='.$token
                            );
                        } else {
                            $smarty->assign('update_errors', $errors);
                        }
                    } elseif (Tools::isSubmit("submitOMhookStatusUpdate")) {
                        $this->_updateOmHookStatusConfig();
                        Tools::redirectAdmin(
                            $currentIndex
                            .'&configure=beezup&confirm=1&om&token='
                            .$token
                        );
                    } else {
                        if (Tools::isSubmit('submitTrackerConfiguration')) {
                            $this->_conf['BEEZUP_TRACKER_ACTIVE']
                                = (int)Tools::getValue('BEEZUP_TRACKER_ACTIVE');
                            $this->_conf['BEEZUP_TRACKER_PRICE']
                                = (int)Tools::getValue('BEEZUP_TRACKER_PRICE');
                            $this->_conf['BEEZUP_TRACKER_URL']
                                = str_replace(
                                    array('http://', 'https://'),
                                    '',
                                    rtrim(
                                        Tools::getValue('BEEZUP_TRACKER_URL'),
                                        '\\/'
                                    )
                                );
                            $this->_conf['BEEZUP_TRACKER_STORE_IDS']
                                = Tools::getValue('BEEZUP_TRACKER_STORE_IDS');
                            $this->_conf['BEEZUP_TRACKER_VALIDATE_STATE']
                                = (int)Tools::getValue(
                                    'BEEZUP_TRACKER_VALIDATE_STATE'
                                );

                            if ($this->updateConf()) {
                                Tools::redirectAdmin(
                                    $currentIndex
                                    .'&configure=beezup&confirm=1&token='
                                    .$token
                                );
                            } else {
                                $smarty->assign('update_errors', $errors);
                            }
                        } elseif (Tools::isSubmit(
                            'submitGeneralConfiguration'
                        )
                        ) {
                            $this->_conf['BEEZUP_SITE_ADDRESS']
                                = rtrim(
                                    Tools::getValue('BEEZUP_SITE_ADDRESS'),
                                    '\\/'
                                );
                            $this->_conf['BEEZUP_COUNTRY']
                                = (int)Tools::getValue('BEEZUP_COUNTRY');
                            $this->_conf['BEEZUP_USE_CACHE']
                                = (int)Tools::getValue('BEEZUP_USE_CACHE');
                            $this->_conf['BEEZUP_CACHE_VALIDITY_DAYS']
                                = Tools::getValue('BEEZUP_CACHE_VALIDITY_DAYS');
                            $this->_conf['BEEZUP_CACHE_VALIDITY_HOURS']
                                = Tools::getValue(
                                    'BEEZUP_CACHE_VALIDITY_HOURS'
                                );
                            $this->_conf['BEEZUP_CACHE_VALIDITY_MINUTES']
                                = Tools::getValue(
                                    'BEEZUP_CACHE_VALIDITY_MINUTES'
                                );
                            $this->_conf['BEEZUP_USE_CRON']
                                = (int)Tools::getValue('BEEZUP_USE_CRON');
                            $this->_conf['BEEZUP_CRON_HOURS']
                                = Tools::getValue('BEEZUP_CRON_HOURS');
                            $this->_conf['BEEZUP_CRON_MINUTES']
                                = Tools::getValue('BEEZUP_CRON_MINUTES');
                            $this->_conf['BEEZUP_ALL_SHOPS']
                                = (int)Tools::getValue('BEEZUP_ALL_SHOPS');
                            $this->_conf['BEEZUP_NEW_PRODUCT_ID_SYSTEM']
                                = (int)Tools::getValue(
                                    'BEEZUP_NEW_PRODUCT_ID_SYSTEM'
                                );
                            $this->_conf['BEEZUP_CATEGORY_DEEPEST']
                                = (int)Tools::getValue(
                                    'BEEZUP_CATEGORY_DEEPEST'
                                );


                            if ($this->updateConf()) {
                                $this->resetCache();
                                Tools::redirectAdmin(
                                    $currentIndex
                                    .'&configure=beezup&confirm=1&token='
                                    .$token
                                );
                            } else {
                                $smarty->assign('update_errors', $errors);
                            }
                        } elseif (Tools::isSubmit('submitDebug')) {
                            $this->_conf['BEEZUP_DEBUG_MODE']
                                = (int)Tools::getValue('BEEZUP_DEBUG_MODE');
                            $this->_conf['BEEZUP_TIME_LIMIT']
                                = (int)Tools::getValue('BEEZUP_TIME_LIMIT');
                            $this->_conf['BEEZUP_MEMORY_LIMIT']
                                = Tools::getValue('BEEZUP_MEMORY_LIMIT');
                            $this->_conf['BEEZUP_BATCH_SIZE']
                                = (int)Tools::getValue('BEEZUP_BATCH_SIZE');
                            $this->_conf['BEEZUP_OM_TEST_MODE']
                                = (int)Tools::getValue('BEEZUP_OM_TEST_MODE');
                            if (!preg_match("/^\d+$/", Tools::getValue('BEEZUP_TIME_LIMIT'))) {
                                $errors[] = $this->l('Time limit field has to be numeric');
                            } elseif (!preg_match("/^\d+$/", Tools::getValue('BEEZUP_BATCH_SIZE'))) {
                                $errors[] = $this->l('Batch size field has to be numeric');
                            } elseif (!preg_match("/^\d+[mM]$/", Tools::getValue('BEEZUP_MEMORY_LIMIT'))) {
                                $errors[] = $this->l('Memory limit field has to be numeric and end with a letter M');
                            }

                            if ($this->updateConf() && empty($errors)) {
                                $this->resetCache();
                                Configuration::updateValue(
                                    "PS_BEEZUP_FEED_CONCURRENT_CALL",
                                    0
                                );
                                Tools::redirectAdmin(
                                    $currentIndex
                                    .'&configure=beezup&confirm=1&token='
                                    .$token
                                );
                            } else {
                                $smarty->assign('update_errors', $errors);
                            }
                        } elseif (Tools::isSubmit('submitConfiguration')) {
                            $feed_carriers
                                = Carrier::getCarriers(_USER_ID_LANG_);
                            $conf_carrier = array();
                            foreach ($feed_carriers as $f_carrier) {
                                if (Tools::isSubmit('carrier_field_'.$f_carrier['id_carrier'])) {
                                    $valor = Tools::getValue(
                                        'carrier_value_'
                                        .$f_carrier['id_carrier']
                                    );
                                    $conf_carrier[$f_carrier['id_carrier']]
                                        = array(
                                        "name"  => $f_carrier['name'],
                                        "value" => $valor,
                                    );
                                }
                            }

                            Configuration::updateValue(
                                "PS_BEEZUP_CARRIERS_FEED",
                                json_encode($conf_carrier)
                            );

                            $enable_category_filter
                                = (int)Tools::getValue(
                                    "enable_filter_categories"
                                );
                            Configuration::updateValue(
                                "PS_BEEZUP_ENABLE_CATEGORY_FILTER",
                                $enable_category_filter
                            );
                            if ($enable_category_filter == 1) {
                                $categories = Tools::getValue("categoryFilter");
                                foreach ($categories as $key => $cat) {
                                    if ($cat < 1) {
                                        unset($categories[$key]);
                                    }
                                }

                                $categories = implode(",", $categories);
                                Configuration::updateValue(
                                    "PS_BEEZUP_SELECTED_CATEGORIES",
                                    $categories
                                );
                            } else {
                                Configuration::updateValue(
                                    "PS_BEEZUP_SELECTED_CATEGORIES",
                                    ""
                                );
                            }

                            $this->saveFluxConfiguration();
                        } elseif (Tools::isSubmit(
                            'createNewFieldConfiguration'
                        )
                        ) {
                            $sBalise = Tools::getValue('name');
                            $sValue = Tools::getValue('value', '');
                            if (empty($sBalise)) {
                                $errors[]
                                    = $this->l(
                                        'Name and Value cannot be empty'
                                    );
                            } elseif (!preg_match(
                                '#^[a-z0-9_]{'
                                .Tools::strlen($sBalise).'}$#i',
                                $sBalise
                            )
                            ) {
                                $errors[]
                                    = $this->l(
                                        'Characters allowed for name are only letters, numbers and underscores.'
                                    );
                            } else {
                                $configuration
                                    = $this->getBeezupConfiguration();
                                if (!Validate::isLoadedObject($configuration)) {
                                    $this->errors[]
                                        = $this->l(
                                            'Unable to load configuration'
                                        );
                                } else {
                                    $oField = new BeezupField();
                                    $oField->id_configuration
                                        = $configuration->id;
                                    $oField->active = 1;
                                    $oField->editable = 1;
                                    $oField->free_field = 1;
                                    $oField->default = $sValue;
                                    $oField->balise = $sBalise;
                                    $oField->function = 'getFreeField';
                                    $oField->fields_group = '07.Champs libres';
                                    if (!$oField->save()) {
                                        $this->errors[]
                                            = $this->l('Unable to save field');
                                    } // if
                                } // if
                            }

                            if (!sizeof($errors)) {
                                $this->resetCache();
                                Tools::redirectAdmin(
                                    $currentIndex
                                    .'&configure=beezup&confirm=4&token='
                                    .$token
                                );
                            } else {
                                $smarty->assign('update_errors', $errors);
                            }
                        } elseif (Tools::getValue('action')
                            == 'deleteFreeField'
                        ) {
                            $id_free_field = Tools::getValue('id_free_field');

                            $oFreeField = new BeezupField($id_free_field);

                            if (!Validate::isLoadedObject($oFreeField)
                                || !$oFreeField->delete()
                            ) {
                                $errors[]
                                    = $this->l('Unable to delete free field');
                                $smarty->assign('update_errors', $errors);
                            } else {
                                $this->resetCache();
                                Tools::redirectAdmin(
                                    $currentIndex
                                    .'&configure=beezup&confirm=5&token='
                                    .$token
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    protected function saveBeezupConfiguration($name = 'default')
    {
        $old_configuration = null;
        $configuration = $this->getBeezupConfiguration($name);
        $id_shop = (int)Shop::getContextShopId(true);
        $id_shop_group = (int)Shop::getContextShopGroupId(true);
        /*
                print "<hr>";
                print "<br> Configuration : " . $configuration->id;
                print "<br> Configuration ID SHOP: " . var_export($configuration->id_shop, true);
                print "<br> Configuration ID SHOP GROUP: " . var_export($configuration->id_shop_group, true);
                print "<br> ID SHOP: " . var_export($id_shop, true);
                print "<br> ID SHOP GROUP: " . var_export($id_shop_group, true);
                print "</hr>";
            */
        $id_shop_conf = (int)$configuration->id_shop;
        $id_group_conf = (int)$configuration->id_shop_group;

        $shop_eq = ($id_shop_conf === (int)$id_shop);
        $group_eq = ($id_group_conf === (int)id_shop_group);

        $same = ($shop_eq
                && (($id_shop && $id_shop_conf)
                    || ($id_shop_group
                        && $id_group_conf
                        && $group_eq)))
            || (!$id_shop && !$id_shop_group && !$id_shop_conf
                && !$id_group_conf);

        if (!$configuration || !$same) {
            $old_configuration = $configuration ? $configuration : null;
            $configuration = new BeezupConfiguration();
        }

        $configuration->name = $name;
        $configuration->id_shop = $id_shop;
        $configuration->id_shop_group = $id_shop ? null : $id_shop_group;
        $configuration->disable_disabled_product
            = (int)Tools::getValue('disable_disabled_product');
        $configuration->disable_not_available
            = (int)Tools::getValue('disable_not_available');
        $configuration->disable_oos_product
            = (int)Tools::getValue('disable_oos_product');
        $configuration->id_carrier = (int)Tools::getValue('id_carrier');
        $configuration->id_zone = (int)Tools::getValue('id_zone');
        $configuration->image_type = Tools::getValue('image_type');
        $configuration->id_default_lang
            = (int)Tools::getValue('id_default_lang');
        $configuration->force_product_tax
            = (int)Tools::getValue('force_product_tax');
        $configuration->set_attributes_as_product
            = (int)Tools::getValue('set_attributes_as_product');

        if ($configuration->save()) {
            $mapping = null;
            // copy fields from $old_configuration to $configuration
            if ($old_configuration && $old_configuration->id
                && !$configuration->fields
            ) {
                $mapping = $configuration->copyFields($old_configuration->id);
            }

            return array($configuration, $mapping);
        }

        return array(null, null);
    }

    protected function saveFluxConfiguration()
    {
        $errors = $this->_errors;
        $smarty = $this->context->smarty;
        $currentIndex = AdminController::$currentIndex;
        $token = Tools::getValue('token');
        list(
            $configuration, $mapping
            )
            = $this->saveBeezupConfiguration('default');

        if ($configuration) {
            foreach ($configuration->fields as $aField) {
                $new_field_id = (int)$aField['id_field'];
                $id_field = $mapping ? (int)$mapping[$new_field_id]
                    : $new_field_id;
                $field = new BeezupField($new_field_id);
                if (Validate::isLoadedObject($field)) {
                    $field->active = (int)Tools::getValue(
                        'field_'.$id_field
                            .'_active'
                    )
                        || $field->forced;
                    $field->default = Tools::getValue(
                        'field_'.$id_field
                        .'_default'
                    );
                    $field->id_configuration = (int)$configuration->id;

                    $tmp_feat_carac = Tools::getValue(
                        'field_'.$id_field
                        .'_attribute_feature'
                    );

                    if (preg_match('/^attr_([1-9][0-9]*)$/', $tmp_feat_carac)) {
                        $field->id_feature = 0;
                        $field->id_attribute_group = (int)str_replace(
                            'attr_',
                            '',
                            $tmp_feat_carac
                        );
                    } elseif (preg_match(
                        '/^feat_([1-9][0-9]*)$/',
                        $tmp_feat_carac
                    )
                    ) {
                        $field->id_feature = (int)str_replace(
                            'feat_',
                            '',
                            $tmp_feat_carac
                        );
                        $field->id_attribute_group = 0;
                    } else {
                        $field->id_feature = 0;
                        $field->id_attribute_group = 0;
                    }

                    if (Tools::getIsset($field->free_field)
                        && $field->free_field == 1
                    ) {
                        $sBalise = Tools::getValue(
                            'field_'.$id_field
                            .'_balise'
                        );

                        if (!empty($sBalise)
                            && preg_match(
                                '#^[a-z0-9_]{'.Tools::strlen($sBalise)
                                .'}$#i',
                                $sBalise
                            )
                        ) {
                            $field->balise = $sBalise;
                        } else {
                            $errors[]
                                = $this->l(
                                    'Error : wrong field free name (characters allowed are only letters, numbers and underscore'
                                );
                        }
                    }

                    if (!$field->save()) {
                        $errors[] = $this->l('Error : can\'t save field').' "'
                            .htmlentities(
                                $field->balise,
                                ENT_COMPAT,
                                'UTF-8'
                            ).'"';
                    }
                } else {
                    $errors[] = $this->l('Error : can\'t load field #').' '
                        .(int)$aField['id_field'];
                }
            }
        } else {
            $errors[] = $this->l('Error : can\'t save configuration');
        }

        if (!sizeof($errors)) {
            $this->resetCache();
            Tools::redirectAdmin(
                $currentIndex
                .'&configure=beezup&confirm=1&token='.$token
            );
        } else {
            $smarty->assign('update_errors', $errors);
        }
    }


    /**
     * Update Configuration subfunction
     *
     * @return boolean
     */
    protected function updateConf()
    {
        $errors = $this->_errors;
        $global_conf_keys = array_keys($this->getDefaultGlobalConfig());
        foreach ($this->_conf as $key => $value) {
            if (in_array(
                $key,
                array(
                    'BEEZUP_OM_STATUS_MAPPING',
                    'BEEZUP_OM_STORES_MAPPING',
                    'BEEZUP_OM_ID_FIELD_MAPPING',
                    'BEEZUP_OM_CARRIERS_MAPPING',
                )
            )
            ) {
                $value = json_encode($value);
            }
            // Config for order managment is always global
            if (in_array($key, $global_conf_keys)) {
                $bResult = Configuration::updateValue(
                    $key,
                    $value,
                    false,
                    0,
                    0
                );
            } else {
                $bResult = Configuration::updateValue($key, $value);
            }
            if (!$bResult) {
                $errors[] = $this->l('Error while updating')." $key";
            }
        }

        Configuration::loadConfiguration();
        $this->loadBeezupConf();

        return (sizeof($errors) == 0);
    }

    /**
     * Delete log file
     *
     * @return void
     */
    public function resetLog()
    {
        if (file_exists(dirname(__FILE__).'/views/log/log.txt')) {
            unlink(dirname(__FILE__).'/views/log/log.txt');
        }

        $this->addLog('Log reseted.');
    }

    /**
     * Add entry to log
     *
     * @return void
     */
    public function addLog()
    {
        if ($this->_log_id === null) {
            $this->_log_id = uniqid('b', true);
        }

        if (func_num_args()) {
            $args = func_get_args();
            $str = date('Y/m/d H:i:s').'|'.$_SERVER['REMOTE_ADDR'].'|'
                .$this->_log_id.'|'.implode('|', $args)."\r\n";
        } else {
            $str = "\r\n";
        }
        if (!file_exists(dirname(__FILE__).'/views/log')) {
            mkdir(dirname(__FILE__).'/views/log', 0755, true);
        }

        file_put_contents(
            dirname(__FILE__).'/views/log/log.txt',
            $str,
            FILE_APPEND
        );
    }

    /**
     * List log entries
     *
     * @return array
     */
    public function getLog()
    {
        $ret = array();
        if (file_exists(dirname(__FILE__).'/views/log/log.txt')) {
            $f = fopen(dirname(__FILE__).'/views/log/log.txt', 'r');

            if ($f) {
                while (!feof($f)) {
                    $ret[] = fgetcsv($f, 0, '|');
                }
                fclose($f);
            }
        }

        return $ret;
    }

    # CACHE MANAGEMENT

    protected function getCacheDir()
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array(dirname(__FILE__), 'views', 'cache')
        );
    }

    protected function getCacheFilename(
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        $lang_name = $id_lang ? Language::getIsoById((int)$id_lang) : '';
        $shop_name = $id_shop ? sprintf('s%d', (int)$id_shop) : '';
        $currency_name = '';

        if ($id_currency && Currency::getCurrency((int)$id_currency)) {
            $currency = Currency::getCurrency((int)$id_currency);
            $currency_name = $currency['iso_code'];
        }
        if (Tools::isSubmit("default")) {
            $lang_name = "";
            $currency_name = "";
            $shop_name = "";
        }

        return sprintf(
            'cache-%s-%s-%s.xml',
            $lang_name,
            $currency_name,
            $shop_name
        );
    }

    public function getCacheFilePath(
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        return $this->getCacheDir().DIRECTORY_SEPARATOR
            .$this->getCacheFilename($id_shop, $id_lang, $id_currency);
    }

    /**
     * Write cache
     *
     * @param string  $content
     * @param integer $id_lang
     *
     * @return void
     */
    protected function setCache(
        $content,
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        $cache_file_path = $this->getCacheFilePath(
            $id_shop,
            $id_lang,
            $id_currency
        );

        if (file_put_contents($cache_file_path, $content)) {
            $this->addLog(
                'Cached into '.$cache_file_path,
                null,
                number_format(filesize($cache_file_path) / (1024 * 1024), 2)
                .'Mo'
            );

            return true;
        } else {
            $this->addLog('Unable to cache into '.$cache_file_path);
            return false;
        }
    }

    /**
     * Get cache by language id
     *
     * @param integer $id_lang
     *
     * @return mixed
     */
    public function getCache(
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        $cache_file_path = $this->getCacheFilePath(
            $id_shop,
            $id_lang,
            $id_currency
        );

        if ($this->isCacheValid($cache_file_path)) {
            $result = Tools::file_get_contents($cache_file_path);

            if ($result !== false) {
                $this->addLog(
                    'Cache readed from '.$cache_file_path.'  '
                    .Tools::strlen($result).' bytes'
                );

                return $result;
            } else {
                $this->addLog('Unable read cache from '.$cache_file_path);
            } // if
        } // if

        return false;
    }

    /**
     * Verify cache validity
     *
     * @param integer $id_lang
     *
     * @return boolean
     */
    public function isCacheValid($cache_file_path)
    {
        return file_exists($cache_file_path)
            && ($this->getCacheMaxLifetime() > time()
                - filemtime($cache_file_path));
    }

    protected function getCacheMaxLifetime()
    {
        return (int)Configuration::get('BEEZUP_CACHE_VALIDITY_DAYS') * 86400
            + (int)Configuration::get('BEEZUP_CACHE_VALIDITY_HOURS') * 3600
            + Configuration::get('BEEZUP_CACHE_VALIDITY_MINUTES') * 60;
    }

    /**
     * Delete cache files
     *
     * @return void
     */
    protected function resetCache()
    {
        @array_map(
            'unlink',
            @glob($this->getCacheDir().DIRECTORY_SEPARATOR.'*.xml')
        );

        $this->addLog('Cache reseted.');
    }

    /**
     * Return XML String
     *
     * @param string $lang
     *
     * @return string
     */
    public function getXML(
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        if (Configuration::get('BEEZUP_USE_CACHE')) {
            $xml = $this->getCache($id_shop, $id_lang, $id_currency);

            if ($xml) {
                return $xml;
            } // if
        } // if

        $xml = $this->generateXML($id_shop, $id_lang, $id_currency);

        if (Configuration::get('BEEZUP_USE_CACHE') && $xml) {
            $this->setCache($xml, $id_shop, $id_lang, $id_currency);
        } // if

        return $xml;
    }


    private function getAttributeCombinations($id_product, $id_lang)
    {
        $sql
            = 'SELECT pa.*, product_attribute_shop.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name,
                        a.`id_attribute`
                    FROM `'._DB_PREFIX_.'product_attribute` pa
                    '.Shop::addSqlAssociation('product_attribute', 'pa').'
                    LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                    LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                    LEFT JOIN `'._DB_PREFIX_
            .'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '
            .(int)$id_lang.')
                    LEFT JOIN `'._DB_PREFIX_
            .'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '
            .(int)$id_lang.')
                    WHERE pa.`id_product` = '.(int)$id_product.'
                    GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                    ORDER BY pa.`id_product_attribute`';
        $res = Db::getInstance()->executeS($sql);
        //Get quantity of each variations
        foreach ($res as $key => $row) {
            $cache_key = $row['id_product'].'_'.$row['id_product_attribute']
                .'_quantity';
            if (!Cache::isStored($cache_key)) {
                Cache::store(
                    $cache_key,
                    StockAvailable::getQuantityAvailableByProduct(
                        $row['id_product'],
                        $row['id_product_attribute']
                    )
                );
            }
            $res[$key]['quantity'] = Cache::retrieve($cache_key);
        }

        return $res;
    }

    /**
     * Generate XML
     *
     * @param integer $id_lang_forced
     *
     * @return string
     */
    private function generateXML(
        $id_shop = null,
        $id_lang = null,
        $id_currency = null
    ) {
        //Context::getContext()->shop = new Shop($id_shop);
        $debug_mode = false;
        if (BeezupRegistry::get('BEEZUP_DEBUG_MODE')
            || (Tools::isSubmit('debug')
                && Tools::getValue('debug') == 1)
        ) {
            $debug_mode = true;
            $concurrent_call
                = Configuration::get("PS_BEEZUP_FEED_CONCURRENT_CALL");
            if ($concurrent_call == 1) {
                echo "There is a call being already placed";
                die();
            }
            $concurrent_call
                = Configuration::updateValue(
                    "PS_BEEZUP_FEED_CONCURRENT_CALL",
                    1
                );
        }

        $enable_category_filter
            = Configuration::get("PS_BEEZUP_ENABLE_CATEGORY_FILTER");
        $selected_categories
            = Configuration::get("PS_BEEZUP_SELECTED_CATEGORIES");
        $selected_carriers
            = json_decode(Configuration::get("PS_BEEZUP_CARRIERS_FEED"), true);
        if (is_array($selected_carriers) && !empty($selected_carriers)) {
            foreach ($selected_carriers as $key => $carrier) {
                $selected_carriers[$key]['object'] = new Carrier((int)$key);
            }
        }

        $this->addLog('Starting XML generation');
        $starttime = microtime(true);
        $this->_nbProducts = 0;
        $this->_nbRealProducts = 0;

        # Require more memory & time
        //@ini_set('memory_limit', BeezupRegistry::get('BEEZUP_MEMORY_LIMIT'));
        //@ini_set(
        //    'max_execution_time',
        //    BeezupRegistry::get('BEEZUP_TIME_LIMIT')
        //);

        $this->setCurrency($id_currency);

        #Load configuration from product
        $configuration = $this->getBeezupConfiguration();
        // forcing language
        if ($id_lang) {
            $configuration->id_default_lang = $id_lang;
        }
        $id_zone = $configuration->id_zone;
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xmlCatalog = new DOMElement('catalog');
        $xml->appendChild($xmlCatalog);
        $xmlCatalog->setAttribute('ps_version', _PS_VERSION_);
        $xmlCatalog->setAttribute('module_version', $this->version);
        $xmlCatalog->setAttribute(
            'lang',
            Language::getIsoById((int)$configuration->id_default_lang)
        );
        $xmlCatalog->setAttribute(
            'currency',
            BeezupProduct::getCurrency()->iso_code
        );

        $nBatchSize = (int)BeezupRegistry::get('BEEZUP_BATCH_SIZE') > 0
            ? (int)BeezupRegistry::get('BEEZUP_BATCH_SIZE')
            : self::PRODUCT_BATCH_SIZE;

        $this->addLog('Getting products');


        $nIndex = 0;
        $counter = 0;
        $duplicate_products = array();

        $product = new Product();

        do {
            $stopXml = Configuration::get("BEEZUP_XML_GENERATION_STOP");
            if ($stopXml != 1) {
                if ($enable_category_filter == 1) {
                    $products
                        = $this->getProductsCategoryFilter(
                            (int)$configuration->id_default_lang,
                            $nIndex * $nBatchSize,
                            $nBatchSize,
                            'id_product',
                            'ASC',
                            $selected_categories,
                            false,
                            Context::getContext()
                        );
                } else {
                    $products
                        = Product::getProducts(
                            (int)$configuration->id_default_lang,
                            $nIndex * $nBatchSize,
                            $nBatchSize,
                            'id_product',
                            'ASC',
                            false,
                            false,
                            Context::getContext()
                        );
                }
                $nIndex++;
            } else {
                Configuration::updateValue("BEEZUP_XML_GENERATION_STOP", 0);
                $products = array();
                $this->addLog(
                    'XML generation forced to stop',
                    time(),
                    number_format(memory_get_peak_usage() / (1024 * 1024), 2)
                    .'Mo'
                );
            }


            foreach ($products as $line) {
                if (in_array($line['id_product'], $duplicate_products)) {
                    continue;
                }
                $duplicate_products[] = $line['id_product'];
                $counter++;
                $product->hydrate($line);
                $product->features = array();
                $product->combinations = array();
                $product->beezup_combinations = array();
                $product->carriers = array();
                $product->carriers = $selected_carriers;
                $product->beezup_id_zone = $id_zone;
                if ($configuration->disable_disabled_product
                    && !$product->active
                ) {
                    continue;
                }
                if ($configuration->disable_not_available
                    && !$product->available_for_order
                ) {
                    continue;
                }
                $product->features
                    = $product->getFrontFeatures(
                        (int)$configuration->id_default_lang
                    );


                $combinations = $this->getAttributeCombinations(
                    $product->id,
                    (int)$configuration->id_default_lang
                );
                $repeatedCombinations = array();
                foreach ($combinations as $row) {
                    $combination = new BeezupCombination();
                    $combination->populate($row);
                    $product->combinations[(int)$row['id_product_attribute']]
                        = $combination;
                    $product->beezup_combinations[(int)$row['id_product_attribute']][]
                        = $combination;
                    $repeatedCombinations[$row['id_product_attribute']] = 1;
                }


                $this->processProduct($product, $configuration, $xmlCatalog);
                if ($counter % $nBatchSize === 0) {
                    $this->addLog(
                        sprintf(
                            '%d products processed, %s used',
                            $counter,
                            number_format(
                                memory_get_peak_usage() / (1024 * 1024),
                                2
                            ).'Mo'
                        )
                    );
                } // if
            } // foreach
        } while ($products);
        unset($products);
        $this->addLog(sprintf('%d products fetched', $counter));
        if ($counter == 0) {
            return false;
        }
        $this->addLog('Finalizing processed');
        $time = number_format(microtime(true) - $starttime, 2).'s';
        $memory = number_format(memory_get_peak_usage(true) / (1024 * 1024), 2)
            .'Mo';
        $xmlCatalog->setAttribute('nb_products', $this->_nbRealProducts);
        $xmlCatalog->setAttribute('nb_entries', $this->_nbProducts);
        $xmlCatalog->setAttribute('gen_time', $time);
        $xmlCatalog->setAttribute('gen_mem', $memory);
        $xml->normalizeDocument();
        $xml->formatOutput = false;
        $this->addLog(
            'XML generated, '.$this->_nbRealProducts.' products',
            $time,
            number_format(memory_get_peak_usage() / (1024 * 1024), 2).'Mo'
        );

        if ($debug_mode) {
            Configuration::updateValue("PS_BEEZUP_FEED_CONCURRENT_CALL", 0);
        }


        return $xml->saveXML();
    }

    /**
     * Insert product datas into xml by processing each field
     *
     * @param Product             $product
     * @param BeezupConfiguration $configuration
     * @param DOMElement          $xmlCatalog
     * @param string              $product_type
     * @param integer             $id_declension
     *
     * @return void
     */
    protected function addProductToXml(
        Product $product,
        BeezupConfiguration $configuration,
        DOMElement $xmlCatalog,
        $product_type = BeezupProduct::PRODUCT_TYPE_SIMPLE,
        $id_declension = null
    ) {
        $xmlProduct = new DOMElement('product');
        $xmlCatalog->appendChild($xmlProduct);

        $xmlProduct->setAttribute('idp', (int)$product->id);
        $xmlProduct->setAttribute('idd', (int)$id_declension);
        $xmlProduct->setAttribute('type', $product_type);

        foreach ($configuration->fields as &$field) {
            if (is_array($field)) {
                $datas = $field;
                $field = new BeezupField();
                $field->hydrate($datas);
            }

            if (!$field->active) {
                continue;
            }

            BeezupFieldProcessor::getInstance()->process(
                $product,
                $field,
                $xmlProduct,
                $configuration,
                (int)$id_declension,
                $product_type
            );
        }

        $this->_nbProducts++;
        if ($product_type != BeezupProduct::PRODUCT_TYPE_PARENT) {
            $this->_nbRealProducts++;
        }
    }

    /**
     * Process given product and extract declensions if needed
     *
     * @param Product             $product
     * @param BeezupConfiguration $configuration
     * @param DOMElement          $xmlCatalog
     *
     * @return void
     */
    protected function processProduct(
        Product $product,
        BeezupConfiguration $configuration,
        DOMElement $xmlCatalog
    ) {
        $beezupSingleton = BeezupStaticProcessor::getInstance();


        //we are not using array_key_exists because it affects performance
        //of the product feed generation
        if (isset($product->combinations)
            && sizeof($product->combinations)
            && $configuration->set_attributes_as_product
        ) {
            $beezupSingleton->setProduct($product, 0);
            if ($beezupSingleton->getQuantity() < 1
                && $configuration->disable_oos_product
            ) {
                return;
            }
            $this->addProductToXml(
                $product,
                $configuration,
                $xmlCatalog,
                BeezupProduct::PRODUCT_TYPE_PARENT
            );
            foreach ($product->combinations as $id => $combination) {
                $beezupSingleton->setProduct($product, $id);
                if ($beezupSingleton->getQuantity() < 1
                    && $configuration->disable_oos_product
                ) {
                    return;
                }
                $this->addProductToXml(
                    $product,
                    $configuration,
                    $xmlCatalog,
                    BeezupProduct::PRODUCT_TYPE_CHILD,
                    (int)$id
                );
            }
        } else {
            $beezupSingleton->setProduct($product, 0);
            if ($beezupSingleton->getQuantity() < 1 && $configuration->disable_oos_product) {
                return;
            }
            $this->addProductToXml($product, $configuration, $xmlCatalog);
        }
    }

    /**
     * Execute CRON Task
     *
     * @return string
     */
    public function cron()
    {
        $argv = BeezupGlobals::$cron_args;

        if (!isset($_SERVER['REMOTE_ADDR'])) {
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        }
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = 'CLI';
        }

        if (sizeof($argv)) {
            $args = array();
            foreach ($argv as $k => &$value) {
                if (preg_match('/=/', $value)) {
                    list($key, $value) = explode('=', $value);
                } else {
                    list($key, $value) = array($k, $value);
                }
                $args[$key] = $value;
            }
        }

        $this->addLog('CRON Task called');

        if (!$this->active
            || !$this->_conf['BEEZUP_USE_CRON']
        ) {
            $this->addLog('Access denied');
            header('HTTP/1.1 403 Forbidden');
            die(
                date('Y-m-d H:i:s | ').$_SERVER['REMOTE_ADDR']
                ." | FORBIDDEN : You don't have permission to access "
                .__PS_BASE_URI__."modules/beezup/_cron.php on this server\r\n"
            );
        }

        $id_lang = array_key_exists('lang', $args)
            ? (int)Language::getIdByIso($args['lang']) : null;
        $id_shop = array_key_exists('shop', $args) ? (int)$args['shop'] : null;
        $id_currency = array_key_exists('currency', $args)
            ? Currency::getIdByIsoCode('currency', $args) : null;

        $content = $this->generateXML($id_shop, $id_lang, $id_currency);
        if ($content) {
            $this->setCache($content, $id_shop, $id_lang, $id_currency);

            return date('Y-m-d H:i:s | ').$_SERVER['REMOTE_ADDR']
                ." | SUCCESS : XML cached.\r\n";
        }
    }

    /**
     * Get used tracker (create one if needed)
     *
     * @param boolean $forceReload
     *
     * @return BeezupTrackerAbstract
     */
    protected function _getTracker($forceReload = false)
    {
        if (!$this->_tracker instanceof BeezupTrackerAbstract || $forceReload) {
            $this->_tracker = new BeezupTrackerPhp();

            $this->_tracker->setActive(
                (bool)$this->_conf['BEEZUP_TRACKER_ACTIVE']
            )
                ->setBaseUrl($this->_conf['BEEZUP_TRACKER_URL'])
                ->setModule($this)
                ->setStoreId($this->getStoreId())
                ->setUseMargins($this->_conf['BEEZUP_TRACKER_PRICE'])
                ->setValidationMethod(
                    $this->_conf['BEEZUP_TRACKER_VALIDATE_STATE']
                );
        }

        return $this->_tracker;
    }

    /**
     * Execute tracker on hook Header
     *
     * @return string
     */
    public function hookHeader()
    {
        return $this->_getTracker()
            ->execTracker('header', array());
    }

    /**
     * Execute tracker on hook  NewOrder
     *
     * @param array $params
     *
     * @return mixed
     */
    public function hookNewOrder($params = array())
    {
        return $this->_getTracker()
            ->execTracker('newOrder', array($params['order']));
    }

    /**
     * Execute tracker on hook UpdateOrderStatus
     *
     * @param array $params
     *
     * @return mixed
     */
    public function hookUpdateOrderStatus($params = array())
    {
        $order = new Order((int)$params['id_order']);
        $orderState = $params['newOrderStatus'];
        if (Configuration::get("BEEZUP_OM_UPDATE_ACTIVE") == 1) {
            $this->updateBeezupStatusOrder($order, $orderState);
        }

        $this->_getTracker()
            ->execTracker('orderUpdate', array($order, $orderState));

        return true;
    }


    private function updateBeezupStatusOrder(Order $order, $status)
    {
        $beezup_status_map
            = json_decode(Configuration::get("BEEZUP_OM_STATUS_MAPPING"));
        if ($status->id != (int)$beezup_status_map->Shipped) {
            return false;
        }

        $this->_jsonCarrierMap
            = json_decode(Configuration::get("PS_BEEZUP_CARRIER_MAP_UP"));

        $shipping = $order->getShipping();
        $beezup_order = Db::getInstance()->getRow(
            "select * from `"._DB_PREFIX_
            ."beezup_order` where `id_order` = '".(int)$order->id."' "
        );
        if ($beezup_order) {
            $id_carrier = $shipping[0]['id_carrier'];
            $today = gmdate("Y-m-d H:i:s");
            $post_data = array();
            $post_data['id_order'] = $order->id;
            $market_technical_code
                = $beezup_order['marketplace_technical_code'];
            $links = json_decode($beezup_order['order_json']);
            $market_business_code = $links->marketplace_business_code;
            foreach ($links->transition_links as $action) {
                if ($action->rel == "ShipOrder") {
                    $post_data['action_id'] = $action->rel;
                    foreach ($action->parameters as $parameter) {
                        $post_data[$parameter->name] = "";
                        if ($parameter->name
                            == "Order_Shipping_FulfillmentDate"
                        ) {
                            $post_data["Order_Shipping_FulfillmentDate"]
                                = $today;
                        } elseif ($parameter->name
                            == "Order_Shipping_ShipperTrackingNumber"
                            && array_key_exists('tracking_number', $shipping[0])
                        ) {
                            $post_data['Order_Shipping_ShipperTrackingNumber']
                                = $shipping[0]['tracking_number'];
                        } elseif ($parameter->name
                            == "Order_Shipping_CarrierName"
                            || $parameter->name == "Order_Shipping_CarrierCode"
                        ) {
                            $carrier_name
                                = $this->marketplaceTechnicalCodeCarriers(
                                    $market_technical_code,
                                    $market_business_code,
                                    $id_carrier
                                );
                            if ($carrier_name && $carrier_name != "") {
                                $post_data[$parameter->name] = $carrier_name;
                            } else {
                                $post_data[$parameter->name]
                                    = $shipping[0]['state_name'];
                            }
                        } elseif ($parameter->name
                            == "Order_Shipping_ShippingUrl"
                        ) {
                            if (array_key_exists('url', $shipping[0])
                                && $shipping[0]['url'] != ""
                            ) {
                                $url = str_replace(
                                    "@",
                                    $shipping[0]['tracking_number'],
                                    $shipping[0]['url']
                                );
                                $post_data['Order_Shipping_ShippingUrl'] = $url;
                            }
                        } elseif ($parameter->name
                            == "Order_Shipping_EstimatedDeliveryDate"
                        ) {
                            //unset($post_data["Order_Shipping_EstimatedDeliveryDate"]);
                            $post_data["Order_Shipping_EstimatedDeliveryDate"]
                                = $today;
                        }
                    }
                    $this->getBeezupOMController()
                        ->changeOrder($order, $post_data);
                    break;
                }
            }
        }

        return true;
    }

    private function marketplaceTechnicalCodeCarriers(
        $code,
        $business_code,
        $id_carrier
    ) {
        $retorno = false;
        if ($code == "PriceMinister") {
            //PriceMinisterCarrierName
            $retorno
                = $this->_getOMStatusCarrier(
                    $this->_jsonCarrierMap->PriceMinister,
                    $id_carrier
                );
        } elseif ($code == "Fnac") {
            //FnacCarrierName
            $retorno = $this->_getOMStatusCarrier(
                $this->_jsonCarrierMap->Fnac,
                $id_carrier
            );
        } elseif ($code == "Bol") {
            //FnacCarrierName
            $retorno = $this->_getOMStatusCarrier(
                $this->_jsonCarrierMap->Bol,
                $id_carrier
            );
        } elseif ($code == "RealDE") {
            //FnacCarrierName
            $retorno
                = $this->_getOMStatusCarrier(
                    $this->_jsonCarrierMap->RealDE,
                    $id_carrier
                );
        } elseif ($code == "Mirakl") {
            if ($business_code == "DARTY") {
                //DartyCarrierCode
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->DARTY,
                        $id_carrier
                    );
                ;
            } elseif ($business_code == "BOULANGER") {
                //BoulangerCarrierCode
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->BOULANGER,
                        $id_carrier
                    );
                ;
            } elseif ($business_code == "LEQUIPE") {
                //LEquipeCarrierCode
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->LEQUIPE,
                        $id_carrier
                    );
                ;
            } elseif ($business_code == "COMPTOIRSANTE") {
                //ComptoirSanteCarrierCode
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->COMPTOIRSANTE,
                        $id_carrier
                    );
                ;
            } elseif ($business_code == "RUEDUCOMMERCE") {
                //RuedDuCommerceCarrierCode
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->RUEDUCOMMERCE,
                        $id_carrier
                    );
                ;
            } elseif ($business_code == "OUTIZ") {
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->OUTIZ,
                        $id_carrier
                    );
            } else {
                $retorno
                    = $this->_getOMStatusCarrier(
                        $this->_jsonCarrierMap->$business_code,
                        $id_carrier
                    );
            }
        }

        return $retorno;
    }


    private function _getOMStatusCarrier($datos, $id_carrier)
    {
        foreach ($datos as $dato) {
            if ($dato->id_carrier == $id_carrier) {
                return $dato->beezup_carrier;
            }
        }

        return false;
    }

    /**
     * Execute hook ExtraRight on product page (it allows declension direct access)
     *
     * @deprecated since version 2.3.0 (No more used in PS 1.5 which include this functionnality)
     * @todo       check and remove if no more used
     * @return void
     */
    public function hookExtraRight()
    {
        $smarty = $this->context->smarty;
        BeezupGlobals::$smartyPrefilterActive = true;

        if (Tools::getIsset('id_product_attribute')) {
            $smarty->force_compile = true;
            include_once(dirname(__FILE__).'/inc/smarty/smarty_filter.php');

            if (Configuration::get('PS_FORCE_SMARTY_2')) {
                $smarty->register_prefilter('smarty_filter_groups');
            } else {
                $smarty->registerFilter('pre', 'smarty_filter_groups');
            }
        }
    }

    /**
     * Execute payment tracker with ValidPayment = false
     *
     * @return void
     */
    public function hookPaymentTop()
    {
        return $this->_getTracker()
            ->execTracker('paymentTop', array());
    }

    /**
     * Hook called when a order is confimed
     * display a message to customer about sponsor discount
     */
    public function hookOrderConfirmation($params)
    {
        if (isset($params['objOrder'])
            && Validate::isLoadedObject($params['objOrder'])
        ) {
            return $this->_getTracker()
                ->execTracker('OrderConfirmation', array($params['objOrder']));
        }
    }

    public function hookUpdateCarrier($params)
    {
        $id_carrier_old = (int)($params['id_carrier']);
        $id_carrier_new = (int)$params['carrier']->id;

        $carriers = Configuration::get('PS_BEEZUP_CARRIERS_FEED');
        $carriers = json_decode($carriers, true);
        if (array_key_exists($id_carrier_old, $carriers)) {
            $carrier = $carriers[$id_carrier_old];
            unset($carriers[$id_carrier_old]);
            $carriers[$id_carrier_new] = $carrier;
        }
        Configuration::updateValue(
            'PS_BEEZUP_CARRIERS_FEED',
            json_encode($carriers)
        );
    }


    /**
     * @todo move it at leas partially(smarty vars) to BeezupOMController
     *
     * @param unknown_type $aParams
     */
    public function hookAdminOrder($aParams)
    {
        try {
            /*	ini_set('display_errors', 1);
            ob_end_flush();*/
            $oPsOrder = new Order(
                array_key_exists('id_order', $aParams)
                    ? $aParams['id_order']
                    : Order::getOrderByCartId($aParams['cart']->id)
            );
            $aResult = array();
            $bPendingSync = false;

            if (!isset($this->context->cookie->beezup_session)) {
                $this->context->cookie->__set('beezup_session', json_encode(array("beezup_msgs" => null, "order_changed" => false, "order_id" => $oPsOrder->id)));
            }
            $session = json_decode($this->context->cookie->beezup_session, true);
            if ($session['order_id'] != $oPsOrder->id) {
                $session['order_changed'] = false;
                $session['beezup_msgs'] = false;
                $session['order_id'] = $oPsOrder->id;
                $this->context->cookie->__set('beezup_session', json_encode($session));
            }

            $aMessages = (array_key_exists('beezup_msgs', $session)
                && is_array($session['beezup_msgs']))
                ? $session['beezup_msgs'] : array();

            $oBeezupOrderResponse = $this->getBeezupOMController()
                ->getBeezupOrderFromPs($oPsOrder);
            if ($oBeezupOrderResponse && $oBeezupOrderResponse->getResult()) {
                $oBeezupOrder = $oBeezupOrderResponse->getResult();
                if (Tools::getValue('beezup_om_action') === 'resync') {
                    $aMessages = $this->getBeezupOMController()
                        ->resyncOrder($oPsOrder);
                    $session['beezup_msgs'] = $aMessages;
                    $session['order_changed'] = false;
                    $this->context->cookie->__set('beezup_session', json_encode($session));
                    Tools::redirectAdmin(
                        'index.php?tab=AdminOrders&id_order='
                        .(int)$oPsOrder->id.'&vieworder'.'&token='
                        .Tools::getAdminTokenLite('AdminOrders')
                    );
                } else if (Tools::getValue('beezup_om_action') === 'order_change'
                        && (!isset($session['order_changed'])
                            || (int)$session['order_changed'] == 0)
                    ) {
                        $bPendingSync = true;
                        // @todo verify return
                        $aMessages = $this->getBeezupOMController()
                            ->changeOrder($oPsOrder, $_POST);
                        $session['beezup_msgs'] = $aMessages;
                        $session['order_changed'] = 1;
                        $this->context->cookie->__set('beezup_session', json_encode($session));
                        Tools::redirectAdmin(
                            'index.php?tab=AdminOrders&id_order='
                            .(int)$oPsOrder->id.'&vieworder'.'&token='
                            .Tools::getAdminTokenLite('AdminOrders')
                        );
                    /*
            // @ todo reload
            // @todo -> resync/import in orderService
            $this->getBeezupOMController()->resyncOrder($oPsOrder);
            $oBeezupOrderResponse = $this->getBeezupOMController()->getBeezupOrderFromPs($oPsOrder);
            if ($oBeezupOrderResponse && $oBeezupOrderResponse->getResult()){
            $oBeezupOrder = $oBeezupOrderResponse->getResult();
            } else {$oBeezupOrder = null;
            }*/
                } else if (array_key_exists('order_changed', $session) && $session['order_changed'] == 1) {
                            // we just displayed messages from order_change (previous branch). activating guard
                            $session['order_changed'] = 2;
                } else if (array_key_exists('order_changed', $session)
                                && $session['order_changed'] > 1
                            ) {
                    // now, refresh were done after previous branch; we are forcing redirection to clean page (without post) and displaying message
                    // Votre demande de changement de statut est déjà en cours
                    $session['order_changed'] = false;

                    if (Tools::getValue('beezup_om_action')
                        === 'order_change'
                    ) {
                        $session['beezup_msgs']
                            = array(
                                'warnings' => array(
                                    $this->l(
                                        'Please wait, your order change request is being processed'
                                    ),
                                ),
                        );
                        Tools::redirectAdmin(
                            'index.php?tab=AdminOrders&id_order='
                            .(int)$oPsOrder->id.'&vieworder'
                            .'&token='
                            .Tools::getAdminTokenLite('AdminOrders')
                        );
                    }
                    $session['beezup_msgs'] = null;
                    $this->context->cookie->__set('beezup_session', json_encode($session));
                } else {
                    $session['beezup_msgs'] = null;
                    $session['order_changed'] = false;
                    $this->context->cookie->__set('beezup_session', json_encode($session));
                }
            } else {
                $oBeezupOrder = null;
                $session['order_changed'] = false;
                $session['beezup_msgs'] = null;
                $this->context->cookie->__set('beezup_session', json_encode($session));
            }
            $this->context->controller->addJQueryUI('ui.dialog');
            $this->context->controller->addJqueryUI('ui.datepicker');

            $aBeezupJSStatusTranslations = $this->getBeezupOMController()
                ->getBeezupOrderStatesList();

            $aBeezupJSTranslations = array(
                'Order_Shipping_FulfillmentDate'       => $this->l('Date'),
                'Order_Shipping_ShipperTrackingNumber' => $this->l(
                    'Tracking number'
                ),
                'Order_Shipping_CarrierName'           => $this->l(
                    'Carrier Name'
                ),
                'Order_Shipping_Method'                => $this->l(
                    'Shipping Method'
                ),
                'ShipOrder'                            => $this->l(
                    'Ship Order'
                ),
                'CancelOrder'                          => $this->l(
                    'Cancel Order'
                ),
                'Order_Shipping_RefundReason'          => $this->l(
                    "Refund Reason"
                ),
                // "Raison remboursement"
                'Restock Order'                        => $this->l(
                    'Waiting for Stock'
                ),
                'RestockOrder'                         => $this->l(
                    'Waiting for Stock'
                ),
                'Order_Restock_DelayInDays'            => $this->l(
                    'Restock Delay (days)'
                ),
                'Order_Restock_Comment'                => $this->l(
                    'Restock Comment'
                ),
                'Order_Cancel_Reason'                  => $this->l(
                    'Cancel Reason'
                ),
                'Order_Cancel_Comment'                 => $this->l(
                    'Cancel Comment'
                ),
                'Order_Comment'                        => $this->l(
                    'Ship comment'
                ),
                'Order_Shipping_ShippingUrl'           => $this->l(
                    'Tracking UR'
                ),
                'ShipOrderUnknowCarrierCode'           => $this->l(
                    'Ship order - other carrier'
                ),
                'Order_Shipping_CarrierCode'           => $this->l('Carrier'),
            );

            $aSmartyVars = array(
                // @todo tab not controller for 1.4
                'beezup_om_debug_mode'      => $this->getBeezupOMController()
                    ->isDebugModeActivated(),
                'beezup_om_test_mode'       => $this->getBeezupOMController()
                    ->isTestModeActivated(),
                'ps_order_id'               => $oPsOrder->id,
                'beezup_module_uri'         => 'index.php?controller=AdminModules&configure=beezup&token='
                    .Tools::getAdminTokenLite('AdminModules'),
                'order_tab_uri'             => 'index.php?controller=AdminOrders&vieworder&id_order='
                    .$oPsOrder->id.'&token='
                    .Tools::getAdminTokenLite('AdminOrders'),
                'beezup_om_order'           => $oBeezupOrder ? $oBeezupOrder
                    : null,
                'beezup_om_actions'         => $this->getBeezupOMController()
                    ->getOrderActions($oBeezupOrder, $oPsOrder),
                'beezup_om_order_translate' => $oBeezupOrder ? array(
                    'beezup_status'      => array_key_exists($oBeezupOrder->getOrderStatusBeezUPOrderStatus(), $aBeezupJSStatusTranslations) ? $aBeezupJSStatusTranslations[$oBeezupOrder->getOrderStatusBeezUPOrderStatus()] : $oBeezupOrder->getOrderStatusBeezUPOrderStatus(),
                    'marketplace_status' => array_key_exists($oBeezupOrder->getOrderStatusMarketPlaceStatus(), $aBeezupJSStatusTranslations) ? $aBeezupJSStatusTranslations[$oBeezupOrder->getOrderStatusMarketPlaceStatus()] : $oBeezupOrder->getOrderStatusMarketPlaceStatus(),) : null,
                'beezup_om_order_infos'     => $oBeezupOrderResponse
                    ? $oBeezupOrderResponse->getInfo()->getInformations()
                    : array(),
                'beezup_om_messages'        => $aMessages,
                'beezup_js_trans'           => json_encode(
                    $aBeezupJSTranslations
                ),
                'info_text_fields'          => $this->l(
                    'Please fill all info before sending'
                ),
                'info_text_no_fields'       => $this->l(
                    'Please confirm this action on this order'
                ),
                'timezone'                  => new DateTimeZone(
                    Configuration::get('PS_TIMEZONE')
                ),
                'pending_sync'              => $bPendingSync,

                'beezup_om_user_id'   => Configuration::get(
                    'BEEZUP_OM_USER_ID'
                ),
                'beezup_om_api_token' => Configuration::get(
                    'BEEZUP_OM_API_TOKEN'
                ),

            );
            $this->context->smarty->assign($aSmartyVars);

            return $this->display(
                __FILE__,
                $this->getRelativeTemplatesPath()
                .DIRECTORY_SEPARATOR.'admin_order.tpl'
            );
        } catch (exception $oException) {
            die($oException->getMessage());
        }
    }

    public function hookDisplayAdminOrderContentOrder($aParams)
    {
        $aParams['id_order'] = $aParams['order']->id;

        return $this->hookAdminOrder($aParams);
    }

    public function hookDisplayAdminOrderTabOrder($aParams)
    {
        return '<li><a href="#beezup"><i class="icon-shopping-cart"></i> BeezUP</a></li>';
    }


    /**
     * Returns store id for the current language. Can return empty string
     *
     * @return string
     */
    protected function getStoreId()
    {
        $id_lang = (int)((Context::getContext()
            && Context::getContext()->language)
            ? Context::getContext()->language->id
            : Configuration::get('PS_LANG_DEFAULT'));

        return array_key_exists($id_lang, $this->_conf['BEEZUP_TRACKER_STORE_IDS']) ? $this->_conf['BEEZUP_TRACKER_STORE_IDS'][$id_lang] : '';
    }

    protected function getRelativeTemplatesPath()
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array(
                'views',
                'templates',
                'admin',
                $this->isPresta16x() ? 'templates' : 'templates1.5',
            )
        );
    }

    public function setCurrency($id_currency = null)
    {
        if (!$id_currency && Tools::getValue('currency_iso')) {
            $id_currency
                = Currency::getIdByIsoCode(
                    Tools::strtoupper(Tools::getValue('currency_iso'))
                );
        }
        if (!$id_currency) {
            $id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        } // if
        BeezupProduct::setCurrency(new Currency($id_currency));

        return $id_currency;
    } //

    # BEEZUP OM CONTROLLER

    /**
     * Returns instance of BeezupOMController. Initializes it if necessary.
     *
     * @return BeezupOMController
     */
    public function getBeezupOMController()
    {
        if ($this->om_controller === null) {
            $this->om_controller = $this->createBeezupOMController();
        }

        return $this->om_controller;
    }

    /**
     * Creates new instance of BeezupOMController
     *
     * @return BeezupOMController
     */
    public function createBeezupOMController()
    {
        require_once(_PS_MODULE_DIR_.'/beezup/inc/om/BeezupOMController.php');

        return new BeezupOMController($this);
    }

    # CONFIGURATION HANDLING

    /**
     * Returns default, one and only configuration object
     *
     * @todo ADD MULTISHOP
     * @return BeezupConfiguration
     */
    protected function getBeezupConfiguration()
    {
        return BeezupConfiguration::get('default');
        // return new BeezupConfiguration(1, _USER_ID_LANG_);
    }

    # HANDLING CLEANUPS

    public function cleanup()
    {
        return $this->deleteModelsFromClassDir();
    }

    protected function doCleanup()
    {
        $messages = array();
        if ($this->cleanup() && !$this->haveModelsInClassDir()) {
            $messages[] = array(
                'class' => 'success alert alert-success',
                'text'  => $this->l('Cleanup successed'),
            );
        } else {
            $messages[] = array(
                'class' => 'error alert alert-danger',
                'text'  => $this->l('Cleanup error'),
            );
        }
        $messages = array_merge($messages, $this->getCleanupMessages());

        return $messages;
    }

    protected function getCleanupMessages()
    {
        $messages = array();
        if ($this->haveModelsInClassDir()) {
            $messages[] = array(
                'class'      => 'warning alert alert-warning',
                'text'       => $this->l('There are models in class dir'),
                'action'     => $this->l('Perform models cleanup'),
                'action_url' => 'index.php?controller=AdminModules&token='
                    .Tools::getAdminTokenLite('AdminModules')
                    .'&configure=beezup&action=cleanup',
            );
        }

        return $messages;
    }

    protected function getModelsFromClassDir()
    {
        clearstatcache();

        return glob(
            implode(
                DIRECTORY_SEPARATOR,
                array(_PS_CLASS_DIR_, 'Beezup*.php')
            )
        );
    }

    public function haveModelsInClassDir()
    {
        return count($this->getModelsFromClassDir()) !== 0;
    }

    protected function deleteModelsFromClassDir()
    {
        $success = true;
        if ($this->getModelsFromClassDir()) {
            $result = @array_map('unlink', $this->getModelsFromClassDir());
            $success = ($result === array_filter($result));
            if (version_compare(_PS_VERSION_, '1.5.0.1', 'ge')) {
                Autoload::getInstance()->generateIndex();
            }
        }

        return $success;
    }



    /** AUTOINSTALLER */

    /**
     * ModuleAutoInstaller version
     *
     * @var string
     */
    public static $MAI_VERSION = '1.0b';
    /**
     * Use log
     *
     * @var boolean
     */
    protected $_logInstall = true;
    /**
     * version error translations
     * ex: array('iso_code'=>'str')
     *
     * @var array
     */
    protected $__lang
        = array(
            'en' => 'WARNING : module %s require at least Prestashop v%s',
            'fr' => 'ATTENTION : le module %s n&eacute;cessite au minimum Prestashop v%s',
        );


    /**
     * Prestashop minimum version
     *
     * @var string
     */
    protected $_min_ps_version = '1.0';
    /**
     * Files to install
     * ex: array('from'=>'to')
     *
     * @var array
     */
    protected $_installFiles = array();
    /**
     * Files to save before installing
     * ex: array('from'=>'to')
     *
     * @var array
     */
    protected $_updateFiles = array();
    /**
     * Dir from module base dir for file saving
     *
     * @var string
     */
    protected $_updateSavePath = 'install/restore/';
    /**
     * SQL files to install
     * ex: array('file_path')
     *
     * @var array
     */
    protected $_installDbFiles = array();
    /**
     * SQL files to uninstall
     * ex: array('file_path')
     *
     * @var array
     */
    protected $_uninstallDbFiles = array();
    /**
     * Hooks to register
     * ex: array('hook_name');
     *
     * @var array
     */
    protected $_installHooks = array();
    /**
     * Tabs to create
     * ex: array( array('class'=>'AdminClassName','name'=>'Tab name'[,'parent'=>1[,'icon'=>'file']]) )
     *
     * @var array
     */
    protected $_installTabs = array();





    /**
     * Install module with this order :
     *   - Files
     *   - SQL
     *   - Tabs
     *   - Module
     *   - Hooks
     *
     * @return boolean
     */
    public function _install()
    {
        $this->_log(
            "--------------------------------------------------\r\n----------        INSTALL STARTED       ----------\r\n--------------------------------------------------",
            'install',
            false
        );

        if (!$this->_isCompatibleVersion()) {
            $this->_log("ERROR: Incompatible PS version.");
            $this->_log(
                "--------------------------------------------------\r\n--- INSTALL ABORTED DUE TO INSTALLATION ERRORS ---\r\n--------------------------------------------------",
                'install',
                false
            );

            return false;
        }

        if (!$this->_checkWritablePaths()) {
            $this->_log("ERROR: Unable to write some files.");
            $this->_log(
                "--------------------------------------------------\r\n--- INSTALL ABORTED DUE TO INSTALLATION ERRORS ---\r\n--------------------------------------------------",
                'install',
                false
            );

            return false;
        }


        parent::install();

        if (!$this->_installFiles()
            || !$this->_updateFiles()
            || !$this->_installSQL()
            || !$this->_installTabs()
            || !$this->_installHooks()
        ) {
            $this->_log(
                "--------------------------------------------------\r\n--- INSTALL ABORTED DUE TO INSTALLATION ERRORS ---\r\n--------------------------------------------------",
                'install',
                false
            );

            return false;
        } else {
            Configuration::updateValue(
                "BEEZUP_OM_IMPORT_FILTER_STATUS",
                "New,InProgress,Shipped,Closed,Aborted,Pending"
            );
            Configuration::updateValue("BEEZUP_ORDER_STATUS_FILTER", 1);
            $this->_log(
                "--------------------------------------------------\r\n----------  INSTALL SUCCESSFULLY ENDED  ----------\r\n--------------------------------------------------",
                'install',
                false
            );

            return true;
        }
    }

    /**
     * Uninstall module with this order :
     *   - Hooks
     *   - Module
     *   - Tabs
     *   - SQL
     *   - Files
     *
     * @return boolean
     */
    public function _uninstall()
    {
        $this->_log(
            "--------------------------------------------------\r\n----------       UNINSTALL STARTED      ----------\r\n--------------------------------------------------",
            'uninstall',
            false
        );

        if (!$this->_isCompatibleVersion()) {
            $this->_log("ERROR: Incompatible PS version.", 'uninstall');
            $this->_log(
                "--------------------------------------------------\r\n-- UNINSTALL ABORTED DUE TO INSTALLATION ERRORS --\r\n--------------------------------------------------",
                'uninstall',
                false
            );

            return false;
        }

        if (!$this->_uninstallHooks()
            || !$this->_uninstallTabs()
            || !$this->_uninstallSQL()
            || !$this->_restoreFiles()
            || !$this->_uninstallFiles()
        ) {
            $this->_log(
                "--------------------------------------------------\r\n-- UNINSTALL ABORTED DUE TO INSTALLATION ERRORS --\r\n--------------------------------------------------",
                'uninstall',
                false
            );

            return false;
        } else {
            $this->_log(
                "--------------------------------------------------\r\n---------- UNINSTALL SUCCESSFULLY ENDED ----------\r\n--------------------------------------------------",
                'uninstall',
                false
            );

            return parent::uninstall();
        }
    }

    /**
     * Insert entry to log file
     *
     * @param string  $str
     * @param string  $action
     * @param boolean $date
     *
     * @return void
     */
    protected function _log($str = '', $action = 'install', $date = true)
    {
        # If log is disabled => do nothing
        if (!$this->_logInstall) {
            return;
        }

        $file = dirname(__FILE__).DIRECTORY_SEPARATOR
            .$action.'.log';

        $content = ($date ? '['.date('Y-m-d H:i:s:u').'] ' : '').$str."\r\n";

        file_put_contents($file, $content, FILE_APPEND);
    }

    /**
     * Compare ps minimum required version and ps current version
     *
     * @return boolean
     */
    protected function _isCompatibleVersion()
    {
        return version_compare(_PS_VERSION_, $this->_min_ps_version, '>=');
    }


    /**
     * Check if files paths are writable
     *
     * @return boolean
     */
    protected function _checkWritablePaths()
    {
        $paths = array_merge(
            array_values($this->_installFiles),
            array_values($this->_updateFiles)
        );

        $return = true;

        foreach ($paths as $path) {
            if (!$this->_checkWritableFile($path)) {
                $this->_errors[] = '['.$path.'] is not writable';
                $this->_log('['.$path.'] is not writable');
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Check if givent path is writable
     *
     * @param string $path
     *
     * @return boolean
     */
    protected function _checkWritableFile($path)
    {
        if (file_exists($path)) {
            return is_writable($path);
        } else {
            return $this->_checkWritableFile(dirname(__FILE__));
        }
    }


    /**
     * Files installation method
     *
     * @return boolean
     */
    protected function _installFiles()
    {
        $this->_log('--- FILES INSTALLATION STARTED ---');
        # File installation disabled
        if ($this->_installFiles === false) {
            $this->_log('SUCCESS: Files installation disabled.');
            $this->_log('--- FILES INSTALLATION ENDED ---');

            return true;
        } # File list is not an array
        elseif (!is_array($this->_installFiles)) {
            $this->_log('ERROR: Files list is not an array.');
            $this->_log('--- FILES INSTALLATION ENDED ---');

            return false;
        } # File list is empty
        elseif (!sizeof($this->_installFiles)) {
            $this->_log('SUCCESS: No file to install.');
            $this->_log('--- FILES INSTALLATION ENDED ---');

            return true;
        }

        foreach ($this->_installFiles as $from => &$to) {
            if (file_exists($to)) {
                $this->_log(
                    "WARNING: file '{$to}' allready exists, it will be overwrited."
                );
            }

            $dir = dirname($to);
            if (!file_exists($dir)) {
                $this->_log(
                    "NOTICE: directory '$dir' doesn't exists, it will be created."
                );

                if (!mkdir($dir, 0755, true)) {
                    $this->_log(
                        "ERROR: Unable to create directory '$dir', unable to install file '"
                        .basename($to)."'."
                    );
                    $this->_log('--- FILES INSTALLATION ENDED ---');

                    return false;
                }

                $this->_log("NOTICE: directory '$dir' created.");
            }

            if (!copy($from, $to)) {
                $this->_log(
                    "ERROR: Unable to install file from:'{$from}' to:'{$to}'"
                );
                $this->_log('--- FILES INSTALLATION ENDED ---');

                return false;
            } else {
                $this->_log("INSTALLED: '$to'");
            }
        }

        $this->_log('--- FILES INSTALLATION ENDED ---');

        return true;
    }

    /**
     * Files uninstallation method
     *
     * @return boolean
     */
    protected function _uninstallFiles()
    {
        $this->_log('--- FILES UNINSTALLATION STARTED ---', 'uninstall');
        # File installation disabled
        if ($this->_installFiles === false) {
            $this->_log('SUCCESS: Files uninstallation disabled.', 'uninstall');
            $this->_log('--- FILES UNINSTALLATION ENDED ---', 'uninstall');

            return true;
        } # File list is not an array
        elseif (!is_array($this->_installFiles)) {
            $this->_log('ERROR: Files list is not an array.', 'uninstall');
            $this->_log('--- FILES UNINSTALLATION ENDED ---', 'uninstall');

            return false;
        } # File list is empty
        elseif (!sizeof($this->_installFiles)) {
            $this->_log('SUCCESS: No file to uninstall.', 'uninstall');
            $this->_log('--- FILES UNINSTALLATION ENDED ---', 'uninstall');

            return true;
        }

        foreach ($this->_installFiles as &$file) {
            if (!file_exists($file)) {
                $this->_log(
                    "WARNING: file '$file' does not exist.",
                    'uninstall'
                );
            } elseif (!unlink($file)) {
                $this->_log(
                    "ERROR: unable to delete file '$file'.",
                    'uninstall'
                );
                $this->_log('--- FILES UNINSTALLATION ENDED ---', 'uninstall');

                return false;
            } else {
                $this->_log("DELETED: '$file'", 'uninstall');
            }
        }

        $this->_log('--- FILES UNINSTALLATION ENDED ---', 'uninstall');

        return true;
    }

    /**
     * File update method
     *
     * @return boolean
     */
    protected function _updateFiles()
    {
        $this->_log('--- FILES UPDATE STARTED ---');
        # File update disabled
        if ($this->_updateFiles === false) {
            $this->_log('SUCCESS: Files update disabled.');
            $this->_log('--- FILES UPDATE ENDED ---');

            return true;
        } # File list is not an array
        elseif (!is_array($this->_updateFiles)) {
            $this->_log('ERROR: Files list is not an array.');
            $this->_log('--- FILES UPDATE ENDED ---');

            return false;
        } # File list is empty
        elseif (!sizeof($this->_updateFiles)) {
            $this->_log('SUCCESS: No file to update.');
            $this->_log('--- FILES UPDATE ENDED ---');

            return true;
        }

        foreach ($this->_updateFiles as $from => &$to) {
            if (!file_exists($to)) {
                $this->_log(
                    "WARNING: file '{$to}' doesn't exists, it can't be saved."
                );
            } elseif (!copy(
                $to,
                dirname(__FILE__).DIRECTORY_SEPARATOR
                .trim($this->_updateSavePath, '\t\s \r\n/\\')
                .DIRECTORY_SEPARATOR.md5($to).'.'.basename($to)
            )
            ) {
                $this->_log(
                    "ERROR: Unable to install file from:'{$from}' to:'"
                    .dirname(__FILE__).DIRECTORY_SEPARATOR
                    .trim($this->_updateSavePath, '\t\s \r\n/\\')
                    .DIRECTORY_SEPARATOR.md5($to).'.'.basename($to)."'"
                );
                $this->_log('--- FILES UPDATE ENDED ---');

                return false;
            }

            if (!copy($from, $to)) {
                $this->_log(
                    "ERROR: Unable to save file from:'{$to}' to:'{$to}'"
                );
                $this->_log('--- FILES UPDATE ENDED ---');

                return false;
            } else {
                $this->_log("UPDATED: '$to'");
            }
        }

        $this->_log('--- FILES UPDATE ENDED ---');

        return true;
    }

    /**
     * File restore method
     *
     * @return boolean
     */
    protected function _restoreFiles()
    {
        $this->_log('--- FILES RESTORATION STARTED ---', 'uninstall');
        # File installation disabled
        if ($this->_updateFiles === false) {
            $this->_log('SUCCESS: Files restoration disabled.', 'uninstall');
            $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

            return true;
        } # File list is not an array
        elseif (!is_array($this->_updateFiles)) {
            $this->_log('ERROR: Files list is not an array.', 'uninstall');
            $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

            return false;
        } # File list is empty
        elseif (!sizeof($this->_updateFiles)) {
            $this->_log('SUCCESS: No file to restore.', 'uninstall');
            $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

            return true;
        }

        foreach ($this->_updateFiles as &$file) {
            if (!file_exists($file)) {
                $this->_log(
                    "WARNING: file '$file' does not exist.",
                    'uninstall'
                );
            } elseif (!unlink) {
                $this->_log(
                    "ERROR: unable to delete file '$file'.",
                    'uninstall'
                );
                $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

                return false;
            } else {
                $this->_log("DELETED: '$file'", 'uninstall');
            }

            $saved = dirname(__FILE__).DIRECTORY_SEPARATOR
                .trim($this->_updateSavePath, '\t\s \r\n/\\')
                .DIRECTORY_SEPARATOR.md5($file);

            if (!file_exists($saved)) {
                $this->_log(
                    "WARNING: file '$saved' does not exist.",
                    'uninstall'
                );
            } elseif (!copy($saved, $file)) {
                $this->_log(
                    "ERROR: unable to restore file '$file'.",
                    'uninstall'
                );
                $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

                return false;
            } else {
                $this->_log("RESTORED: '$file'", 'uninstall');
            }
        }

        $this->_log('--- FILES RESTORATION ENDED ---', 'uninstall');

        return true;
    }

    /**
     * Install DB from files
     *
     * @return boolean
     */
    protected function _installSQL()
    {
        $this->_log('--- DB INSTALLATION STARTED ---');
        # SQL install disabled
        if ($this->_installDbFiles === false) {
            $this->_log('SUCCESS: DB installation disabled.');
            $this->_log('--- DB INSTALLATION ENDED ---');

            return true;
        }

        if (is_string($this->_installDbFiles)) {
            $this->_installDbFiles = array($this->_installDbFiles);
        }

        foreach ($this->_installDbFiles as &$file) {
            if (!file_exists($file)) {
                $this->_log("ERROR: file does'nt exists '$file'.");
                $this->_log('--- DB INSTALLATION ENDED ---');

                return false;
            }

            $queries = explode(';', Tools::file_get_contents($file));
            $queries = array_map('trim', $queries);

            foreach ($queries as &$query) {
                if ($query && !empty($query)) {
                    $query = str_replace('::DB_PREFIX::', _DB_PREFIX_, $query);
                    if (!Db::getInstance()->Execute($query)) {
                        $this->_log(
                            "ERROR: $query\r\n------------\r\n"
                            .Db::getInstance()->getMsgError()
                        );
                        $this->_log('--- DB INSTALLATION ENDED ---');

                        return false;
                    }

                    $this->_log("SUCCESS: QUERY:\r\n$query\r\n");
                }
            }
        }

        $this->_log('--- DB INSTALLATION ENDED ---');

        return true;
    }

    /**
     * Uninstall DB from files
     *
     * @return boolean
     */
    protected function _uninstallSQL()
    {
        $this->_log('--- DB UNINSTALLATION STARTED ---', 'uninstall');
        # SQL install disabled
        if ($this->_uninstallDbFiles === false) {
            $this->_log('SUCCESS: DB uninstallation disabled.', 'uninstall');
            $this->_log('--- DB UNINSTALLATION ENDED ---', 'uninstall');

            return true;
        }

        if (is_string($this->_uninstallDbFiles)) {
            $this->_uninstallDbFiles = array($this->_uninstallDbFiles);
        }

        foreach ($this->_uninstallDbFiles as &$file) {
            if (!file_exists($file)) {
                $this->_log("ERROR: file does'nt exists '$file'.", 'uninstall');
                $this->_log('--- DB UNINSTALLATION ENDED ---', 'uninstall');

                return false;
            }

            $queries = explode(';', Tools::file_get_contents($file));
            $queries = array_map('trim', $queries);

            foreach ($queries as &$query) {
                if ($query && !empty($query)) {
                    $query = str_replace('::DB_PREFIX::', _DB_PREFIX_, $query);
                    if (!Db::getInstance()->Execute($query)) {
                        $this->_log(
                            "ERROR: $query\r\n------------\r\n"
                            .Db::getInstance()->getMsgError(),
                            'uninstall'
                        );
                        $this->_log(
                            '--- DB UNINSTALLATION ENDED ---',
                            'uninstall'
                        );

                        return false;
                    }

                    $this->_log("SUCCESS: QUERY:\r\n$query\r\n", 'uninstall');
                }
            }
        }

        $this->_log('--- DB UNINSTALLATION ENDED ---', 'uninstall');

        return true;
    }

    /**
     * Add module to hook. Create hook if it doesn't exists
     *
     * @return boolean
     */
    protected function _installHooks()
    {
        $this->_log('--- HOOK INSTALLATION STARTED ---');
        # Hook install disabled
        if ($this->_installHooks === false) {
            $this->_log('SUCCESS: Hook installation disabled.');
            $this->_log('--- HOOK INSTALLATION ENDED ---');

            return true;
        }

        if (is_string($this->_installHooks)) {
            $this->_installHooks = array($this->_installHooks);
        }

        foreach ($this->_installHooks as &$hook_name) {
            $existingHook = is_callable(array('Hook', 'getIdByName'))
                ? Hook::getIdByName($hook_name) : Hook::get($hook_name);
            if (!$existingHook) {
                $this->_log("NOTICE: Hook '$hook_name' doesn't exist.");

                $hook = new Hook();
                $hook->name = $hook_name;
                if (!$hook->add()) {
                    $this->_log("ERROR: Unable to create hook '$hook_name'.");
                    $this->_log('--- HOOK INSTALLATION ENDED ---');

                    return false;
                }
                $this->_log("NOTICE: Hook '$hook_name' created.");
            }

            if (!$this->registerHook($hook_name)) {
                $this->_log(
                    "ERROR: Unable to register module '{$this->name}' to hook '$hook_name'."
                );
                $this->_log('--- HOOK INSTALLATION ENDED ---');

                return false;
            } else {
                $this->_log(
                    "SUCCESS: Module '{$this->name}' registered to hook '$hook_name'."
                );
            }
        }

        $this->_log('--- HOOK INSTALLATION ENDED ---');

        return true;
    }

    /**
     * Delete module from hook
     *
     * @return boolean
     */
    protected function _uninstallHooks()
    {
        $this->_log('--- HOOK UNINSTALLATION STARTED ---', 'uninstall');

        # Hook uninstall disabled
        if ($this->_installHooks === false) {
            $this->_log('SUCCESS: Hook uninstallation disabled.', 'uninstall');
            $this->_log('--- HOOK UNINSTALLATION ENDED ---', 'uninstall');

            return true;
        }

        if (is_string($this->_installHooks)) {
            $this->_installHooks = array($this->_installHooks);
        }

        foreach ($this->_installHooks as $hook_name) {
            if (method_exists('Hook', 'getNameById')) {
                $id_hook = (int)Hook::getNameById($hook_name);
            } else {
                $id_hook = (int)Hook::getIdByName($hook_name);
            }

            if (!$id_hook) {
                $this->_log(
                    "WARNING: Hook '$hook_name' doesn't exists.",
                    'uninstall'
                );
                continue;
            }

            if (!$this->unregisterHook($id_hook)) {
                $this->_log(
                    "ERROR: Unable to unregister module '' from hook ''.",
                    'uninstall'
                );
                $this->_log('--- HOOK UNINSTALLATION ENDED ---', 'uninstall');

                return false;
            } else {
                $this->_log(
                    "SUCCESS: Module '{$this->name}' unregistered from hook '$hook_name'.",
                    'uninstall'
                );
            }
        }

        $this->_log('--- HOOK UNINSTALLATION ENDED ---', 'uninstall');

        return true;
    }

    /**
     * Admin Tab Installation
     *
     * @return boolean
     */
    protected function _installTabs()
    {
        $this->_log('--- TAB INSTALLATION STARTED ---');

        # Hook uninstall disabled
        if ($this->_installTabs === false) {
            $this->_log('SUCCESS: Tab installation disabled.');
            $this->_log('--- TAB INSTALLATION ENDED ---');

            return true;
        } elseif (!is_array($this->_installTabs)) {
            $this->_log("ERROR: Tab list isn't an array.");
            $this->_log('--- TAB INSTALLATION ENDED ---');

            return false;
        }

        foreach ($this->_installTabs as &$tab) {
            if (!isset($tab['class'])
                || !isset($tab['name'])
            ) {
                $this->_log("ERROR: Tab entry wrong format");
                $this->_log('--- TAB INSTALLATION ENDED ---');

                return false;
            }

            $adminTab = new Tab();
            $adminTab->class_name = $tab['class'];

            if (!isset($tab['parent'])) {
                $adminTab->id_parent = 0;
            } elseif (preg_match('/^[0-9]+$/', $tab['parent'])) {
                $adminTab->id_parent = (int)$tab['parent'];
            } elseif (!empty($tab['parent'])) {
                $id_parent = (int)Tab::getIdFromClassName($tab['parent']);

                if (!$id_parent) {
                    $this->_log("ERROR: Tab parent doesn't exists.");
                    $this->_log('--- TAB INSTALLATION ENDED ---');

                    return false;
                }
                $adminTab->id_parent = $id_parent;
            } else {
                $adminTab->id_parent = 0;
            }

            if (array_key_exists('active', $tab)) {
                $adminTab->active = $tab['active'];
            }

            $adminTab->module = $this->name;

            $adminTab->name = (is_array($tab['name'])
                ? $tab['name']
                : array(
                    (int)Configuration::get(
                        'PS_LANG_DEFAULT'
                    ) => $tab['name'],
                ));

            if (!$adminTab->add()) {
                $this->_log("ERROR: Unable to add tab for '{$tab['class']}'");
                $this->_log('--- TAB INSTALLATION ENDED ---');

                return false;
            }

            if (array_key_exists('icon', $tab)) {
                if (!file_exists($tab['icon'])) {
                    $this->_log(
                        "ERROR: Icon file '{$tab['icon']}' doesn't exists."
                    );
                    $this->_log('--- TAB INSTALLATION ENDED ---');

                    return false;
                }

                if (version_compare(_PS_VERSION_, '1.2', '>=')) {
                    if (!copy(
                        $tab['icon'],
                        _PS_IMG_DIR_.'t/'.$tab['class'].'.gif'
                    )
                    ) {
                        $this->_log(
                            "ERROR: Unable to copy icon file '{$tab['icon']}'."
                        );
                        $this->_log('--- TAB INSTALLATION ENDED ---');

                        return false;
                    }
                } else {
                    if (!copy(
                        $tab['icon'],
                        _PS_IMG_DIR_.'t/'.(int)$adminTab->id.'.gif'
                    )
                    ) {
                        $this->_log(
                            "ERROR: Unable to copy icon file '{$tab['icon']}'."
                        );
                        $this->_log('--- TAB INSTALLATION ENDED ---');

                        return false;
                    }
                }
            }

            $this->_log("SUCCESS: Tab created for '{$tab['class']}' class.");
        }
        $this->_log('--- TAB INSTALLATION ENDED ---');

        return true;
    }

    /**
     * Admin Tab Uninstallation
     *
     * @return boolean
     */
    protected function _uninstallTabs()
    {
        $this->_log('--- TAB UNINSTALLATION STARTED ---', 'uninstall');

        # Hook uninstall disabled
        if ($this->_installTabs === false) {
            $this->_log('SUCCESS: Tab uninstallation disabled.', 'uninstall');
            $this->_log('--- TAB UNINSTALLATION ENDED ---', 'uninstall');

            return true;
        } elseif (!is_array($this->_installTabs)) {
            $this->_log("ERROR: Tab list isn't an array.", 'uninstall');
            $this->_log('--- TAB UNINSTALLATION ENDED ---', 'uninstall');

            return false;
        }

        foreach ($this->_installTabs as &$tab) {
            if (!isset($tab['class'])) {
                $this->_log("ERROR: Tab entry wrong format", 'uninstall');
                $this->_log('--- TAB UNINSTALLATION ENDED ---', 'uninstall');

                return false;
            }

            $id_tab = (int)Tab::getIdFromClassName($tab['class']);

            if (!$id_tab) {
                $this->_log(
                    "NOTICE: No tabs for class '{$tab['class']}'",
                    'uninstall'
                );
                continue;
            }

            $adminTab = new Tab($id_tab);

            if (!$adminTab->delete()) {
                $this->_log(
                    "ERROR: Unable to uninstall tab #{$id_tab}.",
                    'uninstall'
                );
                $this->_log('--- TAB UNINSTALLATION ENDED ---', 'uninstall');

                return false;
            }

            $this->_log(
                "SUCCESS: Tab uninstalled for class '{$tab['class']}'",
                'uninstall'
            );
        }

        $this->_log('--- TAB UNINSTALLATION ENDED ---', 'uninstall');

        return true;
    }

    /** END AUTOINSTALLER */
}

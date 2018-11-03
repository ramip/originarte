<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupRegistry
{
    /** @var BeezupRegistry Registry instance (Singleton Pattern) */
    protected static $instance;

    /** @var array Registry datas */
    protected $datas = array();

    /** @var array Datas properties */
    protected $confOpts
        = array(
            // Tracker keys
            'BEEZUP_TRACKER_ACTIVE'    => array('override' => false),
            'BEEZUP_TRACKER_URL'       => array('override' => false),
            'BEEZUP_TRACKER_PRICE'     => array('override' => false),
            'BEEZUP_TRACKER_STORE_IDS' => array('override' => false),

            'BEEZUP_TRACKER_VALIDATE_STATE' => array('override' => false),

            // General
            'BEEZUP_SITE_ADDRESS'           => array('override' => false),
            'BEEZUP_DEBUG_MODE'             => array('override' => true),
            'BEEZUP_COUNTRY'                => array('override' => true),
            'BEEZUP_LANG'                   => array('override' => true),
            'BEEZUP_ALL_SHOPS'              => array('override' => true),
            'BEEZUP_NEW_PRODUCT_ID_SYSTEM'  => array('override' => true),
            'BEEZUP_CATEGORY_DEEPEST'       => array('override' => true),

            // Caching + Cron
            'BEEZUP_USE_CACHE'              => array('override' => true),
            'BEEZUP_CACHE_VALIDITY'         => array('override' => true),
            'BEEZUP_USE_CRON'               => array('override' => true),
            'BEEZUP_CRON'                   => array('override' => true),

            // technical
            'BEEZUP_MEMORY_LIMIT'           => array('override' => true),
            'BEEZUP_TIME_LIMIT'             => array('override' => true),
            'BEEZUP_BATCH_SIZE'             => array('override' => true),


            'BEEZUP_OM_USER_ID'      => array('override' => false),
            'BEEZUP_OM_API_TOKEN'    => array('override' => false),
            'BEEZUP_OM_SYNC_TIMEOUT' => array('override' => false),

            'BEEZUP_OM_LAST_SYNCHRONIZATION' => array('override' => false),
            'BEEZUP_OM_STATUS_MAPPING'       => array('override' => false),
            'BEEZUP_OM_STORES_MAPPING'       => array('override' => false),
            'BEEZUP_OM_CARRIERS_MAPPING'     => array('override' => false),
            'BEEZUP_OM_ID_FIELD_MAPPING'     => array('override' => false),

            'BEEZUP_OM_DEFAULT_CARRIER_ID' => array('override' => false),
            'BEEZUP_OM_FORCE_CART_ADD'     => array('override' => false),
            'BEEZUP_OM_TOLERANCE'          => array('override' => false),


        );

    /**
     * Registry constructor; load datas from DB. Restricted for Singleton pattern
     *
     * @return void
     */
    protected function __construct()
    {
        $this->reload();
    }

    /**
     * Restricted for Singleton pattern
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * Get registry instance (Singleton Pattern)
     *
     * @return BeezupRegistry
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Reload datas
     *
     * @return BeezupRegistry
     */
    public function reload()
    {
        foreach ($this->confOpts as $key => $opts) {
            $this->datas[$key] = Configuration::get($key);

            if ($opts['override']) {
                $val = Tools::getValue($key, '');
                if (is_array($val) || trim((string)$val) !== '') {
                    $this->datas[$key] = $val;
                }
            }
        }

        return $this;
    }

    /**
     * Magic method to use datas as class properties
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return isset($this->datas[$key]) ? $this->datas[$key] : null;
    }

    /**
     * Get data by its name
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        return self::getInstance()->$key;
    }
}

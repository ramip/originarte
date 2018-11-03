<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

final class BeezupAutoloader
{
    /** @var boolean Is Autoloader allready registered */
    protected static $registered = false;

    /** @var array Class path links */
    protected static $paths
        = array(
            'BeezupConfiguration'       => 'beezup/inc/BeezupConfiguration.php',
            'BeezupTest'                => 'beezup/inc/BeezupTest.php',
            'BeezupField'               => 'beezup/inc/BeezupField.php',
            'BeezupFieldProcessor'      => 'beezup/inc/BeezupFieldProcessor.php',
            'BeezupModuleAutoInstaller' => 'beezup/inc/BeezupModuleAutoInstaller.php',
            'BeezupProcessorAbstract'   => 'beezup/inc/processors/common/BeezupProcessorAbstract.php',
            'BeezupProcessorInterface'  => 'beezup/inc/processors/common/BeezupProcessorInterface.php',
            'BeezupProduct'             => 'beezup/inc/BeezupProduct.php',
            'BeezupRegistry'            => 'beezup/inc/BeezupRegistry.php',
            'BeezupTrackerAbstract'     => 'beezup/inc/trackers/common/BeezupTrackerAbstract.php',
            'BeezupTrackerPhp'          => 'beezup/inc/trackers/BeezupTrackerPhp.php',
            'BeezupCombination'         => 'beezup/inc/BeezupCombination.php',
            'BeezupStaticProcessor'     => 'beezup/inc/processors/common/BeezupStaticProcessor.php',
        );

    /**
     * Load class if known in path list
     *
     * @param string $className
     *
     * @return void
     */
    public static function load($className)
    {
        if (array_key_exists($className, self::$paths)) {
            require_once _PS_MODULE_DIR_.ltrim(self::$paths[$className], '\\/');
        }
    }

    /**
     * Register autoloader in spl_autoload if not allready registered
     *
     * @return void
     */
    public static function register()
    {
        if (!self::$registered) {
            spl_autoload_register(array('BeezupAutoloader', 'load'));
            self::$registered = true;
        }
    }

    /**
     * Unregister autoloader from spl_autoload
     *
     * @return void
     */
    public static function unregister()
    {
        if (self::$registered) {
            spl_autoload_unregister(array('BeezupAutoloader', 'load'));
            self::$registered = false;
        }
    }
}

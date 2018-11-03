<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupTest
{
    /** @var boolean is PHP timeout configuration compatible */
    protected $time = false;

    /** @var boolean is PHP memory limit configuration compatible */
    protected $memory = false;

    /** @var boolean is cache dir writable */
    protected $cacheDir = false;

    /** @var boolean is log dir writable */
    protected $logDir = false;

    /** @var boolean is PHP CURL module installed */
    protected $curlModule = false;

    /** @var boolean is Prestashop version compatible */
    protected $psVersion = false;

    /** @var array Convertion table (b, Kb, Mb, Gb) */
    protected static $conv
        = array(
            'b'  => 1,
            'kb' => 1024,
            'mb' => 1048576,
            'gb' => 1073741824,
        );

    /**
     * Magic method to get test result
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __get($name)
    {
        return (bool)$this->$name;
    }

    /**
     * Launch test suite
     *
     * @return void
     */
    public function test()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (preg_match('/^test[0-9a-z]+$/i', $method)) {
                $this->$method();
            }
        }
    }

    /**
     * Test timeout configuration
     *
     * @return void
     */
    protected function testTime()
    {
        $old = (int)ini_get('max_execution_time');
        $new = $old + 1;
        ini_set('max_execution_time', $new);
        $this->time = ini_get('max_execution_time') == $new;
        ini_set('max_execution_time', $old);
    }

    /**
     * Test memory limit configuration
     *
     * @return void
     */
    protected function testMemory()
    {
        $old = ini_get('memory_limit');
        preg_match('/^([0-9]+)([KMG]?)$/i', $old, $matches);

        $unit = Tools::strtolower($matches[2]).'b';
        $size = (int)$matches[1] * (Tools::getIsset(self::$conv[$unit])
                ? self::$conv[$unit] : 1);
        $size += 1024;

        ini_set('memory_limit', $size);
        $this->memory = ini_get('memory_limit') == $size;
        ini_set('memory_limit', $old);
    }

    /**
     * Test cache directory access
     *
     * @return void
     */
    protected function testCacheDir()
    {
        $cache = _PS_MODULE_DIR_.'beezup/views/cache';
        $this->cacheDir = file_exists($cache) && is_dir($cache)
            && is_writable($cache);
    }

    /**
     * Test log directory access
     *
     * @return void
     */
    protected function testLogDir()
    {
        $dir = _PS_MODULE_DIR_.'beezup/views/log';
        $file = $dir.'/log.txt';

        $this->logDir = file_exists($file) && is_file($file)
            ? is_writable($file)
            : file_exists($dir) && is_dir($dir) && is_writable($dir);
    }

    /**
     * Test PHP CURL module installation
     *
     * @return void
     */
    protected function testCurl()
    {
        $this->curlModule = extension_loaded('curl');
    }

    /**
     * Test Prestashop version
     *
     * @return void
     */
    protected function testPsVersion()
    {
        $module = Module::getInstanceByName('beezup');
        $this->psVersion = (version_compare(
            _PS_VERSION_,
            $module->ps_versions_compliancy['min']
        ) >= 0
            && version_compare(
                _PS_VERSION_,
                $module->ps_versions_compliancy['max']
            ) < 0);
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

abstract class BeezupTrackerAbstract
{
    protected $_active;

    protected $_module;

    protected $_baseUrl;

    protected $_storeId;

    protected $_validationMethod;

    /** @var boolean display products margins in order tracker */
    protected $_useMargins;

    protected $_url;

    public function setModule(beezup $module)
    {
        $this->_module = $module;

        return $this;
    }

    public function setBaseUrl($url)
    {
        $url = preg_replace('!^https?://!', '', $url);

        if (isset($_SERVER)
            && ((isset($_SERVER['SERVER_PORT'])
                    && $_SERVER['SERVER_PORT'] == _PS_SSL_PORT_)
                || (isset($_SERVER['HTTPS'])
                    && ($_SERVER['HTTPS'] == 1 || $_SERVER['HTTPS'] == 'on')))
        ) {
            $this->_url = 'https://'.$url;
        } else {
            $this->_url = 'http://'.$url;
        }

        return $this;
    }

    public function setStoreId($id)
    {
        $this->_storeId = (string)$id;

        return $this;
    }

    public function setActive($flag)
    {
        $this->_active = (bool)$flag;

        return $this;
    }

    public function setValidationMethod($method)
    {
        $this->_validationMethod = (int)$method;

        return $this;
    }


    /**
     * Set magin use flag
     *
     * @param boolean $flag
     *
     * @return BeezupTrackerAbstract
     */
    public function setUseMargins($flag)
    {
        $this->_useMargins = (bool)$flag;

        return $this;
    }

    /**
     * Get beezup module
     *
     * @return beezup
     */
    public function getModule()
    {
        return $this->_module;
    }

    public function getBaseUrl()
    {
        return rtrim((string)$this->_url, '/');
    }

    public function getStoreId()
    {
        return (string)$this->_storeId;
    }

    public function getActive()
    {
        return (bool)$this->_active;
    }

    public function getValidationMethod()
    {
        return (int)$this->_validationMethod;
    }

    /**
     * Is tracker using product margin
     *
     * @return type
     */
    public function getUseMargins()
    {
        return (bool)$this->_useMargins;
    }

    public function execTracker($action, $args = array())
    {
        $action = 'track'.Tools::strtoupper($action{0}).Tools::substr(
            $action,
            1
        );

        if ($this->getActive() && method_exists($this, $action)) {
            return call_user_func_array(array($this, $action), $args);
        } else {
            return false;
        }
    }
}

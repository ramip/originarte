<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMResponseData
{
    protected $aHeaders = array();
    protected $mResponse = null;
    protected $aCurlInfo = array();
    protected $aCurlError = array();
    public $rawJson = "";

    /**
     * @return $aHeaders
     */
    public function getHeaders()
    {
        return $this->aHeaders;
    }

    /**
     * @param multitype: $aHeaders
     *
     * @return BeezupOMResponseData
     */
    public function setHeaders(array $aHeaders = array())
    {
        $this->aHeaders = $aHeaders;

        return $this;
    }

    /**
     * @return $mResponse
     */
    public function getResponse()
    {
        return $this->mResponse;
    }

    /**
     * @param NULL $mResponse
     *
     * @return BeezupOMResponseData
     */
    public function setResponse($mResponse)
    {
        $this->mResponse = $mResponse;

        return $this;
    }

    /**
     * @return $aCurlInfo
     */
    public function getCurlInfo()
    {
        return $this->aCurlInfo;
    }

    /**
     * @param multitype: $aCurlInfo
     *
     * @return BeezupOMResponseData
     */
    public function setCurlInfo(array $aCurlInfo = array())
    {
        $this->aCurlInfo = $aCurlInfo;

        return $this;
    }

    /**
     * @return $aCurlError
     */
    public function getCurlError()
    {
        return $this->aCurlError;
    }

    /**
     * @param multitype: $aCurlError
     *
     * @return BeezupOMResponseData
     */
    public function setCurlError(array $aCurlError = array())
    {
        $this->aCurlError = $aCurlError;

        return $this;
    }
}

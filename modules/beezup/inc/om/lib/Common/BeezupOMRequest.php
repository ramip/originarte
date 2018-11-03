<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';

    protected $sMethod = self::METHOD_GET;

    protected $sDateFormat = 'Y-m-d\TH:i:s\Z';//DateTime::ISO8601;

    public function getMethod()
    {
        return $this->sMethod;
    }

    public function setMethod($sMethod)
    {
        if (in_array($sMethod, array(self::METHOD_GET, self::METHOD_POST))) {
            $this->sMethod = $sMethod;
        }

        return $this;
    }

    public static function fromArray(array $aData = array())
    {
        $oRequest = new BeezupOMRequest();
        foreach ($aData as $sKey => $mValue) {
            $oRequest->$sKey = $mValue;
        }

        return $oRequest;
    }

    public function toArray()
    {
        return array();
    }
}

<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMOrderHistoryResponse extends BeezupOMResponse
{
    protected $sEtag = null;
    protected $nHttpStatus = null;

    /**
     * @return the $sEtag
     */
    public function getEtag()
    {
        return $this->sEtag;
    }

    /**
     * @param NULL $sEtag
     */
    public function setEtag($sEtag)
    {
        $this->sEtag = $sEtag;

        return $this;
    }

    /**
     * @return the $nHttpStatus
     */
    public function getHttpStatus()
    {
        return $this->nHttpStatus;
    }

    /**
     * @param NULL $nHttpStatus
     */
    public function setHttpStatus($nHttpStatus)
    {
        $this->nHttpStatus = (int)$nHttpStatus;

        return $this;
    }

    public function createResult(array $aData = array())
    {
        return BeezupOMOrderHistoryResult::fromArray($aData);
    }

    public function createRequest(array $aData = array())
    {
        return BeezupOMOrderHistoryRequest::fromArray($aData);
    }
}

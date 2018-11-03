<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMOrderListResponse extends BeezupOMResponse
{
    protected $sExecutionId = null;

    public function getExecutionId()
    {
        return $this->sExecutionId;
    }

    public function setExecutionId($sExecutionId)
    {
        $this->sExecutionId = $sExecutionId;

        return $this;
    }

    public function createResult(array $aData = array())
    {
        return BeezupOMOrderListResult::fromArray($aData);
    }

    public function createRequest(array $aData = array())
    {
        return BeezupOMOrderListRequest::fromArray($aData);
    }
}

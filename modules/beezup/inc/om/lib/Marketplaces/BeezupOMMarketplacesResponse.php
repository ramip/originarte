<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMMarketplacesResponse extends BeezupOMResponse
{
    public function createResult(array $aData = array())
    {
        return BeezupOMMarketplacesResult::fromArray($aData);
    }

    public function createRequest(array $aData = array())
    {
        return BeezupOMMarketplacesRequest::fromArray($aData);
    }
}
<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

interface BeezupOMOrderServiceInterface
{
    public function changeOrder();

    public function checkSynchronizationAlreadyInProgress();

    public function synchronizeOrders();

    public function synchronizeOrder();
}

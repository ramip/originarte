<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

ob_start();
$aResult = array(
    'error' => 'Initialization error',
    'result' => null,
    'beezup_order_id' => null,
    'debug' => null,
    'id_order' => null

);

try {
    require_once implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'config', 'config.inc.php'));
    ini_set('display_errors', true);
    error_reporting(-1);
    $oBeezup = Module::getInstanceByName('beezup');
    if (!$oBeezup) {
        $aResult['error'] = 'Unable to initialize module BeezUP';
    } elseif (!$oBeezup->active) {
        $aResult['error'] = 'Module BeezUP is desactivated';
    } elseif (!isset($_REQUEST['key']) || empty($_REQUEST['key']) || $_REQUEST['key'] !== $oBeezup->getBeezupOMController()->getHarvestKey()) {
        $aResult['error'] = 'Invalid security key';
    } elseif ((!isset($_REQUEST['url']) || empty($_REQUEST['url'])) && (!isset($_REQUEST['marketplacetechnicalcode']) || empty($_REQUEST['marketplacetechnicalcode']))) {
        $aResult['error'] = 'Invalid Marketplace Technical Code';
    } elseif ((!isset($_REQUEST['url']) || empty($_REQUEST['url'])) && (!isset($_REQUEST['beezuporderuuid']) || empty($_REQUEST['beezuporderuuid']))) {
        $aResult['error'] = 'Invalid Beezup Order UUID';
    } elseif ((!isset($_REQUEST['url']) || empty($_REQUEST['url'])) && (!isset($_REQUEST['accountid']) || empty($_REQUEST['accountid']))) {
        $aResult['error'] = 'Invalid Account Id';
    } else {
        // all preliminary checks are ok
        $aResult['error'] = null;
        if (isset($_REQUEST['debug'])) {
            $oBeezup->getBeezupOMController()->setDebugMode((bool)$_REQUEST['debug']);
        }
        $aResult['debug'] = $oBeezup->getBeezupOMController()->isDebugModeActivated();
        $aBeezupOrderId = array();
        if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
            $oBeezupOrderId = BeezupOMOrderIdentifier::fromUrl($_REQUEST['url']);
            if ($oBeezupOrderId) {
                $aBeezupOrderId = array_change_key_case($oBeezupOrderId->toArray(), CASE_LOWER);
            }
        } else {
            $aBeezupOrderId = array(
                'accountid' => $_REQUEST['accountid'],
                'beezuporderuuid' => $_REQUEST['beezuporderuuid'],
                'marketplacetechnicalcode' => $_REQUEST['marketplacetechnicalcode']
            );
        }

        if (count(array_filter($aBeezupOrderId)) !== 3) {
            $aResult['error'] = 'Unable to fetch order id';
            $bResult = false;
        } else {
            $aResult['beezup_order_id'] = $aBeezupOrderId;
            // synchronization
            list ($bResult, $sOperation, $sError) =  $oBeezup->getBeezupOMController()->synchronizeOrder($aBeezupOrderId['marketplacetechnicalcode'], $aBeezupOrderId['accountid'], $aBeezupOrderId['beezuporderuuid']);
            $aResult['result']  = $bResult;
            $aResult['operation']  = $sOperation;
            if (!$bResult) {
                // its shitty, thanks The Great Architect. In addition, we need to be in debug mode to do that (if not, no internal log in orderservice)
                $sError .= implode(PHP_EOL, $oBeezup->getBeezupOMController()->getOrderService()->getRepository()->getLog());
                $aResult['error'] = $sError ? $sError : 'Unable to synchronize order';
            } else {
                $oOrderIdentifier = BeezupOMOrderIdentifier::fromArray($aBeezupOrderId);
                $oBeezupOrder = BeezupOrder::fromBeezupOrderId($oOrderIdentifier);
                if (!$oBeezupOrder || !$oBeezupOrder->id_order) {
                    $aResult['error'] = 'Synchronisation succeeded, but unable to fetch Prestashop order';
                } else {
                    $aResult['id_order'] = (int)$oBeezupOrder->id_order;
                }
            }
        }
    }
} catch (Exception $oException) {
    $aResult['error'] = $oException->getMessage();
}
if (is_string($aResult['error']) && $oBeezup) {
    $aResult['error'] = $oBeezup->l($aResult['error']);
}
$aResult['raw_output'] = ob_get_clean();
header('Content-Type: application/json', true);
print json_encode($aResult);

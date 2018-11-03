<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

ob_start();
//define('_PS_CACHE_ENABLED_', '0');
// Loading Prestashop + Beezup loader (Only PS 1.5)
include(dirname(__FILE__).'/../../config/config.inc.php');
include(_PS_MODULE_DIR_.'beezup/inc/BeezupAutoloader.php');
BeezupAutoloader::register();
ob_end_flush();
ignore_user_abort(true);

if (BeezupRegistry::get('BEEZUP_DEBUG_MODE')
    || (Tools::getIsset(Tools::getValue('debug'))
        && Tools::getValue('debug') == 1)
) {
    ini_set('display_errors', 'on');
    error_reporting(-1);
} else {
    ini_set('display_errors', 'on');
    error_reporting(-1);
}

include(dirname(__FILE__).'/../../init.php');
/**
 * @var beezup
 */
$beezup = Module::getInstanceByName('beezup');

if (!Validate::isLoadedObject($beezup) || !$beezup->active) {
    if (!BeezupRegistry::get('BEEZUP_DEBUG_MODE')) {
        ob_end_clean();
        header('HTTP/1.1 403 Forbidden');
    }

    die(
        "<h1>Forbidden</h1><p>You don't have permission to access "
        .__PS_BASE_URI__."modules/beezup/xml.php on this server</p>"
    );
}

if (BeezupRegistry::get('BEEZUP_ALL_SHOPS')) {
    Shop::setContext(Shop::CONTEXT_ALL);
}

if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = preg_replace(
        '#^https?://([^/]+)/?.*#',
        '$1',
        Configuration::get('BEEZUP_SITE_ADDRESS')
    );
}

$id_shop = Tools::getValue('id_shop')
    ? (int)Tools::getValue('id_shop')
    : ((Context::getContext() && Context::getContext()->shop)
        ? (int)Context::getContext()->shop->id : null);
$id_lang = Tools::getValue('lang_iso')
    ? (int)Language::getIdByIso(Tools::strtolower(Tools::getValue('lang_iso')))
    : null;
$id_currency = Tools::getValue('currency_iso')
    ? (int)Currency::getIdByIsoCode(
        Tools::strtoupper(Tools::getValue('currency_iso'))
    )
    : null;

if ($id_shop && Context::getContext()
    && (!Context::getContext()->shop
        || (int)Context::getContext()->shop->id !== (int)$id_shop)
) {
    Context::getContext()->shop = new Shop($id_shop);
}


// fast path for reading cache without the fuss
if (Configuration::get('BEEZUP_USE_CACHE')) {
    $cache_file_path = $beezup->getCacheFilePath(
        $id_shop,
        $id_lang,
        $id_currency
    );
    if ($beezup->isCacheValid($cache_file_path)) {
        ob_end_clean();
        header("Content-Type: text/xml;");
        header("Cache-control: no-cache");
        readfile($cache_file_path);
        die();
    } // if
} //

$content = $beezup->getXML($id_shop, $id_lang, $id_currency);
if (!$content) {
    if (!BeezupRegistry::get('BEEZUP_DEBUG_MODE')) {
        ob_end_clean();
        header('HTTP/1.1 500 Internal Server Error');
        header('X-beezup-error: No contents');
    }

    die('BeezUP export module failed to create xml feed');
} else {
    if (!BeezupRegistry::get('BEEZUP_DEBUG_MODE')) {
        ob_end_clean();
        header("Content-Type: text/xml;");
        header("Cache-control: no-cache");
    }
    echo $content;
}

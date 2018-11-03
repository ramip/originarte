<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/redsys.php');
Tools::displayFileAsDeprecated();
if (!empty($_POST))
{

	/** Recoger datos de respuesta **/
	$total     = Tools::getValue('Ds_Amount');
	$pedido    = Tools::getValue('Ds_Order');
	$codigo    = Tools::getValue('Ds_MerchantCode');
	$moneda    = Tools::getValue('Ds_Currency');
	$respuesta = Tools::getValue('Ds_Response');
	$firma_remota = Tools::getValue('Ds_Signature');

	/** Creamos objeto **/
	$redsys = new redsys();
	/** Verificamos opciones **/
	$error_pago = Configuration::get('REDSYS_ERROR_PAGO');
	/** Contraseña Secreta **/
	$clave = Configuration::get('REDSYS_CLAVE');

	/** SHA1 **/
	$mensaje = $total.$pedido.$codigo.$moneda.$respuesta.$clave;
	$firma_local = Tools::strtoupper(sha1($mensaje));
	$pedido = Tools::substr($pedido, 0, 8);
	$pedido = (int)$pedido;

	if ($firma_local == $firma_remota)
	{
		/** Formatear variables **/
		$total  = number_format($total / 100, 4, '.', '');
		$respuesta = (int)$respuesta;
		$moneda_tienda = 1;
		if ($respuesta < 101)
		{
			/** Compra válida **/
			$mailvars = array();
			$cart = new Cart($pedido);
			$redsys->validateOrder($pedido, _PS_OS_PAYMENT_, $total, $redsys->displayName, null, $mailvars, null, false, $cart->secure_key);
		}
		else
			/** se anota el pedido como no pagado **/
			$redsys->validateOrder($pedido, _PS_OS_ERROR_, 0, $redsys->displayName, 'errores:'.$respuesta);
	}
	else
		/** se anota el pedido como no pagado **/
		$redsys->validateOrder($pedido, _PS_OS_ERROR_, 0, $redsys->displayName, 'errores:'.$respuesta);
}
else if (!empty($_GET))
{

	/** Recoger datos de respuesta **/
	$total     = Tools::getValue('Ds_Amount');
	$pedido    = Tools::getValue('Ds_Order');
	$codigo    = Tools::getValue('Ds_MerchantCode');
	$moneda    = Tools::getValue('Ds_Currency');
	$respuesta = Tools::getValue('Ds_Response');
	$firma_remota = Tools::getValue('Ds_Signature');

	/** Creamos objeto **/
	$redsys = new redsys();
	/** Verificamos opciones **/
	$error_pago = Configuration::get('REDSYS_ERROR_PAGO');
	/** Contraseña Secreta **/
	$clave = Configuration::get('REDSYS_CLAVE');

	/** SHA1 **/
	$mensaje = $total.$pedido.$codigo.$moneda.$respuesta.$clave;
	$firma_local = Tools::strtoupper(sha1($mensaje));
	$pedido = Tools::substr($pedido, 0, 8);
	$pedido = (int)$pedido;

	if ($firma_local == $firma_remota)
	{
		/** Formatear variables **/
		$total  = number_format($total / 100, 4, '.', '');
		$respuesta = (int)$respuesta;
		$moneda_tienda = 1;
		if ($respuesta < 101)
		{
			/** Compra válida **/
			Tools::redirect('index.php');
		}
		else
			Tools::redirect('index.php');
	}
	else
		Tools::redirect('index.php');
}
?>

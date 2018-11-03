{*
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
*}

<p class="payment_module">
	<a class="bankwire" href="javascript:$('#redsys_form').submit();" title="{l s='Conectar con el TPV' mod='redsys'}" style="float:left">
		
		<img src="{$module_dir|escape:'htmlall'}img/tarjetas_redsys.png" alt="{l s='Conectar con el TPV' mod='redsys'}" style="float:left;margin:-15px 15px 15px -5px;"/>
		
		{l s='Pagar con tarjeta  - Pasarela de pago Redsys' mod='redsys'}
	{if $fee>0}
		<br /><br />
		{l s='Esta forma de pago lleva asociada un recargo de ' mod='redsys'} <font color="red"><b>{convertPrice price=$fee}.</b></font> {l s='El recargo se suma a los gastos de env.' mod='redsys'}
	{/if}
	</a><br /><br /><br /><br /><br /><br /><br />
</p>

<form action="{$urltpv|escape:'htmlall'}" method="post" id="redsys_form" class="hidden">	
	<input type="hidden" name="Ds_Merchant_Amount" value="{$cantidad|escape:'htmlall'}" />
    <input type="hidden" name="Ds_Merchant_Currency" value="{$moneda|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_Order" value="{$pedido|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_MerchantCode" value="{$codigo|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_Terminal" value="{$terminal|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_TransactionType" value="{$trans|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_Titular" value="{$titular|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_MerchantData" value="{$merchantdata|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_MerchantName" value="{$nombre|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_MerchantURL" value="{$urltienda|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_ProductDescription" value="{$productos|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_UrlOK" value="{$UrlOk|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_UrlKO" value="{$UrlKO|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_MerchantSignature" value="{$firma|escape:'htmlall'}" />
	<input type="hidden" name="Ds_Merchant_ConsumerLanguage" value="{$idioma_tpv|escape:'htmlall'}" />
    <input type="hidden" name="Ds_Merchant_PayMethods" value="{$tipopago|escape:'htmlall'}" />
</form>
{*
<p class="payment_module">
	<a href="javascript:$('#servired_form').submit();" title="{l s='Conectar con el TPV' mod='servired'}" style="float:left"/>

		<img src="{$module_dir}tarjetas.jpg" alt="{l s='Conectar con el TPV' mod='servired'}" style="float:left"/>
		{l s='Credit card payment. Safe connection via Servired. We will not have access to your data at any moment during this operation. Once your payment is acomplished, you will be redirected to our shop again.' mod='servired'}
	{if $fee>0}
		<br /><br />
		{l s='Este m�todo de pago lleva asociado un recargo de ' mod='servired'} <font color="red"/><b/>{convertPrice price=$fee}.</b/></font/> {l s='El recargo se sumar� a los gastos de env�o' mod='servired'}
	</a>
	{/if}
</p>
*}


<!--<form action="http://sis-d.redsys.es/sis/realizarPago" method="post" id="servired_form" class="hidden">
	<input type="hidden" name="Ds_Merchant_Amount" value="{$cantidad}" />
    <input type="hidden" name="Ds_Merchant_Currency" value="{$moneda}" />
	<input type="hidden" name="Ds_Merchant_Order" value="{$pedido}" />
	<input type="hidden" name="Ds_Merchant_MerchantCode" value="{$codigo}" />
	<input type="hidden" name="Ds_Merchant_Terminal" value="{$terminal}" />
	<input type="hidden" name="Ds_Merchant_TransactionType" value="{$trans}" />
	<input type="hidden" name="Ds_Merchant_Titular" value="{$titular}" />
	<input type="hidden" name="Ds_Merchant_MerchantName" value="{$nombre}" />
  {if $notificacion>0}
	<input type="hidden" name="Ds_Merchant_MerchantURL" value="{$urltienda}" />
  {/if}
	<input type="hidden" name="Ds_Merchant_ProductDescription" value="{$productos}" />
	<input type="hidden" name="Ds_Merchant_UrlOK" value="{$UrlOk}" />
	<input type="hidden" name="Ds_Merchant_UrlKO" value="{$UrlKO}" />
	<input type="hidden" name="Ds_Merchant_MerchantSignature" value="{$firma}" />
	<input type="hidden" name="Ds_Merchant_ConsumerLanguage" value="{$idioma_tpv}" />
    <input type="hidden" name="Ds_Merchant_PayMethods" value="T" />
</form>-->


{if $smarty.const._PS_VERSION_ >= 1.6}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module">
			<a class="bankwire" href="javascript:$('#servired_form').submit();" title="{l s='Conectar con el TPV' mod='servired'}">
				<img src="{$module_dir|escape:'htmlall'}img/servired.png" alt="{l s='Conectar con el TPV' mod='servired'}" height="48" />
				{l s='Pagar con tarjeta  - Pasarela de pago Redsys' mod='servired'}
			</a>
		</p>
	</div>
</div>
{else}
<p class="payment_module">
	<a class="bankwire" href="javascript:$('#servired_form').submit();" title="{l s='Conectar con el TPV' mod='servired'}">
		<img src="{$module_dir|escape:'htmlall'}img/payment.jpg" alt="{l s='Conectar con el TPV' mod='servired'}" height="48" />
		{l s='Pay with your VISA / MasterCard / 4B' mod='servired'}
	</a>
	<span class="payment_aclaration">
		{l s='Sin cargo' mod='redsys'}
	</span>
</p>
{/if}

<form  action="{$url_nueva}" method="POST" id="servired_form" class="hidden" >
<input type="hidden" name="Ds_SignatureVersion" value="{$version}"/>
 <input type="hidden" name="Ds_MerchantParameters" value="{$params}"/></br>
 <input type="hidden" name="Ds_Signature" value="{$signature}"/>
</form>

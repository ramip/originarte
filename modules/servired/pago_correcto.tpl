{*<div class="pago" style="min-height: 100px; margin-top: 22px;"><img src="{$this_path}modules/servired/pago-ok.gif" alt="Payment success" longdesc="Payment success" /></td></tr><tr><h2 style="font-style: normal;">{l s='The payment has been successfully accomplished!' mod='servired'}</h2><br />
<p>
{l s='Great!' mod='servired'}
</p>
<br/>
<p>
<a href="{$base_dir_ssl}history.php" title="{l s='Pedidos'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Orders'}" class="icon" />{l s='Click here to check your order details' mod='servired'}</a>
</p>
<br/>
<p>
{l s='Remember that you can contact us at any time if you have any doubt about your order.' mod='servired'}
</P></div>
*}

<img src="{$this_path|escape:'htmlall'}img/servired.png" /><br /><br />
{if $status == 'ok'}
  <p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='servired'}
    <br /><br />- {l s='Payment amount.' mod='servired'} <span class="price"><strong>{$total_to_pay|escape:'htmlall'}</strong></span>
		<br /><br />- {l s='Pedido N#' mod='servired'} <span class="price"><strong>{$id_order|escape:'htmlall'}</strong></span>
		<br /><br />{l s='An email has been sent to you with this information.' mod='servired'}
		<br /><br />{l s='For any questions or for further information, please contact our' mod='servired'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='servired'}</a>.
  </p>
{else}
  <p class="warning">
    {l s='We have noticed that there is a problem with your order. If you think this is an error, you can contact our' mod='servired'}
    <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer service department.' mod='servired'}</a>.
  </p>
{/if}

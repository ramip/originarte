{*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
$(document).ready(function () {
	var width = $(window).width();

	if(width >=767){
			$("#header_user_info").css("display","block");
			$("#leo-button3").css("display","none");
		}
	else {
		$("#header_user_info").css("display","none");
		$("#leo-button3").css("display","block");
		$("#leo-button3").css("padding-left","20px");
		}

	$('#header_user').each(function(){
		$(this).find('a.leo-mobile').click(function(){
		 $('#header_user_info').slideToggle('slow');

		});
	  });

  $(window).resize(function(){
		var width = $(window).width();
		if(width >=767){
			$("#header_user_info").css("display","block");
			$("#leo-button3").css("display","none");
		}
		else{
			$("#header_user_info").css("display","none");
			$("#leo-button3").css("display","block");
			$("#leo-button3").css("padding-left","20px");

		}
	});


});
</script>


<!-- Block user information module HEADER -->
<div class="login_userinfo">

	<div id="header_user" >
		<div id="leo-button3" class="hidden"><a class="leo-mobile">{l s='User Information'  mod='blockuserinfo'}</a></div>
		<div id="header_user_info">

			<div class="nav-item" id="your_account"><div class="item-top"><a href="{$link->getPageLink('my-account', true)}" title="{l s='Your Account' mod='blockuserinfo'}">{l s='Your Account' mod='blockuserinfo'}</a></div></div>
<!-- Ocultamos wishlist
			{if $logged}
			<div class="nav-item" id="wishlist_block">
				<div class="item-top"><a href="{$link->getModuleLink('blockwishlist', 'mywishlist')}" title="{l s='My wishlist' mod='blockuserinfo'}">{l s='My wishlist' mod='blockuserinfo'}</a></div>
			</div>
			{/if}
-->
				<!--{if $logged} -->
					<div class="nav-item">
						<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
					</div>
					<div class="nav-item">
					<a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow">{l s='Log out' mod='blockuserinfo'}</a></div>
				<!-- Ocultar el log-in
                {else} 
				<div class="nav-item">
					<div class="item-top"><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" rel="nofollow">{l s='Login' mod='blockuserinfo'}</a></div>
					</div>
                
				{/if} -->

			<div class="nav-item"><div class="item-top"><a href="https://originarte.com/content/6-guia-de-impresion">{l s='Help' mod='blockuserinfo'}</a></div></div>
            <div class="nav-item"><div class="item-top"><a href="https://originarte.com/content/1-faq">{l s='FAQ' mod='blockuserinfo'}</a></div></div>
            <div class="nav-item"><div class="item-top"><a href="https://originarte.com/contact-us">{l s='Contact' mod='blockuserinfo'}</a></div></div>
		</div>
	</div>
	<div id="topminibasket">
		<div id="header_nav">
				{if !$PS_CATALOG_MODE}
				<div id="shopping_cart">

						<a class="kenyan_coffee_rg" href="{$link->getPageLink($order_process, true)|escape:'html'}" title="{l s='Your Shopping Cart' mod='blockuserinfo'}">
						<span class="title_cart">{l s='Shopping Cart' mod='blockuserinfo'}</span>
						<span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
						<span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='item' mod='blockuserinfo'}</span>
						<span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='items' mod='blockuserinfo'}</span>
						<span class="hidden-totalsp ajax_cart_total{if $cart_qties == 0} hidden{/if}">
							{if $cart_qties > 0}
								{if $priceDisplay == 1}
									{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
									{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
								{else}
									{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
									{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
								{/if}
							{/if}
						</span>

						<span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span>
						</a>


				</div>
				{/if}

			</div>
	</div>
</div>
<!-- /Block user information module HEADER -->
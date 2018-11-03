{*
* 2007-2012 PrestaShop 
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8337 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if count($categoryProducts) > 0 && $categoryProducts !== false}
<div class="clearfix blockproductscategory">
	<div class="title-productscategory">
		<h3 class="productscategory_h3">{l s='Related Products: ' mod='productscategory'} {$categoryProducts|@count}</h3>
	</div>
	<div id="{if count($categoryProducts) > 5}productscategory{else}productscategory_noscroll{/if}">
	{if count($categoryProducts) > 5}<a id="productscategory_scroll_left" title="{l s='Previous' mod='productscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Previous' mod='productscategory'}</a>{/if}
	<div id="productscategory_list">
		<ul {if count($categoryProducts) > 5}style="width: {math equation="width * nbImages" width=107 nbImages=$categoryProducts|@count}px"{/if}>
			{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
			<li><div class="product-container hover">
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product_image" title="{$categoryProduct.name|htmlspecialchars}">
				<img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>
				<div class="entry-content">
						{if $ProdDisplayPrice AND $categoryProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
							<p class="price_display entry-price content_price price_container">
								<span class="price">{convertPrice price=$categoryProduct.displayed_price}</span>
							</p>
						{/if}
					<h4 class="entry-title"><a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:42:'...'|escape:'htmlall':'UTF-8'}</a></h4>
					<div class="ptx-main-puplic">
							<div class="box-button">
							{if ($categoryProduct.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $categoryProduct.available_for_order && !isset($restricted_country_mode) && $categoryProduct.minimal_quantity <= 1 && $categoryProduct.customizable != 2 && !$PS_CATALOG_MODE}
								<div>
									{if ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
										{if isset($static_token)}
											<a class="ajax_add_to_cart_button btn btn-custom " rel="ajax_id_product_{$categoryProduct.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$categoryProduct.id_product|intval}&amp;token={$static_token}", false)}" title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a>
										{else}
											<a class="ajax_add_to_cart_button btn btn-custom " rel="ajax_id_product_{$categoryProduct.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$categoryProduct.id_product|intval}", false)} title="{l s='Add to cart'}"><span>{l s='Add to cart'}</span></a>
										{/if}						
									{else}
										<span class="exclusive"><span>{l s='Add to cart'}</span></span>
									{/if}<br/>				
										<a class="box-favorite" href="#">{l s='Favorite'}</a>	
									
										</br>
									<a class="product_desc" href="{$categoryProduct.link|escape:'htmlall':'UTF-8'}" title="{l s='View'}">{$categoryProduct.description_short|strip_tags:'UTF-8'|truncate: 65:'...'}</a>
								</div>
							</div>
					</div>
				</div>
				{/if}
				</div>
			</li>
			{/foreach}
		</ul>
	</div>
	{if count($categoryProducts) > 5}<a id="productscategory_scroll_right" title="{l s='Next' mod='productscategory'}" href="javascript:{ldelim}{rdelim}">{l s='Next' mod='productscategory'}</a>{/if}
	</div>
	<script type="text/javascript">
		$('#productscategory_list').trigger('goto', [{$middlePosition}-3]);
	</script>
</div>
{/if}

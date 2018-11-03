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

<div id="view_wishlist">
<h2 class="title_block">{l s='Wishlist' mod='blockwishlist'}</h2>
<br />
{if $wishlists}
<p>
	{l s='Other wishlists of' mod='blockwishlist'} {$current_wishlist.firstname} {$current_wishlist.lastname}:
	{foreach from=$wishlists item=wishlist name=i}
		{if $wishlist.id_wishlist != $current_wishlist.id_wishlist}
			<a href="{$base_dir_ssl}modules/blockwishlist/view.php?token={$wishlist.token}">{$wishlist.name}</a>
			{if !$smarty.foreach.i.last}
				/
			{/if}
		{/if}
	{/foreach}
</p>
{/if}
{if $products}

<div class="addresses" id="featured-products_block_center">
	<h3>{l s='Welcome to the wishlist of' mod='blockwishlist'} {$current_wishlist.firstname} {$current_wishlist.lastname}: {$current_wishlist.name}</h3>
	<p />
	{foreach from=$products item=product name=i}
	
	{if ($product@iteration%4==1)}
		<div class="row-fluid">
	{/if}
	
	<div  class="address span3 item" id="block_{$product.id_product}_{$product.id_product_attribute}">
		<div class="ajax_block_product product-container clearfix">
			<div class="address_title center_block">
				<a	href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)}" title="{l s='Product detail' mod='blockwishlist'}" class="product_image">
					<img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_default')}" alt="{$product.name|escape:'htmlall':'UTF-8'}" />				
				</a>
			
			</div>
			<div class="address_name right_block">

				<h3 class="s_title_block"><a href="{$link->getProductLink($product.id_product,
				$product.link_rewrite, $product.category_rewrite)}" title="{l s='View' mod='blockwishlist'}">{$product.name|truncate:30:'...'|escape:'htmlall':'UTF-8'}</a></h3>
				
				<div class="wishlist_product_detail">
				{if isset($product.attributes_small)}
					<div class="product_desc"><a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)}" title="{l s='Product detail' mod='blockwishlist'}">{$product.attributes_small|escape:'htmlall':'UTF-8'}</a></div>
				{/if}
					<p><label>{l s='Quantity :' mod='blockwishlist'}</label><input type="text" id="{$product.id_product}_{$product.id_product_attribute}" size="3" value="{$product.quantity|intval}" readonly/></p>
					
					<p>{l s='Priority :' mod='blockwishlist'}
					{if $product.priority eq 0}
						<span>{l s='High' mod='blockwishlist'}</span></p>
					{elseif $product.priority eq 1}
						<span>{l s='Medium' mod='blockwishlist'}</span></p>
					{else}
						<span>{l s='Low' mod='blockwishlist'}</span></p>
					{/if}
				</div>
				
				<div class="address_address1 clear">
					<a class="button_small clear" href="{$link->getProductLink($product.id_product,  $product.link_rewrite, $product.category_rewrite)}" title="{l s='View' mod='blockwishlist'}">{l s='View' mod='blockwishlist'}</a>
					{if isset($product.attribute_quantity) AND $product.attribute_quantity >= 1 OR !isset($product.attribute_quantity) AND $product.product_quantity >= 1}
					{if !$ajax}
					<form id="addtocart_{$product.id_product|intval}_{$product.id_product_attribute|intval}" action="{$link->getPageLink('cart')}" method="post">
					<p class="hidden">
						<input type="hidden" name="id_product" value="{$product.id_product|intval}" id="product_page_product_id"  />
						<input type="hidden" name="add" value="1" />
						<input type="hidden" name="token" value="{$token}" />
						<input type="hidden" name="id_product_attribute" id="idCombination" value="{$product.id_product_attribute|intval}" />
					</p>
					</form>
					{/if}
					<a href="javascript:;" class="exclusive" onclick="WishlistBuyProduct('{$token|escape:'htmlall':'UTF-8'}', '{$product.id_product}', '{$product.id_product_attribute}', '{$product.id_product}_{$product.id_product_attribute}', this, {$ajax});" title="{l s='Add to cart' mod='homefeatured'}">{l s='Add to cart' mod='blockwishlist'}</a>
					{else}
					<span class="exclusive">{l s='Add to cart' mod='blockwishlist'}</span>
					{/if}
				</div>
				
			</div>
			
		</div>
	</div>
	
	{if $product@iteration%4==0||$smarty.foreach.i.last}
		</div>
	{/if}
	
	{/foreach}
	<p class="clear" />
</div>
{else}
	<p class="warning">{l s='No products' mod='blockwishlist'}</p>
{/if}
</div>

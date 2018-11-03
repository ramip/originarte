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

{if isset($products)}
	<!-- Products list -->
{*print_r('<PRE>')}
{print_r($products)}
{print_r('</PRE>')*}
<div id="product_list" class="products_block">
	{foreach from=$products item=product name=products}
		{if $product@iteration%Configuration::get('productlistcols')==1}
        <div class="row-fluid">
        {/if}
	
		<div class="p-item span{(12/Configuration::get('productlistcols'))} product_block ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if} clearfix">
					<div class="product-container clearfix">
							<div class="center_block">
								<div class="bg_div_hide"></div>
								<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">
									<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{if !empty($product.legend)}{$product.legend|escape:'htmlall':'UTF-8'}{else}{$product.name|escape:'htmlall':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'htmlall':'UTF-8'}{else}{$product.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
									{if isset($product.new) && $product.new == 1}<span class="new">{l s='New'}</span>{/if}
								</a>
								
								{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="on_sale">{l s='On sale!'}</span>
								
								{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="discount">{l s='Reduced price!'}</span>
								
								{/if}
								
								<div class="div_hide_product">
									<a href="#" id="wishlist_button{$product.id_product}" title="{l s='Add to wishlist'}" class="btn-add-wishlist box-wishlist" onclick="LeoWishlistCart('wishlist_block_list', 'add', '{$product.id_product}', $('#idCombination').val(), 1 ); return false;">{l s='Add to wishlist'}</a>
						
									<a class="lnk_more" href="{$product.link}" title="{l s='View'}">{l s='View'}</a>
									
									{*if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
											{if ($product.allow_oosp || $product.quantity > 0)}
												{if isset($static_token)}
													<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html'}" title="{l s='Add to cart'}"><span></span>{l s='Add to cart'}</a>
												{else}
													<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}", false)|escape:'html'}" title="{l s='Add to cart'}"><span></span>{l s='Add to cart'}</a>
												{/if}						
											{else}
												<span class="exclusive">{l s='Add to cart'}</span>
											{/if}
									{/if*}
								</div>
							</div>
							<div class="right_block">
								<h3 class="s_title_block">{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}<a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:70:'...'|escape:'htmlall':'UTF-8'}</a></h3>
								
								<div class="product_desc">
                                                                    <a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}" >{$product.description_short|truncate:360:'...'}</a>
                                                                    
                                                                    {assign var="feature" value=$product.features}
                                                                    {foreach from=$feature key=num item=feat}
                                                                        {$feat['name']}: {$feat['value']}<br>                                                                        
                                                                    {/foreach}
                                                                </div>
								
								
								
								{if isset($comparator_max_item) && $comparator_max_item}
									<p class="compare">
										<input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked="checked"{/if} autocomplete="off"/> 
										<label for="comparator_item_{$product.id_product}">{l s='Select to compare'}</label>
									</p>
								{/if}
                                                                
                                                                
{*print_r('<PRE>')}
{print_r($productsAttributes[$product.id_product])}
{print_r('</PRE>')*}
<div class="boxattribute">	
                                        {assign var="groups" value=$productsAttributes[$product.id_product]}
                                        {assign var="combinations" value=$productsCombinations[$product.id_product]}
					{if isset($groups)}
					<!-- attributes -->
					<div class="attributes">
					{foreach from=$groups key=id_attribute_group item=group}
						{if $group.attributes|@count}
							<fieldset class="attribute_fieldset">
								<label class="attribute_label" for="{$product.id_product}_group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'}</label>
								{assign var="groupName" value="group_$id_attribute_group"}
								<div class="attribute_list">
								{if ($group.group_type == 'select')}
                                                                    {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                        {if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute}
                                                                        <input type="text" class="attr" value="{$group_attribute|escape:'htmlall':'UTF-8'}" name="un_{$product.id_product}" readonly>
                                                                        {/if}
                                                                    {/foreach}
                                                                    {*
									<select name="{$groupName}" id="{$product.id_product}_group_{$id_attribute_group|intval}" class="attribute_select" onchange="findCombination();getProductAttribute();">
                                                                            {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                                    <option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
                                                                            {/foreach}
									</select>
                                                                        *}
								{elseif ($group.group_type == 'radio')}
									<ul>
                                                                            {foreach from=$group.attributes key=id_attribute item=group_attribute}
                                                                                <li>
                                                                                    {if ($group.default == $id_attribute)}
                                                                                        <input type="text" class="attr" value="{$group_attribute|escape:'htmlall':'UTF-8'}" name="prod_{$product.id_product}" readonly>
                                                                                    {/if}
                                                                                    {*
                                                                                    <input type="radio" class="attribute_radio" name="{$product.id_product}_{$groupName}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} onclick="findCombination();getProductAttribute();">
                                                                                    {$group_attribute|escape:'htmlall':'UTF-8'}
                                                                                    *}
                                                                                </li>
                                                                            {/foreach}
									</ul>
								{/if}
								</div>
							</fieldset>
						{/if}
					{/foreach}
                                            <div class="clear"></div>
					</div>
				{/if}    
    
    

								{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
									<div class="content_price price_container">
										{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span><br />{/if}
										{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}<span class="availability">{if ($product.allow_oosp || $product.quantity > 0)}{l s='Available'}{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}{l s='Product available with different options'}{else}<span class="warning_inline">{l s='Out of stock'}</span>{/if}</span>{/if}
									</div>
							
										{if isset($product.online_only) && $product.online_only}<span class="online_only">{l s='Online only!'}</span>{/if}
								{/if}
<a class="exclusive" href="{$product.link}" title="{l s='View'}">{l s='View'}</a>
                                                                <div class="clearBoth"></div>
</div>
                                                                
                                                                
								{*if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
									<div class="content_price price_container">
										{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span><br />{/if}
										{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}<span class="availability">{if ($product.allow_oosp || $product.quantity > 0)}{l s='Available'}{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}{l s='Product available with different options'}{else}<span class="warning_inline">{l s='Out of stock'}</span>{/if}</span>{/if}
									</div>
							
										{if isset($product.online_only) && $product.online_only}<span class="online_only">{l s='Online only!'}</span>{/if}
								{/if*}
								
							</div>
							
							
							
				</div>
		</div>
      
		{if $product@iteration%Configuration::get('productlistcols')==0||$smarty.foreach.products.last}
        
        </div>
		{/if}
	{/foreach}
    </div>
	<!-- /Products list -->
     
{/if}

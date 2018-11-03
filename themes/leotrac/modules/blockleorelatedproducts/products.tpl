<div class=" carousel slide" id="{$tabname}">
	 {if count($products)>$itemsperpage}	
	<a class="carousel-control left" href="#{$tabname}"   data-slide="prev"></a>
	<a class="carousel-control right" href="#{$tabname}"  data-slide="next"></a>
	{/if}
		<div class="carousel-inner">
		{$mproducts=array_chunk($products,$itemsperpage)}
		{foreach from=$mproducts item=products name=mypLoop}
			<div class="item {if $smarty.foreach.mypLoop.first}active{/if}">
					{foreach from=$products item=product name=products}
					{if $product@iteration%$columnspage==1}
					  <div class="row-fluid">
					{/if}
					<div class="span{$scolumn} product_block ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}p-item{/if} clearfix">
							<div class="list-products">
								<div class="product-container clearfix">
								
									<div class="center_block">
										<div class="bg_div_hide"></div>
										<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">
											<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
											{if isset($product.new) && $product.new == 1}<span class="new">{l s='New' mod='blockleorelatedproducts'}</span>{/if}
										</a>
										{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="on_sale">{l s='On sale!' mod='blockleorelatedproducts'}</span>
										{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}<span class="discount">{l s='Reduced price!' mod='blockleorelatedproducts'}</span>{/if}
										
										<div class="div_hide_product">	
											<a href="#" id="wishlist_button{$product.id_product}" title="{l s='Add to wishlist' mod='blockleorelatedproducts'}" class="btn-add-wishlist box-wishlist" onclick="LeoWishlistCart('wishlist_block_list', 'add', '{$product.id_product}', $('#idCombination').val(), 1 ); return false;">{l s='Add to wishlist' mod='blockleorelatedproducts'}</a>
						
											<a class="lnk_more" href="{$product.link}" title="{l s='View' mod='blockleorelatedproducts'}">{l s='View' mod='blockleorelatedproducts'}</a>
											
											{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
												{if ($product.allow_oosp || $product.quantity > 0)}
													{if isset($static_token)}
														<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)}" title="{l s='Add to cart'}">{l s='Add to cart' mod='blockleorelatedproducts'}</a>
													{else}
														<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}", false)}" title="{l s='Add to cart'}">{l s='Add to cart' mod='blockleorelatedproducts'}</a>
													{/if}						
												{else}
													<span class="exclusive">{l s='Add to cart' mod='blockleorelatedproducts'}</span>
												{/if}
											{/if}
	
										</div>	
										
									</div>
									<div class="right_block">
										<h3 class="s_title_block"><a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'|truncate:70:'...'}</a></h3>
										<div class="product_desc">
                                        	<!--{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'} -->
                                            
                                                <!-- product's features -->
                                                {assign var="feature" value=$product.features}
                                                            {foreach from=$feature key=num item=feat}
                                                                {$feat['name']}: {$feat['value']}<br>                                                                        
                                                            {/foreach}
                                            

                                        </div>										
										{if isset($comparator_max_item) && $comparator_max_item}
											<p class="compare">
												<input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked="checked"{/if} /> 
												<label for="comparator_item_{$product.id_product}">{l s='Select to compare' mod='blockleorelatedproducts'}</label>
											</p>
										{/if}										
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
										{*if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}<span class="availability">{if ($product.allow_oosp || $product.quantity > 0)}{l s='Available'}{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}{l s='Product available with different options'}{else}<span class="warning_inline">{l s='Out of stock'}</span>{/if}</span>{/if*}
									</div>
							
										{if isset($product.online_only) && $product.online_only}<span class="online_only">{l s='Online only!' mod='blockleorelatedproducts'}</span>{/if}
								{/if}
<a class="exclusive" href="{$product.link}" title="{l s='View' mod='blockleorelatedproducts'}">{l s='View' mod='blockleorelatedproducts'}</a>
                                                                <div class="clearBoth"></div>
</div>
									{*if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
										<div class="content_price price_container">
											{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="price" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span><br />{/if}										
										</div>
										{/if*}						
									
									</div>
								</div>
							</div>
					</div>
					
					{if $product@iteration%$columnspage==0||$smarty.foreach.products.last}
						</div>
					{/if}
						
					{/foreach}
			</div>		
		{/foreach}
		</div>
</div>
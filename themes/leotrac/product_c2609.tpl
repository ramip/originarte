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

{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var productBasePriceTaxExcluded = {$product->base_price} - {$product->ecotax};

var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction|floatval}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var product_specific_price = new Array();
{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
	product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
{/foreach}
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var stock_management = {$stock_management|intval};
{if !isset($priceDisplayPrecision)}
	{assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
	{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
	{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
	{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}


var productPriceWithoutReduction = '{$productPriceWithoutReduction}';
var productPrice = '{$productPrice}';

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='This combination does not exist for this product. Please select another combination.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others.' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please be patient.' js=1}';
var fieldRequired = '{l s='Please fill in all the required fields before saving your customization.' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		var specific_price_combination = new Array();
		var available_date = new Array();
		specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
		specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
		specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
		specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
		specific_price_combination['id_product_attribute'] = {if $combination.specific_price}{$combination.specific_price.id_product_attribute|intval}{else}0{/if};
		available_date['date'] = '{$combination.available_date}';
		available_date['date_formatted'] = '{dateFormat date=$combination.available_date full=false}';
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, available_date, specific_price_combination);
	{/foreach}
{/if}

{if isset($attributesCombinations)}
	// Combinations attributes informations
	var attributesCombinations = new Array();
	{foreach from=$attributesCombinations key=id item=aC}
		tabInfos = new Array();
		tabInfos['id_attribute'] = '{$aC.id_attribute|intval}';
		tabInfos['attribute'] = '{$aC.attribute}';
		tabInfos['group'] = '{$aC.group}';
		tabInfos['id_attribute_group'] = '{$aC.id_attribute_group|intval}';
		attributesCombinations.push(tabInfos);
	{/foreach}
{/if}
//]]>
</script>
<div id="primary_block" class="no-background row-fluid">

	{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.'}
		<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
		<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		</p>
		<p id="admin-action-result"></p>
		</p>
	</div>
	{/if}

	{if isset($confirmation) && $confirmation}
	<p class="confirmation">
		{$confirmation}
	</p>
	{/if}
	<!-- right infos-->
		<div id="pb-right-column" class="span4">
			<!-- product img-->	
				<div class="alld_product">
					<div class="row-fluid">
							<div id="image-block">
							{if $have_image}
								<span id="view_full_size">
									<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html'}"{if $jqZoomEnabled && $have_image} class="jqzoom"{/if} title="{if !empty($cover.legend)}{$cover.legend|escape:'htmlall':'UTF-8'}{else}{$product->name|escape:'htmlall':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'htmlall':'UTF-8'}{else}{$product->name|escape:'htmlall':'UTF-8'}{/if}" id="bigpic" width="{$largeSize.width}" height="{$largeSize.height}"/>
								<span class="span_link">{l s='Maximize'}</span>
								</span>
							{else}
								<span id="view_full_size">
									<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
									<span class="span_link">{l s='Maximize'}</span>
								</span>
							{/if}
							</div>
							{if isset($images) && count($images) > 0}
							<!-- thumbnails -->
							<div id="views_block" class="row-fluid {if isset($images) && count($images) < 2}hidden{/if}">
								{if isset($images) && count($images) > 3}<span class="view_scroll_spacer"><a id="view_scroll_left" class="hidden" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Previous'}</a></span>{/if}
								<div id="thumbs_list">
									<ul id="thumbs_list_frame">
										{if isset($images)}
											{foreach from=$images item=image name=thumbnails}
											{assign var=imageIds value="`$product->id`-`$image.id_image`"}
											{if !empty($image.legend)}
												{assign var=imageTitlte value=$image.legend|escape:'htmlall':'UTF-8'}
											{else}
												{assign var=imageTitlte value=$product->name|escape:'htmlall':'UTF-8'}
											{/if}
											<li id="thumbnail_{$image.id_image}">
												<a href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html'}" rel="other-views" class="thickbox{if $smarty.foreach.thumbnails.first} shown{/if}" title="{$imageTitlte}">
													<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')|escape:'html'}" alt="{$imageTitlte}" title="{$imageTitlte}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
												</a>
											</li>
											{/foreach}
										{/if}
									</ul>
								</div>
									{if isset($images) && count($images) > 3}<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Next'}</a>{/if}
								</div>
								{/if}
							
									{if isset($images) && count($images) > 1}<p class="resetimg clear"><span id="wrapResetImages" style="display: none;"><img src="{$img_dir}icon/cancel_11x13.gif" alt="{l s='Cancel'}" width="11" height="13"/> <a id="resetImages" href="{$link->getProductLink($product)|escape:'html'}" onclick="$('span#wrapResetImages').hide('slow');return (false);">{l s='Display all pictures'}</a></span></p>{/if}
								<!-- usefull links-->
							</div>
						</div>
			</div>					

		<!-- left infos-->
		<div id="pb-left-column" class="span5">

			<h1 class="title_detailproduct">{$product->name|escape:'htmlall':'UTF-8'}</h1>
			<ul id="usefull_link_block">
				{if $HOOK_EXTRA_LEFT}{$HOOK_EXTRA_LEFT}{/if}
				<li class="print"><a href="javascript:print();">{l s='Print'}</a></li>
				{if $have_image && !$jqZoomEnabled}
				{/if}
			</ul>
			{if $product->description_short OR $packItems|@count > 0}
			<div id="short_description_block">
				{if $product->description_short}
					<div id="short_description_content" class="rte align_justify">{$product->description_short}</div>
				{/if}
				{if $product->description}
				<p class="buttons_bottom_block"><a href="javascript:{ldelim}{rdelim}" class="button">{l s='More details'}</a></p>
				{/if}
				{if $packItems|@count > 0}
				<div class="short_description_pack">
					<h3>{l s='Pack content'}</h3>
					{foreach from=$packItems item=packItem}
					<div class="pack_content">
						{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)|escape:'html'}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
						<p>{$packItem.description_short}</p>
					</div>
					{/foreach}
				</div>
				{/if}
			</div>
			{/if}
            
            {if isset($features) && $features}
		<!-- product's features -->
		<ul>
		{foreach from=$features item=feature}
            {if isset($feature.value)}
			    <li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> {$feature.value|escape:'htmlall':'UTF-8'}</li>
            {/if}
		{/foreach}
		</ul>
	{/if}

			{*{if isset($colors) && $colors}
			<!-- colors -->
			<div id="color_picker">
				<p>{l s='Pick a color:' js=1}</p>
				<div class="clear"></div>
				<ul id="color_to_pick_list" class="clearfix">
				{foreach from=$colors key='id_attribute' item='color'}
					<li><a id="color_{$id_attribute|intval}" class="color_pick" style="background: {$color.value};" onclick="updateColorSelect({$id_attribute|intval});$('#wrapResetImages').show('slow');" title="{$color.name}">{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$color.name}" width="20" height="20" />{/if}</a></li>
				{/foreach}
				</ul>
				<div class="clear"></div>
			</div>
			{/if}*}

			{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
			<!-- add to cart form-->
			<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')|escape:'html'}" method="post">

				<!-- hidden datas -->
				<p class="hidden">
					<input type="hidden" name="token" value="{$static_token}" />
					<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
					<input type="hidden" name="add" value="1" />
					<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
				</p>

				<div class="product_attributes">
					
					{if isset($groups)}
					<!-- attributes -->
					<div id="attributes">
					{foreach from=$groups key=id_attribute_group item=group}
						{if $group.attributes|@count}
							<fieldset class="attribute_fieldset">
								<label class="attribute_label" for="group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
								{assign var="groupName" value="group_$id_attribute_group"}
								<div class="attribute_list">
								{if ($group.group_type == 'select')}
									<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="attribute_select" onchange="findCombination();getProductAttribute();">
										{foreach from=$group.attributes key=id_attribute item=group_attribute}
											<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
										{/foreach}
									</select>
								{elseif ($group.group_type == 'color')}
									<ul id="color_to_pick_list" class="clearfix">
										{assign var="default_colorpicker" value=""}
										{foreach from=$group.attributes key=id_attribute item=group_attribute}
										<li{if $group.default == $id_attribute} class="selected"{/if}>
											<a id="color_{$id_attribute|intval}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}" style="background: {$colors.$id_attribute.value};" title="{$colors.$id_attribute.name}" onclick="colorPickerClick(this);getProductAttribute();">
												{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
													<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$colors.$id_attribute.name}" width="20" height="20" /><br>
												{/if}
											</a>
										</li>
										{if ($group.default == $id_attribute)}
											{$default_colorpicker = $id_attribute}
										{/if}
										{/foreach}
									</ul>
									<input type="hidden" class="color_pick_hidden" name="{$groupName}" value="{$default_colorpicker}" />
								{elseif ($group.group_type == 'radio')}
									<ul>
										{foreach from=$group.attributes key=id_attribute item=group_attribute}
											<li>
												<input type="radio" class="attribute_radio" name="{$groupName}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} onclick="findCombination();getProductAttribute();">
												{$group_attribute|escape:'htmlall':'UTF-8'}
											</li>
										{/foreach}
									</ul>
								{/if}
								</div>
							</fieldset>
						{/if}
					{/foreach}
					</div>
                    <div class="clear"></div>
				{/if}
                
                <!-- quantity wanted -->
				<div class="leo-product-suffix ">
				
						<div class="leo_pri_cart">
							<div class="content_prices">
								<!-- prices -->
								{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

								{if $product->online_only}
								<p class="online_only">{l s='Online only'}</p>
								{/if}

								<div class="price">
									<p class="our_price_display">
									{if $priceDisplay >= 0 && $priceDisplay <= 2}
										<span id="our_price_display">{convertPrice price=$productPrice}</span>
										<!--{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
											{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
										{/if}-->
									{/if}
									</p>

									{if $product->on_sale}
										<img src="{$img_dir}onsale_{$lang_iso}.gif" alt="{l s='On sale'}" class="on_sale_img"/>
										<span class="on_sale">{l s='On sale!'}</span>
									{elseif $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutReduction > $productPrice}
										<span class="discount">{l s='Reduced price!'}</span>
									{/if}
									{if $priceDisplay == 2}
										<br />
										<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
									{/if}
								</div>
								<p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}><span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>
								<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' || $product->specificPrice.reduction|intval ==0} style="display:none"{/if}>
									<span id="reduction_amount_display">
									{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' AND $product->specificPrice.reduction|intval !=0}
									-{convertPrice price=$productPriceWithoutReduction-$productPrice|floatval}
									{/if}
									</span>
								</p>
								<p id="old_price"{if !$product->specificPrice || !$product->specificPrice.reduction} class="hidden"{/if}>
								{if $priceDisplay >= 0 && $priceDisplay <= 2}
										<span id="old_price_display">{if $productPriceWithoutReduction > $productPrice}{convertPrice price=$productPriceWithoutReduction}{/if}</span>
										<!-- {if $tax_enabled && $display_tax_label == 1}{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}{/if} -->
								{/if}
								</p>
								{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
									<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
									<br class="clear" />
								{/if}
								{if $product->ecotax != 0}
									<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
										{if $product->specificPrice AND $product->specificPrice.reduction}
										<br />{l s='(not impacted by the discount)'}
										{/if}
									</p>
								{/if}
								{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
									 {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
									<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
								{/if}
								{*close if for show price*}
								{/if}
								<div class="clear"></div>
							</div>
							
							<p id="add_to_cart" {if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}style="display:none"{/if} class="buttons_bottom_block">
								<span></span>
								<input type="submit" name="Submit" value="{l s='Add to cart'}" class="exclusive" />
							</p>
							
							<p id="quantity_wanted_p" class="pull-right"{if (!$allow_oosp && $product->quantity <= 0) OR $virtual OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
								<label>{l s='Quantity:'}</label>
								<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" size="2" maxlength="3" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />
							</p>
						<div class="clear"></div>
						
						</div>
						
						
						
						<!-- minimal quantity wanted -->
						<p id="minimal_quantity_wanted_p"{if $product->minimal_quantity <= 1 OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
							{l s='This product is not sold individually. You must select at least'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b> {l s='quantity for this product.'}
						</p>
						{if $product->minimal_quantity > 1}
						<script type="text/javascript">
							checkMinimalQuantity();
						</script>
						{/if}
						<!-- Out of stock hook -->
						{if $HOOK_PRODUCT_OOS}
						<p id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
							{$HOOK_PRODUCT_OOS}
						</p>
						{/if}
						<p class="warning_inline" id="last_quantities"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
					
				</div>
                <!-- fin product price -->
                
				
				<!-- availability -->
				<p id="availability_statut" class="bg-stock" {if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
					
					<span id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>
					{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}
					</span>
				</p>

				<!-- number of item in stock -->
				{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
				<p id="pQuantityAvailable" class="bg-stock" {if $product->quantity <= 0} style="display: none;"{/if}>
					<span id="quantityAvailable">{$product->quantity|intval}</span>
					<span {if $product->quantity > 1} style="display: none;"{/if} id="quantityAvailableTxt">{l s='Item in stock'}</span>
					<span {if $product->quantity == 1} style="display: none;"{/if} id="quantityAvailableTxtMultiple">{l s='Items in stock'}</span>
				</p>
				{/if}

				<!--<p id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>
					<label>{l s='Reference:'} </label>
					<span class="editable">{$product->reference|escape:'htmlall':'UTF-8'}</span>
				</p> -->
				
		</div>
        
		{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}
			
		</form>
        
		{/if}
		{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if} 
        <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style ">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
                    <!--<a class="addthis_counter addthis_pill_style"></a> -->
                </div>
                {literal}
                <script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51265ed31cf46354"></script>
                <!--script type="text/javascript">$('a.iframe').fancybox();</script-->
				<script type="text/javascript">
					$(document).ready(function() { 
						$("a.iframe").fancybox({
							'type' : 'iframe',
							'width':600,
							'height':600
						});
					});
				</script>					
                {/literal}
                <!-- AddThis Button END -->
        
	</div>
    <!-- extraleft infos-->
		<div id="pb-extra-left-column" class="span3">
        	{$HOOK_EXTRALEFT}
        </div>
</div>

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<!-- quantity discount -->
<ul class="idTabs clearfix">
	<li><a href="#discount" style="cursor: pointer" class="selected">{l s='Sliding scale pricing'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
        <thead>
            <tr>
                <th>{l s='Product'}</th>
                <th>{l s='From (qty)'}</th>
                <th>{if Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')}{l s='Price'}{else}{l s='Discount'}{/if}</th>
            </tr>
        </thead>
		<tbody>
            {foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
            <tr id="quantityDiscount_{$quantity_discount.id_product_attribute}" class="quantityDiscount_{$quantity_discount.id_product_attribute}">
                <td>
                    {if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}
                        {$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
                    {else}
                        {$product->getProductName($quantity_discount.id_product)}
                    {/if}
                </td>
                <td>{$quantity_discount.quantity|intval}</td>
                <td>
                    {if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
			{if Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')}
				{convertPrice price=$productPrice-$quantity_discount.real_value|floatval}
			{else}
                       		-{convertPrice price=$quantity_discount.real_value|floatval}
			{/if}
                   {else}
			{if Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')}
				{convertPrice price = $productPrice-($productPrice*$quantity_discount.reduction)|floatval}
			{else}
				-{$quantity_discount.real_value|floatval}%
			{/if}

                   {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
	</table>
</div>
{/if}

<!-- description and features -->
{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
<div id="more_info_block" class="clear">
	<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">
		{if $product->description}<li><a id="more_info_tab_more_info" href="#idTab1">{l s='More info'}</a></li>{/if}
		<!--{if $features}<li><a id="more_info_tab_data_sheet" href="#idTab2">{l s='Data sheet'}</a></li>{/if} -->
		{if $attachments}<li><a id="more_info_tab_attachments" href="#idTab9">{l s='Download'}</a></li>{/if}
		{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}
		{if isset($product) && $product->customizable}<li><a href="#idTab10">{l s='Product customization'}</a></li>{/if}
		{$HOOK_PRODUCT_TAB}
	</ul>
	<div id="more_info_sheets" class="sheets align_justify">
	{if isset($product) && $product->description}
		<!-- full description -->
		<div id="idTab1" class="rte">{$product->description}</div>
	{/if}
	<!--	{if isset($features) && $features}
		
		<ul id="idTab2" class="bullet">
		{foreach from=$features item=feature}
            {if isset($feature.value)}
			    <li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> {$feature.value|escape:'htmlall':'UTF-8'}</li>
            {/if}
		{/foreach}
		</ul>
	{/if} -->
    
	{if isset($attachments) && $attachments}
		<ul id="idTab9" class="bullet">
		{foreach from=$attachments item=attachment}
			<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html'}">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
		{/foreach}
		</ul>
	{/if}
	{if isset($accessories) AND $accessories}
		<!-- accessories -->
		<div id="idTab4" class="bullet">
			<div class="products_block accessories_block clearfix">
				{foreach from=$accessories item=accessory name=accessories_list}
						{if ($accessory.allow_oosp || $accessory.quantity_all_versions > 0 || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode)}
						{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
						{assign var='nbItemsPerLine' value=3}
							{if $smarty.foreach.accessories_list.iteration%$nbItemsPerLine == 1 }
								<div class="row-fluid">
							{/if}
							<div class="ajax_block_product span{(12/Configuration::get('productlistcols'))} {if $smarty.foreach.accessories_list.first}first_item{elseif $smarty.foreach.accessories_list.last}last_item{else}item{/if} product_accessories_description product_block">
									<div class="product-container clearfix">
										<div class="center_block">
											<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.name|escape:'htmlall':'UTF-8'}" class="product_img_link"><img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html'}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}"  /></a>
										</div>
										<div class="right_block">
											<h4 class="s_title_block">
												<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.name|escape:'htmlall':'UTF-8'}">{$accessory.name|escape:'htmlall':'UTF-8'}</a>					
											</h4>
											<div class="product_desc">
												<a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{l s='More'}" class="product_description">{$accessory.description_short|strip_tags|truncate:400:'...'}</a>
											</div>
									
										
											{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE} 
												<div class="content_price price_container"> <span class="price">{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}</span>	</div>
											{/if}
											{if !$PS_CATALOG_MODE}
												<a class="cart_button exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html'}" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Add to cart'}"><i class="icon-leocart"></i>{l s='Add to cart'}</a>
											{/if}
										</div>
									</div>
							</div>
							{if $smarty.foreach.accessories_list.iteration % $nbItemsPerLine == 0 || $smarty.foreach.accessories_list.last}
								</div>
							{/if}
					{/if}
				{/foreach}
		</div>
	</div>
	{/if}


	<!-- Customizable products -->
	{if isset($product) && $product->customizable}
		<div id="idTab10" class="bullet customization_block">
			<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
				<p class="infoCustomizable">
					{l s='After saving your customized product, remember to add it to your cart.'}
					{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
				</p>
				{if $product->uploadable_files|intval}
				<div class="customizableProductsFile">
					<h3>{l s='Pictures'}</h3>
					<ul id="uploadable_files" class="clearfix">
						{counter start=0 assign='customizationField'}
						{foreach from=$customizationFields item='field' name='customizationFields'}
							{if $field.type == 0}
								<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
									{if isset($pictures.$key)}
									<div class="customizationUploadBrowse">
										<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
										<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html'}" title="{l s='Delete'}" >
											<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
										</a>
									</div>
									{/if}
									<div class="customizationUploadBrowse">
										<label class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your computer'}{/if}{if $field.required}<sup>*</sup>{/if}</label>
										<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="customization_block_input {if isset($pictures.$key)}filled{/if}" />
									</div>
								</li>
								{counter}
							{/if}
						{/foreach}
					</ul>
				</div>
				{/if}
				{if $product->text_fields|intval}
				<div class="customizableProductsText">
					<h3>{l s='Text'}</h3>
					<ul id="text_fields">
					{counter start=0 assign='customizationField'}
					{foreach from=$customizationFields item='field' name='customizationFields'}
						{if $field.type == 1}
						<li class="customizationUploadLine{if $field.required} required{/if}">
							<label for ="textField{$customizationField}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}</label>
							<textarea name="textField{$field.id_customization_field}" id="textField{$customizationField}" rows="1" cols="40" class="customization_block_input">{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}</textarea>
						</li>
						{counter}
						{/if}
					{/foreach}
					</ul>
				</div>
				{/if}
				<p id="customizedDatas">
					<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
					<input type="hidden" name="submitCustomizedDatas" value="1" />
					<input type="button" class="button" value="{l s='Save'}" onclick="javascript:saveCustomization()" />
					<span id="ajax-loader" style="display:none"><img src="{$img_ps_dir}loader.gif" alt="loader" /></span>
				</p>
			</form>
			<p class="clear required"><sup>*</sup> {l s='required fields'}</p>
		</div>
	{/if}

	{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
	</div>
</div>
{/if}
{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
{if isset($packItems) && $packItems|@count > 0}
	<div id="blockpack">
		<h2 class="title_block">{l s='Pack content'}</h2>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}
	</div>
{/if}
{/if}



<!-- MODULE Block specials -->
<div id="special_block_right" class="block products_block exclusive leospecials">
	<h4 class="title_block"><a href="{$link->getPageLink('prices-drop')}" title="{l s='Specials' mod='leospecials'}">{l s='Specials' mod='leospecials'}</a></h4>
	<div class="block_content">

{foreach from=$specials item=special}
		<ul class="products clearfix">
			<li class="product_image">
				<a href="{$special.link}"><img src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'medium_default')}" alt="{$special.legend|escape:html:'UTF-8'}" height="{$mediumSize.height}" width="{$mediumSize.width}" title="{$special.name|escape:html:'UTF-8'}" /></a>
			</li>
			<li>
				{if !$PS_CATALOG_MODE}
					{if $special.specific_prices}
						{assign var='specific_prices' value=$special.specific_prices}
						{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
							<span class="reduction"><span>-{$specific_prices.reduction*100|floatval}%</span></span>
						{/if}
					{/if}
				{/if}

					<h5 class="s_title_block"><a href="{$special.link}" title="{$special.name|escape:html:'UTF-8'}">{$special.name|escape:html:'UTF-8'}</a></h5>
				{if !$PS_CATALOG_MODE}
					<span class="price-discount">{if !$priceDisplay}{displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$special.priceWithoutReduction_tax_excl}{/if}</span>
					<span class="price">{if !$priceDisplay}{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}{/if}</span>
				{/if}
			</li>
		</ul>
{/foreach}
	<p>
		<a href="{$link->getPageLink('prices-drop')}" title="{l s='All specials' mod='leospecials'}">&raquo; {l s='All specials' mod='leospecials'}</a>
	</p>
	</div>
</div>
<!-- /MODULE Block specials -->
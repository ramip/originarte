{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog -->
<section class="page-product-box">
	<h3 class="page-product-heading">{l s='Related articles on blog' mod='prestablog'}</h3>
	{if $listeNewsLinked}
		<ul class="related_blog_product">
		{foreach from=$listeNewsLinked item=Item name=myLoop}
				<li>
					<a href="{$Item.url}">
						{if isset($Item.image_presente)}<img src="{$prestablog_theme_dir}up-img/adminth_{$Item.id}.jpg?{$md5pic}" alt="{$Item.title}" class="lastlisteimg" />{/if}
						<strong>{$Item.title}</strong>
					</a>
				</li>
			{if !$smarty.foreach.myLoop.last}{/if}
		{/foreach}
		</ul>
	{else}
		<p>{l s='No related articles on blog' mod='prestablog'}</p>
	{/if}
</section>
<!-- /Module Presta Blog -->

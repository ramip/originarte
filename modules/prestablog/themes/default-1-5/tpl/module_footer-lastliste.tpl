{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog -->
<div id="block_footer_last_list">
	<p class="title_block">{l s='Last blog articles' mod='prestablog'}</p>
	<ul>
		{if $ListeBlocLastNews}
			{foreach from=$ListeBlocLastNews item=Item name=myLoop}
				<li>{$Item.date}<br/>
					{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}">{/if}
						<strong>{$Item.title}</strong>
						{if $prestablog_config.prestablog_footlastnews_intro}<br /><span>{$Item.paragraph_crop}</span>{/if}
					{if isset($Item.link_for_unique)}</a>{/if}
				</li>
				{if !$smarty.foreach.myLoop.last}{/if}
			{/foreach}
		{else}
			<li>{l s='No news' mod='prestablog'}</li>
		{/if}
	</ul>
	{if $prestablog_config.prestablog_footlastnews_showall}
		<p>
			<a href="{PrestaBlogUrl}" class="button_large">{l s='See all' mod='prestablog'}</a>
		</p>
	{/if}
</div>
<!-- /Module Presta Blog -->

{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog -->
<div class="block">
	<h4 class="title_block">{l s='Last blog articles' mod='prestablog'}</h4>
	<div class="block_content" id="prestablog_lastliste">
		{if $ListeBlocLastNews}
			{foreach from=$ListeBlocLastNews item=Item name=myLoop}
				<p>
					{if isset($Item.link_for_unique)}<a href="{PrestaBlogUrl id=$Item.id_prestablog_news seo=$Item.link_rewrite titre=$Item.title}">{/if}
						{if isset($Item.image_presente) && $prestablog_config.prestablog_lastnews_showthumb}<img src="{$prestablog_theme_dir}up-img/adminth_{$Item.id_prestablog_news}.jpg?{$md5pic}" alt="{$Item.title}" class="lastlisteimg" />{/if}
						<strong>{$Item.title}</strong>
						{if $prestablog_config.prestablog_lastnews_showintro}<br /><span>{$Item.paragraph_crop}</span>{/if}
					{if isset($Item.link_for_unique)}</a>{/if}
				</p>
				{if !$smarty.foreach.myLoop.last}{/if}
			{/foreach}
		{else}
			<p>{l s='No news' mod='prestablog'}</p>
		{/if}
		{if $prestablog_config.prestablog_lastnews_showall}<a href="{PrestaBlogUrl}" class="button_large">{l s='See all' mod='prestablog'}</a>{/if}
	</div>
</div>
<!-- /Module Presta Blog -->

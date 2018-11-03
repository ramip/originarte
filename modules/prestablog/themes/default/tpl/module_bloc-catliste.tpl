{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog -->
<div class="block">
	<h4 class="title_block">
		{if $prestablog_categorie_courante->id}
			{if $prestablog_categorie_parent->id == 0 && $prestablog_categorie_courante->id != 0}
				<a href="{PrestaBlogUrl}">{l s='Blog' mod='prestablog'}</a>&nbsp;>
			{elseif $prestablog_categorie_parent->id > 0}
				<a href="{PrestaBlogUrl c=$prestablog_categorie_parent->id titre=$prestablog_categorie_parent->link_rewrite}">{$prestablog_categorie_parent->title}</a>&nbsp;>
			{/if}
			{$prestablog_categorie_courante->title}
		{else}
			{l s='Blog categories' mod='prestablog'}
		{/if}
	</h4>
	<div class="block_content" id="prestablog_catliste">
		{if sizeof($ListeBlocCatNews)}
			{foreach from=$ListeBlocCatNews item=Item name=myLoop}
				<p>
					<a href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}">
						{if isset($Item.image_presente) && $prestablog_config.prestablog_catnews_showthumb}<img src="{$prestablog_theme_dir}up-img/c/adminth_{$Item.id_prestablog_categorie}.jpg?{$md5pic}" alt="{$Item.link_rewrite}" class="lastlisteimg" />{/if}
						<strong>{$Item.title}</strong>
						{if $prestablog_config.prestablog_catnews_shownbnews && $Item.nombre_news_recursif > 0}&nbsp;<span>({$Item.nombre_news_recursif})</span>{/if}
					</a>
					{if $prestablog_config.prestablog_catnews_rss}<a target="_blank" href="{PrestaBlogUrl rss=$Item.id_prestablog_categorie}"><img src="{$prestablog_theme_dir}/img/rss.png" alt="Rss feed" align="absmiddle" /></a>{/if}
					{if $prestablog_config.prestablog_catnews_showintro}
					<a href="{PrestaBlogUrl c=$Item.id_prestablog_categorie titre=$Item.link_rewrite}"><br /><span>{$Item.description_crop}</span></a>{/if}
				</p>
			{/foreach}
		{else}
			<p>{l s='No subcategories' mod='prestablog'}</p>
		{/if}
		{if $prestablog_config.prestablog_catnews_showall}<a href="{PrestaBlogUrl}" class="button_large">{l s='See all' mod='prestablog'}</a>{/if}
	</div>
</div>
<!-- /Module Presta Blog -->


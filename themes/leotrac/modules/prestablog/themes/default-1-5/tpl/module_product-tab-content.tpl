{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog -->
	<div id="idTabBlog">
		{if $listeNewsLinked}
			<h3>{l s='Related articles on blog' mod='prestablog'}</h3>
			<ul class="related_blog_product">
			{foreach from=$listeNewsLinked item=Item name=myLoop}
					<li>
						<a href="{$Item.url}">
							{if isset($Item.image_presente)}<img src="{$prestablog_theme_dir}up-img/thumb_{$Item.id}.jpg?{$md5pic}" alt="{$Item.title}" class="lastlisteimg" />{/if}
							<span>{$Item.title}</span>
						</a>
					</li>
				{if !$smarty.foreach.myLoop.last}{/if}
			{/foreach}
			</ul>
		{else}
			<p>{l s='No related articles on blog' mod='prestablog'}</p>
		{/if}
	</div>
    <div class="clear">
    </div>
<!-- /Module Presta Blog -->

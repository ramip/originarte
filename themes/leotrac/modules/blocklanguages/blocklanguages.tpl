<!-- Block languages module -->
<div id="languages_block_top" class="nav-item pull-right">
	<div id="first-languages">
		<div class="item-top block-cur-languages">
		<p class="first-item">
		{foreach from=$languages key=k item=language name="languages"}
			{if $language.iso_code == $lang_iso}
			<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />
			<!-- Ocultamos texto del lenguaje
            <a href="javascript:void(0)"><span class="ptx_language">{$language.name}</span></a>-->
			{/if}
			
		{/foreach}
		</p>
			<div class="item-data hidden">
			<ul>
			{foreach from=$languages key=k item=language name="languages"}
				{if $language.iso_code != $lang_iso}
					{assign var=indice_lang value=$language.id_lang}
					{if isset($lang_rewrite_urls.$indice_lang)}
						<li><a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
					{else}
						<li><a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">
					{/if}
					<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />
					<span class="ptx_language">{$language.name}</span>
					</a></li>
				{/if}
			{/foreach}
			</ul>
			</div>
		</div>
	</div>
</div>
<!-- /Block languages module -->
<script type="text/javascript">
$(document).ready(function () {
	$("#first-languages .item-top").mouseover(function(){
		$(".item-data").removeClass("hidden");
	});
	$("#first-languages .item-top").mouseout(function(){
		$(".item-data").addClass("hidden");
	});
	$('span.ptx_language').each(function(){
		$(this).text(ptx_replace($(this).text()));
	});
	function ptx_replace(str) {
		re= /\([a-zA-Z0-9]+\)/;
		return str.replace(re,"");
	}
});
</script>
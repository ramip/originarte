
<div id="leobttslider{$leobtslider_modid}" class="carousel slide span9 hidden-phone leobttslider">
	<div class="carousel-inner">
		{foreach from=$leobtslider_slides item=slide name=slidename}
			<div class="item{if $smarty.foreach.slidename.index == 0} active{/if}">
				{if $slide.url}
					<a href="{$slide.url}"><img src="{$slide.mainimage}" alt="{$slide.title}" /></a>
				{else}
					<img src="{$slide.mainimage}" alt="{$slide.title}" />
				{/if}
				{if $slide.title  || $slide.description}
					<div class="slide-info">
						 
							<div class="slide-title">{$slide.title}</div>
							<div class="slide-description">{$slide.description}</div>
						 
					</div>
				{/if}
			</div>
		{/foreach}
	</div>
	<div class="carousel-thumb">
		{if count($leobtslider_slides) > 1}
		<div class="carousel-button">
			<a class="carousel-control left" href="#leobttslider{$leobtslider_modid}" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#leobttslider{$leobtslider_modid}" data-slide="next">&rsaquo;</a>
		</div>
		{/if}

	{if count($leobtslider_slides) > 1}
		{if $leobtslider.image_navigator}
			<ol class="carousel-indicators thumb-indicators hidden-phone">
			{foreach from=$leobtslider_slides item=item name=itemname}
				<li data-target="#leobttslider{$leobtslider_modid}" data-slide-to="{$smarty.foreach.itemname.index}" class="{if $smarty.foreach.itemname.index == 0}active{/if}">
					<img src="{$item.thumbnail}"/>
				</li>
			{/foreach}
			</ol>
		{else}
			<ol class="carousel-indicators">
			{foreach from=$leobtslider_slides item=item name=itemname}
				<li data-target="#leobttslider{$leobtslider_modid}" data-slide-to="{$smarty.foreach.itemname.index}" class="{if $smarty.foreach.itemname.index == 0}active{/if}"></li>
			{/foreach}
			</ol>
		{/if}
	{/if}
	
	</div>
</div>
{if $leobtslider.auto}
<script type="text/javascript">
	{literal}
	jQuery(document).ready(function(){
		$('#leobttslider{/literal}{$leobtslider_modid}{literal}').carousel({
		  interval: {/literal}{$leobtslider.delay}{literal}
		});
	});
	{/literal}
</script>
{/if}
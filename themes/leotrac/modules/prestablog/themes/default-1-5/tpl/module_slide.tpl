{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

<!-- Module Presta Blog
<div class="prestablog_slide">
	<div class="sliders_prestablog">
    <img class="img-prestablog" src="https://originarte.com/modules/prestablog/themes/default-1-5/up-img/slide_103.jpg" />
	{foreach from=$ListeBlogNews item=slide name=slides}
		<a href="{PrestaBlogUrl id=$slide.id_prestablog_news seo=$slide.link_rewrite titre=$slide.title}">
			<img src="{$prestablog_theme_dir}up-img/slide_{$slide.id_prestablog_news}.jpg?{$md5pic}" class="visu" alt="{$slide.title}" title="{$slide.title}" />
		</a>
	{/foreach}
    </div>
</div>

<script type="text/javascript">
	{literal}
		jQuery(document).ready(function(){
			$('.sliders_prestablog').nivoSlider({
				effect:'fold', //Specify sets like: 'fold,fade,sliceDown'
				slices: 15, 
				boxCols: 8,  // For box animations
				boxRows: 4,  // For box animations
				animSpeed:500, //Slide transition speed
				pauseTime:5000,
				startSlide:0, //Set starting Slide (0 index)
				directionNav:true, //Next & Prev
				directionNavHide:true, //Only show on hover
				controlNav:true, //1,2,3...
				keyboardNav:true, //Use left & right arrows
				pauseOnHover:true, //Stop animation while hovering
			});
		});
	{/literal}
</script>
<div class="clearfix"></div>
<!-- /Module Presta Blog -->

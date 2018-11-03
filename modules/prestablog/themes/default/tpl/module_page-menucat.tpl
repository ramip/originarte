{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}
<!-- Module Presta Blog -->
<script type="text/javascript">
	( function($) {
		$(function() {
			$("div#menu-mobile, div#menu-mobile-close").click(function() {
			$("#prestablog_menu_cat nav").toggle();
			 });

		});
	} ) ( jQuery );
</script>
<div id="prestablog_menu_cat">
<div id="menu-mobile"></div>
	<nav>
		{$MenuCatNews}
	</nav>
</div>
<!-- Module Presta Blog -->

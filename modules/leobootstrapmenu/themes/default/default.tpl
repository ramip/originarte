<div class="navbar">
<div class="navbar-inner">
		<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		</a>
	<div class="nav-collapse collapse">
            {$leobootstrapmenu_menu_tree}
						{*
            <div class="nav_item_r"><a href="https://originarte.com/es/?fc=module&module=prestablog&controller=blog">{l s='Portfolio' mod='leobootstrapmenu'}</a></div>
						*}
						<div class="nav_item_r"><a href="https://originarte.com/blog">{l s='Imprenta PRO' mod='leobootstrapmenu'}</a></div>

	</div>
</div>
</div>
<script type="text/javascript">
// <![CDATA[
    var currentURL = window.location;
    currentURL = String(currentURL);
    currentURL = currentURL.replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
    baseURL = baseUri.replace("https://","").replace("http://","").replace("www.","");
    isHomeMenu = 0;
    if($("body").attr("id")=="index") isHomeMenu = 1;
    $(".megamenu > li > a").each(function() {
        menuURL = $(this).attr("href").replace("https://","").replace("http://","").replace("www.","").replace( /#\w*/, "" );
        if( isHomeMenu && (baseURL == menuURL || baseURL == menuURL.substring(0,menuURL.length-3) || baseURL.replace("index.php","")==menuURL)){
            $(this).parent().addClass("active");
            return false;
        }
        if(currentURL == menuURL){
            $(this).parent().addClass("active");
            return false;
        }
    });
// ]]>
</script>

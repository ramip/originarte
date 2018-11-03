<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/modules/leobootstrapmenu/themes/default/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8286718475bde326db762d4-03982544%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '49602bedfdb955bb81ac0f79c34537ddc5ef2f55' => 
    array (
      0 => '/var/www/html/originarte2018/modules/leobootstrapmenu/themes/default/default.tpl',
      1 => 1541287252,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8286718475bde326db762d4-03982544',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'leobootstrapmenu_menu_tree' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326db88ce3_47107127',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326db88ce3_47107127')) {function content_5bde326db88ce3_47107127($_smarty_tpl) {?><div class="navbar">
<div class="navbar-inner">
		<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		</a>
	<div class="nav-collapse collapse">
            <?php echo $_smarty_tpl->tpl_vars['leobootstrapmenu_menu_tree']->value;?>

						
						<div class="nav_item_r"><a href="https://originarte.com/blog"><?php echo smartyTranslate(array('s'=>'Imprenta PRO','mod'=>'leobootstrapmenu'),$_smarty_tpl);?>
</a></div>

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
<?php }} ?>
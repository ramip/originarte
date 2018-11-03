<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:38
         compiled from "/var/www/html/originarte2018/themes/leotrac/modules/blocklanguages/blocklanguages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3037599675bde326e1a3db4-51381549%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '473266c14c7a9d8c9f18d0516330a94e036b09a4' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/modules/blocklanguages/blocklanguages.tpl',
      1 => 1541287511,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3037599675bde326e1a3db4-51381549',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'languages' => 0,
    'language' => 0,
    'lang_iso' => 0,
    'img_lang_dir' => 0,
    'indice_lang' => 0,
    'lang_rewrite_urls' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326e24f2f5_44334144',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326e24f2f5_44334144')) {function content_5bde326e24f2f5_44334144($_smarty_tpl) {?><!-- Block languages module -->
<div id="languages_block_top" class="nav-item pull-right">
	<div id="first-languages">
		<div class="item-top block-cur-languages">
		<p class="first-item">
		<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
			<?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']==$_smarty_tpl->tpl_vars['lang_iso']->value){?>
			<img src="<?php echo $_smarty_tpl->tpl_vars['img_lang_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
" width="16" height="11" />
			<!-- Ocultamos texto del lenguaje
            <a href="javascript:void(0)"><span class="ptx_language"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</span></a>-->
			<?php }?>
			
		<?php } ?>
		</p>
			<div class="item-data hidden">
			<ul>
			<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['language']->key;
?>
				<?php if ($_smarty_tpl->tpl_vars['language']->value['iso_code']!=$_smarty_tpl->tpl_vars['lang_iso']->value){?>
					<?php $_smarty_tpl->tpl_vars['indice_lang'] = new Smarty_variable($_smarty_tpl->tpl_vars['language']->value['id_lang'], null, 0);?>
					<?php if (isset($_smarty_tpl->tpl_vars['lang_rewrite_urls']->value[$_smarty_tpl->tpl_vars['indice_lang']->value])){?>
						<li><a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['lang_rewrite_urls']->value[$_smarty_tpl->tpl_vars['indice_lang']->value], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" title="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
">
					<?php }else{ ?>
						<li><a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getLanguageLink($_smarty_tpl->tpl_vars['language']->value['id_lang']), ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" title="<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
">
					<?php }?>
					<img src="<?php echo $_smarty_tpl->tpl_vars['img_lang_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>
" width="16" height="11" />
					<span class="ptx_language"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</span>
					</a></li>
				<?php }?>
			<?php } ?>
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
</script><?php }} ?>
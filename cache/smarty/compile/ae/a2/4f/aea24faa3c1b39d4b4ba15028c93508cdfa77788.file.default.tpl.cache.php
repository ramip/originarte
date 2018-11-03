<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/modules/leoctnavfooter/tmpl/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1586191435bde326dd55286-89370680%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aea24faa3c1b39d4b4ba15028c93508cdfa77788' => 
    array (
      0 => '/var/www/html/originarte2018/modules/leoctnavfooter/tmpl/default.tpl',
      1 => 1541287321,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1586191435bde326dd55286-89370680',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'class_prefix' => 0,
    'pos' => 0,
    'show_title' => 0,
    'module_title' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326dd68811_47750789',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326dd68811_47750789')) {function content_5bde326dd68811_47750789($_smarty_tpl) {?><div class="customhtml block <?php echo $_smarty_tpl->tpl_vars['class_prefix']->value;?>
 link_footer" id="leo-ctfooter-<?php echo $_smarty_tpl->tpl_vars['pos']->value;?>
">
	<?php if ($_smarty_tpl->tpl_vars['show_title']->value){?>
		<h3 class="title_block"><?php echo $_smarty_tpl->tpl_vars['module_title']->value;?>
</h3>
	<?php }?>
	<div class="block_content">
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	</div>
</div><?php }} ?>
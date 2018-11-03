<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/modules/leoctgotop/tmpl/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17276772735bde326dd2c210-07237202%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e277cd7bb77935d46f7c1fcf73fd69d830e7d88a' => 
    array (
      0 => '/var/www/html/originarte2018/modules/leoctgotop/tmpl/default.tpl',
      1 => 1541287148,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17276772735bde326dd2c210-07237202',
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
  'unifunc' => 'content_5bde326dd41854_29451877',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326dd41854_29451877')) {function content_5bde326dd41854_29451877($_smarty_tpl) {?><div class="customhtml block <?php echo $_smarty_tpl->tpl_vars['class_prefix']->value;?>
 " id="leo-customgoto-<?php echo $_smarty_tpl->tpl_vars['pos']->value;?>
">
	<?php if ($_smarty_tpl->tpl_vars['show_title']->value){?>
		<h3 class="title_block"><?php echo $_smarty_tpl->tpl_vars['module_title']->value;?>
</h3>
	<?php }?>
	<div class="block_content">
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	</div>
</div><?php }} ?>
<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/modules/leocustobottom/tmpl/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20558440045bde326dcb4cb7-60222649%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5dd6f4fcf4363b0d66ceacc4a41b603fccad1852' => 
    array (
      0 => '/var/www/html/originarte2018/modules/leocustobottom/tmpl/default.tpl',
      1 => 1541287317,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20558440045bde326dcb4cb7-60222649',
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
  'unifunc' => 'content_5bde326dcc9712_54197033',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326dcc9712_54197033')) {function content_5bde326dcc9712_54197033($_smarty_tpl) {?><div class="customhtml block <?php echo $_smarty_tpl->tpl_vars['class_prefix']->value;?>
 hidden-phone clearfix" id="leo-customhtml1-<?php echo $_smarty_tpl->tpl_vars['pos']->value;?>
">
	<?php if ($_smarty_tpl->tpl_vars['show_title']->value){?>
		<h3 class="title_block"><?php echo $_smarty_tpl->tpl_vars['module_title']->value;?>
</h3>
	<?php }?>
	<div class="block_content">
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	</div>
</div><?php }} ?>
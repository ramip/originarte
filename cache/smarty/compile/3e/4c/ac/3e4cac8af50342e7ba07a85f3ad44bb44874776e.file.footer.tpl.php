<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:41
         compiled from "/var/www/html/originarte2018/themes/leotrac/layout/default/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14432753685bde32719baef2-44238962%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e4cac8af50342e7ba07a85f3ad44bb44874776e' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/layout/default/footer.tpl',
      1 => 1541287536,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14432753685bde32719baef2-44238962',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'HOOK_CONTENTBOTTOM' => 0,
    'page_name' => 0,
    'LAYOUT_COLUMN_SPANS' => 0,
    'HOOK_RIGHT_COLUMN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde32719ddc49_54715713',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde32719ddc49_54715713')) {function content_5bde32719ddc49_54715713($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['HOOK_CONTENTBOTTOM']->value&&in_array($_smarty_tpl->tpl_vars['page_name']->value,array('index'))){?>
	<div id="contentbottom" class="no-border">
	<?php echo $_smarty_tpl->tpl_vars['HOOK_CONTENTBOTTOM']->value;?>

	</div>
<?php }?>
</section>
<?php if (isset($_smarty_tpl->tpl_vars['LAYOUT_COLUMN_SPANS']->value[2])&&$_smarty_tpl->tpl_vars['LAYOUT_COLUMN_SPANS']->value[2]){?> 
<!-- Right -->
<section id="right_column" class="column span<?php echo $_smarty_tpl->tpl_vars['LAYOUT_COLUMN_SPANS']->value[2];?>
 omega">
	<?php echo $_smarty_tpl->tpl_vars['HOOK_RIGHT_COLUMN']->value;?>

</section>
<?php }?><?php }} ?>
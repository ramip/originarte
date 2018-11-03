<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:40:54
         compiled from "/var/www/html/originarte2018/adm_arte/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:310276535bde32069064a4-22854093%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '13a9ebf67eae4015c65b8291f9b75fc901c2e88f' => 
    array (
      0 => '/var/www/html/originarte2018/adm_arte/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1541287669,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '310276535bde32069064a4-22854093',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde3206912894_36340307',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde3206912894_36340307')) {function content_5bde3206912894_36340307($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" class="edit" title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
">
	<img src="../img/admin/edit.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a><?php }} ?>
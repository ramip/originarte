<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:40:54
         compiled from "/var/www/html/originarte2018/adm_arte/themes/default/template/helpers/list/list_action_delete.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15728849495bde32069159c4-51668709%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1f518c1d7e3a05ae9fa83c8153e9203ad70f4111' => 
    array (
      0 => '/var/www/html/originarte2018/adm_arte/themes/default/template/helpers/list/list_action_delete.tpl',
      1 => 1541287668,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15728849495bde32069159c4-51668709',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'confirm' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde320692d5e9_81860733',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde320692d5e9_81860733')) {function content_5bde320692d5e9_81860733($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" class="delete" <?php if (isset($_smarty_tpl->tpl_vars['confirm']->value)){?>onclick="if (confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
')){ return true; }else{ event.stopPropagation(); event.preventDefault();};"<?php }?> title="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
">
	<img src="../img/admin/delete.gif" alt="<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
" />
</a><?php }} ?>
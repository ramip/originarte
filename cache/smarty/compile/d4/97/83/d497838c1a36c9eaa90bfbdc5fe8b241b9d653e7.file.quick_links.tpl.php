<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:40:42
         compiled from "/var/www/html/originarte2018/modules/minicslider/views/templates/hooks/quick_links.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6715349735bde31fab0e473-23697953%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd497838c1a36c9eaa90bfbdc5fe8b241b9d653e7' => 
    array (
      0 => '/var/www/html/originarte2018/modules/minicslider/views/templates/hooks/quick_links.tpl',
      1 => 1541287283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6715349735bde31fab0e473-23697953',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'minicslider' => 0,
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde31fab4d7e8_38063659',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde31fab4d7e8_38063659')) {function content_5bde31fab4d7e8_38063659($_smarty_tpl) {?><li>
  <a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules');?>
&configure=<?php echo $_smarty_tpl->tpl_vars['minicslider']->value;?>
" style="background: url('<?php echo $_smarty_tpl->tpl_vars['module_dir']->value;?>
logo.png') no-repeat center 25px #F8F8F8">
    <h4><?php echo smartyTranslate(array('s'=>'Minic Slider','mod'=>'minicslider'),$_smarty_tpl);?>
</h4>
    <p><?php echo smartyTranslate(array('s'=>'Powerfull image slider for advertising','mod'=>'minicslider'),$_smarty_tpl);?>
</p>
  </a>
</li><?php }} ?>
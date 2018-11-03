<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:41
         compiled from "/var/www/html/originarte2018/themes/leotrac/breadcrumb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14947410745bde32718799d9-88411559%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'add391156c737bf1587d730941262b657f0d4a59' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/breadcrumb.tpl',
      1 => 1541287432,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14947410745bde32718799d9-88411559',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page_name' => 0,
    'base_dir' => 0,
    'path' => 0,
    'category' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde32718d3e55_04159597',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde32718d3e55_04159597')) {function content_5bde32718d3e55_04159597($_smarty_tpl) {?>

<!-- Breadcrumb -->
<?php if ($_smarty_tpl->tpl_vars['page_name']->value!='index'){?>
<?php if (isset(Smarty::$_smarty_vars['capture']['path'])){?><?php $_smarty_tpl->tpl_vars['path'] = new Smarty_variable(Smarty::$_smarty_vars['capture']['path'], null, 0);?><?php }?>

<div id="breadcrumb">
    <ul class="breadcrumb">
        <li>
        <a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Return to Home'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a>
        <?php if (isset($_smarty_tpl->tpl_vars['path']->value)&&$_smarty_tpl->tpl_vars['path']->value){?>
            <span class="divider" <?php if (isset($_smarty_tpl->tpl_vars['category']->value)&&$_smarty_tpl->tpl_vars['category']->value->id_category==1){?>style="display:none;"<?php }?></span>
        <?php }?>
        </li>
        <?php if (isset($_smarty_tpl->tpl_vars['path']->value)&&$_smarty_tpl->tpl_vars['path']->value){?>
            <?php if (!strpos($_smarty_tpl->tpl_vars['path']->value,'span')){?>
                <li><?php echo $_smarty_tpl->tpl_vars['path']->value;?>
</li>
            <?php }else{ ?>
                <li class="active"><?php echo $_smarty_tpl->tpl_vars['path']->value;?>
</li>
            <?php }?>
        <?php }?>
    </ul>
</div>
<?php }?>
<!-- /Breadcrumb -->
<?php }} ?>
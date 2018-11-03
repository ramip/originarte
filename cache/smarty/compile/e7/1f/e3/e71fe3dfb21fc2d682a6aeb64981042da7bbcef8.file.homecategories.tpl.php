<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:38
         compiled from "/var/www/html/originarte2018/modules/homecategories/homecategories.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18605837715bde326eb9cc10-17224235%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e71fe3dfb21fc2d682a6aeb64981042da7bbcef8' => 
    array (
      0 => '/var/www/html/originarte2018/modules/homecategories/homecategories.tpl',
      1 => 1541287352,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18605837715bde326eb9cc10-17224235',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categories' => 0,
    'category' => 0,
    'link' => 0,
    'categoryLink' => 0,
    'img_cat_dir' => 0,
    'categorySize' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326ec01587_56298474',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326ec01587_56298474')) {function content_5bde326ec01587_56298474($_smarty_tpl) {?><!-- MODULE Home categories -->
<div class="home_categories">
    <h2><?php echo smartyTranslate(array('s'=>'Categories','mod'=>'homecategories'),$_smarty_tpl);?>
</h2>
    <?php if (isset($_smarty_tpl->tpl_vars['categories']->value)&&$_smarty_tpl->tpl_vars['categories']->value){?>
            <ul>
            <?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['category']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['category']->iteration=0;
 $_smarty_tpl->tpl_vars['category']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value){
$_smarty_tpl->tpl_vars['category']->_loop = true;
 $_smarty_tpl->tpl_vars['category']->iteration++;
 $_smarty_tpl->tpl_vars['category']->index++;
 $_smarty_tpl->tpl_vars['category']->first = $_smarty_tpl->tpl_vars['category']->index === 0;
 $_smarty_tpl->tpl_vars['category']->last = $_smarty_tpl->tpl_vars['category']->iteration === $_smarty_tpl->tpl_vars['category']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['homeCategories']['first'] = $_smarty_tpl->tpl_vars['category']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['homeCategories']['last'] = $_smarty_tpl->tpl_vars['category']->last;
?>
                <?php $_smarty_tpl->tpl_vars['categoryLink'] = new Smarty_variable($_smarty_tpl->tpl_vars['link']->value->getcategoryLink($_smarty_tpl->tpl_vars['category']->value['id_category'],$_smarty_tpl->tpl_vars['category']->value['link_rewrite']), null, 0);?>
                <li class="ajax_block_category <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['homeCategories']['first']){?>first_item<?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['homeCategories']['last']){?>last_item<?php }else{ ?>item<?php }?>">
                <div><a href="<?php echo $_smarty_tpl->tpl_vars['categoryLink']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['category']->value['legend'];?>
" class="category_image"><img src="<?php echo $_smarty_tpl->tpl_vars['img_cat_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['category']->value['id_category'];?>
-large_default.jpg" alt="<?php echo $_smarty_tpl->tpl_vars['category']->value['name'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['category']->value['name'];?>
" class="categoryImage" width="<?php echo $_smarty_tpl->tpl_vars['categorySize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['categorySize']->value['height'];?>
" /></a></div>
                <a href="<?php echo $_smarty_tpl->tpl_vars['categoryLink']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['category']->value['legend'];?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['category']->value['name'],35);?>
</a>

                </li>
            <?php } ?>
            </ul>
    <?php }else{ ?>
        <p><?php echo smartyTranslate(array('s'=>'No categories','mod'=>'homecategories'),$_smarty_tpl);?>
</p>
  <?php }?>
    <div class="cr"></div>
</div>
<!-- /MODULE Home categories --><?php }} ?>
<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:38
         compiled from "/var/www/html/originarte2018/modules/minicslider/views/templates/front/front.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17092579495bde326e9a94e8-35219152%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f97468538a6c653016abb170a4c827a925c95789' => 
    array (
      0 => '/var/www/html/originarte2018/modules/minicslider/views/templates/front/front.tpl',
      1 => 1541287283,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17092579495bde326e9a94e8-35219152',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'slides' => 0,
    'minicSlider' => 0,
    'page_name' => 0,
    'image' => 0,
    'caption' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326eb579a7_94229830',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326eb579a7_94229830')) {function content_5bde326eb579a7_94229830($_smarty_tpl) {?><?php if (count($_smarty_tpl->tpl_vars['slides']->value)!=0){?>
    <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['front']==1&&$_smarty_tpl->tpl_vars['page_name']->value!='index'){?>
        <!-- Minic Slider -->
    <?php }else{ ?>
        <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['position']=='top'){?>
        </div>  
            </div>
        <?php }?>
        <div id="minic_slider" class="theme-default<?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['thumbnail']==1&&$_smarty_tpl->tpl_vars['minicSlider']->value['options']['control']!=0){?> controlnav-thumbs<?php }?>">   
            <div id="slider" class="nivoSlider" style="<?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['min_width']){?>min-width:<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['min_width'];?>
;<?php }?><?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['max_width']){?>max-width:<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['max_width'];?>
;<?php }?><?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['max_height']){?>max-height:<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['max_height'];?>
;<?php }else{ ?>min-height:<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['min_height'];?>
px;<?php }?><?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['control']!=1){?>margin-bottom:0;<?php }?>">
                <?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slides']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
?>
                    <?php if ($_smarty_tpl->tpl_vars['image']->value['url']!=''){?><a href="<?php echo $_smarty_tpl->tpl_vars['image']->value['url'];?>
" <?php if ($_smarty_tpl->tpl_vars['image']->value['target']==1){?>target="_blank"<?php }?>><?php }?>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['path']['images'];?>
<?php echo $_smarty_tpl->tpl_vars['image']->value['image'];?>
" class="slider_image" 
                            <?php if ($_smarty_tpl->tpl_vars['image']->value['alt']){?>alt="<?php echo $_smarty_tpl->tpl_vars['image']->value['alt'];?>
"<?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['image']->value['title']!=''||$_smarty_tpl->tpl_vars['image']->value['caption']!=''){?>title="#htmlcaption_<?php echo $_smarty_tpl->tpl_vars['image']->value['id_slide'];?>
"<?php }?> 
                            <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['thumbnail']==1){?>data-thumb="<?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['path']['thumbs'];?>
<?php echo $_smarty_tpl->tpl_vars['image']->value['image'];?>
"<?php }?>/>
                    <?php if ($_smarty_tpl->tpl_vars['image']->value['url']!=''){?></a><?php }?>
                <?php } ?>
            </div>
            <?php  $_smarty_tpl->tpl_vars['caption'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['caption']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slides']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['caption']->key => $_smarty_tpl->tpl_vars['caption']->value){
$_smarty_tpl->tpl_vars['caption']->_loop = true;
?>
                <?php if ($_smarty_tpl->tpl_vars['caption']->value['title']!=''||$_smarty_tpl->tpl_vars['caption']->value['caption']!=''){?>
                    <div id="htmlcaption_<?php echo $_smarty_tpl->tpl_vars['caption']->value['id_slide'];?>
" class="nivo-html-caption">
                        <h3><?php echo $_smarty_tpl->tpl_vars['caption']->value['title'];?>
</h3>
                        <p><?php echo $_smarty_tpl->tpl_vars['caption']->value['caption'];?>
</p>
                    </div>
                <?php }?>
            <?php } ?>
        </div> 
          
        <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['position']=='top'){?>
        <div>
            <div>
        <?php }?>   

        <script type="text/javascript">
        $(window).load(function() {
            $('#slider').nivoSlider({
                effect: '<?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['current']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['current'];?>
<?php }else{ ?>random<?php }?>', 
                slices: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['slices']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['slices'];?>
<?php }else{ ?>15<?php }?>, 
                boxCols: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['slices']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['cols'];?>
<?php }else{ ?>8<?php }?>, 
                boxRows: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['rows']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['rows'];?>
<?php }else{ ?>4<?php }?>, 
                animSpeed: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['speed']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['speed'];?>
<?php }else{ ?>500<?php }?>, 
                pauseTime: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['pause']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['pause'];?>
<?php }else{ ?>3000<?php }?>, 
                startSlide: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['startSlide']!=''){?><?php echo $_smarty_tpl->tpl_vars['minicSlider']->value['options']['startSlide'];?>
<?php }else{ ?>0<?php }?>,
                directionNav: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['buttons']==1){?>true<?php }else{ ?>false<?php }?>, 
                controlNav: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['control']==1){?>true<?php }else{ ?>false<?php }?>, 
                controlNavThumbs: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['thumbnail']==1){?>true<?php }else{ ?>false<?php }?>,
                pauseOnHover: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['hover']==1){?>true<?php }else{ ?>false<?php }?>, 
                manualAdvance: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['manual']==1){?>true<?php }else{ ?>false<?php }?>, 
                prevText: '<?php echo smartyTranslate(array('s'=>'Prev','mod'=>'minicslider'),$_smarty_tpl);?>
', 
                nextText: '<?php echo smartyTranslate(array('s'=>'Next','mod'=>'minicslider'),$_smarty_tpl);?>
', 
                randomStart: <?php if ($_smarty_tpl->tpl_vars['minicSlider']->value['options']['random']==1){?>true<?php }else{ ?>false<?php }?>,
                
                afterLoad: function(){
                    $('#slider').css({'min-height' : '1px'});
                }
                
            });
        });
        </script>   
    <?php }?>
<?php }?><?php }} ?>
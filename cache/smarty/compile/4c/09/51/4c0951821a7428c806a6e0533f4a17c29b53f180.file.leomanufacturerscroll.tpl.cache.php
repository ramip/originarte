<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/themes/leotrac/modules/leomanufacturerscroll/leomanufacturerscroll.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10151579425bde326dc0eb06-48803165%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4c0951821a7428c806a6e0533f4a17c29b53f180' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/modules/leomanufacturerscroll/leomanufacturerscroll.tpl',
      1 => 1541287499,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10151579425bde326dc0eb06-48803165',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'leomanufacturers' => 0,
    'show_title' => 0,
    'module_title' => 0,
    'manufacturer' => 0,
    'leoManufactureConfig' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326dc674b5_58723499',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326dc674b5_58723499')) {function content_5bde326dc674b5_58723499($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['leomanufacturers']->value){?>
	<?php if ($_smarty_tpl->tpl_vars['show_title']->value){?>
		<div>
			<h2><?php echo $_smarty_tpl->tpl_vars['module_title']->value;?>
</h2>
		</div>
	<?php }?>
	<div id="mycarouselHolder" align="center" class="block">
		<div class="row-fluid">		
			<div class="jcarousel-wrap">		
				<div id="wrap">
				  <ul id="lofjcarousel" class="jcarousel-skin-tango">
					<?php  $_smarty_tpl->tpl_vars['manufacturer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['manufacturer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['leomanufacturers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['manufacturer']->key => $_smarty_tpl->tpl_vars['manufacturer']->value){
$_smarty_tpl->tpl_vars['manufacturer']->_loop = true;
?>
						<li class="lof-item">
							<a href="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['link'];?>
">
								<img src="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['linkIMG'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['manufacturer']->value['name'];?>
" vspace="0" border="0" />
								
							</a>
						</li>
					<?php } ?>
				  </ul>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#lofjcarousel").flexisel({
			visibleItems: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['limit'];?>
,
			animationSpeed: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['animate_time'];?>
,
			autoPlay: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['auto_play'];?>
,
			autoPlaySpeed: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['auto_time'];?>
,
			pauseOnHover: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['pause_on_hover'];?>
,
			enableResponsiveBreakpoints: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['enable_responsive'];?>
,
	    	responsiveBreakpoints: { 
	    		portrait: { 
	    			changePoint:<?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['portraint_change_point'];?>
,
	    			visibleItems: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['portraint_visible_items'];?>

	    		},
	    		landscape: { 
	    			changePoint:<?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['landscape_change_point'];?>
,
	    			visibleItems: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['landscape_visible_items'];?>

	    		},
	    		tablet: {
	    			changePoint:<?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['tablet_change_point'];?>
,
	    			visibleItems: <?php echo $_smarty_tpl->tpl_vars['leoManufactureConfig']->value['tablet_visible_items'];?>

	    		}
	    	}
	    });
	});
</script>
<?php }?>
<?php }} ?>
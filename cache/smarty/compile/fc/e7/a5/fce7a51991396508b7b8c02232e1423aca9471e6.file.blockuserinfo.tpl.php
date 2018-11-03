<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:38
         compiled from "/var/www/html/originarte2018/themes/leotrac/modules/blockuserinfo/blockuserinfo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19254342245bde326e2637e3-38151376%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fce7a51991396508b7b8c02232e1423aca9471e6' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/modules/blockuserinfo/blockuserinfo.tpl',
      1 => 1541287491,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19254342245bde326e2637e3-38151376',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'logged' => 0,
    'cookie' => 0,
    'PS_CATALOG_MODE' => 0,
    'order_process' => 0,
    'cart_qties' => 0,
    'priceDisplay' => 0,
    'blockuser_cart_flag' => 0,
    'cart' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326e353b42_38105095',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326e353b42_38105095')) {function content_5bde326e353b42_38105095($_smarty_tpl) {?>

<script type="text/javascript">
$(document).ready(function () {
	var width = $(window).width();

	if(width >=767){
			$("#header_user_info").css("display","block");
			$("#leo-button3").css("display","none");
		}
	else {
		$("#header_user_info").css("display","none");
		$("#leo-button3").css("display","block");
		$("#leo-button3").css("padding-left","20px");
		}

	$('#header_user').each(function(){
		$(this).find('a.leo-mobile').click(function(){
		 $('#header_user_info').slideToggle('slow');

		});
	  });

  $(window).resize(function(){
		var width = $(window).width();
		if(width >=767){
			$("#header_user_info").css("display","block");
			$("#leo-button3").css("display","none");
		}
		else{
			$("#header_user_info").css("display","none");
			$("#leo-button3").css("display","block");
			$("#leo-button3").css("padding-left","20px");

		}
	});


});
</script>


<!-- Block user information module HEADER -->
<div class="login_userinfo">

	<div id="header_user" >
		<div id="leo-button3" class="hidden"><a class="leo-mobile"><?php echo smartyTranslate(array('s'=>'User Information','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div>
		<div id="header_user_info">

			<div class="nav-item" id="your_account"><div class="item-top"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true);?>
" title="<?php echo smartyTranslate(array('s'=>'Your Account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Your Account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div></div>
<!-- Ocultamos wishlist
			<?php if ($_smarty_tpl->tpl_vars['logged']->value){?>
			<div class="nav-item" id="wishlist_block">
				<div class="item-top"><a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getModuleLink('blockwishlist','mywishlist');?>
" title="<?php echo smartyTranslate(array('s'=>'My wishlist','mod'=>'blockuserinfo'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'My wishlist','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div>
			</div>
			<?php }?>
-->
				<!--<?php if ($_smarty_tpl->tpl_vars['logged']->value){?> -->
					<div class="nav-item">
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'View my customer account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
" class="account" rel="nofollow"><span><?php echo $_smarty_tpl->tpl_vars['cookie']->value->customer_firstname;?>
 <?php echo $_smarty_tpl->tpl_vars['cookie']->value->customer_lastname;?>
</span></a>
					</div>
					<div class="nav-item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('index',true,null,"mylogout"), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Log me out','mod'=>'blockuserinfo'),$_smarty_tpl);?>
" class="logout" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Log out','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div>
				<!-- Ocultar el log-in
                <?php }else{ ?> 
				<div class="nav-item">
					<div class="item-top"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('my-account',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Login to your customer account','mod'=>'blockuserinfo'),$_smarty_tpl);?>
" class="login" rel="nofollow"><?php echo smartyTranslate(array('s'=>'Login','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div>
					</div>
                
				<?php }?> -->

			<div class="nav-item"><div class="item-top"><a href="https://originarte.com/content/6-guia-de-impresion"><?php echo smartyTranslate(array('s'=>'Help','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div></div>
            <div class="nav-item"><div class="item-top"><a href="https://originarte.com/content/1-faq"><?php echo smartyTranslate(array('s'=>'FAQ','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div></div>
            <div class="nav-item"><div class="item-top"><a href="https://originarte.com/contact-us"><?php echo smartyTranslate(array('s'=>'Contact','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</a></div></div>
		</div>
	</div>
	<div id="topminibasket">
		<div id="header_nav">
				<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>
				<div id="shopping_cart">

						<a class="kenyan_coffee_rg" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['order_process']->value,true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Your Shopping Cart','mod'=>'blockuserinfo'),$_smarty_tpl);?>
">
						<span class="title_cart"><?php echo smartyTranslate(array('s'=>'Shopping Cart','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</span>
						<span class="ajax_cart_quantity<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value==0){?> hidden<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cart_qties']->value;?>
</span>
						<span class="ajax_cart_product_txt<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value!=1){?> hidden<?php }?>"><?php echo smartyTranslate(array('s'=>'item','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</span>
						<span class="ajax_cart_product_txt_s<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value<2){?> hidden<?php }?>"><?php echo smartyTranslate(array('s'=>'items','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</span>
						<span class="hidden-totalsp ajax_cart_total<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value==0){?> hidden<?php }?>">
							<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value>0){?>
								<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value==1){?>
									<?php $_smarty_tpl->tpl_vars['blockuser_cart_flag'] = new Smarty_variable(constant('Cart::BOTH_WITHOUT_SHIPPING'), null, 0);?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['cart']->value->getOrderTotal(false,$_smarty_tpl->tpl_vars['blockuser_cart_flag']->value)),$_smarty_tpl);?>

								<?php }else{ ?>
									<?php $_smarty_tpl->tpl_vars['blockuser_cart_flag'] = new Smarty_variable(constant('Cart::BOTH_WITHOUT_SHIPPING'), null, 0);?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['cart']->value->getOrderTotal(true,$_smarty_tpl->tpl_vars['blockuser_cart_flag']->value)),$_smarty_tpl);?>

								<?php }?>
							<?php }?>
						</span>

						<span class="ajax_cart_no_product<?php if ($_smarty_tpl->tpl_vars['cart_qties']->value>0){?> hidden<?php }?>"><?php echo smartyTranslate(array('s'=>'(empty)','mod'=>'blockuserinfo'),$_smarty_tpl);?>
</span>
						</a>


				</div>
				<?php }?>

			</div>
	</div>
</div>
<!-- /Block user information module HEADER -->
<?php }} ?>
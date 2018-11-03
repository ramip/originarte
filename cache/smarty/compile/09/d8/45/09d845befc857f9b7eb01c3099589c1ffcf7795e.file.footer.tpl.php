<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:41
         compiled from "/var/www/html/originarte2018/themes/leotrac/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17116110245bde327196c8d3-77940665%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '09d845befc857f9b7eb01c3099589c1ffcf7795e' => 
    array (
      0 => '/var/www/html/originarte2018/themes/leotrac/footer.tpl',
      1 => 1541287431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17116110245bde327196c8d3-77940665',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content_only' => 0,
    'LEO_LAYOUT_DIRECTION' => 0,
    'HOOK_BOTTOM' => 0,
    'HOOK_FOOTER' => 0,
    'LEO_COPYRIGHT' => 0,
    'HOOK_FOOTNAV' => 0,
    'LEO_PANELTOOL' => 0,
    'LEO_PATTERN' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde32719b8292_12647189',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde32719b8292_12647189')) {function content_5bde32719b8292_12647189($_smarty_tpl) {?>

		<?php if (!$_smarty_tpl->tpl_vars['content_only']->value){?>
		<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./layout/".((string)$_smarty_tpl->tpl_vars['LEO_LAYOUT_DIRECTION']->value)."/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</div></div></section>

<!-- Footer -->
			<?php if ($_smarty_tpl->tpl_vars['HOOK_BOTTOM']->value){?>
			<section id="bottom">
				<div class="container">
					<div class="row-fluid">
						<div class="clearfix">
							
						 <?php echo $_smarty_tpl->tpl_vars['HOOK_BOTTOM']->value;?>

							
						 </div>
					</div>
				</div>
			</section>
			<?php }?>
			<footer id="footer" class="omega clearfix">
				<section class="footer">
				<div class="container">
					<div class="row-fluid nice-columns">
					<?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTER']->value;?>

					</div>
				</div>
				</section>	
				<section id="footer-bottom">
							<div class="container"><div class="row-fluid">
								<div class="clearfix">
									<div class="span6">
										<div class="copyright">
										<!--<?php echo $_smarty_tpl->tpl_vars['LEO_COPYRIGHT']->value;?>
.<br>  -->
											Copyright <a href="http://www.originarte.com" title="Originarte Diseño e Impresión">Originarte</a>. Todos los derechos reservados.
										</div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['HOOK_FOOTNAV']->value){?>
										<div class="span6"><div class="footnav"><?php echo $_smarty_tpl->tpl_vars['HOOK_FOOTNAV']->value;?>
</div></div>		
									<?php }?>
								</div>
							</div>
						</div>	
				</section>
				
			</footer>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['LEO_PANELTOOL']->value){?>
    	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./info/paneltool.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    <?php }?>
	
	<script type="text/javascript">
			var classBody = "<?php echo $_smarty_tpl->tpl_vars['LEO_PATTERN']->value;?>
";
			$("body").addClass( classBody.replace(/\.\w+$/,"")  );
		</script>
	
	</body>
</html>
<?php }} ?>
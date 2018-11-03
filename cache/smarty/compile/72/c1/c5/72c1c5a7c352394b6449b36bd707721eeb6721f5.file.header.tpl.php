<?php /* Smarty version Smarty-3.1.14, created on 2018-11-04 00:42:37
         compiled from "/var/www/html/originarte2018/modules/ganalytics/views/templates/hook/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9947170215bde326deab573-06212374%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72c1c5a7c352394b6449b36bd707721eeb6721f5' => 
    array (
      0 => '/var/www/html/originarte2018/modules/ganalytics/views/templates/hook/header.tpl',
      1 => 1541287419,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9947170215bde326deab573-06212374',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'universal_analytics' => 0,
    'ganalytics_id' => 0,
    'pageTrack' => 0,
    'isOrder' => 0,
    'trans' => 0,
    'items' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5bde326e0e63b9_51740328',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5bde326e0e63b9_51740328')) {function content_5bde326e0e63b9_51740328($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/var/www/html/originarte2018/tools/smarty/plugins/modifier.escape.php';
?>
<script type="text/javascript">
    <?php if ($_smarty_tpl->tpl_vars['universal_analytics']->value==true){?>
    
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
    

    ga('create', '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['ganalytics_id']->value, 'htmlall', 'UTF-8');?>
'<?php if (isset($_smarty_tpl->tpl_vars['pageTrack']->value)){?>, '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['pageTrack']->value, 'htmlall', 'UTF-8');?>
'<?php }?>);

    <?php if ($_smarty_tpl->tpl_vars['isOrder']->value==true){?>
    ga('require', 'ecommerce', 'ecommerce.js');
    <?php }else{ ?>
    ga('send', 'pageview');
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['isOrder']->value==true){?>
    ga('ecommerce:addTransaction', {
        'id': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['id'], 'htmlall', 'UTF-8');?>
',
        'affiliation': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['store'], 'htmlall', 'UTF-8');?>
',
        'revenue': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['total'], 'htmlall', 'UTF-8');?>
',
        'tax': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['tax'], 'htmlall', 'UTF-8');?>
',
        'shipping': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['shipping'], 'htmlall', 'UTF-8');?>
',
        'city': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['city'], 'htmlall', 'UTF-8');?>
',
        'state': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['state'], 'htmlall', 'UTF-8');?>
',
        'country': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['country'], 'htmlall', 'UTF-8');?>
',
        'currency': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['currency'], 'htmlall', 'UTF-8');?>
'
    });

    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
    ga('ecommerce:addItem', {
        'id': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['OrderId'], 'htmlall', 'UTF-8');?>
',
        'sku': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['SKU'], 'htmlall', 'UTF-8');?>
',
        'name': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Product'], 'htmlall', 'UTF-8');?>
',
        'category': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Category'], 'htmlall', 'UTF-8');?>
',
        'price': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Price'], 'htmlall', 'UTF-8');?>
',
        'quantity': '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Quantity'], 'htmlall', 'UTF-8');?>
'
    });
    <?php } ?>
    
    (function() {
	    <?php if ($_smarty_tpl->tpl_vars['isOrder']->value==true){?>
			var key = 'ga_trans';
			var idtrans = <?php echo intval($_smarty_tpl->tpl_vars['trans']->value['id']);?>
;
			if (!!$.prototype.totalStorage)
				var view_ga_trans = parseInt($.totalStorage(key));
			else if (typeof localStorage !== 'undefined' && localStorage)
				var view_ga_trans = parseInt(localStorage.getItem(key));

			if (typeof view_ga_trans !== 'undefined' &&  view_ga_trans > 0 && idtrans == view_ga_trans )
				return false;

			if (!!$.prototype.totalStorage)
				$.totalStorage(parseInt(key, idtrans));
			else if (typeof localStorage !== 'undefined' && localStorage)
				localStorage.setItem(key, parseInt(idtrans));
		<?php }?>
		ga('ecommerce:send');
    })();
    
    <?php }?>
    <?php }else{ ?>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['ganalytics_id']->value, 'htmlall', 'UTF-8');?>
']);
    // Recommended value by Google doc and has to before the trackPageView
    _gaq.push(['_setSiteSpeedSampleRate', 5]);

    _gaq.push(['_trackPageview'<?php if (isset($_smarty_tpl->tpl_vars['pageTrack']->value)){?>, '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['pageTrack']->value, 'htmlall', 'UTF-8');?>
'<?php }?>]);

    <?php if ($_smarty_tpl->tpl_vars['isOrder']->value==true){?>            
    _gaq.push(['_addTrans',
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['id'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['store'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['total'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['tax'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['shipping'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['city'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['state'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['trans']->value['country'], 'htmlall', 'UTF-8');?>
' 
    ]);

    <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['items']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
    _gaq.push(['_addItem',
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['OrderId'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['SKU'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Product'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Category'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Price'], 'htmlall', 'UTF-8');?>
', 
        '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['item']->value['Quantity'], 'htmlall', 'UTF-8');?>
' 
    ]);
    <?php } ?>
    
    
    _gaq.push(['_trackTrans']);
    
    <?php }?>
    
    (function() {
	    <?php if ($_smarty_tpl->tpl_vars['isOrder']->value==true){?>
			var key = 'ga_trans';
			var idtrans = <?php echo intval($_smarty_tpl->tpl_vars['trans']->value['id']);?>
;
			if (!!$.prototype.totalStorage)
				var view_ga_trans = parseInt($.totalStorage(key));
			else if (typeof localStorage !== 'undefined' && localStorage)
				var view_ga_trans = parseInt(localStorage.getItem(key));

			if (typeof view_ga_trans !== 'undefined' &&  view_ga_trans > 0 && idtrans == view_ga_trans )
				return false;

			if (!!$.prototype.totalStorage)
				$.totalStorage(parseInt(key, idtrans));
			else if (typeof localStorage !== 'undefined' && localStorage)
				localStorage.setItem(key, parseInt(idtrans));
		<?php }?>

        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
    
    <?php }?>
</script><?php }} ?>
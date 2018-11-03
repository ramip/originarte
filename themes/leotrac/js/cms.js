/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function submitPublishCMS(url, redirect, token)
{
	var id_cms = $('#admin-action-cms-id').val();

	$.ajaxSetup({async: false});
	$.post(url+'/index.php', { 
			action: 'PublishCMS',
			id_cms: id_cms, 
			status: 1, 
			redirect: redirect,
			ajax: 1,
			tab: 'AdminCmsContent',
			token: token
		},
		function(data)
		{
			if (data.indexOf('error') === -1)
				document.location.href = data;
		}
	);

	return true;
}

$(document).ready(function() {

$('.faq_question').click(function() {

if ($(this).parent().is('.open')){
$(this).closest('.faq').find('.faq_answer_container').animate({'height':'0'},500);
$(this).closest('.faq').removeClass('open');

}else{
var newHeight =$(this).closest('.faq').find('.faq_answer').height() +'px';
$(this).closest('.faq').find('.faq_answer_container').animate({'height':newHeight},500);
$(this).closest('.faq').addClass('open');
}

});

});
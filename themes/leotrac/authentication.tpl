{*
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
*}

{capture name=path}
	{if !isset($email_create)}{l s='Authentication'}{else}
		<a href="{$link->getPageLink('authentication', true)|escape:'html'}" rel="nofollow" title="{l s='Authentication'}">{l s='Authentication'}</a>
		<span class="navigation-pipe">{$navigationPipe}</span>{l s='Create your account'}
	{/if}
{/capture}
	{include file="$tpl_dir./breadcrumb.tpl"}

<script type="text/javascript">
// <![CDATA[
var idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}false{/if};
var countries = new Array();
var countriesNeedIDNumber = new Array();
var countriesNeedZipCode = new Array();
{if isset($countries)}
	{foreach from=$countries item='country'}
		{if isset($country.states) && $country.contains_states}
			countries[{$country.id_country|intval}] = new Array();
			{foreach from=$country.states item='state' name='states'}
				countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state|intval}', 'name' : '{$state.name|addslashes}'{rdelim});
			{/foreach}
		{/if}
		{if $country.need_identification_number}
			countriesNeedIDNumber.push({$country.id_country|intval});
		{/if}
		{if isset($country.need_zip_code)}
			countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
		{/if}
	{/foreach}
{/if}
//]]>
{literal}
$(document).ready(function() {
	$('#company').on('input',function(){
		vat_number();
	});
	$('#company_invoice').on('input',function(){
		vat_number_invoice();
	});
	function vat_number()
	{
		if (($('#company').length) && ($('#company').val() != '')) 
			$('#vat_number').show();
		else
			$('#vat_number').hide();
	}
	function vat_number_invoice()
	{
		if (($('#company_invoice').length) && ($('#company_invoice').val() != '')) 
			$('#vat_number_block_invoice').show();
		else
			$('#vat_number_block_invoice').hide();
	}
	vat_number();
	vat_number_invoice();
{/literal}
	$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{/if}]').prop('selected', true);
{literal}
});
{/literal}
</script>

<h1 class="title_block">{if !isset($email_create)}{l s='Authentication'}{else}{l s='Create an account'}{/if}</h1>
{if !isset($back) || $back != 'my-account'}{assign var='current_step' value='login'}{include file="$tpl_dir./order-steps.tpl"}{/if}
{include file="$tpl_dir./errors.tpl"}
{assign var='stateExist' value=false}
{assign var="postCodeExist" value=false}
{assign var="dniExist" value=false}
{if !isset($email_create)}
	<script type="text/javascript">
	{literal}
	$(document).ready(function(){
		// Retrocompatibility with 1.4
		if (typeof baseUri === "undefined" && typeof baseDir !== "undefined")
		baseUri = baseDir;
		$('#create-account_form').submit(function(){
			submitFunction();
			return false;
		});
		$('#invoice_address').click(function() {
			bindCheckbox();
		});
		bindCheckbox();
	});
	function submitFunction()
	{
		$('#create_account_error').html('').hide();
		//send the ajax request to the server
		$.ajax({
			type: 'POST',
			url: baseUri,
			async: true,
			cache: false,
			dataType : "json",
			data: {
				controller: 'authentication',
				SubmitCreate: 1,
				ajax: true,
				email_create: $('#email_create').val(),
				back: $('input[name=back]').val(),
				token: token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(error in jsonData.errors)
						//IE6 bug fix
						if(error != 'indexOf')
							errors += '<li>'+jsonData.errors[error]+'</li>';
					$('#create_account_error').html('<ol>'+errors+'</ol>').show();
				}
				else
				{
					// adding a div to display a transition
					$('#center_column').html('<div id="noSlide">'+$('#center_column').html()+'</div>');
					$('#noSlide').fadeOut('slow', function(){
						$('#noSlide').html(jsonData.page);
						// update the state (when this file is called from AJAX you still need to update the state)
						bindStateInputAndUpdate();
						$(this).fadeIn('slow', function(){
							document.location = '#account-creation';
						});
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	function bindCheckbox()
	{
		if ($('#invoice_address:checked').length > 0)
		{
			$('#opc_invoice_address').slideDown('slow');
			if ($('#company_invoice').val() == '')
				$('#vat_number_block_invoice').hide();
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
{/literal}
			$('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{/if}]').prop('selected', true);
			$('.id_state_invoice option[value={if isset($smarty.post.id_state_invoice)}{$smarty.post.id_state_invoice|intval}{/if}]').prop('selected', true);
{literal}
		}
		else
			$('#opc_invoice_address').slideUp('slow');
	}
	{/literal}
	</script>
	<!--{if isset($authentification_error)}
	<div class="error">
		{if {$authentification_error|@count} == 1}
			<p>{l s='There\'s at least one error'} :</p>
			{else}
			<p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>
		{/if}
		<ol>
			{foreach from=$authentification_error item=v}
				<li>{$v}</li>
			{/foreach}
		</ol>
	</div>
	{/if}-->
	<div class="authentication-page row-fluid">
		<div class="span6">
			<form action="{$link->getPageLink('authentication', true)|escape:'html'}" method="post" id="create-account_form" class="std block">
				<fieldset>
					<h3 class="title_block">{l s='Create an account'}</h3>
					<div class="form_content block_content clearfix">
						<p class="s_title_block">{l s='Please enter your email address to create an account.'}.</p>
						<div class="error" id="create_account_error" style="display:none"></div>
						<div class="control-group text">
							<label class="control-label" for="email_create">{l s='Email address'}</label>
							<div class="controls">
								<input class="input-xlarge" type="text" id="email_create" name="email_create" value="{if isset($smarty.post.email_create)}{$smarty.post.email_create|stripslashes}{/if}" class="account_input" />
							</div>
						</div>
						<div class="control-group submit">
							<div class="controls">
							{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back}" />{/if}
								<input type="submit" id="SubmitCreate" name="SubmitCreate" class="button_large" value="{l s='Create an account'}" />
								<input type="hidden" class="hidden" name="SubmitCreate" value="{l s='Create an account'}" />
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
		<div class="span6">
			<form action="{$link->getPageLink('authentication', true)|escape:'html'}" method="post" id="login_form" class="std block">
				<fieldset>
					<h3 class="title_block">{l s='Already registered?'}</h3>
					<div class="form_content block_content clearfix">
						<div class="control-group text">
							<label class="control-label" for="email">{l s='Email address'}</label>
							<div class="controls">
								<input class="input-xlarge" type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" class="account_input" />
							</div>
						</div>
						<div class="control-group text">
							<label class="control-label" for="passwd">{l s='Password'}</label>
							<div class="controls">
								<input class="input-xlarge" type="password" id="passwd" name="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|stripslashes}{/if}" class="account_input" />
							</div>
						</div>
						<p class="lost_password"><a href="{$link->getPageLink('password')|escape:'html'}" title="{l s='Recover your forgotten password'}" rel="nofollow">{l s='Forgot your password?'}</a></p>
						<div class="control-group submit">
							<div class="controls">
								{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
								<input class="button_large" type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="{l s='Authentication'}" />
							</div>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
	{if isset($inOrderProcess) && $inOrderProcess && $PS_GUEST_CHECKOUT_ENABLED}
	<form action="{$link->getPageLink('authentication', true, NULL, "back=$back")|escape:'html'}" method="post" id="new_account_form" class="form-horizontal std block clearfix">
		<fieldset>
			<h3 class="title_block">{l s='Instant checkout'}</h3>
			<div id="opc_account_form" style="display: block; ">
				<!-- Account -->
				<div class="required text control-group">
					<label class="control-label" for="guest_email">{l s='Email address'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" id="guest_email" name="guest_email" value="{if isset($smarty.post.guest_email)}{$smarty.post.guest_email}{/if}" />
					</div>
				</div>				
				
				<div class="radio required control-group">
					<label class="control-label">{l s='Title'}</label>
					<div class="controls">
						{foreach from=$genders key=k item=gender}
							<label for="id_gender{$gender->id}" class="top">
								<input class="input-xlarge" type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
							{$gender->name}</label>
						{/foreach}
					</div>
				</div>
				<div class="required text control-group">
					<label class="control-label" for="firstname">{l s='First name'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" id="firstname" name="firstname" onblur="$('#customer_firstname').val($(this).val());" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
						<input class="input-xlarge" type="hidden" class="text" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
					</div>
				</div>
				<div class="required text control-group">
					<label class="control-label" for="lastname">{l s='Last name'} <sup>*</sup></label>
					<div class="controls">	
						<input class="input-xlarge" type="text" class="text" id="lastname" name="lastname" onblur="$('#customer_lastname').val($(this).val());" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
						<input class="input-xlarge" type="hidden" class="text" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
					
					</div>
				</div>
				<div class="select control-group">
					<label class="control-label">{l s='Date of Birth'}</label>
					<div class="controls">
						<select id="days" class="input-mini" name="days">
							<option value="">-</option>
							{foreach from=$days item=day}
								<option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
							{/foreach}
						</select>
						{*
						  {l s='January'}
						  {l s='February'}
						  {l s='March'}
						  {l s='April'}
						  {l s='May'}
						  {l s='June'}
						  {l s='July'}
						  {l s='August'}
						  {l s='September'}
						  {l s='October'}
						  {l s='November'}
						  {l s='December'}
						*}
						<select id="months" class="input-medium" name="months">
							<option value="">-</option>
							{foreach from=$months key=k item=month}
								<option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
							{/foreach}
						</select>
						<select  id="years" class="input-mini" name="years">
							<option value="">-</option>
							{foreach from=$years item=year}
								<option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
							{/foreach}
						</select>
					</div>
				</div>
				{if isset($newsletter) && $newsletter}
					<div class="checkbox control-group">
					<div class="controls checkbox">
						<input class="input-xlarge" type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == '1'}checked="checked"{/if}>
						<label  for="newsletter">{l s='Sign up for our newsletter!'}</label>
					</div>
					</div>
					<div class="checkbox control-group">
						<div class="controls checkbox">
							<input class="input-xlarge" type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == '1'}checked="checked"{/if}>
							<label for="optin">{l s='Receive special offers from our partners!'}</label>
						</div>
					</div>
					
				{/if}
				<h3 class="title_block">{l s='Delivery address'}</h3>
				{foreach from=$dlv_all_fields item=field_name}
				{if $field_name eq "company"  && $b2b_enable}
				<div class="control-group text">
					<label class="control-label" for="company">{l s='Company'}</label>
					<div class="controls">
							<input type="text" class="text input-xlarge" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
					</div>
				</div>
						{elseif $field_name eq "vat_number"}
						<div id="vat_number" style="display:none;">
							<p class="text">
								<label for="vat_number">{l s='VAT number'}</label>
								<input class="input-xlarge" type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
							</p>
						</div>
				 	{elseif $field_name eq "dni"}
					{assign var='dniExist' value=true}
						<div class="text control-group">
							<label class="control-label" for="dni">{l s='Identification number'}</label>
							<div class="controls"> <input type="text" class="text input-xlarge" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" /> </div>
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
						{elseif $field_name eq "address1"}
				<div class="control-group required text">
					<label class="control-label" for="address1">{l s='Address'} <sup>*</sup></label>
					<div class="controls">
							<input class="input-xlarge" type="text" class="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
					</div>
				</div>
						{elseif $field_name eq "postcode"}
						{assign var='postCodeExist' value=true}
				<div class="control-group required postcode text">
					<label class="control-label" for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
					<div class="controls">
							<input class="input-xlarge" type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onblur="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
				</div>
						{elseif $field_name eq "city"}
				<div class="control-group required text">
					<label class="control-label" for="city">{l s='City'} <sup>*</sup></label>
					<div class="controls">
							<input class="input-xlarge" type="text" class="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
					</div>
				</div>
						<!--
							   if customer hasn't update his layout address, country has to be verified
							   but it's deprecated
						   -->
						{elseif $field_name eq "Country:name" || $field_name eq "country"}
				<div class="control-group required select">
					<label class="control-label" for="id_country">{l s='Country'} <sup>*</sup></label>
					<div class="controls">
						<select class="input-xlarge" name="id_country" id="id_country">
							<option value="">-</option>
							{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND  $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				{elseif $field_name eq "State:name"}
				{assign var='stateExist' value=true}

					<div class="control-group required id_state select">
						<label class="control-label" for="id_state">{l s='State'} <sup>*</sup></label>
						<div class="controls">
							<select class="input-xlarge" name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</div>
					</div>
				
				
					{/if}
				{/foreach}
				{if $postCodeExist eq false}
					<div class="required postcode text hidden control-group">
						<label class="control-label" for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
						<div class="controls"><input type="text" class="text input-xlarge" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onblur="$('#postcode').val($('#postcode').val().toUpperCase());" /> </div>
					</div>
				{/if}
				{if $stateExist eq false}
					<div class="required control-group id_state select hidden">
						<label class="control-label" for="id_state">{l s='State'} <sup>*</sup></label>
						<div class="controls">
							<select class="input-xlarge" name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</div>
				</div>
				{/if}
				{if $dniExist eq false}
				<div class="required text dni control-group">
					<label for="dni" class="control-label" >{l s='Identification number'} <sup>*</sup></label>
					<div class="controls">	
						<input type="text" class="text input-xlarge" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					
					</div>
				</div>
				{/if}	
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}text control-group">
					<label for="phone_mobile" class="control-label" >{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
					<div class="controls" ><input type="text" class="text input-xlarge" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" /> </div>
				</div>

				<input type="hidden" name="alias" id="alias" value="{l s='My address'}" />
				<input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
				<div class="checkbox control-group">
					<div class="controls" >
						<label for="invoice_address"><input class="input-xlarge" type="checkbox" name="invoice_address" id="invoice_address"{if isset($smarty.post.invoice_address) && $smarty.post.invoice_address} checked="checked"{/if} autocomplete="off"/> 
						<b>{l s='Please use another address for invoice'}</b></label>
					</div>
				</div>
				<div id="opc_invoice_address" class="hidden">
					{assign var=stateExist value=false}
					{assign var=postCodeExist value=false}
					{assign var=dniExist value=false}
					<h3 class="title_block">{l s='Invoice address'}</h3>
					{foreach from=$inv_all_fields item=field_name}
					{if $field_name eq "company" &&  $b2b_enable}
					<div class="text control-group">
						<label class="control-label" for="company_invoice">{l s='Company'}</label>
						<div class="controls" ><input type="text" class="text input-xlarge" id="company_invoice" name="company_invoice" value="{if isset($smarty.post.company_invoice)}{$smarty.post.company_invoice}{/if}" /> </div>
					</div>
					{elseif $field_name eq "vat_number"}
					<div id="vat_number_block_invoice" class="hidden">
						<div  class="text control-group">
							<label class="cotrol-label" for="vat_number_invoice">{l s='VAT number'}</label>
							<div class="controls" ><input type="text" class="text input-xlarge" id="vat_number_invoice" name="vat_number_invoice" value="{if isset($smarty.post.vat_number_invoice)}{$smarty.post.vat_number_invoice}{/if}" /> </div>
						</div>
					</div>
					{elseif $field_name eq "dni"}
					{assign var=dniExist value=true}
					<div class="text control-group">
						<label for="dni_invoice" class="control-label" >{l s='Identification number'}</label>
						<div class="controls" ><input type="text" class="text input-xlarge" name="dni_invoice" id="dni_invoice" value="{if isset($smarty.post.dni_invoice)}{$smarty.post.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span> </div>
					</div>
					{elseif $field_name eq "firstname"}
					<div class="required text control-group">
						<label class="control-label" for="firstname_invoice">{l s='First name'} <sup>*</sup></label>
						<div class="controls" ><input type="text" class="text input-xlarge" id="firstname_invoice" name="firstname_invoice" value="{if isset($smarty.post.firstname_invoice)}{$smarty.post.firstname_invoice}{/if}" /> </div>
					</div>
					{elseif $field_name eq "lastname"}
					<div class="required text control-group">
						<label for="lastname_invoice" class="control-label" >{l s='Last name'} <sup>*</sup></label>
						<div class="controls" ><input type="text" class="text input-xlarge" id="lastname_invoice" name="lastname_invoice" value="{if isset($smarty.post.firstname_invoice)}{$smarty.post.firstname_invoice}{/if}" /> </div>
					</div>
					{elseif $field_name eq "address1"}
					<div class="required text control-group">
						<label  class="control-label" for="address1_invoice">{l s='Address'} <sup>*</sup></label>
						<div class="controls" ><input type="text" class="text input-xlarge" name="address1_invoice" id="address1_invoice" value="{if isset($smarty.post.address1_invoice)}{$smarty.post.address1_invoice}{/if}" /> </div>
					</div>
					{elseif $field_name eq "postcode"}
					{$postCodeExist = true}
					<div class="required postcode_invoice text control-group">
						<label class="control-label" for="postcode_invoice">{l s='Zip / Postal Code'} <sup>*</sup></label>
					      <div class="controls" ><input type="text" class="text input-xlarge" name="postcode_invoice" id="postcode_invoice" value="{if isset($smarty.post.postcode_invoice)}{$smarty.post.postcode_invoice}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" /> </div>
					</div>
					{elseif $field_name eq "city"}
					<div  class="required text control-group">
						<label class="control-label" for="city_invoice">{l s='City'} <sup>*</sup></label>
						<div class="controls" > 
							<input type="text" class="text input-xlarge" name="city_invoice" id="city_invoice" value="{if isset($smarty.post.city_invoice)}{$smarty.post.city_invoice}{/if}" /> 
						</div>
					</div>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
					<div class="required select control-group">
						<label class="control-label" for="id_country_invoice">{l s='Country'} <sup>*</sup></label>
						<div class="controls">
							<select class="input-xlarge" name="id_country_invoice" id="id_country_invoice">
								<option value="">-</option>
								{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($smarty.post.id_country_invoice) && $smarty.post.id_country_invoice == $v.id_country) OR (!isset($smarty.post.id_country_invoice) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
					{$stateExist = true}
					<div class="required id_state_invoice select control-group" style="display:none;">
						<label class="control-label" for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<div class="controls" >
							<select class="input-xlarge" name="id_state_invoice" id="id_state_invoice">
								<option value="">-</option>
							</select>
						</div>
					</div>
					{/if}
					{/foreach}
					{if !$postCodeExist}
					<div class="required postcode_invoice text hidden control-group">
						<label class="control-label" for="postcode_invoice">{l s='Zip / Postal Code'} <sup>*</sup></label>
						<input type="text" class="text input-xlarge" name="postcode_invoice" id="postcode_invoice" value="{if isset($smarty.post.postcode_invoice)}{$smarty.post.postcode_invoice}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
					{/if}					
					{if !$stateExist}
					<div class="required id_state_invoice select hidden control-group">
						<label class="control-label" for="id_state_invoice">{l s='State'} <sup>*</sup></label>
						<div class="controls" > <select class="input-xlarge" name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
						</div>
					</div>
					{/if}
					{if !$dniExist}
					<div class="required text dni_invoice control-group">
						<label class="control-label" for="dni_invoice">{l s='Identification number'} <sup>*</sup></label>
						<div class="controls" ><input type="text" class="text input-xlarge" name="dni_invoice" id="dni_invoice" value="{if isset($smarty.post.dni_invoice)}{$smarty.post.dni_invoice}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span> </div>
					</div>
					{/if}
					<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}text control-group">
						<label class="control-label" for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
						<div class="controls" > <input class="input-xlarge" type="text" class="text" name="phone_mobile_invoice" id="phone_mobile_invoice" value="{if isset($smarty.post.phone_mobile_invoice)}{$smarty.post.phone_mobile_invoice}{/if}" /> </div>
					</div>
					<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" /> 
				</div>
			</div>
		</fieldset>
		{$HOOK_CREATE_ACCOUNT_FORM}
		<div class="cart_navigation required block_content submit">
			<span><sup>*</sup>{l s='Required field'}</span>
			<input type="hidden" name="display_guest_checkout" value="1" />
			<input type="submit"  class="exclusive" name="submitGuestAccount" id="submitGuestAccount" value="{l s='Continue'}" />
		</div>
	</form>
	{/if}
{else}
	<!--{if isset($account_error)}
	<div class="error">
		{if {$account_error|@count} == 1}
			<p>{l s='There\'s at least one error'} :</p>
			{else}
			<p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>
		{/if}
		<ol>
			{foreach from=$account_error item=v}
				<li>{$v}</li>
			{/foreach}
		</ol>
	</div>
	{/if}-->

<form action="{$link->getPageLink('authentication', true)|escape:'html'}" method="post" id="account-creation_form" class=" form-horizontal std">
	{$HOOK_CREATE_ACCOUNT_TOP}
	<fieldset class="account_creation block">
		<h3>{l s='Your personal information'}</h3>
        <div class="radio required control-group hide_rpc">
	     <label class="control-label">{l s='Title'}</label>
            <div class="controls">
			{foreach from=$genders key=k item=gender}
				<label for="id_gender{$gender->id}" class="top">
				<input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
				{$gender->name} </label>
			{/foreach}
            </div>
		</div>
		
		<div class="control-group required text">
			<label for="customer_firstname" class="control-label">{l s='First name'} <sup>*</sup></label>
			<div class="controls">
                <input class="input-xlarge" onkeyup="$('#firstname').val(this.value);" type="text" class="text input-xlarge" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
            </div>
		</div>
		{*<div class="required text control-group hide_rpc" >*}
        <div class="text control-group hide_rpc" >
			<label for="customer_lastname" class="control-label">{l s='Last name'} <sup>*</sup></label>
            <div class="controls">
			    <input class="input-xlarge" onkeyup="$('#lastname').val(this.value);" type="text" class="text" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}." />
            </div>
		</div>
        <div class="required text control-group">
			<label for="email" class="control-label">{l s='Email'} <sup>*</sup></label>
            <div class="controls">
			    <input class="input-xlarge" type="text" class="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
            </div>
		</div>
        <div class="required password control-group">
			<label class="control-label" for="passwd">{l s='Password'} <sup>*</sup></label>
            <div class="controls">
                <input class="input-xlarge" type="password" class="text" name="passwd" id="passwd" />
                <span class="form_info">{l s='(Five characters minimum)'}</span>
            </div>
		</div>
        <div class="control-group select hide_rpc" >
			<label class="control-label">{l s='Date of Birth'}</label>
            <div class="controls">
			<select id="days" name="days" class="input-mini">
				<option value="">-</option>
				{foreach from=$days item=day}
					<option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
			{*
				{l s='January'}
				{l s='February'}
				{l s='March'}
				{l s='April'}
				{l s='May'}
				{l s='June'}
				{l s='July'}
				{l s='August'}
				{l s='September'}
				{l s='October'}
				{l s='November'}
				{l s='December'}
			*}
			<select id="months" name="months" class="input-medium">
				<option value="">-</option>
				{foreach from=$months key=k item=month}
					<option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
				{/foreach}
			</select>
			<select id="years" name="years" class="input-mini">
				<option value="">-</option>
				{foreach from=$years item=year}
					<option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
				{/foreach}
			</select>
            </div>
		</div>
		{if $newsletter}
        <div class="control-group">
            <div class="controls checkbox">
                <input class="input-xlarge" type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} autocomplete="off" />
		<label for="newsletter" >{l s='Sign up for our newsletter!'}</label>
            </div>
        </div>
        <div class="control-group">
            <div class="controls checkbox">
	    	<input class="input-xlarge" type="checkbox"name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} autocomplete="off" />
	    	<label for="optin" >{l s='Receive special offers from our partners!'}</label>
            </div>
		</div>
		{/if}
	</fieldset>
	{if $b2b_enable}
	<fieldset class="account_creation  block">
		<h3>{l s='Your company information'}</h3>
		 <div class="control-group text">
			<label class="control-label" for="">{l s='Company'}</label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
		 	</div>
		</div>
		<div class="control-group text">
			<label class="control-label" for="siret">{l s='SIRET'}</label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
			</div>
		</div>
		<div class="control-group text">
			<label class="control-label" for="ape">{l s='APE'}</label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
			</div>
		</div>
		<div class="control-group text">
			<label class="control-label" for="website">{l s='Website'}</label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
			</div>
		</div>
	</fieldset>
	{/if}
	{if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}
	<fieldset class="account_creation block">
		<h3>{l s='Your address'}</h3>
		{foreach from=$dlv_all_fields item=field_name}
			{if $field_name eq "company"}
				{if !$b2b_enable}
				<div class="control-group text">
					<label class="control-label" for="company">{l s='Company'}</label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
					</div>
				</div>
				{/if}
			{elseif $field_name eq "vat_number"}
				<div class="control-group text" id="vat_number" style="display:none;">
					
						<label class="control-label" for="vat_number">{l s='VAT number'}</label>
						<div class="controls">
							<input class="input-xlarge" type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
						</div>
				</div>
			{elseif $field_name eq "firstname"}
				<div class="control-group required text">
					<label class="control-label" for="firstname">{l s='First name'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
					</div>
				</div>
			{elseif $field_name eq "lastname"}
				<div class="control-group required text">
					<label class="control-label" for="lastname">{l s='Last name'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
					</div>
				</div>
			{elseif $field_name eq "address1"}
				<div class="control-group required text">
					<label class="control-label" for="address1">{l s='Address'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
						<span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
					</div>
				</div>
			{elseif $field_name eq "address2"}
				<div class="control-group text">
					<label class="control-label" for="address2">{l s='Address (Line 2)'}</label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
						<span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
					</div>
				</div>
			{elseif $field_name eq "postcode"}
				{assign var='postCodeExist' value=true}
				<div class="control-group required postcode text">
					<label class="control-label" for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					</div>
				</div>
			{elseif $field_name eq "city"}
				<div class="control-group required text">
					<label class="control-label" for="city">{l s='City'} <sup>*</sup></label>
					<div class="controls">
						<input class="input-xlarge" type="text" class="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
					</div>
				</div>
				<!--
					if customer hasn't update his layout address, country has to be verified
					but it's deprecated
				-->
			{elseif $field_name eq "Country:name" || $field_name eq "country"}
				<div class="control-group required select">
					<label class="control-label" for="id_country">{l s='Country'} <sup>*</sup></label>
					<div class="controls">
						<select class="input-xlarge" name="id_country" id="id_country">
							<option value="">-</option>
							{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{elseif $field_name eq "State:name" || $field_name eq 'state'}
				{assign var='stateExist' value=true}
				<div class="control-group required select id_state">
					<label class="control-label" for="id_state">{l s='State'} <sup>*</sup></label>
					<div class="controls">
						<select class="input-xlarge" name="id_state" id="id_state">
							<option value="">-</option>
						</select>
					</div>
				</div>
			{/if}
		{/foreach}
 		{if $postCodeExist eq false}
			<div class="required postcode text hidden control-group">
			<div class="controls">	
				<label class="control-label" for="postcode">{l s='Zip / Postal Code'} <sup>*</sup></label>
			</div>
				<input class="input-xlarge" type="text" class="text" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
			</div>
		{/if}	
		
		{if $stateExist eq false}
			<div class="required id_state select hidden control-group">
				<label class="control-label" for="id_state">{l s='State'} <sup>*</sup></label>
				<div class="controls">
					<select name="id_state" id="id_state" class="input-xlarge">
						<option value="">-</option>
					</select>
				</div>
			</div>
		{/if}
		<div class="control-group textarea">
			<label class="control-label" for="other">{l s='Additional information'}</label>
			<div class="controls">
				<textarea class="input-xlarge" name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
			</div>
		</div>
		<div class="control-group">
			{if isset($one_phone_at_least) && $one_phone_at_least}
				<div class="controls">
					<p class="inline-infos required">{l s='You must register at least one phone number.'} </p>
				</div>
			{/if}
		</div>
		<div class="control-group text">
			<label class="control-label" for="phone">{l s='Home phone'}</label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
			</div>
		</div>
		<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if} text control-group">
			<label for="phone_mobile" class="control-label">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
			<div class="controls"><input class="input-xlarge" type="text" class="text" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" /></div>
		</div>
		<div class="required text control-group" id="address_alias">
			<label class="control-label" for="alias">{l s='Assign an address alias for future reference.'} <sup>*</sup></label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
			</div>
		</div>
	</fieldset>
	<fieldset class="account_creation dni block">
		<h3>{l s='Tax identification'}</h3>
		<div class="required text control-group">
			<label class="control-label" for="dni">{l s='Identification number'} <sup>*</sup></label>
			<div class="controls">
				<input class="input-xlarge" type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
				<span class="form_info">{l s='DNI / NIF / NIE'}</span>
			</div>
		</div>
	</fieldset>
	{/if}
	{$HOOK_CREATE_ACCOUNT_FORM}
	<p class="cart_navigation required submit">
		<input type="hidden" name="email_create" value="1" />
		<input type="hidden" name="is_new_customer" value="1" />
		{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
		<input type="submit" name="submitAccount" id="submitAccount" value="{l s='Register'}" class="exclusive" />
		<span><sup>*</sup>{l s='Required field'}</span>
	</p>

</form>
{/if}
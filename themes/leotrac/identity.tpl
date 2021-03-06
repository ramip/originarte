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

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Your personal information'}{/capture}

<h3 class="title_block">{l s='Your personal information'}</h3>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation}
	<p class="success">
		{l s='Your personal information has been successfully updated.'}
		{if isset($pwd_changed)}<br />{l s='Your password has been sent to your e-mail:'} {$email}{/if}
	</p>
{else}
	<p>{l s='Please be sure to update your personal information if it has changed.'}</p>
	<p class="required"><sup>*</sup>{l s='Required field'}</p>
	<form action="{$link->getPageLink('identity', true)|escape:'html'}" method="post" class="std form-horizontal">
		<fieldset>
			<div class="control-group radio hide_rpc">
				<label class="control-label">{l s='Title'}</label>
                <div class="controls">
				{foreach from=$genders key=k item=gender}
					<label for="id_gender{$gender->id}" class="radio top"><input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />{$gender->name}</label>
				{/foreach}
                </div>
            </div>
            <div class="control-group required text">
				<label for="firstname" class="control-label">{l s='First name'} <sup>*</sup></label>
                <div class="controls">
				    <input class="input-xlarge" type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" />
                </div>
			</div>
            <div class="control-group required text hide_rpc">
				<label for="lastname" class="control-label">{l s='Last name'} <sup>*</sup></label>
                <div class="controls">
				    <input class="input-xlarge" type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" />
                </div>
			</div>
			<div class="control-group required text">
				<label class="control-label" for="email">{l s='E-mail'} <sup>*</sup></label>
                <div class="controls">
				    <input class="input-xlarge" type="text" name="email" id="email" value="{$smarty.post.email}" />
                </div>
            </div>
            <div class="control-group required text">
				<label for="old_passwd" class="control-label">{l s='Current Password'} <sup>*</sup></label>
                <div class="controls">
				    <input class="input-xlarge" type="password" name="old_passwd" id="old_passwd" />
                </div>
			</div>
			<div class="control-group password">
				<label class="control-label" for="passwd">{l s='New Password'}</label>
				<div class="controls">
                    <input class="input-xlarge" type="password" name="passwd" id="passwd"  />
                </div>
            </div>
            <div class="control-group password">
				<label class="control-label" for="confirmation">{l s='Confirmation'}</label>
                <div class="controls">
				    <input class="input-xlarge" type="password" name="confirmation" id="confirmation" />
                </div>
			</div>
			<div class="control-group select hide_rpc">
				<label class="control-label">{l s='Date of Birth'}</label>
				<div class="controls select">
                <select name="days" id="days" class="input-mini">
					<option value="">-</option>
					{foreach from=$days item=v}
						<option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
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
					{foreach from=$months key=k item=v}
						<option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
					{/foreach}
				</select>
				<select id="years" name="years" class="input-mini">
					<option value="">-</option>
					{foreach from=$years item=v}
						<option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
					{/foreach}
				</select>
                </div>
            </div>
			{if $newsletter}
			<div class="control-group">
               
                <div class="controls checkbox">
				    <input type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if} />
					 <label for="newsletter" >{l s='Sign up for our newsletter'}</label>
                </div>
            </div>
			<div class="control-group">
				
                <div class="controls checkbox">
				    <input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if} />
					<label for="optin" >{l s='Receive special offers from our partners'}</label>
					
                </div>
            </div>
			{/if}
			<div class="control-group">
                <div class="controls submit">
				    <input type="submit" class="button" name="submitIdentity" value="{l s='Save'}" />
                </div>
            </div>
            <div class="control-group" id="security_informations">
                <div class="controls">
				{l s='[Insert customer data privacy clause here, if applicable]'}
			    </div>
            </div>
		</fieldset>
	</form>
{/if}

<ul class="footer_links">
	<li><a href="{$link->getPageLink('my-account', true)}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a><a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='Back to your account'}</a></li>
	<li class="f_right"><a href="{$base_dir}"><img src="{$img_dir}icon/home.gif" alt="" class="icon" /></a><a href="{$base_dir}">{l s='Home'}</a></li>
</ul>

{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- GENERAL.TPL -->
<form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">
    <div class="panel">
        <h3><i class="icon-cog"></i> {l s='General Configuration' mod='beezup'}</h3>

        <!-- SITE ADDRESS -->
        <div class="form-group">
            <label for="BEEZUP_SITE_ADDRESS" class="control-label col-sm-3">{l s='Site address:' mod='beezup'}</label>
            <div class="col-sm-9 col-xs-12">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <div class="row">
                            <input type="text" name="BEEZUP_SITE_ADDRESS"
                                   value="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EXPORT ALL SHOPS  -->
        <div class="form-group">
            <label for="BEEZUP_ALL_SHOPS" class="control-label col-sm-3">{l s='Export all shops:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_ALL_SHOPS" id="BEEZUP_ALL_SHOPS_on" value="1"
                                   {if $beezup_conf.BEEZUP_ALL_SHOPS}checked="checked"{/if}/>
							<label for="BEEZUP_ALL_SHOPS_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_ALL_SHOPS" id="BEEZUP_ALL_SHOPS_off" value="0"
                                   {if !$beezup_conf.BEEZUP_ALL_SHOPS}checked="checked"{/if}/>
							<label for="BEEZUP_ALL_SHOPS_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- COUNTRY -->
        <div class="form-group">
            <label for="BEEZUP_COUNTRY" class="control-label col-sm-3">{l s='Country:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <select name="BEEZUP_COUNTRY" class="col-sm-2" style="float: none;">
                        {foreach from=$countries item=country}
                            <option value="{$country.id_country|intval}"
                                    {if $country.id_country == $beezup_conf.BEEZUP_COUNTRY}selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <p class="help-block"> {l s='used for tax calculation' mod='beezup'}</p>
                </div>
            </div>
        </div>

        <!-- CACHE  -->
        <div class="form-group">
            <label for="BEEZUP_USE_CACHE" class="control-label col-sm-3">{l s='Use cache:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_USE_CACHE" id="BEEZUP_USE_CACHE_on" value="1"
                                   {if $beezup_conf.BEEZUP_USE_CACHE}checked="checked"{/if}/>
							<label for="BEEZUP_USE_CACHE_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_USE_CACHE" id="BEEZUP_USE_CACHE_off" value="0"
                                   {if !$beezup_conf.BEEZUP_USE_CACHE}checked="checked"{/if}/>
							<label for="BEEZUP_USE_CACHE_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- CACHE VALIDITY -->
        <div class="form-group">
            <label for="BEEZUP_CACHE_VALIDITY_DAYS"
                   class="control-label col-sm-3">{l s='Cache validity:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-lg-1 col-md-2 col-sm-2">
                        <select name="BEEZUP_CACHE_VALIDITY_DAYS" class="BEEZUP_CACHE_VALIDITY">
                            {section name=days loop=7}
                                <option value="{$smarty.section.days.index|intval}"
                                        {if $beezup_conf.BEEZUP_CACHE_VALIDITY_DAYS == $smarty.section.days.index}selected="selected"{/if}>{$smarty.section.days.index|intval} {if $smarty.section.days.index<2}{l s='day' mod='beezup'}{else}{l s='days' mod='beezup'}{/if}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2">
                        <select name="BEEZUP_CACHE_VALIDITY_HOURS" class="BEEZUP_CACHE_VALIDITY">
                            {section name=hours loop=24}
                                <option value="{$smarty.section.hours.index|intval}"
                                        {if $beezup_conf.BEEZUP_CACHE_VALIDITY_HOURS == $smarty.section.hours.index}selected="selected"{/if}>{if $smarty.section.hours.index<10}0{/if}{$smarty.section.hours.index|intval} {if $smarty.section.hours.index<2}{l s='hour' mod='beezup'}{else}{l s='hours' mod='beezup'}{/if}</option>
                            {/section}
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2">
                        <select name="BEEZUP_CACHE_VALIDITY_MINUTES" class="BEEZUP_CACHE_VALIDITY">
                            {section name=minutes loop=60}
                                <option value="{$smarty.section.minutes.index|intval}"
                                        {if $beezup_conf.BEEZUP_CACHE_VALIDITY_MINUTES == $smarty.section.minutes.index}selected="selected"{/if}>{if $smarty.section.minutes.index<10}0{/if}{$smarty.section.minutes.index|intval} {if $smarty.section.minutes.index<2}{l s='minute' mod='beezup'}{else}{l s='minutes' mod='beezup'}{/if}</option>
                            {/section}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear">&nbsp;</div>

        <!-- CRON  -->
        <div class="form-group">
            <label for="BEEZUP_USE_CRON" class="control-label col-sm-3">{l s='Use CRON:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_USE_CRON" id="BEEZUP_USE_CRON_on" value="1"
                                   {if $beezup_conf.BEEZUP_USE_CRON}checked="checked"{/if}/>
							<label for="BEEZUP_USE_CRON_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_USE_CRON" id="BEEZUP_USE_CRON_off" value="0"
                                   {if !$beezup_conf.BEEZUP_USE_CRON}checked="checked"{/if}/>
							<label for="BEEZUP_USE_CRON_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Cron Time -->
        <div class="form-group">
            <div>

                <label for="BEEZUP_CRON_HOURS" class="control-label col-sm-3">{l s='CRON time:' mod='beezup'}</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-1">
                            <select name="BEEZUP_CRON_HOURS" class="BEEZUP_CRON_TIME">
                                {section name=hours loop=24}
                                    <option value="{$smarty.section.hours.index|intval}"
                                            {if $beezup_conf.BEEZUP_CRON_HOURS == $smarty.section.hours.index}selected="selected"{/if}>{if $smarty.section.hours.index<10}0{/if}{$smarty.section.hours.index|intval}{l s='h' mod='beezup'}</option>
                                {/section}
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <select name="BEEZUP_CRON_MINUTES" class="BEEZUP_CRON_TIME">
                                {section name=minutes loop=60}
                                    <option value="{$smarty.section.minutes.index|intval}"
                                            {if $beezup_conf.BEEZUP_CRON_MINUTES == $smarty.section.minutes.index}selected="selected"{/if}>{if $smarty.section.minutes.index<10}0{/if}{$smarty.section.minutes.index|intval}{l s='min' mod='beezup'}</option>
                                {/section}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {if $beezup_conf.BEEZUP_USE_CRON}
                <div class="col-sm-12">

                    <div class="col-sm-3"></div>
                    <div class="col-sm-8">
                        <br/>
                        <textarea>
# Prestashop Beezup Export Module
                            {$beezup_conf.BEEZUP_CRON_MINUTES|intval} {$beezup_conf.BEEZUP_CRON_HOURS|intval} * * * php -f {$cron_path|escape:'htmlall':'UTF-8'}
                            >> {$cron_log|escape:'htmlall':'UTF-8'}
</textarea>
                    </div>
                </div>
            {/if}
        </div>


        <div class="form-group">
            <label for="BEEZUP_NEW_PRODUCT_ID_SYSTEM"
                   class="control-label col-sm-3">{l s=' New ProductID logic:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_NEW_PRODUCT_ID_SYSTEM" id="BEEZUP_NEW_PRODUCT_ID_SYSTEM_on"
                                   value="1" {if $beezup_conf.BEEZUP_NEW_PRODUCT_ID_SYSTEM}checked="checked"{/if}/>
							<label for="BEEZUP_NEW_PRODUCT_ID_SYSTEM_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_NEW_PRODUCT_ID_SYSTEM"
                                   id="BEEZUP_NEW_PRODUCT_ID_SYSTEM_off" value="0"
                                   {if !$beezup_conf.BEEZUP_NEW_PRODUCT_ID_SYSTEM}checked="checked"{/if}/>
							<label for="BEEZUP_NEW_PRODUCT_ID_SYSTEM_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                    </div>
                    <div class="help-block">{l s='WARNING, option with high impact, disable it only if it was already disabled in your previous setup. If not : enable it. If you doubt, contact BeezUP.' mod='beezup'}</div>
                </div>
            </div>
        </div>


        <!-- CATEGORY -->
        <div class="form-group">
            <label for="BEEZUP_CATEGORY_DEEPEST"
                   class="control-label col-sm-3">{l s='Logic of product categories:' mod='beezup'}</label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="input-group col-sm-3">
                        <input type="radio" name="BEEZUP_CATEGORY_DEEPEST" id="BEEZUP_CATEGORY_DEEPEST_off" value="0"
                               {if !$beezup_conf.BEEZUP_CATEGORY_DEEPEST}checked="checked"{/if}/>
                        &nbsp; <label for="BEEZUP_CATEGORY_DEEPEST_off">{l s='Default category' mod='beezup'}</label>
                        <br/>
                        <input type="radio" name="BEEZUP_CATEGORY_DEEPEST" id="BEEZUP_CATEGORY_DEEPEST_on" value="1"
                               {if $beezup_conf.BEEZUP_CATEGORY_DEEPEST}checked="checked"{/if}/>
                        &nbsp; <label for="BEEZUP_CATEGORY_DEEPEST_on">{l s='Deepest category' mod='beezup'}</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAVE -->
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submitGeneralConfiguration"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
            </button>
        </div>
    </div>
</form>
<!-- /GENERAL.TPL -->

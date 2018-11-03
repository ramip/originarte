{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}">
    <fieldset id="beezup-general-configuration">
        <legend><img src="../img/admin/cog.gif" alt=""/> {l s='General Configuration' mod='beezup'}</legend>

        <!-- Site Address -->
        <label>{l s='Site address:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="text" name="BEEZUP_SITE_ADDRESS" size="80"
                   value="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div class="clear"></div>

        <!-- Export all shops -->
        <label>{l s='Export all shops:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_ALL_SHOPS" value="1"
                   {if $beezup_conf.BEEZUP_ALL_SHOPS}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                  alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_ALL_SHOPS" value="0"
                   {if !$beezup_conf.BEEZUP_ALL_SHOPS}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                   alt="{l s='No' mod='beezup'}"/>
        </div>
        <div class="clear"></div>

        <!-- PS1.4 / Country -->
        <label>{l s='Country:' mod='beezup'}</label>
        <div class="margin-form">
            <select name="BEEZUP_COUNTRY">
                {foreach from=$countries item=country}
                    <option value="{$country.id_country|intval}"
                            {if $country.id_country == $beezup_conf.BEEZUP_COUNTRY}selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <p>{l s='used for tax calculation' mod='beezup'}</p>
        </div>

        <!-- Use Cache -->
        <label>{l s='Use cache:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_USE_CACHE" value="1"
                   {if $beezup_conf.BEEZUP_USE_CACHE}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                  alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_USE_CACHE" value="0"
                   {if !$beezup_conf.BEEZUP_USE_CACHE}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                   alt="{l s='No' mod='beezup'}"/>
        </div>
        <div class="clear"></div>

        <!-- Cache Validity -->
        <label>{l s='Cache validity:' mod='beezup'}</label>
        <div class="margin-form">
            <select name="BEEZUP_CACHE_VALIDITY_DAYS" class="BEEZUP_CACHE_VALIDITY">
                {section name=days loop=7}
                    <option value="{$smarty.section.days.index|intval}"
                            {if $beezup_conf.BEEZUP_CACHE_VALIDITY_DAYS == $smarty.section.days.index}selected="selected"{/if}>{$smarty.section.days.index|intval} {if $smarty.section.days.index<2}{l s='day' mod='beezup'}{else}{l s='days' mod='beezup'}{/if}</option>
                {/section}
            </select>
            <select name="BEEZUP_CACHE_VALIDITY_HOURS" class="BEEZUP_CACHE_VALIDITY">
                {section name=hours loop=24}
                    <option value="{$smarty.section.hours.index|intval}"
                            {if $beezup_conf.BEEZUP_CACHE_VALIDITY_HOURS == $smarty.section.hours.index}selected="selected"{/if}>{if $smarty.section.hours.index<10}0{/if}{$smarty.section.hours.index|intval} {if $smarty.section.hours.index<2}{l s='hour' mod='beezup'}{else}{l s='hours' mod='beezup'}{/if}</option>
                {/section}
            </select>
            <select name="BEEZUP_CACHE_VALIDITY_MINUTES" class="BEEZUP_CACHE_VALIDITY">
                {section name=minutes loop=60}
                    <option value="{$smarty.section.minutes.index|intval}"
                            {if $beezup_conf.BEEZUP_CACHE_VALIDITY_MINUTES == $smarty.section.minutes.index}selected="selected"{/if}>{if $smarty.section.minutes.index<10}0{/if}{$smarty.section.minutes.index|intval} {if $smarty.section.minutes.index<2}{l s='minute' mod='beezup'}{else}{l s='minutes' mod='beezup'}{/if}</option>
                {/section}
            </select>
        </div>
        <div class="clear">&nbsp;</div>

        <!-- Use CRON -->
        <label>{l s='Use CRON:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_USE_CRON" value="1"
                   {if $beezup_conf.BEEZUP_USE_CRON}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                 alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_USE_CRON" value="0"
                   {if !$beezup_conf.BEEZUP_USE_CRON}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                  alt="{l s='No' mod='beezup'}"/>
        </div>
        <div class="clear"></div>

        <!-- Cron Time -->
        <label>{l s='CRON time:' mod='beezup'}</label>
        <div class="margin-form">
            <select name="BEEZUP_CRON_HOURS" class="BEEZUP_CRON_TIME">
                {section name=hours loop=24}
                    <option value="{$smarty.section.hours.index|intval}"
                            {if $beezup_conf.BEEZUP_CRON_HOURS == $smarty.section.hours.index}selected="selected"{/if}>{if $smarty.section.hours.index<10}0{/if}{$smarty.section.hours.index|intval}{l s='h' mod='beezup'}</option>
                {/section}
            </select>
            <select name="BEEZUP_CRON_MINUTES" class="BEEZUP_CRON_TIME">
                {section name=minutes loop=60}
                    <option value="{$smarty.section.minutes.index|intval}"
                            {if $beezup_conf.BEEZUP_CRON_MINUTES == $smarty.section.minutes.index}selected="selected"{/if}>{if $smarty.section.minutes.index<10}0{/if}{$smarty.section.minutes.index|intval}{l s='min' mod='beezup'}</option>
                {/section}
            </select>
            <br/>
            {if $beezup_conf.BEEZUP_USE_CRON}
                <p class="BEEZUP_CRON_COMMAND">
                    # Prestashop Beezup Export Module<br/>
                    {$beezup_conf.BEEZUP_CRON_MINUTES|intval} {$beezup_conf.BEEZUP_CRON_HOURS|intval} * * * php
                    -f {$cron_path|escape:'htmlall':'UTF-8'} >> {$cron_log|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </div>
        <div class="clear">&nbsp;</div>


        <!--  -->
        <label>{l s=' New ProductID logic:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_NEW_PRODUCT_ID_SYSTEM" value="1"
                   {if $beezup_conf.BEEZUP_NEW_PRODUCT_ID_SYSTEM}checked="checked"{/if}/><img
                    src="../img/admin/enabled.gif" alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_NEW_PRODUCT_ID_SYSTEM" value="0"
                   {if !$beezup_conf.BEEZUP_NEW_PRODUCT_ID_SYSTEM}checked="checked"{/if}/><img
                    src="../img/admin/disabled.gif" alt="{l s='No' mod='beezup'}"/>
            <p>{l s='WARNING, option with high impact, disable it only if it was already disabled in your previous setup. If not : enable it. If you doubt, contact BeezUP.' mod='beezup'}</p>
        </div>


        <div class="clear"></div>

        <!--  -->
        <label>{l s='Logic of product categories:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_CATEGORY_DEEPEST" value="0"
                   {if !$beezup_conf.BEEZUP_CATEGORY_DEEPEST}checked="checked"{/if}/>{l s='Default category' mod='beezup'}
            <br/>
            <input type="radio" name="BEEZUP_CATEGORY_DEEPEST" value="1"
                   {if $beezup_conf.BEEZUP_CATEGORY_DEEPEST}checked="checked"{/if}/>{l s='Deepest category' mod='beezup'}
            <br/>
        </div>

        <div class="clear"></div>


        <!-- SAVE -->
        <div class="margin-form">
            <input class="button" type="submit" name="submitGeneralConfiguration"
                   value="{l s='Save general configuration' mod='beezup'}"/>
        </div>
    </fieldset>
</form>
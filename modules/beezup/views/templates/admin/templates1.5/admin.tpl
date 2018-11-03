{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<link rel="stylesheet" href="{$module_dir|escape:'htmlall':'UTF-8'}views/css/admin.css" type="text/css"/>
<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/admin-config.js"></script>

{assign var=header_tpl value="file:`$templates_path`/header.tpl"}
{include file="$header_tpl"}
<br/>

{assign var=tracker_tpl value="file:`$templates_path`/tracker.tpl"}
{include file="$tracker_tpl"}
<br/>

{assign var=general_tpl value="file:`$templates_path`/general.tpl"}
{include file="$general_tpl"}
<br/>

<fieldset>
    <legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.gif" alt=""/>{l s='Feed configuration' mod='beezup'} </legend>
    <br/><br/>
    <span style="float:right">ID: #{$configuration->id|intval}</span>
    <div id="beezup-conf-tab">
        {assign var=configuration_tpl value="file:`$templates_path`/configuration.tpl"}
        {include file="$configuration_tpl" configuration=$configuration}
    </div>
</fieldset>
<br/>


{assign var=om_tpl value="file:`$templates_path`/om.tpl"}
{include file="$om_tpl"}
<br/>


{assign var=log_tpl value="file:`$templates_path`/log.tpl"}
{include file="$log_tpl"}


<form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" style="width: 48%; float: right; margin: 0 1%; margin-bottom: 15px;">
    <fieldset id="fieldsetTest"
              style="width: 49%; float: left; margin: 2px 1% 0 0; margin-bottom: 15px; box-sizing: border-box;">
        <legend><img
                    src="../img/admin/{if $time_test && $memory_test && $log_file_test && $cache_file_test}enabled{else}disabled{/if}.gif"
                    alt=""/>{l s='Tests' mod='beezup'}</legend>
        <label>{l s='Memory limit test:' mod='beezup'}</label>
        <div class="margin-form" style="font-size:1em;color:#000;padding-top:0.2em;">
            <img src="../img/admin/{if $memory_test}enabled{else}disabled{/if}.gif" alt=""> {$memory_limit|escape:'htmlall':'UTF-8'}o
            {l s='extensible to:' mod='beezup'} {$memory_limit_ext|escape:'htmlall':'UTF-8'}o
            {if !$memory_test}
                <p style="color:#C00;">{l s='The generation of xml feed may require more memory than your server allows. If problems occur, change the value of "memory_limit" in php.ini' mod='beezup'}</p>
            {/if}
        </div>

        <label>{l s='Time limit test:' mod='beezup'}</label>
        <div class="margin-form" style="font-size:1em;color:#000;padding-top:0.2em;">
            <img src="../img/admin/{if $time_test}enabled{else}disabled{/if}.gif" alt=""> {$time_limit|escape:'htmlall':'UTF-8'}s
            {l s='extensible to:' mod='beezup'} {$time_limit_ext|escape:'htmlall':'UTF-8'}s
            {if !$time_test}
                <p style="color:#C00;">{l s='The generation of xml feed may require more time than your server allows. If problems occur, change the value of "max_execution_time" in php.ini' mod='beezup'}</p>
            {/if}
        </div>

        <label>{l s='Log file test:' mod='beezup'}</label>
        <div class="margin-form" style="font-size:1em;color:#000;padding-top:0.2em;">
            {if $log_file_test}
                <img src="../img/admin/enabled.gif" alt="">
                {l s='Log file is writable' mod='beezup'}
            {else}
                <img src="../img/admin/disabled.gif" alt="">
                {l s='Log file is not writable' mod='beezup'}
                <p style="color:#C00;">{l s='Without a writable log file, beezup module can\'t log access and configuration actions' mod='beezup'}
                    <br/>
                    {l s='Set /modules/beezup/views/log/log.txt file permissions to 077 to solve this problem.' mod='beezup'}
                </p>
            {/if}
        </div>

        <label>{l s='Cache directory test:' mod='beezup'}</label>
        <div class="margin-form" style="font-size:1em;color:#000;padding-top:0.2em;">
            {if $cache_file_test}
                <img src="../img/admin/enabled.gif" alt="">
                {l s='Cache directory is writable' mod='beezup'}
            {else}
                <img src="../img/admin/disabled.gif" alt="">
                {l s='Cache directory is not writable' mod='beezup'}
                <p style="color:#C00;">{l s='Without a writable cache directory, beezup module can\'t use cache and CRON options' mod='beezup'}
                    <br/>
                    {l s='Set /modules/beezup/views/cache file permissions to 077 to solve this problem.' mod='beezup'}
                </p>
            {/if}
        </div>

    </fieldset>

    <fieldset id="fieldsetDebug"
              style="width: 49%; float: right; margin: 0 0 0 1%; margin-bottom: 15px; box-sizing: border-box;">
        <legend><img src="../img/admin/AdminTools.gif" alt=""/>{l s='Debug' mod='beezup'}</legend>
        <label>{l s='DEBUG Mode:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_DEBUG_MODE" value="1"
                   {if $beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                   alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_DEBUG_MODE" value="0"
                   {if !$beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                    alt="{l s='No' mod='beezup'}"/>
            <p>{l s='Set enabled to display error repporting (should be desabled in production shop)' mod='beezup'}</p>
        </div>

        <label>{l s='TEST Mode:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_OM_TEST_MODE" value="1"
                   {if $beezup_conf.BEEZUP_OM_TEST_MODE}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                     alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_OM_TEST_MODE" value="0"
                   {if !$beezup_conf.BEEZUP_OM_TEST_MODE}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                      alt="{l s='No' mod='beezup'}"/>
            <p>{l s='Set enabled to use TestMode for Order status change' mod='beezup'}</p>
        </div>

        <!-- Memory limit -->
        <label>{l s='Memory limit:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="text" name="BEEZUP_MEMORY_LIMIT" style="width: 100px;"
                   value="{$beezup_conf.BEEZUP_MEMORY_LIMIT|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div class="clear"></div>

        <!-- Time limit -->
        <label>{l s='Time limit:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="text" name="BEEZUP_TIME_LIMIT" style="width: 100px;"
                   value="{$beezup_conf.BEEZUP_TIME_LIMIT|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div class="clear"></div>

        <!-- Batch size -->
        <label>{l s='Batch size:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="text" name="BEEZUP_BATCH_SIZE" style="width: 100px;"
                   value="{$beezup_conf.BEEZUP_BATCH_SIZE|escape:'htmlall':'UTF-8'}"/>
        </div>
        <div class="clear"></div>

        <div class="margin-form">
            <input class="button" type="submit" name="submitDebug"
                   value="{l s='Save general configuration' mod='beezup'}"/>
        </div>
    </fieldset>
</form>

<div class="clear"></div>

<div id="newFieldConfiguration" style="display:none;">
    <div class="cache">&nbsp;</div>
    <div class="form">
        <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}">
            <h2>{l s='New Field' mod='beezup'}</h2>
            <p>{l s='New name:' mod='beezup'}
                <input type="text" name="name" value="{l s='New field name' mod='beezup'}"/>
            </p>
            <p>{l s='New value:' mod='beezup'}
                <input type="text" name="value" value=""/>
            </p>
            <input type="submit" name="createNewFieldConfiguration" value="{l s='OK' mod='beezup'}" class="button"/>
            <a href="#cancel" id="fieldConfigCancel" class="button">{l s='Cancel' mod='beezup'}</a>
        </form>
    </div>
</div>
<br/>

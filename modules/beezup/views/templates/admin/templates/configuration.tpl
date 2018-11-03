{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- CONFIGURATION.TPL -->
<div class="row">
    {include file="{$templates_path}/menu.tpl"}

    <div class="col-md-10">


        <div class="panel">
            <h3><i class="icon-rss-sign"></i> {l s='Feed' mod='beezup'}</h3>
            <fieldset>
                <a href="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php"
                   target="_blank" id="base_url" display="none"></a>
                <div class="col-lg-12 form-group">
                    <div class="col-lg-2">
                        <select id="url_langs" class="fixed-width-sm" style="display:inline">
                            <option value="">--</option>
                            {foreach $languages as $lang}
                                <option value="{$lang.iso_code|escape:'htmlall':'UTF-8'}">{$lang.iso_code|strtoupper|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                        <select id="url_currencies" class="fixed-width-sm" style="display:inline">
                            <option value="">--</option>
                            {foreach $currencies as $currency}
                                <option value="{$currency.iso_code|escape:'htmlall':'UTF-8'}">{$currency.iso_code|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-5">
                        <a href="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php"
                           target="_blank" id="url_href" style="line-height:27px">
                            <i class="icon-external-link-sign"></i> 
                            <span id="url_txt">{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php</span>
                        </a>
                    </div>
                </div>
            </fieldset>
        </div>
        <script>
            {literal}

            $(function () {

                var url = $("#base_url").attr('href');

                $("#url_currencies,#url_langs").change(function () {

                    var new_url = url;
                    var lang_iso = $('#url_langs').val();
                    var currency_iso = $('#url_currencies').val();
                    var q = [];
                    if (lang_iso != '') {
                        q.push(encodeURIComponent('lang_iso') + '=' + encodeURIComponent(lang_iso));
                    }

                    if (currency_iso != '') {
                        q.push(encodeURIComponent('currency_iso') + '=' + encodeURIComponent(currency_iso));
                    }

                    if (q.length > 0) {
                        new_url = new_url + '?' + q.join('&');
                    }
                    $("#url_href").attr('href', new_url);
                    $("#url_txt").text(new_url);
                })


            });
            {/literal}
        </script>


        {include file="{$templates_path}/general.tpl"}

        <div class="panel">
            <fieldset>
                <legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.gif" alt=""/> &nbsp; {l s='Feed configuration' mod='beezup'} <span
                            style="float:right">ID: #{$configuration->id|intval}</span></legend>
                <br/><br/>


                <div class="tab-content" id="tab-content-{$configuration->id|intval}">
                    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">
                        {assign var=main_tpl value="file:`$templates_path`/configuration_main.tpl"}
                        {include file="$main_tpl"}
                        {assign var=fields_tpl value="file:`$templates_path`/configuration_fields.tpl"}
                        {include file="$fields_tpl"}
                    </form>
                </div>


                <div class="panel">
                    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" style="">
                        <h3><img src="../img/admin/AdminTools.gif" alt=""/>{l s='Debug' mod='beezup'}</h3>
                        <fieldset id="fieldsetDebug" style="">

                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='Stop Current Feed Execution:' mod='beezup'}</label>
                                <a href="{$request_uri|escape:'htmlall':'UTF-8'}&stopXmlGeneration"
                                   class="btn btn-primary">{l s='Stop' mod='beezup'}</a>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='DEBUG Mode:' mod='beezup'}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="input-group col-sm-12">
                                            <input type="radio" name="BEEZUP_DEBUG_MODE" value="1"
                                                   {if $beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/><img
                                                    src="../img/admin/enabled.gif" alt="{l s='Yes' mod='beezup'}"/>
                                            <input type="radio" name="BEEZUP_DEBUG_MODE" value="0"
                                                   {if !$beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/><img
                                                    src="../img/admin/disabled.gif" alt="{l s='No' mod='beezup'}"/>
                                            <p>{l s='Set enabled to display error repporting (should be desabled in production shop)' mod='beezup'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='TEST Mode:' mod='beezup'}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="input-group col-sm-12">
                                            <input type="radio" name="BEEZUP_OM_TEST_MODE" value="1"
                                                   {if $beezup_conf.BEEZUP_OM_TEST_MODE}checked="checked"{/if}/><img
                                                    src="../img/admin/enabled.gif" alt="{l s='Yes' mod='beezup'}"/>
                                            <input type="radio" name="BEEZUP_OM_TEST_MODE" value="0"
                                                   {if !$beezup_conf.BEEZUP_OM_TEST_MODE}checked="checked"{/if}/><img
                                                    src="../img/admin/disabled.gif" alt="{l s='No' mod='beezup'}"/>
                                            <p>{l s='Set enabled to use TestMode for Order status change' mod='beezup'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Memory limit -->
                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='Memory limit:' mod='beezup'}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="margin-form col-sm-6 col-xs-12">
                                            <input type="text" name="BEEZUP_MEMORY_LIMIT" style="width: 100px;"
                                                   value="{$beezup_conf.BEEZUP_MEMORY_LIMIT|escape:'htmlall':'UTF-8'}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Time limit -->
                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='Time limit:' mod='beezup'}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="margin-form col-sm-6 col-xs-12">
                                            <input type="text" name="BEEZUP_TIME_LIMIT" style="width: 100px;"
                                                   value="{$beezup_conf.BEEZUP_TIME_LIMIT|escape:'htmlall':'UTF-8'}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Batch size -->
                            <div class="form-group">
                                <label class="control-label col-sm-3">{l s='Batch size:' mod='beezup'}</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="row">
                                        <div class="margin-form col-sm-6 col-xs-12">
                                            <input type="text" name="BEEZUP_BATCH_SIZE" style="width: 100px;"
                                                   value="{$beezup_conf.BEEZUP_BATCH_SIZE|escape:'htmlall':'UTF-8'}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" value="{l s='Save general configuration' mod='beezup'}"
                                    id="submitDebug" name="submitDebug" class="btn btn-default pull-right">
                                <i class="process-icon-save"></i> Enregistrer
                            </button>

                            <!-- <div class="margin-form">
			<input class="button" type="submit" name="submitDebug" value="{l s='Save general configuration' mod='beezup'}"/>
	</div> -->

                        </fieldset>
                    </form>
                </div>

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
                            <input type="submit" name="createNewFieldConfiguration" value="{l s='OK' mod='beezup'}"
                                   class="button"/>
                            <a href="#cancel" id="fieldConfigCancel" class="button">{l s='Cancel' mod='beezup'}</a>
                        </form>
                    </div>
                </div>


                {assign var=tests_tpl value="file:`$templates_path`/tests.tpl"}
                {include file="$tests_tpl"}

                {assign var=log_tpl value="file:`$templates_path`/log.tpl"}
                {include file="$log_tpl"}
            </fieldset>
        </div>

    </div>

    <!-- /CONFIGURATION.TPL -->
</div>
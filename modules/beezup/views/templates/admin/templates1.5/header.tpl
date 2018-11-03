{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<div id="beezup-header">
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/BEEZUP.jpg" class="fleft beezup-logo" alt=""/>
    <div class="beezup-description">
        <h2>{l s='Boost your sales with your catalog' mod='beezup'}</h2>
        <p>{l s='BeezUP, software that allows you to reference your products on the following networks :' mod='beezup'}</p>
        <ul>
            <li>{l s='Compare price' mod='beezup'}</li>
            <li>{l s='Market places' mod='beezup'}</li>
            <li>{l s='Cashback websites' mod='beezup'}</li>
            <li>{l s='Affiliation board' mod='beezup'}</li>
        </ul>
    </div>
</div>

<p class="clear"></p>


{if isset($update_success) && $update_success}
    <p class="success conf">
        <img src="../img/admin/ok.gif" alt=""/>
        {if $update_success==1}
            {l s='Update successfull.' mod='beezup'}
        {elseif $update_success==2}
            {l s='New configuration successfully created.' mod='beezup'}
        {elseif $update_success==3}
            {l s='Configuration successfully deleted.' mod='beezup'}
        {elseif $update_success==4}
            {l s='Field successfully created.' mod='beezup'}
        {elseif $update_success==5}
            {l s='Field successfully deleted.' mod='beezup'}
        {/if}
    </p>
{elseif isset($update_errors) && $update_errors && is_array($update_errors)}
    <div class="warning">
        <p><img src="../img/admin/warning.gif"
                alt=""/>{if count($update_errors)>1}{l s='There are some errors:' mod='beezup'}{else}{l s='There is an error:' mod='beezup'}{/if}
        </p>
        <ul>
            {foreach item=error from=$update_errors}
                <li>{$error|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ul>
    </div>
{/if}

{include file='./cleanup_messages.tpl'}

<fieldset>
    <legend><img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.gif" alt=""/>{l s='Feed' mod='beezup'}</legend>
    <a href="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php" target="_blank"
       id="base_url" display="none"></a>

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

    <a href="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php" target="_blank"
       id="url_href" style="line-height:27px">
        <i class="icon-external-link-sign"></i> <span
                id="url_txt">{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/xml.php</span>
    </a>
    </div>
</fieldset>
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
</fieldset>

<br/>


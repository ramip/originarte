{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- HEADER.TPL -->
{if isset($update_success) && $update_success}
    <div class="alert alert-success">
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
    </div>
{elseif isset($update_errors) && $update_errors && is_array($update_errors)}
    <div class="alert alert-danger">
        <p>{if count($update_errors)>1}{l s='There are some errors:' mod='beezup'}{else}{l s='There is an error:' mod='beezup'}{/if}</p>
        <ul>
            {foreach item=error from=$update_errors}
                <li>{$error|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
        </ul>
    </div>
{/if}

{include file='./cleanup_messages.tpl'}

<div id="beezup-header" class="panel">
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


<!-- /HEADER.TPL -->

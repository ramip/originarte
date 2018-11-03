{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<link rel="stylesheet" href="{$module_dir|escape:'htmlall':'UTF-8'}views/css/admin.css" type="text/css"/>
<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/admin-config.js"></script>

{literal}
    <style>
        #fieldsetTest, #fieldsetDebug {
            width: 100%;
            float: none;
            margin-left: 0px;
        }

        #fieldsetTest label, #fieldsetDebug label {
            width: 25%;
        }

        #fieldsetTest .margin-form, #fieldsetDebug .margin-form {
            padding-left: 0;
        }
    </style>
{/literal}
<div class="col-md-12">

    {assign var=header_tpl value="file:`$templates_path`/header.tpl"}
    {include file="$header_tpl"}

</div>


<div class="col-md-2">
    <div class="panel">
        <ul class="nav nav-pills nav-stacked">
            <li role="presentation" class="{if $currentPage eq 'home'}active{/if}">
                <a href="{$request_uri|escape:'htmlall':'UTF-8'}">{l s='Product Feed' mod='beezup'}</a>
            </li>
            <li role="presentation" class="{if $currentPage eq 'tracking'}active{/if}">
                <a href="{$request_uri|escape:'htmlall':'UTF-8'}&tracking">{l s='Tracking' mod='beezup'}</a>
            </li>
            <li role="presentation" class="{if $currentPage eq 'om'}active{/if}">
                <a href="{$request_uri|escape:'htmlall':'UTF-8'}&om">{l s='Order Management' mod='beezup'}</a>
            </li>
        </ul>
    </div>
</div>
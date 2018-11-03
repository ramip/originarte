{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<div class="tab-pane " id="beezup">
    <h4 class="visible-print">{l s='BeezUP' mod='beezup'}</h4>
    {literal}
        <style>
            .beezup-om-info {
                position: relative
            }

            .beezup-om-info .resync_container {
                position: absolute;
                padding: 10px;
                margin: 10px;
                top: 10px;
                right: 10px
            }

            .beezup-om-info-table {
            }

            .invalid {
                border: 1px solid red !important;
                background-color: #fdd
            }

            .beezup-om-messages {
            }

            .confirm P {
                margin: 0.5em 0 ! important;
            }

            /* datepicker stacking hack */
            .ui-front {
                z-index: 101 !important
            }

            .required {
            }

            LABEL {
                padding: 5px;
                text-align: right
            }

            LABEL.required::after, LABEL.required:after {
                content: " *";
                color: red
            }

            #beezup-dialog LABEL {
                min-width: 400px;
            }

            #beezup-dialog LABEL SPAN {
                display: inline-block;
                min-width: 150px;
                text-align: right;
                margin-right: 10px
            }

            .conf {
                background-color: green;
                color: white;
                padding: 5px;
                font-weight: bold;
            }

            .error {
                background-color: red;
                color: white;
                padding: 5px;
                font-weight: bold;
            }

            .warn {
                background-color: orange;
                color: white;
                padding: 5px;
                font-weight: bold;
            }

        </style>
    {/literal}
    <div id="beezup-dialog" title="Order management" style="display:none">
        <p id="beezup-dialog-info-no-fields">{$info_text_no_fields|escape:'htmlall':'UTF-8'}</p>
        <p id="beezup-dialog-info-fields">{$info_text_fields|escape:'htmlall':'UTF-8'}</p>
        <form id="beezup-om-form" action="{$order_tab_uri|escape:'htmlall':'UTF-8'}" method="post">
            <input type="hidden" name="beezup_om_action" id="beezup_om_action" value="order_change"/>
            <input type="hidden" name="id_order" id="id_order" value="{$ps_order_id|escape:'htmlall':'UTF-8'}"/>
        </form>
    </div>
    {if $beezup_om_order}
    {literal}
        <script>
            $(function () {
                $("#beezup-dialog").dialog({
                    autoOpen: false, modal: true, minWidth: 500,
                    buttons: {
                        "Close": function () {
                            $("#beezup-dialog").dialog("close")
                        },
                        "Execute": function () {
                            $("#beezup-om-form").submit();
                            return false
                        }
                    }
                });
                var beezup_js_trans = {/literal}{$beezup_js_trans|escape:'quotes'}{literal};

                var beezup_actions = {};
                var beezup_values = {};
                var beezup_lovs = {};
                var beezup_infos = {};

                {/literal}
                {foreach $beezup_om_actions as $action}
                beezup_actions["{$action.id|escape:'htmlall':'UTF-8'}"] = {$action.fields|escape:'quotes'};
                beezup_values["{$action.id|escape:'htmlall':'UTF-8'}"] = {$action.values|escape:'quotes'};
                beezup_lovs["{$action.id|escape:'htmlall':'UTF-8'}"] = {$action.lovs|escape:'quotes'};
                beezup_infos["{$action.id|escape:'htmlall':'UTF-8'}"] = {$action.info|escape:'quotes'};
                {/foreach}
                {literal}
                $('.beezup-om-action').click(function () {
                    var id = $(this).attr("id");
                    $("#beezup-dialog").dialog("option", "title", id + " " + "{/literal}{$beezup_om_order->getOrderMarketPlaceOrderId()|escape:'htmlall':'UTF-8'}{literal}").dialog("open");
                    var form = $("#beezup-om-form");
                    $(".beezup-om-generated").remove();

                    if (typeof beezup_infos[id] !== "undefined" && beezup_infos[id] != '') {

                        $("<div />")
                            .addClass('info')
                            .addClass('beezup-om-generated')
                            .text(beezup_infos[id]);
                    }

                    $("<input />")
                        .addClass("beezup-om-generated")
                        .attr("type", "hidden")
                        .attr("name", "action_id")
                        .attr("value", id)
                        .appendTo(form);
                    if (beezup_actions[id]["parameters"].length > 0) {
                        $("#beezup-dialog-info-fields").show();
                        $("#beezup-dialog-info-no-fields").hide();
                    } else {$("#beezup-dialog-info-fields").hide();
                        $("#beezup-dialog-info-no-fields").show();
                    }
                    $.each(beezup_actions[id]["parameters"], function (i, v) {
                        var tag = 'input';
                        var type = 'text';


                        var label = $("<label />")
                            .attr("for", "beezup_om_" + v.name)
                            .addClass("beezup-om-generated")
                            .html("<span >" + (typeof beezup_js_trans[v.name] !== "undefined" ? beezup_js_trans[v.name] : v.name) + " : " + "</span>")
                            .appendTo(form);
                        if (typeof beezup_lovs[id] != "undefined" && typeof beezup_lovs[id][v.name] != "undefined" && typeof beezup_lovs[id][v.name]['values'] != "undefined" && beezup_lovs[id][v.name]['values'].length > 0) {

                            var input = $("<select/>")
                                .addClass("beezup-om-generated")
                                .attr("id", "beezup_om_" + v.name)
                                .attr("name", v.name)
                                .attr("value", "")
                                .appendTo(label);
                            $.each(beezup_lovs[id][v.name]['values'], function (ii, vv) {
                                input.append($('<option>').text(vv['TranslationText'] ? vv['TranslationText'] : vv['CodeIdentifier']).val(vv['CodeIdentifier']));
                            });
                        } else {var input = $("<" + tag + " />")
                                .addClass("beezup-om-generated")
                                .data(v)
                                .attr("type", type)
                                .attr("id", "beezup_om_" + v.name)
                                .attr("name", v.name)
                                .attr("value", "")
                                .appendTo(label);

                        }
                        if (v['isMandatory'] == true) {
                            label.addClass('required');
                        }
                        if (beezup_values[id][v.name]) {
                            input.val(beezup_values[id][v.name]);
                        }
                        if (v.cSharpType == 'System.DateTime') {
                            input.addClass("datepicker").datepicker({
                                timezoneIso8609: true,
                                dateFormat: 'yy-mm-dd'
                            }).datepicker("setDate", new Date());
                            $("#ui-datepicker-div").css("z-index", 9999);
                        }
                    });

                    form.submit(function () {
                        var validated = true;
                        $(this).find("INPUT, SELECT, TEXTAREA").each(function (i, v) {
                            if ($(v).data('isMandatory') && $(v).val() == "") {
                                validated = false;
                                $(v).addClass("invalid");
                            } else {$(v).removeClass('invalid');
                            }
                        });
                        return validated;
                    });

                    // form.append("<hr />");

                });

            });
        </script>
    {/literal}
    {/if}
    <br/><br/>
    {if $beezup_om_messages}
        <div class="beezup-om-messages">
            {if isset($beezup_om_messages.errors) && $beezup_om_messages.errors}
                <div class="error">
                    {foreach $beezup_om_messages.errors as $message}
                        <p>{$message|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                </div>
            {/if}
            {if isset($beezup_om_messages.warnings) &&  $beezup_om_messages.warnings}
                <div class="warn">
                    {foreach $beezup_om_messages.warnings as $message}
                        <p>{$message|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                </div>
            {/if}
            {if isset($beezup_om_messages.infos) &&  $beezup_om_messages.infos}
                <div class="conf">
                    {foreach $beezup_om_messages.infos as $message}
                        <p>{$message|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                </div>
            {/if}
            {if isset($beezup_om_messages.successes) &&  $beezup_om_messages.successes}
                <div class="conf">
                    {foreach $beezup_om_messages.successes as $message}
                        <p>{$message|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                </div>
            {/if}
            <br/>
        </div>
    {/if}
    {if $beezup_om_order_infos}
        <div class="hint" style="display:block">
            {foreach $beezup_om_order_infos as $k=>$info}
                <p class="{$info->getCode()|escape:'htmlall':'UTF-8'}">{if $info->getMessage()}{$info->getMessage()|escape:'htmlall':'UTF-8'}{else}{$info->getCode()|escape:'htmlall':'UTF-8'}{/if}</p>
            {/foreach}
        </div>
        <br/>
    {/if}

    <fieldset class="beezup-om-info">
        <legend><img src="../img/admin/delivery.gif" alt="Beezup info">{l s='Beezup info' mod='beezup'}</legend>
        {if $beezup_om_order}
            <div class="resync_container"><a href="{$order_tab_uri|escape:'htmlall':'UTF-8'}&beezup_om_action=resync" id="resync"
                                             class=" button beezup-om-resync"
                                             title="{l s='Resynchronize' mod='beezup'}">{l s='Resynchronize' mod='beezup'}</a>
            </div>
            <br/>
            <table class="beezup-om-info-table">
                <tr>
                    <td>{l s='Beezup ID' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getBeezupOrderUUID()|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Beezup status' mod='beezup'} :</td>
                    <td>{$beezup_om_order_translate.beezup_status|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Last modification' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getOrderLastModificationUtcDate()->setTimezone($timezone)->format('Y-m-d H:i:s')|escape:'htmlall':'UTF-8'}</td>
                </tr>

                <tr>
                    <td>{l s='Marketplace' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getMarketplaceBusinessCode()|lower|capitalize|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Marketplace order id' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getOrderMarketPlaceOrderId()|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Marketplace status' mod='beezup'} :</td>
                    <td>{$beezup_om_order_translate.marketplace_status|escape:'htmlall':'UTF-8'}
                        {if ($beezup_om_order && $beezup_om_order->isPendingSynchronization()) || $pending_sync}
                            <span style="background-color:orange; color: white; font-weight: bold"> ( {l s='Status to be confirmed at the end of current synchronization' mod='beezup'}
                                )</span>
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td>{l s='Purchase date' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getOrderPurchaseUtcDate()->setTimezone($timezone)->format('Y-m-d H:i:s')|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Marketplace last modification' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getOrderMarketPlaceLastModificationUtcDate()->setTimezone($timezone)->format('Y-m-d H:i:s')|escape:'htmlall':'UTF-8'}</td>
                </tr>

                <tr>
                    <td>{l s='Total	paid' mod='beezup'} :</td>
                    <td>{$beezup_om_order->getOrderTotalPrice()|escape:'htmlall':'UTF-8'} {$beezup_om_order->getOrderCurrencyCode()|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr>
                    <td>{l s='Commision' mod='beezup'} :</td>
                    <td>{if $beezup_om_order->getOrderTotalCommission() > 0}{$beezup_om_order->getOrderTotalCommission()|escape:'htmlall':'UTF-8'} {$beezup_om_order->getOrderCurrencyCode()|escape:'htmlall':'UTF-8'}{/if}</td>
                </tr>

            </table>
            <p>
                <a class="button"
                   href="https://go2.beezup.com/index.html#!/order/{$beezup_om_order->getMarketPlaceTechnicalCode()|escape:'htmlall':'UTF-8'}/{$beezup_om_order->getAccountId()|escape:'htmlall':'UTF-8'}/{$beezup_om_order->getBeezupOrderUUID()|escape:'htmlall':'UTF-8'}"
                   target="_blank"> {l s='See this order on BeezUP' mod='beezup'} </a>
                {if $beezup_om_debug_mode}
                    &nbsp;
                    <a class="button"
                       href="https://api.beezup.com/orders/v1/{$beezup_om_user_id|escape:'htmlall':'UTF-8'}/{$beezup_om_order->getMarketPlaceTechnicalCode()|escape:'htmlall':'UTF-8'}/{$beezup_om_order->getAccountId()|escape:'htmlall':'UTF-8'}/{$beezup_om_order->getBeezupOrderUUID()|escape:'htmlall':'UTF-8'}?subscription-key={$beezup_om_api_token|escape:'htmlall':'UTF-8'}"
                       target="_blank"> {l s='See direct API output' mod='beezup'} </a>
                {/if}
            </p>
        {else}
            {l s='No order data' mod='beezup'}
        {/if}
        <hr/>

        {if $beezup_om_test_mode}
            <div style="background:  orange; margin: 0;  padding-top:  10px;   padding-bottom: 10px;  padding-left:  10px;">{l s='TEST MODE ACTIVATED' mod='beezup'}</div>
        {/if}
        {if ($beezup_om_order && $beezup_om_order->isPendingSynchronization()) || $pending_sync}
            {l s='There is synchronisation pending, no action possible' mod='beezup'}
        {else}
            {if $beezup_om_actions}
                {foreach $beezup_om_actions as $action}
                    <button href="{$action.href|escape:'htmlall':'UTF-8'}" id="{$action.id|escape:'htmlall':'UTF-8'}" class="beezup-om-action"
                            title="{$action.name|escape:'htmlall':'UTF-8'}">{$action.translated_name|escape:'htmlall':'UTF-8'}</button>
                {/foreach}
            {else}
                {l s='No actions possibles' mod='beezup'}
            {/if}
        {/if}
    </fieldset>
</div>
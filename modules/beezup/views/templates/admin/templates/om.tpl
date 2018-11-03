{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<div class="row">
    {include file="{$templates_path}/menu.tpl"}

    <div class="col-md-10">

        <!-- OM.TPL -->
        <script>

            var om_carriers_up = '{$om_carriers_up|escape:'quotes'}';

            function populateCarriers() {

                var obj = jQuery.parseJSON(om_carriers_up);
                $.each(obj, function (key, object) {
                    var map = "#map_" + key;
                    var beezup_carriers = $(map).prev(".col-md-6");
                    var ps_carriers = beezup_carriers.next(".row");
                    var content = beezup_carriers.html();
                    if (content != null || content != undefined) {
                        content = content.replace("tmp_", "");
                        content = content.replace("tmp_", "");
                    }
                    //	content = content.replace('tmp_', '');

                    $.each(object, function (i, value) {

                        ps_carriers.append("<div class='col-md-offset-3  col-md-6' style='margin-bottom:10px;'>" + content + '</div>');
                        ps_carriers.children(".col-md-6").find(".beezup_carr").last().val(value.beezup_carrier);
                        ps_carriers.children(".col-md-6").find(".ps_input").last().val(value.id_carrier);
                    });
                });

            }
        </script>
        {literal}
        <style>
            .beezup-om-info {
                border-bottom: 1px solid #aaa;
                padding: 10px;
            }

            .beezup-om-info p {
                margin: 0 0 15px 0;
            }

            .beezup-om-info A {
                color: #008
            }

            .beezup-om-info .ok {
                padding: 5px;
                background-color: #060;
                color: #fff;
                font-weight: bold;
                border-radius: 3px
            }

            .beezup-om-info .ko {
                padding: 5px;
                background-color: #600;
                color: #fff;
                font-weight: bold;
                border-radius: 3px
            }

            .beezup-om-info .inactive {
                padding: 5px;
                background-color: #666;
                color: #fff;
                font-weight: bold;
                border-radius: 3px
            }

            .beezup-om-info .active {
                padding: 5px;
                background-color: orange;
                color: #fff;
                font-weight: bold;
                border-radius: 3px
            }

            .bootstrap input[type="integer"] {
                display: block;
                width: 100%;
                height: 27px;
                padding: 4px 8px;
                font-size: 12px;
                line-height: 1.42857;
                color: #555;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 3px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
                -o-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
                transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
            }

            #beezup_id_fiel_mapping select {
                width: 85%;
                margin: 5px 0;
                float: left;
            }

            #beezup_id_fiel_mapping .del_om {
                float: left;
                margin-left: 5px;
                padding-top: 6px;
            }

        </style>


        <script type="text/javascript">


            $(function () {
                populateCarriers();
                $(".new_status_om").click(function () {
                    var beezup_carriers = $(this).closest(".col-md-3").next(".col-md-6");
                    var ps_carriers = beezup_carriers.next(".row");
                    //console.log(beezup_carriers.html() );
                    var content = beezup_carriers.html();
                    content = content.replace("tmp_", "");
                    content = content.replace("tmp_", "");
                    ps_carriers.append("<div class='col-md-offset-3  col-md-6' style='margin-bottom:10px;'>" + content + '</div>');

                });


                $("#sync_input_trigger").click(function () {
                    $(".sync_input").toggle();
                    $(".sync_input").attr('disabled', !$("#sync_input").is(':visible'));
                    return false;
                });
                var om_ps_id_fields_json = {/literal} {$om_ps_id_fields_json|escape:'quotes'} {literal} ;

                $(".new_om").click(function () {
                    // max number fields == filters
                    var current_len = $(".om_cls_" + $(this).data("store")).length;
                    if (current_len + 1 > om_ps_id_fields_json.length) {
                        //$(this).hide();
                        alert("Max " + om_ps_id_fields_json.length + " fields");
                        return false;
                    }

                    var keys = $(".om_cls_" + $(this).data("store")).map(function () {
                        return parseInt($(this).data("field"), 10);
                    }).get();
                    var new_key = Math.max.apply(Math, keys) + 1

                    var select = $("<select />")
                        .attr("name", "BEEZUP_OM_ID_FIELD_MAPPING[" + $(this).data("store") + "][" + new_key + "]")
                        .attr("for", $(this).data("store"))
                        .addClass("om_cls_" + $(this).data("store"))
                        .attr("id", "om_fi_" + $(this).data("store") + "_" + new_key)
                        .data("field", new_key)
                        .data("store", $(this).data("store"))
                        .insertAfter($(this));

                    $(om_ps_id_fields_json).each(function (i, v) {
                        $("<option />")
                            .val(v.value)
                            .text(v.name)
                            .appendTo(select);
                    });

                    var a = $("<a />")
                        .addClass("del_om")
                        .attr("href", "#")
                        .data("field", new_key)
                        .data("store", $(this).data("store"))
                        .html('<img src="../img/admin/delete.gif" alt="-" title="-"/>')
                        .click(function () {
                            $("#om_fi_" + $(this).data("store") + "_" + $(this).data("field")).remove();
                            $(this).remove();
                            return false;
                        })
                        .insertAfter(select);

                    return false;
                });


                $(".del_om").click(function () {
                    $("#om_fi_" + $(this).data("store") + "_" + $(this).data("field")).remove();
                    $(this).remove();
                    return false;
                });


            });
        </script>
        {/literal}
        <div class="panel">

            <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">

                <fieldset>
                    <h3><img src="../img/admin/cog.gif" alt=""/> {l s='Beezup Order Management API' mod='beezup'}</h3>
                    <div class="beezup-om-info clearfix">
                        <p class="col-sm-3">{l s='API BeezUP Connection status' mod='beezup'} : </p>
                        <p class="col-sm-9">{if $is_connection_ok}<span class="ok">{l s='OK' mod='beezup'}</span>{else}
                                <span class="ko">{l s='NOT OK' mod='beezup'}</span>{/if}</p>
                        <div class="clearfix"></div>
                        {if !empty($current_sync)}
                            <p class="col-sm-3">{l s='Sync status' mod='beezup'} : </p>
                            <p class="col-sm-9"><span class="active">{l s='Currently syncing.' mod='beezup'}</span>
                                &nbsp;
                                {l s='Synchronization started' mod='beezup'} {$current_sync->creation_utc_date|escape:'htmlall':'UTF-8'} (UTC
                                time),
                                {l s='last updated:' mod='beezup'} {$current_sync->last_update_utc_date|escape:'htmlall':'UTF-8'} (UTC
                                time), {l s='processing status:' mod='beezup'} {$current_sync->processing_status|escape:'htmlall':'UTF-8'}
                                <input class="button" type="submit" name="submitOMPurgeLocks"
                                       value="{l s='Reset ongoing synchonization status' mod='beezup'}"/>
                            </p>
                            <div class="clearfix"></div>
                        {else}
                            <p class="col-sm-3">{l s='Sync status' mod='beezup'} : </p>
                            <p class="col-sm-9"><span class="inactive">{l s='Not syncing' mod='beezup'}</span></p>
                            <div class="clearfix"></div>
                        {/if}
                        <p class="col-sm-3">{l s='Last synchronization time' mod='beezup'} : </p>
                        <p class="col-sm-9">{$om_last_sync|escape:'htmlall':'UTF-8'} (UTC Time)
                            <button value="{l s='Change' mod='beezup'}"
                                    id="sync_input_trigger">{l s='Change' mod='beezup'}</button>
                            <input type="text" class="sync_input" style="width: 200px; display:none"
                                   name="BEEZUP_OM_LAST_SYNCHRONIZATION" id="sync_input" value="{$om_last_sync|escape:'htmlall':'UTF-8'}"
                                   disabled="disabled"/>
                            <input class="button sync_input" type="submit" name="submitOMLastSynchro"
                                   style="display:none" value="{l s='Save' mod='beezup'} "/>
                        </p>
            </form>
            <div class="clearfix"></div>
            {if $is_connection_ok}
                <p class="col-sm-3">
                    {l s='Manual orders retrieval link: ' mod='beezup'} </p>
                <p class="col-sm-9">
                    <a href="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/harvest.php?key={$harvest_key|escape:'htmlall':'UTF-8'}"
                       target="_blank">{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}
                        /modules/beezup/harvest.php?key={$harvest_key|escape:'htmlall':'UTF-8'}</a>
                </p>
                <div class="clearfix"></div>
                <p class="col-sm-3">{l s='Cron' mod='beezup'} : </p>
                <p class="col-sm-9"><code>{$cron_call|escape:'htmlall':'UTF-8'}</code> (<a href="http://en.wikipedia.org/wiki/Cron"
                                                                  target="_blank">{l s='More info on cron' mod='beezup'}</a>)
                </p>
                <div class="clearfix"></div>
                {assign var=harvest_one_tpl value="file:`$templates_path`/harvest_one.tpl"}
                {include file="$harvest_one_tpl"}
                <div class="clearfix"></div>
            {/if}
        </div>
        <div class="clear"></div>
        <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">
            <div class="beezup-om-info">
                <!-- BEEZUP_OM_USER_ID -->
                <div class="form-group">
                    <label for="BEEZUP_OM_USER_ID"
                           class="control-label col-sm-3">{l s='Beezup API User Id:' mod='beezup'}</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <input type="text" name="BEEZUP_OM_USER_ID"
                                       value="{$beezup_conf.BEEZUP_OM_USER_ID|escape:'htmlall':'UTF-8'}"/> <a
                                        href="https://go.beezup.com/Account/MyAccount"
                                        target="_blank">{l s='My account' mod='beezup'} </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEEZUP_OM_API_TOKEN -->
                <div class="form-group">
                    <label for="BEEZUP_OM_API_TOKEN"
                           class="control-label col-sm-3">{l s='Beezup API Primary Token:' mod='beezup'}</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <input type="text" name="BEEZUP_OM_API_TOKEN"
                                       value="{$beezup_conf.BEEZUP_OM_API_TOKEN|escape:'htmlall':'UTF-8'}"/> <a
                                        href="https://go.beezup.com/Account/MyAccount"
                                        target="_blank">{l s='My account' mod='beezup'} </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>

            {if $om_statuses}
                <!-- BEEZUP_OM_STATUS_MAPPING -->
                <div class="beezup-om-info">
                    <label for="BEEZUP_OM_STATUS_MAPPING" class="">{l s='Beezup API Status mapping' mod='beezup'}
                        : </label>
                    <div class="clear"></div>
                    <br/>
                    {foreach $om_statuses as $om_status_code=>$om_status_name}
                        <div class="form-group">
                            <label class="control-label col-sm-3"
                                   for="BEEZUP_OM_STATUS_MAPPING[{$om_status_code|escape:'htmlall':'UTF-8'}]">{$om_status_name|escape:'htmlall':'UTF-8'}({$om_status_code|escape:'htmlall':'UTF-8'}
                                )</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="row">
                                    <div class="margin-form col-sm-6 col-xs-12">
                                        <select name="BEEZUP_OM_STATUS_MAPPING[{$om_status_code|escape:'htmlall':'UTF-8'}]">
                                            {foreach $ps_statuses as $ps_status}
                                                <option value="{$ps_status.id_order_state|intval}"
                                                        {if isset($beezup_conf.BEEZUP_OM_STATUS_MAPPING.$om_status_code) && $ps_status.id_order_state == $beezup_conf.BEEZUP_OM_STATUS_MAPPING.$om_status_code}selected="selected"{/if}>{$ps_status.name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="clear"></div>
            {/if}


            {if $om_stores}

                <!-- BEEZUP_OM_STORES_MAPPING -->
                <div class="beezup-om-info">
                    <label for="BEEZUP_OM_STORES_MAPPING" class="">{l s='Beezup Stores mapping' mod='beezup'} : </label>
                    <div class="clear"></div>
                    <br/>
                    {foreach $om_stores as $om_store_code=>$om_store_name}
                        <div class="form-group">
                            <label class="control-label col-sm-3" for="BEEZUP_OM_STORES_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}]"
                                   title="{$om_store_code|escape:'htmlall':'UTF-8'}">{$om_store_name|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="row">
                                    <div class="margin-form col-sm-6 col-xs-12">
                                        <select name="BEEZUP_OM_STORES_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}]">
                                            {foreach $ps_stores as $store}
                                                <option value="{$store.id_shop|intval}"
                                                        {if isset($beezup_conf.BEEZUP_OM_STORES_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}]) && $beezup_conf.BEEZUP_OM_STORES_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}] == $store.id_shop}selected="selected"{/if}>{$store.name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="clear"></div>
                <!-- BEEZUP_OM_ID_FIELD_MAPPING -->
                <div id="beezup_id_fiel_mapping" class="beezup-om-info">
                    <label for="BEEZUP_OM_ID_FIELD_MAPPING" class="">{l s='Beezup id field mapping' mod='beezup'}
                        : </label>
                    <div class="clear"></div>
                    <br/>
                    {foreach $om_stores as $om_store_code=>$om_store_name}
                        <div class="form-group">
                            <label class="control-label col-sm-3" style="padding-right:25px;"
                                   for="BEEZUP_OM_ID_FIELD_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}]"
                                   title="{$om_store_code|escape:'htmlall':'UTF-8'}">{$om_store_name|escape:'htmlall':'UTF-8'}</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="row">
                                    <div class="margin-form col-sm-6 col-xs-12">
                                        <a href="#" class="new_om" id="om_fi_add_{$om_store_code|escape:'htmlall':'UTF-8'}"
                                           data-store="{$om_store_code|escape:'htmlall':'UTF-8'}"
                                           style="margin-right:10px;position: absolute;left: -20px;top: 5px;"><img
                                                    src="../img/admin/duplicate.gif" alt="+" title="+"/></a>
                                        {if isset($beezup_conf.BEEZUP_OM_ID_FIELD_MAPPING[{$om_store_code}])}
                                            {foreach $beezup_conf.BEEZUP_OM_ID_FIELD_MAPPING[{$om_store_code}] as $k=>$field}
                                                {if !$om_debug_mode &&  $field=="fakereference"}
                                                    {continue}
                                                {/if}
                                                <select name="BEEZUP_OM_ID_FIELD_MAPPING[{$om_store_code|escape:'htmlall':'UTF-8'}][{$k|escape:'htmlall':'UTF-8'}]"
                                                        id="om_fi_{$om_store_code|escape:'htmlall':'UTF-8'}_{$k}" data-field="{$k|escape:'htmlall':'UTF-8'}"
                                                        data-store="{$om_store_code|escape:'htmlall':'UTF-8'}" class="om_cls_{$om_store_code|escape:'htmlall':'UTF-8'}">
                                                    {foreach $om_ps_id_fields as $om_ps_id_field}
                                                        <option value="{$om_ps_id_field.value|escape:'htmlall':'UTF-8'}"
                                                                {if  $field == $om_ps_id_field.value|escape:'htmlall':'UTF-8'}selected="selected"{/if}>{$om_ps_id_field.name|escape:'htmlall':'UTF-8'}</option>
                                                    {/foreach}
                                                </select>
                                                <a href="#" class="del_om" data-field="{$k|escape:'htmlall':'UTF-8'}"
                                                   data-store="{$om_store_code|escape:'htmlall':'UTF-8'}"><img src="../img/admin/delete.gif"
                                                                                      alt="-" title="-"/></a>
                                            {/foreach}
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="clear"></div>
            {/if}


            <!-- BEEZUP_OM_CARRIERS_MAPPING -->

            <div class="beezup-om-info">
                <label for="BEEZUP_OM_CARRIERS_MAPPING" class="">{l s='Carrier mapping' mod='beezup'} : </label>
                <!-- BEEZUP_OM_DEFAULT_CARRIER_ID -->
                <div class="form-group">
                    <label for="BEEZUP_OM_DEFAULT_CARRIER_ID"
                           class="control-label col-sm-3">{l s='Default carrier' mod='beezup'}</p>
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <select name="BEEZUP_OM_DEFAULT_CARRIER_ID">
                                    {foreach $carriers as $carrier}
                                        <option value="{$carrier.id_carrier|intval}"
                                                {if isset($beezup_conf.BEEZUP_OM_DEFAULT_CARRIER_ID) && $carrier.id_carrier == $beezup_conf.BEEZUP_OM_DEFAULT_CARRIER_ID}selected="selected"{/if}>{$carrier.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                {if $carriers && $om_carriers}
                <div class="clear"></div>
                <br/>
                {assign var="last_tc" value=""}
                {foreach $om_carriers as $marketplace_carrier}
                {assign var="mc_idx" value="`$marketplace_carrier.marketplace_technical_code``$marketplace_carrier.code`"}
                {assign var="mc_idx" value="`$marketplace_carrier.mc_idx`"}
                {assign var="mc_name" value="`$marketplace_carrier.marketplace_business_code|lower|capitalize``$marketplace_carrier.name`"}
                {if $last_tc != $marketplace_carrier.marketplace_technical_code}
                {if $last_tc != ""}
            </div>
            {/if}
            {assign var="last_tc" value=$marketplace_carrier.marketplace_technical_code}
            <div class="clear"></div>
        <hr/>
            <h4 class="om_marketplace" data-target="om_tc_{$marketplace_carrier.marketplace_technical_code|escape:'htmlall':'UTF-8'}">
                {$marketplace_carrier.marketplace_business_code|escape:'htmlall':'UTF-8'}{if $marketplace_carrier.marketplace_business_code|lower !=$marketplace_carrier.marketplace_technical_code|lower} ({$marketplace_carrier.marketplace_technical_code|escape:'htmlall':'UTF-8'}){/if}
                &nbsp;<i class="icon-plus-square"></i>
            </h4>
            <div id="om_tc_{$marketplace_carrier.marketplace_technical_code|escape:'htmlall':'UTF-8'}">
                <br/>
                {/if}
                <div class="form-group">
                    <label class="control-label col-sm-3" for="BEEZUP_OM_CARRIERS_MAPPING[{$mc_idx|escape:'htmlall':'UTF-8'}]"
                           title="{$mc_idx|escape:'htmlall':'UTF-8'}">{$marketplace_carrier.code|escape:'htmlall':'UTF-8'}</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <select name="BEEZUP_OM_CARRIERS_MAPPING[{$mc_idx|escape:'htmlall':'UTF-8'}]">
                                    <option value="0"
                                            {if (!isset($beezup_conf.BEEZUP_OM_CARRIERS_MAPPING[$mc_idx]) || !$beezup_conf.BEEZUP_OM_CARRIERS_MAPPING[$mc_idx])}selected="selected"{/if}>
                                        [ {l s='Default carrier' mod='beezup'} ]
                                    </option>
                                    {foreach $carriers as $carrier}
                                        <option value="{$carrier.id_reference|intval}"
                                                {if isset($beezup_conf.BEEZUP_OM_CARRIERS_MAPPING[$mc_idx]) && $carrier.id_reference == $beezup_conf.BEEZUP_OM_CARRIERS_MAPPING[$mc_idx]}selected="selected"{/if}>{$carrier.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
                {if $last_tc!=""}
            </div>
            {/if}
            {literal}
            <script>
                $(function () {
                    $('.om_marketplace').click(function () {
                        var target = $("#" + $(this).data('target'));
                        if (target.is(":visible")) {
                            target.hide();
                            $(this).find("i").removeClass("icon-minus-square").addClass("icon-plus-square");
                        }
                        else {
                            target.show();
                            $(this).find("i").removeClass("icon-plus-square").addClass("icon-minus-square");
                        }
                    });
                    $('.om_marketplace').trigger("click");

                });
            </script>
            {/literal}
            {/if}
    </div>

    <div class="clear"></div>


    <br/>
    <br/>

    <button type="submit" value="{l s='Save' mod='beezup'}" id="submitOMConfiguration" name="submitOMConfiguration"
            class="btn btn-default pull-right">
        <i class="process-icon-save"></i> Enregistrer
    </button>

    <!-- <div class="margin-form">
		<input class="button" type="submit" name="submitOMConfiguration" value="{l s='Save' mod='beezup'}"/>
	</div> -->

    </fieldset>
    </form>
</div>


<div class="panel">

    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">

        <fieldset>
            <h3><img src="../img/admin/cog.gif" alt=""/> {l s='Beezup Order Stock Options' mod='beezup'}</h3>


            <div class="beezup-om-info">

                <!-- BEEZUP_OM_FORCE_CART_ADD -->
                <div class="form-group">
                    <label class="control-label col-sm-3">{l s='Allow creating orders with non available products' mod='beezup'}
                        : </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="input-group col-sm-3">
                                <input type="radio" name="BEEZUP_OM_FORCE_CART_ADD" value="1"
                                       {if $beezup_conf.BEEZUP_OM_FORCE_CART_ADD}checked="checked"{/if}/><img
                                        style="vertical-align: top; margin-left: 5px;" src="../img/admin/enabled.gif"
                                        alt="{l s='Yes' mod='beezup'}"/>
                                <br/>
                                <input type="radio" name="BEEZUP_OM_FORCE_CART_ADD" value="0"
                                       {if !$beezup_conf.BEEZUP_OM_FORCE_CART_ADD}checked="checked"{/if}/><img
                                        style="vertical-align: top; margin-left: 5px;" src="../img/admin/disabled.gif"
                                        alt="{l s='No' mod='beezup'}"/>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="clear"></div>


            <div class="beezup-om-info">

                <!-- BEEZUP_OM_FORCE_CART_ADD -->
                <div class="form-group">
                    <label class="control-label col-sm-3">{l s='Do not affect stock level for' mod='beezup'} : </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <select multiple name="marketChannelFilter[]" class="form-control">
                                    {foreach from=$marketChannelFilters item=filter}
                                        <option value="{$filter.value|escape:'htmlall':'UTF-8'}"
                                                {if $filter.active == 1}selected{/if}>{$filter.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>

                                <div class="help-block">
                                    {l s='(Press CTRL to select/unselect)' mod='beezup'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="clear"></div>


            <br/>
            <br/>

            <button type="submit" value="{l s='Save' mod='beezup'}" id="submitOMStock" name="submitOMStock"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Enregistrer
            </button>
        </fieldset>
    </form>
</div>


<div class="panel">

    <!--	om_carriers -->
    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">

        <fieldset>
            <h3><img src="../img/admin/cog.gif" alt=""/> {l s='Auto shipment setup' mod='beezup'}</h3>

            <div class="form-group">

                <div class="row">
                    <div class="col-md-3" style="text-align:right;position:relative;top:8px;">
                        <label>    {l s='Enable Auto-shipment:' mod='beezup'}</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="BEEZUP_OM_UPDATE_ACTIVE" id="BEEZUP_OM_UPDATE_ACTIVE_on"
                                       onclick="$('#omUpdateMapping').show();" value="1"
                                       {if $beezup_conf.BEEZUP_OM_UPDATE_ACTIVE }checked="checked"{/if}/>
								<label for="BEEZUP_OM_UPDATE_ACTIVE_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="BEEZUP_OM_UPDATE_ACTIVE" id="BEEZUP_OM_UPDATE_ACTIVE_off"
                                       onclick="$('#omUpdateMapping').hide();" value="0"
                                       {if !$beezup_conf.BEEZUP_OM_UPDATE_ACTIVE }checked="checked"{/if}/>
								<label for="BEEZUP_OM_UPDATE_ACTIVE_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="omUpdateMapping" {if !$beezup_conf.BEEZUP_OM_UPDATE_ACTIVE}style="display:none;" {/if}>

                <hr>
                <h4 id="OM_STATUS_CARR_MAP">{l s='Carrier Mapping' mod='beezup'}</h4>
                <hr>
                {assign var="old_carrier" value=""}
                {assign var="carrier_inc" value="0"}
                {foreach from=$om_scarriers_up item=carrier}
                {assign var="carrierM" value=$carrier.marketplace_technical_code}
                {if $carrierM eq "PriceMinister" or $carrierM eq "Fnac" or $carrierM eq "Mirakl" or $carrierM eq "Bol" or $carrierM eq "RealDE" }
                {assign var="carrier_inc" value=$carrier_inc+1}
                {if $carrierM eq "Mirakl"}
                    {assign var="carrierCodigo" value=$carrier.marketplace_business_code}
                {else}
                    {assign var="carrierCodigo" value=$carrierM}
                {/if}

                {if $old_carrier neq $carrierCodigo }
                {if $carrier_inc > 1}
                </select>

                <select class="form-control ps_input" style="width:45%;float:left;"
                        name="tmp_om_carrier_{$select_input|escape:'htmlall':'UTF-8'}[][ps]">
                    {foreach $carriers as $ps_carrier}
                        <option value="{$ps_carrier.id_carrier|intval}">{$ps_carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select><a style="position:relative;top:5px;cursor:pointer;"
                            onclick="$(this).closest('.col-md-6').remove();"><img src="../img/admin/delete.gif" alt="-"
                                                                                  title="-"></a>
            </div>
            <div class="row" id="map_{$select_input|escape:'htmlall':'UTF-8'}" style="position: relative; top: -16px;"></div>
</div>
{/if}
<div class="form-group">
    <div class="col-md-3" style="text-align:right;position:relative;top:8px;z-index:999999;">
        <label>{$carrier.marketplace_business_code|escape:'htmlall':'UTF-8'}: <a class="new_status_om" style="cursor:pointer;"><img
                        src="../img/admin/duplicate.gif" alt="+" title="+"></a></label>
    </div>
    <div class="col-md-6" style="display:none;">
        {assign var="select_input" value=$carrierM}
        {if $carrierM eq "Mirakl"}
            {assign var="select_input" value=$carrier.marketplace_business_code}
        {/if}
        <select style="width:40%;float:left;" class="form-control beezup_carr"
                name="tmp_om_carrier_{$select_input|escape:'htmlall':'UTF-8'}[][beezup]">
            <option value="{$carrier.code|escape:'htmlall':'UTF-8'}">{$carrier.name|escape:'htmlall':'UTF-8'}</option>
            {else}
            <option value="{$carrier.code|escape:'htmlall':'UTF-8'}">{$carrier.name|escape:'htmlall':'UTF-8'}</option>
            {/if}

            {assign var="old_carrier" value=$carrierCodigo}
            {/if}
            {/foreach}
            {if $carrier_inc > 1}
        </select>
        <select class="form-control ps_input" style="width:45%;float:left;" name="tmp_om_carrier_{$select_input|escape:'htmlall':'UTF-8'}[][ps]">
            {foreach $carriers as $ps_carrier}
                <option value="{$ps_carrier.id_carrier|intval}">{$ps_carrier.name|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
        <a style="position:relative;top:5px;cursor:pointer;" onclick="$(this).closest('.col-md-6').remove();"><img
                    src="../img/admin/delete.gif" alt="-" title="-"></a>
    </div>
    <div class="row " id="map_{$select_input|escape:'htmlall':'UTF-8'}" style="position: relative; top: -16px;"></div>
</div>
{/if}
</div>

{if $carrier_inc < 0}
    <style>#OM_STATUS_CARR_MAP {
            display: none;
        }</style>
{/if}
<button type="submit" value="{l s='Save' mod='beezup'}" id="submitOMhookStatusUpdate" name="submitOMhookStatusUpdate"
        class="btn btn-default pull-right">
    <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
</button>


</fieldset>
</form>

</div>


<div class="panel">

    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal" style="margin-bottom: 15px;">
        <br/>
        <fieldset>
            <legend><img src="../img/admin/AdminTools.gif"
                         alt=""/> {l s='Beezup Order Management API Debug' mod='beezup'}</legend>
            <div class="beezup-om-info">
                <div class="form-group">
                    <label class="control-label col-sm-3">
                        {l s='Debug mode' mod='beezup'} :
                    </label>
                    <p class="col-sm-9" style="line-height: 20px; margin-bottom: 0;">
                        {if $om_debug_mode}{l s='YES' mod='beezup'}{else}{l s='NON' mod='beezup'}{/if}
                    </p>
                </div>

                <div class="clear"></div>


                <div class="beezup-on-info">

                    <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Do not modify status if not changed on marketplace side' mod='beezup'}
                        </label>

                        <div class="col-md-9">
                            <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="input_filter_status_enable" id="input_filter_status_enable_on"
                                       value="1" {if $orderStatusFilter == 1 }checked="checked"{/if}/>
								<label for="input_filter_status_enable_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="input_filter_status_enable"
                                       id="input_filter_status_enable_off" value="0"
                                       {if $orderStatusFilter != 1 }checked="checked"{/if}/>
								<label for="input_filter_status_enable_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                            </div>
                        </div>


                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Do not import Amazon FBA Orders' mod='beezup'}
                        </label>
                        <div class="col-md-9">
                            <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="BEEZUP_OM_IMPORT_FBA" id="BEEZUP_OM_IMPORT_FBA_on"
                                       value="1" {if $beezup_conf.BEEZUP_OM_IMPORT_FBA == 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_IMPORT_FBA_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="BEEZUP_OM_IMPORT_FBA"
                                       id="BEEZUP_OM_IMPORT_FBA_off" value="0"
                                       {if $beezup_conf.BEEZUP_OM_IMPORT_FBA != 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_IMPORT_FBA_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Do not import Cdiscount a Volonté Orders' mod='beezup'}
                        </label>
                        <div class="col-md-9">
                            <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="BEEZUP_OM_IMPORT_CDISCOUNT" id="BEEZUP_OM_IMPORT_CDISCOUNT_on"
                                       value="1" {if $beezup_conf.BEEZUP_OM_IMPORT_CDISCOUNT == 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_IMPORT_CDISCOUNT_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="BEEZUP_OM_IMPORT_CDISCOUNT"
                                       id="BEEZUP_OM_IMPORT_CDISCOUNT_off" value="0"
                                       {if $beezup_conf.BEEZUP_OM_IMPORT_CDISCOUNT != 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_IMPORT_CDISCOUNT_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Import partial orders - if at least 1 ordered item is in stock' mod='beezup'}
                        </label>
                        <div class="col-md-9">
                            <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="BEEZUP_OM_MULTIPLE_STOCK_FILTER" id="BEEZUP_OM_MULTIPLE_STOCK_FILTER_on"
                                       value="1" {if $beezup_conf.BEEZUP_OM_MULTIPLE_STOCK_FILTER == 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_MULTIPLE_STOCK_FILTER_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="BEEZUP_OM_MULTIPLE_STOCK_FILTER"
                                       id="BEEZUP_OM_MULTIPLE_STOCK_FILTER_off" value="0"
                                       {if $beezup_conf.BEEZUP_OM_MULTIPLE_STOCK_FILTER != 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_MULTIPLE_STOCK_FILTER_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                            </div>
                        </div>
                    </div>

                   <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Enable Harvest Debug Logs' mod='beezup'}
                        </label>
                        <div class="col-md-9">
                            <div class="input-group col-sm-2 col-xs-12">
							<span class="switch prestashop-switch">
								<input type="radio" name="BEEZUP_OM_DEBUG_LOGS" id="BEEZUP_OM_DEBUG_LOGS_on"
                                       value="1" {if $beezup_conf.BEEZUP_OM_DEBUG_LOGS == 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_DEBUG_LOGS_on">{l s='Yes' mod='beezup'}</label>
								<input type="radio" name="BEEZUP_OM_DEBUG_LOGS"
                                       id="BEEZUP_OM_DEBUG_LOGS_off" value="0"
                                       {if $beezup_conf.BEEZUP_OM_DEBUG_LOGS != 1 }checked="checked"{/if}/>
								<label for="BEEZUP_OM_DEBUG_LOGS_off">{l s='No' mod='beezup'}</label>
								<a class="slide-button btn btn-default"></a>
							</span>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-sm-3">
                            {l s='Import only new orders that' mod='beezup'}
                        </label>
                        <div class="col-sm-9">
                            <div class="col-md-12">


                                <div class="col-md-12">

                                    <input type="checkbox" name="input_filter_days_enable"
                                           {if $importFilterDaysEnable == 1}checked{/if}
                                           value="1"/> {l s='Have less than' mod='beezup'} <input type="number"
                                                                                                  name="import_filter_days"
                                                                                                  min="0" step="1"
                                                                                                  value="{$importFilterDays|escape:'htmlall':'UTF-8'}"
                                                                                                  style="width:60px;"/> {l s='days of difference between purchase date and current date' mod='beezup'}

                                </div>

                                <label class="control-label col-sm-3" style="text-align:left;">
                                    {l s='Have one of this status:' mod='beezup'}
                                </label>
                                <div class="col-sm-9">
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'New'|in_array:$importFilterStatus}checked{/if} value="New"/> New<br>
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'InProgress'|in_array:$importFilterStatus}checked{/if}
                                           value="InProgress"/> In Progress<br>
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'Shipped'|in_array:$importFilterStatus}checked{/if} value="Shipped"/>
                                    Shipped<br>
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'Closed'|in_array:$importFilterStatus}checked{/if} value="Closed"/>
                                    Closed<br>
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'Aborted'|in_array:$importFilterStatus}checked{/if} value="Aborted"/>
                                    Aborted<br>
                                    <input type="checkbox" name="import_filter_status[]"
                                           {if 'Pending'|in_array:$importFilterStatus}checked{/if} value="Pending"/>
                                    Pending<br>
                                </div>
                            </div>


                        </div>

                    </div>
                </div>

				<div class="form-group">
					<label class="control-label col-sm-3">
                    {l s='Log lifetime' mod='beezup'}
                    </label>
                    <div class="col-sm-9">
                    	<select class="form-control" name="BEEZUP_OM_CLEAN_LOG_DAYS">
                    		<option value="0" {if $beezup_conf.BEEZUP_OM_CLEAN_LOG_DAYS == 0}selected{/if}>{l s='For ever' mod='beezup'}</option>
                    		<option value="10" {if $beezup_conf.BEEZUP_OM_CLEAN_LOG_DAYS == 10}selected{/if}>{l s='10 days' mod='beezup'}</option>
                    		<option value="20" {if $beezup_conf.BEEZUP_OM_CLEAN_LOG_DAYS == 20}selected{/if}>{l s='20 days' mod='beezup'}</option>
                    		<option value="30" {if $beezup_conf.BEEZUP_OM_CLEAN_LOG_DAYS == 30}selected{/if}>{l s='30 days' mod='beezup'}</option>
                    		<option value="60" {if $beezup_conf.BEEZUP_OM_CLEAN_LOG_DAYS == 60}selected{/if}>{l s='60 days' mod='beezup'}</option>
                    	</select>
                    </div>
				</div>

                <!-- BEEZUP_OM_TOLERANCE -->
                <div class="form-group">
                    <label class="control-label col-sm-3">{l s='Max tolerance time (min.)' mod='beezup'} : </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="input-group col-sm-3">
                                <input type="integer" name="BEEZUP_OM_TOLERANCE"
                                       value="{$beezup_conf.BEEZUP_OM_TOLERANCE|escape:'htmlall':'UTF-8'}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>

                <!-- BEEZUP_OM_SYNC_TIMEOUT -->
                <div class="form-group">
                    <label for="BEEZUP_OM_SYNC_TIMEOUT"
                           class="control-label col-sm-3">{l s='Max synchronization lock time (sec.)' mod='beezup'}</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-8 col-xs-12 clearfix">
                                <input type="text" name="BEEZUP_OM_SYNC_TIMEOUT"
                                       value="{$beezup_conf.BEEZUP_OM_SYNC_TIMEOUT|escape:'htmlall':'UTF-8'}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-3">
                        {l s='Unlock from command line' mod='beezup'} :
                    </label>
                    <p class="col-sm-9" style="line-height: 28px;">
                        <code>{$sync_purge_call|escape:'htmlall':'UTF-8'}</code>
                    </p>
                </div>

            </div>
            <div class="clear"></div>
            <br/>
            <br/>

            <button type="submit" value="{l s='Save' mod='beezup'}" id="submitOMDebugConfiguration"
                    name="submitOMDebugConfiguration" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Enregistrer
            </button>

            <!-- <div class="margin-form">
						<input class="button" type="submit" name="submitOMDebugConfiguration" value="{l s='Save' mod='beezup'}"/>
					</div> -->
        </fieldset>
    </form>
</div>


</div>


<!-- /OM.TPL -->
</div>
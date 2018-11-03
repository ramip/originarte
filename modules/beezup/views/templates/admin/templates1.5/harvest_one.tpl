{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- HARVEST ONE //-->
{literal}
    <style>
        #beezup_ho_info {
            cursor: pointer
        }

        #beezup_ho_container input[type=text] {
            margin-bottom: 15px;
        }

        #beezup_ho_container label {
            vertical-align: middle
        }

        #beezup_ho_result {
            min-height: 50px;
            padding: 10px;
            margin: 10px;
        }

        #beezup_ho_result .alert-danger {
            background: #FFBABA;
            border: 1px solid #CC0000;
            color: #383838;
            font-size: 12px;
            font-weight: normal;
            margin: 0 0 10px 0;
            line-height: 20px;
            padding: 13px 5px 5px 40px;
            min-height: 28px;
            border-radius: 3px;
        }

        #beezup_ho_result .alert-success {
            background: #BAFFBA;
            border: 1px solid #00CC00;
            color: #383838;
            font-size: 12px;
            font-weight: normal;
            margin: 0 0 10px 0;
            line-height: 20px;
            padding: 13px 5px 5px 40px;
            min-height: 28px;
            border-radius: 3px;
        }
    </style>
{/literal}
<div id="beezup_ho" class="col-sm-9">
    <p id="beezup_ho_info">{l s='Click here if you want to synchronize precise order' mod='beezup'} <img
                src="../img/admin/more.png"/>
    </p>
    <div id="beezup_ho_container">
        <form action="{$beezup_conf.BEEZUP_SITE_ADDRESS|escape:'htmlall':'UTF-8'}/modules/beezup/harvest_one.php"
              method="post" name="beezup_ho_form" id="beezup_ho_form" target="_blank">
            <input type="hidden" name="key" id="beezup_ho_key" value="{$harvest_key|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="ajax" id="beezup_ajax" value="1"/>
            <div style="float:left">

                <div class="form-group">
                    <label class="control-label col-sm-4"
                           for="beezup_ho_accountid"><strong>{l s='Account id' mod='beezup'} : </strong></label>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-9 col-xs-12">
                                <input type="text" name="accountid" id="beezup_ho_accountid" value=""
                                       placeholder="{l s='Account id' mod='beezup'}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-4"
                           for="beezup_ho_marketplace"><strong>{l s='Marketplace' mod='beezup'} : </strong></label>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-9 col-xs-12">
                                <input type="text" name="marketplacetechnicalcode" id="beezup_ho_marketplace" value=""
                                       placeholder="{l s='Marketplace technical code' mod='beezup'}"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-4" for="beezup_ho_uuid"><strong>{l s='UUID' mod='beezup'}
                            : </strong></label>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-9 col-xs-12">
                                <input type="text" name="beezuporderuuid" id="beezup_ho_uuid" value=""
                                       placeholder="{l s='Beezup order UUID' mod='beezup'}"/>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <input type="submit" name="submitHarvestOrder"
                                       value="{l s='Harvest order' mod='beezup'}" class="button"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div style="float:left;  font-weight: bold; font-size: 2em; padding: 10px">
                {l s='OR' mod='beezup'}
            </div>
            <div style="float:left">

                <div class="form-group">
                    <label class="control-label col-sm-4" for="beezup_ho_url"><strong>{l s='URL' mod='beezup'}
                            : </strong></label>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-9 col-xs-12">
                                <input type="text" name="url" id="beezup_ho_url" value=""
                                       placeholder="{l s='Beezup order URL' mod='beezup'}"/>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8 col-xs-12">
                        <div class="row">
                            <div class="margin-form col-sm-6 col-xs-12">
                                <input type="submit" name="submitHarvestOrder"
                                       value="{l s='Harvest order' mod='beezup'}" class="button"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <div class="clearfix"></div>
        <div id="beezup_ho_result" style="display:none"></div>
        <div class="clearfix"></div>
    </div>
</div>
{literal}
<script>
    $(function () {
        var order_link = "{/literal}{$order_link|escape:'htmlall':'UTF-8'}{literal}";
        $("#beezup_ho_form").submit(function (e) {
            e.preventDefault();
            if ($("#beezup_ho_url").val().trim() == "" && ($("#beezup_ho_uuid").val().trim() == "" || $("#beezup_ho_marketplace").val().trim() == "" || $("#beezup_ho_accountid").val().trim() == "")) {
                alert({/literal}"{l s='Please fill all fields!' mod='beezup'}"{literal});
                return false;
            }
            $("#beezup_ho_result").show().html("<i class='icon-spinner fa-spin' style='-webkit-animation: fa-spin 2s infinite linear; -moz-animation: fa-spin 2s infinite linear; animation: fa-spin 2s infinite linear; margin-left: 15px'></i>")
            var form = $("#beezup_ho_form");
            $.ajax({
                type: form.attr("method").toUpperCase(),
                url: form.attr("action"),
                data: form.serialize(),
                dataType: 'json',
                success: function (data) {
                    var result = ""
                    if (data && data['id_order']) {
                        result = $("<p />").html($("<a />").attr("target", "_blank").attr("href", order_link + data['id_order']).html("{/literal}{l s='See synchronized order' mod='beezup'}{literal}" + " #" + data['id_order'])).addClass("alert alert-success");
                    } else if (data && data['error']) {
                        result = $("<p />").addClass("alert alert-danger").html(data['error'].split("\n").join("<br />"));
                    } else {result = $("<p />").addClass("alert alert-danger").text(JSON.stringify(data));
                    }
                    $("#beezup_ho_result").html(result).show();
                },
                error: function (data) {
                    var result = ""
                    result = $("<p />").addClass("alert alert-danger").html(data ? JSON.stringify(data) : {/literal}"{l s='Error' mod='beezup'}"{literal});
                    console.log(data);
                    $("#beezup_ho_result").html(result).show();
                }
            });
        });

        $("#beezup_ho_info").click(function () {
            var target = $("#beezup_ho_container");
            if (target.is(":visible")) {
                target.hide();
                $("#beezup_ho_info img").attr("src", "../img/admin/more.png")
            } else {target.show();
                $("#beezup_ho_info img").attr("src", "../img/admin/less.png")
            }
        });

        $("#beezup_ho_info").trigger("click");

    });
</script>
{/literal}
<!-- /HARVEST ONE //-->
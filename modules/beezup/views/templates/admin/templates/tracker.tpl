{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- TRACKER.TPL -->
<div class="row">
    {include file="{$templates_path}/menu.tpl"}

    <div class="col-md-10">
        <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}" class="form-horizontal">
            <div class="panel">
                <h3><i class="icon-share"></i> {l s='Trackers' mod='beezup'}</h3>
                <fieldset>
                    <!-- ACTIVATE TRACKERS -->
                    <div class="form-group">
                        <label for="BEEZUP_TRACKER_ACTIVE"
                               class="control-label col-sm-3 col-xs-12">{l s='Activate trackers:' mod='beezup'}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="row">
                                <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_TRACKER_ACTIVE" id="BEEZUP_TRACKER_ACTIVE_on" value="1"
                                   {if $beezup_conf.BEEZUP_TRACKER_ACTIVE}checked="checked"{/if}/>
							<label for="BEEZUP_TRACKER_ACTIVE_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_TRACKER_ACTIVE" id="BEEZUP_TRACKER_ACTIVE_off" value="0"
                                   {if !$beezup_conf.BEEZUP_TRACKER_ACTIVE}checked="checked"{/if}/>
							<label for="BEEZUP_TRACKER_ACTIVE_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <!-- TRACKER URL -->
                    <div class="form-group">
                        <label for="BEEZUP_TRACKER_URL"
                               class="control-label col-sm-3 col-xs-12">{l s='Trackers url:' mod='beezup'}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="row">
                                <div class="input-group col-sm-6 col-xs-12">
                                    <span class="input-group-addon">http(s)://</span><input type="text"
                                                                                            name="BEEZUP_TRACKER_URL"
                                                                                            value="{$beezup_conf.BEEZUP_TRACKER_URL|escape:'htmlall':'UTF-8'}"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WHOLESALE PRICE -->
                    <div class="form-group">
                        <label for="BEEZUP_TRACKER_PRICE"
                               class="control-label col-sm-3 col-xs-12">{l s='Use wholesale price:' mod='beezup'}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="row">
                                <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_TRACKER_PRICE" id="BEEZUP_TRACKER_PRICE_on" value="1"
                                   {if $beezup_conf.BEEZUP_TRACKER_PRICE}checked="checked"{/if}/>
							<label for="BEEZUP_TRACKER_PRICE_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_TRACKER_PRICE" id="BEEZUP_TRACKER_PRICE_off" value="0"
                                   {if !$beezup_conf.BEEZUP_TRACKER_PRICE}checked="checked"{/if}/>
							<label for="BEEZUP_TRACKER_PRICE_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VALIDATE METHOD -->
                    <div class="form-group">
                        <label for="BEEZUP_TRACKER_VALIDATE_STATE"
                               class="control-label col-sm-3 col-xs-12">{l s='Validate method:' mod='beezup'}</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="row">
                                <div class="input-group col-sm-12 col-xs-12">
                                    <input type="radio" name="BEEZUP_TRACKER_VALIDATE_STATE"
                                           id="BEEZUP_TRACKER_VALIDATE_STATE_off" value="0"
                                           {if !$beezup_conf.BEEZUP_TRACKER_VALIDATE_STATE}checked="checked"{/if}/>
                                    &nbsp; <label
                                            for="BEEZUP_TRACKER_VALIDATE_STATE_off">{l s='On new order' mod='beezup'}</label>
                                    <br/>
                                    <input type="radio" name="BEEZUP_TRACKER_VALIDATE_STATE"
                                           id="BEEZUP_TRACKER_VALIDATE_STATE_on" value="1"
                                           {if $beezup_conf.BEEZUP_TRACKER_VALIDATE_STATE}checked="checked"{/if}/>
                                    &nbsp; <label
                                            for="BEEZUP_TRACKER_VALIDATE_STATE_on">{l s='On delivered status' mod='beezup'}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STORE ID -->

                    <div class="form-group translatable-field">
                        <label for="BEEZUP_TRACKER_STORE_IDS_{$id_default_lang|intval}"
                               class="control-label col-sm-3 col-xs-12">{l s='Store IDs:' mod='beezup'}</label>
                        <div class="input-group col-sm-9 col-xs-12">
                            {foreach item=language from=$languages}
                                <div class="input-group col-sm-6 col-xs-12">
                                    <span class="input-group-addon">{$language.iso_code|strtoupper|escape:'htmlall':'UTF-8'} : </span> <input
                                            class="input-medium col-sm-3 col-xs-12" type="text"
                                            id="BEEZUP_TRACKER_STORE_IDS_{$language.id_lang|intval}"
                                            name="BEEZUP_TRACKER_STORE_IDS[{$language.id_lang|intval}]"
                                            value="{$beezup_conf.BEEZUP_TRACKER_STORE_IDS[$language.id_lang]|escape:'htmlall':'UTF-8'}"
                                            size="25"/>
                                </div>
                                <br/>
                            {/foreach}
                        </div>

                    </div>

                    <div class="panel-footer">
                        <button type="submit" value="1" id="configuration_form_submit_btn"
                                name="submitTrackerConfiguration" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
                        </button>
                    </div>
                </fieldset>
            </div>
        </form>

        <!-- /TRACKER.TPL -->
    </div>
</div>
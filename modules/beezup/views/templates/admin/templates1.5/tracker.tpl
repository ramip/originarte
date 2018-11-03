{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}">
    <fieldset>
        <legend><img src="../img/admin/cog.gif" alt=""/>{l s='Trackers' mod='beezup'}</legend>

        <!-- Activate -->
        <label>{l s='Activate trackers:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_TRACKER_ACTIVE" value="1"
                   {if $beezup_conf.BEEZUP_TRACKER_ACTIVE}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                       alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_TRACKER_ACTIVE" value="0"
                   {if !$beezup_conf.BEEZUP_TRACKER_ACTIVE}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                        alt="{l s='No' mod='beezup'}"/>
        </div>
        <div class="clear"></div>

        <!-- URL -->
        <label>{l s='Trackers url:' mod='beezup'}</label>
        <div class="margin-form">
            <span><b>http(s)://</b></span><span
                    id="beezup-tracker-view">{$beezup_conf.BEEZUP_TRACKER_URL|escape:'htmlall':'UTF-8'} <a href="#edit"
                                                                                                           class="edit"><img
                            src="../img/admin/edit.gif" alt="{l s='Edit' mod='beezup'}"/></a></span>
            <span id="beezup-tracker-edit" style="display:none;">
				<input type="text" name="BEEZUP_TRACKER_URL"
                       value="{$beezup_conf.BEEZUP_TRACKER_URL|escape:'htmlall':'UTF-8'}" size="80"/>
			</span>
        </div>
        <div class="clear"></div>

        <!-- Store ID -->
        <label>{l s='Store IDs:' mod='beezup'}</label>
        <div class="margin-form translatable">
            {foreach item=language from=$languages}
                <div class="lang_{$language.id_lang|intval}" id="btsi_{$language.id_lang|intval}"
                     style="float: left; display:{if $language.id_lang==$id_default_lang}block{else}none{/if}">
                    <input type="text" id="BEEZUP_TRACKER_STORE_IDS_{$language.id_lang|intval}"
                           name="BEEZUP_TRACKER_STORE_IDS[{$language.id_lang|intval}]"
                           value="{$beezup_conf.BEEZUP_TRACKER_STORE_IDS[$language.id_lang]|escape:'htmlall':'UTF-8'}"
                           size="25"/>
                </div>
            {/foreach}
        </div>
        <div class="clear"></div>

        <script>
            {literal}
            $(function () {
                {/literal}
                var languages = new Array();
                {foreach from=$languages item=language key=k}
                languages[{$k|escape:'htmlall':'UTF-8'}] = {literal}{{/literal}
                    id_lang: {$language.id_lang|intval},
                    iso_code: '{$language.iso_code|escape:'quotes'}',
                    name: '{$language.name|escape:'quotes'}'
                    {literal}}{/literal};
                {/foreach}
                displayFlags(languages, {$id_default_lang|intval});
                {literal}
            });
            {/literal}
        </script>


        <label>{l s='Use wholesale price:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_TRACKER_PRICE" value="1"
                   {if $beezup_conf.BEEZUP_TRACKER_PRICE}checked="checked"{/if}/><img src="../img/admin/enabled.gif"
                                                                                      alt="{l s='Yes' mod='beezup'}"/>
            <input type="radio" name="BEEZUP_TRACKER_PRICE" value="0"
                   {if !$beezup_conf.BEEZUP_TRACKER_PRICE}checked="checked"{/if}/><img src="../img/admin/disabled.gif"
                                                                                       alt="{l s='No' mod='beezup'}"/>
        </div>
        <div class="clear"></div>

        <label>{l s='Validate method:' mod='beezup'}</label>
        <div class="margin-form">
            <input type="radio" name="BEEZUP_TRACKER_VALIDATE_STATE" class="tacker-validate-state" value="0"
                   {if $beezup_conf.BEEZUP_TRACKER_VALIDATE_STATE=='0'}checked="checked"{/if}/> {l s='On new order' mod='beezup'}
            <br/>
            <input type="radio" name="BEEZUP_TRACKER_VALIDATE_STATE" class="tacker-validate-state" value="1"
                   {if $beezup_conf.BEEZUP_TRACKER_VALIDATE_STATE=='1'}checked="checked"{/if}/> {l s='On delivered status' mod='beezup'}
        </div>


        <div class="margin-form">
            <input type="submit" class="button" name="submitTrackerConfiguration"
                   value="{l s='Save trackers configuration' mod='beezup'}"/>
        </div>
    </fieldset>
</form>
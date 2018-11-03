{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- CONFIGURATION_FIELD.TPL -->

<div class="feed">

    {assign var=current_tab value=''}
    <table class="table config-fields">
        <tr>
            <th class="id center">{l s='ID' mod='beezup'}</th>
            <th class="active center">{l s='Active' mod='beezup'}</th>
            <th class="name center">{l s='XML balise name' mod='beezup'}</th>
            <th class="default center">{l s='Default value' mod='beezup'}</th>
            <th class="attribute_feature center">{l s='Features / Attributes' mod='beezup'}</th>
            <th class="center"></th>

        </tr>

        {assign var=current_class value=0}
        {foreach from=$configuration->fields item=field}
            {if $field.fields_group!=$current_tab}
                {assign var=current_class value=$current_class+1}
                {assign var=current_tab value=$field.fields_group}
                <tr>
                    <th colspan="6" class="tab center"
                        onclick="javascript:toggleTab('.tab-content-{$configuration->id|intval}-{$current_class|intval}');">{$current_tab|regex_replace:'!^([0-9]+\.)!':''|escape:'htmlall':'UTF-8'}</th>
                </tr>
            {/if}
            <tr class="tab-content-{$configuration->id|intval}-{$current_class|intval}">
                <td class="id center">{$field.id_field|intval}</td>
                <td class="active center">
                    <input type="checkbox" name="field_{$field.id_field|intval}_active"
                           {if $field.forced}disabled="disabled" checked="checked"
                           {elseif $field.active}checked="checked"{/if} value="1"/>
                </td>
                <td class="name">
                    {if $field.free_field}
                        <input type="text" name="field_{$field.id_field|intval}_balise"
                               value="{$field.balise|escape:'htmlall':'UTF-8'}"/>
                    {else}
                        {$field.balise|escape:'htmlall':'UTF-8'}
                    {/if}
                </td>
                <td class="default">
                    {if $field.editable || $field.free_field}
                        {if $field.values_list}
                            <select name="field_{$field.id_field|intval}_default" class="default">
                                <option value="">{l s='Choose...' mod='beezup'}</option>
                                {explode var=defaults str=$field.values_list delim='|'}
                                {if !empty($defaults)}
                                    {array_to_options content=$defaults selected=$field.default}
                                {/if}
                            </select>
                        {else}
                            <input type="text" class="default" name="field_{$field.id_field|intval}_default"
                                   value="{$field.default|escape:'htmlall':'UTF-8'}"/>
                        {/if}
                    {/if}
                </td>
                <td class="attribute_feature">{if $field.editable}
                        <select name="field_{$field.id_field|intval}_attribute_feature" class="attribute_feature">
                            <option value="">{l s='Choose...' mod='beezup'}</option>
                            <optgroup label="{l s='Features' mod='beezup'}">
                                {if $features && !empty($features)}
                                    {array_to_options content=$features selected="feat_`$field.id_feature`"}
                                {/if}
                            </optgroup>
                            <optgroup label="{l s='Attributes' mod='beezup'}">
                                {if $attribute_groups && !empty($attribute_groups)}
                                    {array_to_options content=$attribute_groups selected="attr_`$field.id_attribute_group`"}
                                {/if}
                            </optgroup>
                        </select>
                    {/if}
                </td>
                <td>
                    {if $field.free_field}
                        <a href="{$request_uri|escape:'htmlall':'UTF-8'}&action=deleteFreeField&id_free_field={$field.id_field|intval}"
                           title="{l s='Delete free field' mod='beezup'}" class="deleteFreeField">
                            <img src="../img/admin/disabled.gif" alt="{l s='Delete free field' mod='beezup'}"/>
                        </a>
                    {/if}
                </td>
            </tr>
        {/foreach}


        <tr>
            <th colspan="6" class="tab center" onclick="javascript:toggleTab('.tab-content-carriers');">Carriers</th>
        </tr>

        {assign var="last_id" value=$field.id_field+1}
        {foreach from=$available_carriers item=av_carrier}
            <tr class="tab-content-carriers">
                <td class="id center">{$last_id|intval}</td>
                <td class="active center">
                    <input type="checkbox" name="carrier_field_{$av_carrier.id_carrier|intval}"
                           {if $av_carrier.in_feed}checked{/if} value="1"/>
                </td>
                <td class="name">

                    {$av_carrier.name|escape:'htmlall':'UTF-8'}

                </td>
                <td class="default">
                    <input type="text" class="form-control" name="carrier_value_{$av_carrier.id_carrier|intval}"
                           value="{$av_carrier.feed_value|escape:'htmlall':'UTF-8'}"/>
                </td>
                <td></td>
                <td></td>
            </tr>
            {assign var="last_id" value=$last_id+1}
        {/foreach}

        </tr>


        <tr>
            <td class="center" colspan="5">
                <button href="#newFieldConfiguration" class="addNewFreeField"><i
                            class="icon-plus-circle"></i> {l s='Add new field configuration' mod='beezup'}</button>
            </td>
        </tr>


    </table>

</div>
<!-- SAVE -->
<div class="panel-footer" style="height: 82px;">
    <button type="submit" value="1" id="configuration_form_submit_btn" name="submitConfiguration"
            class="btn btn-default pull-right">
        <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
    </button>
</div>
<!-- /CONFIGURATION_FIELD.TPL -->

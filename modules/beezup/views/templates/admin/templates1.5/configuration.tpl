{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<div class="tab-content" id="tab-content-{$configuration->id|intval}">
    <form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="id_configuration" value="{$configuration->id|intval}"/>
        <div class="general">
            <table class="conf-table" style="border-collapse:collapse;">

                <tr>
                    <td class="label">{l s='Stop Current Feed Execution:' mod='beezup'}</td>
                    <td class="input">
                        <a href="{$request_uri|escape:'htmlall':'UTF-8'}&stopXmlGeneration" class="btn btn-primary">{l s='Stop' mod='beezup'}</a>
                    </td>
                </tr>


                <!-- Inactives products -->
                <tr>
                    <td class="label">{l s='Disable inactives products:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="disable_not_available" value="1"
                               {if $configuration->disable_not_available}checked="checked"{/if}/><img class="icon"
                                                                                                      src="../img/admin/enabled.gif"
                                                                                                      alt=""/>
                        <input type="radio" name="disable_not_available" value="0"
                               {if !$configuration->disable_not_available}checked="checked"{/if}/><img class="icon"
                                                                                                       src="../img/admin/disabled.gif"
                                                                                                       alt=""/>
                    </td>
                </tr>
                <!-- Inactives products -->
                <tr>
                    <td class="label">{l s='Disable unavailable products:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="disable_disabled_product" value="1"
                               {if $configuration->disable_disabled_product}checked="checked"{/if}/><img class="icon"
                                                                                                         src="../img/admin/enabled.gif"
                                                                                                         alt=""/>
                        <input type="radio" name="disable_disabled_product" value="0"
                               {if !$configuration->disable_disabled_product}checked="checked"{/if}/><img class="icon"
                                                                                                          src="../img/admin/disabled.gif"
                                                                                                          alt=""/>
                    </td>
                </tr>

                <!-- Out Of Stock products -->
                <tr>
                    <td class="label">{l s='Disable out of stock products:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="disable_oos_product" value="1"
                               {if $configuration->disable_oos_product}checked="checked"{/if}/><img class="icon"
                                                                                                    src="../img/admin/enabled.gif"
                                                                                                    alt=""/>
                        <input type="radio" name="disable_oos_product" value="0"
                               {if !$configuration->disable_oos_product}checked="checked"{/if}/><img class="icon"
                                                                                                     src="../img/admin/disabled.gif"
                                                                                                     alt=""/>
                    </td>
                </tr>

                <!-- Force product tax -->
                <tr>
                    <td class="label">{l s='Force product tax:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="force_product_tax" value="1"
                               {if $configuration->force_product_tax}checked="checked"{/if}/><img class="icon"
                                                                                                  src="../img/admin/enabled.gif"
                                                                                                  alt=""/>
                        <input type="radio" name="force_product_tax" value="0"
                               {if !$configuration->force_product_tax}checked="checked"{/if}/><img class="icon"
                                                                                                   src="../img/admin/disabled.gif"
                                                                                                   alt=""/>
                    </td>
                </tr>

                <!-- Separate product attribute -->
                <tr>
                    <td class="label">{l s='Get attributes as products:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="set_attributes_as_product" value="1"
                               {if $configuration->set_attributes_as_product}checked="checked"{/if}/><img class="icon"
                                                                                                          src="../img/admin/enabled.gif"
                                                                                                          alt=""/>
                        <input type="radio" name="set_attributes_as_product" value="0"
                               {if !$configuration->set_attributes_as_product}checked="checked"{/if}/><img class="icon"
                                                                                                           src="../img/admin/disabled.gif"
                                                                                                           alt=""/>
                    </td>
                </tr>

                <!-- Carriers -->
                <tr>
                    <td class="label">{l s='Default carrier:' mod='beezup'}</td>
                    <td class="input">
                        <select name="id_carrier">
                            {foreach item=carrier from=$carriers}
                                <option value="{$carrier.id_carrier|intval}"
                                        {if $carrier.id_carrier==$configuration->id_carrier}selected="selected"{/if}>{$carrier.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>

                <!-- Zones -->
                <tr>
                    <td class="label">{l s='Default zone:' mod='beezup'}</td>
                    <td class="input">
                        <select name="id_zone">
                            {foreach item=zone from=$zones}
                                <option value="{$zone.id_zone|intval}"
                                        {if $zone.id_zone==$configuration->id_zone}selected="selected"{/if}>{$zone.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>

                <!-- Image Type -->
                <tr>
                    <td class="label">{l s='Image type:' mod='beezup'}</td>
                    <td class="input">
                        <select name="image_type">
                            {foreach item=image_type from=$image_types}
                                <option value="{$image_type.name|escape:'htmlall':'UTF-8'}"
                                        {if $image_type.name==$configuration->image_type}selected="selected"{/if}>{$image_type.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>

                <!-- Default language -->
                <tr>
                    <td class="label">{l s='Default language:' mod='beezup'}</td>
                    <td class="input">
                        <ul>
                            {foreach item=language from=$languages}
                                <li>
                                    <input type="radio" name="id_default_lang" value="{$language.id_lang|intval}"
                                           {if $language.id_lang==$configuration->id_default_lang}checked="checked"{/if}/>
                                    <img class="icon" src="../img/l/{$language.id_lang|intval}.jpg"
                                         alt=""/>{$language.name|escape:'htmlall':'UTF-8'}
                                </li>
                            {/foreach}
                        </ul>
                    </td>
                </tr>


                <tr>
                    <td class="label">{l s='Filter Catalog by categories:' mod='beezup'}</td>
                    <td class="input">
                        <input type="radio" name="enable_filter_categories" id="enable_filter_categories_on"
                               onclick="$('#panelCategories').show();" value="1"
                               {if $enable_category_filter == 1}checked="checked"{/if}/><img class="icon"
                                                                                             src="../img/admin/enabled.gif"
                                                                                             alt=""/>
                        <input type="radio" name="enable_filter_categories" id="enable_filter_categories_off"
                               onclick="$('#panelCategories').hide();" value="0"
                               {if $enable_category_filter == 0}checked="checked"{/if}/><img class="icon"
                                                                                             src="../img/admin/disabled.gif"
                                                                                             alt=""/>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <div id="panelCategories" {if $enable_category_filter == 0}style="display:none;"{/if}>
                            {$category_tree}
                        </div>
                    </td>
                </tr>


                <!-- Save -->
                <tr>
                    <td class="label"><br/>{l s='Save:' mod='beezup'}</td>
                    <td>
                        <br/>
                        <input class="button" type="submit" name="submitConfiguration"
                               value="{l s='Save configuration' mod='beezup'}"/>
                    </td>
                </tr>
            </table>

            <hr/>

        </div>
        <div class="feed">
            {assign var=current_tab value=''}
            <table class="table config-fields" id="config-fields-{$configuration->id|intval}">
                <tr>
                    <th class="id center">{l s='ID' mod='beezup'}</th>
                    <th class="active center">{l s='Active' mod='beezup'}</th>
                    <th class="name center">{l s='XML balise name' mod='beezup'}</th>
                    <th class="default center">{l s='Default value' mod='beezup'}</th>
                    <th class="attribute_feature center">{l s='Features / Attributes' mod='beezup'}</th>
                </tr>
                {assign var=current_class value=0}
                {foreach from=$configuration->fields item=field}
                    {if $field.fields_group!=$current_tab}
                        {assign var=current_class value=$current_class+1}
                        {assign var=tmp_key value=0}
                        {assign var=current_tab value=$field.fields_group}
                        <tr>
                            <th colspan="5" class="tab center"
                                onclick="javascript:toggleTab('.tab-content-{$configuration->id|intval}-{$current_class|intval}');">{$current_tab|regex_replace:'!^([0-9]+\.)!':''|escape:'htmlall':'UTF-8'}</th>
                        </tr>
                    {/if}
                    <tr class="tab-content-{$configuration->id|intval}-{$current_class|intval} {if $tmp_key%2==1}alt-row{/if}">
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
                                <a href="{$request_uri|escape:'htmlall':'UTF-8'}&action=deleteFreeField&id_free_field={$field.id_field|intval}"
                                   title="{l s='Delete free field' mod='beezup'}" class="deleteFreeField">
                                    <img src="../img/admin/disabled.gif" alt="{l s='Delete free field' mod='beezup'}"/>
                                </a>
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
                                        {array_to_options content=$defaults selected=$field.default}
                                    </select>
                                {else}
                                    <input type="text" class="default" name="field_{$field.id_field|intval}_default"
                                           value="{$field.default|escape:'htmlall':'UTF-8'}"/>
                                {/if}
                            {/if}
                        </td>
                        <td class="attribute_feature">{if $field.editable}
                                <select name="field_{$field.id_field|intval}_attribute_feature"
                                        class="attribute_feature">
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
                    </tr>
                    {assign var=tmp_key value=$tmp_key+1}
                {/foreach}

                <tr>
                    <th colspan="6" class="tab center" onclick="javascript:toggleTab('.tab-content-carriers');">
                        Carriers
                    </th>
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
                        <br/>
                        <a href="#newFieldConfiguration" class="addNewFreeField">
                            <img src="../img/admin/add.gif" alt=""/>{l s='Add new field configuration' mod='beezup'}
                        </a>
                        <br/><br/>
                    </td>
                </tr>
            </table>
            <br/>
            <div class="center">
                <input class="button" type="submit" name="submitConfiguration"
                       value="{l s='Save configuration' mod='beezup'}"/>
            </div>
        </div>
    </form>
</div>

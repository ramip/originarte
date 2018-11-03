{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- CONFIGURATION_MAIN.TPL -->

<input type="hidden" name="id_configuration" value="{$configuration->id|intval}"/>


<!-- OUT OF STOCK PRODUCTS -->
<div class="form-group">
    <label for="disable_oos_product"
           class="control-label col-sm-3">{l s='Disable out of stock products:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="disable_oos_product" id="disable_oos_product_on" value="1"
                                   {if $configuration->disable_oos_product}checked="checked"{/if}/>
							<label for="disable_oos_product_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="disable_oos_product" id="disable_oos_product_off" value="0"
                                   {if !$configuration->disable_oos_product}checked="checked"{/if}/>
							<label for="disable_oos_product_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>

<!-- INACTIVE PRODUCTS -->
<div class="form-group">
    <label for="disable_disabled_product"
           class="control-label col-sm-3">{l s='Disable inactives products:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="disable_disabled_product" id="disable_disabled_product_on"
                                   value="1" {if $configuration->disable_disabled_product}checked="checked"{/if}/>
							<label for="disable_disabled_product_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="disable_disabled_product" id="disable_disabled_product_off"
                                   value="0" {if !$configuration->disable_disabled_product}checked="checked"{/if}/>
							<label for="disable_disabled_product_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>

<!-- INACTIVE PRODUCTS -->
<div class="form-group">
    <label for="disable_not_available"
           class="control-label col-sm-3">{l s='Disable unavailable products:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="disable_not_available" id="disable_not_available_on" value="1"
                                   {if $configuration->disable_not_available}checked="checked"{/if}/>
							<label for="disable_not_available_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="disable_not_available" id="disable_not_available_off" value="0"
                                   {if !$configuration->disable_not_available}checked="checked"{/if}/>
							<label for="disable_not_available_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>


<!-- Force product tax   -->
<div class="form-group">
    <label for="force_product_tax" class="control-label col-sm-3">{l s='Force product tax:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="force_product_tax" id="force_product_tax_on" value="1"
                                   {if $configuration->force_product_tax}checked="checked"{/if}/>
							<label for="force_product_tax_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="force_product_tax" id="force_product_tax_off" value="0"
                                   {if !$configuration->force_product_tax}checked="checked"{/if}/>
							<label for="force_product_tax_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>

<!-- Separate product attribute -->
<div class="form-group">
    <label for="set_attributes_as_product"
           class="control-label col-sm-3">{l s='Get attributes as products:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="set_attributes_as_product" id="set_attributes_as_product_on"
                                   value="1" {if $configuration->set_attributes_as_product}checked="checked"{/if}/>
							<label for="set_attributes_as_product_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="set_attributes_as_product" id="set_attributes_as_product_off"
                                   value="0" {if !$configuration->set_attributes_as_product}checked="checked"{/if}/>
							<label for="set_attributes_as_product_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>

<!-- Carriers -->
<div class="form-group">
    <label for="id_carrier" class="control-label col-sm-3">{l s='Default carrier:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-3">
                <select name="id_carrier" class="id_carrier">
                    {foreach item=carrier from=$carriers}
                        <option value="{$carrier.id_carrier|intval}"
                                {if $carrier.id_carrier==$configuration->id_carrier}selected="selected"{/if} > {$carrier.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Zones -->
<div class="form-group">
    <label for="id_zone" class="control-label col-sm-3">{l s='Default zone:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-3">
                <select name="id_zone" class="id_zone">
                    {foreach item=zone from=$zones}
                        <option value="{$zone.id_zone|intval}"
                                {if $zone.id_zone==$configuration->id_zone}selected="selected"{/if}>{$zone.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Image Type -->
<div class="form-group">
    <label for="image_type" class="control-label col-sm-3">{l s='Image type:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="col-sm-3">
                <select name="image_type" class="image_type">
                    {foreach item=image_type from=$image_types}
                        <option value="{$image_type.name|escape:'htmlall':'UTF-8'}"
                                {if $image_type.name==$configuration->image_type}selected="selected"{/if}>{$image_type.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Default language  -->
<div class="form-group">
    <label for="image_type" class="control-label col-sm-3">{l s='Default language:' mod='beezup'}</label>
    <div class="col-sm-4">
        {foreach item=language from=$languages}
            <br>
            <input type="radio" name="id_default_lang" value="{$language.id_lang|intval}"
                   {if $language.id_lang==$configuration->id_default_lang}checked="checked"{/if}/>
            <img class="icon" src="../img/l/{$language.id_lang|intval}.jpg" alt=""/>
            {$language.name|escape:'htmlall':'UTF-8'}
        {/foreach}
    </div>
</div>


<div class="form-group">
    <label for="enable_filter_categories"
           class="control-label col-sm-3">{l s='Filter Catalog by categories:' mod='beezup'}</label>
    <div class="col-sm-9">
        <div class="row">
            <div class="input-group col-sm-2 col-xs-12">
						<span class="switch prestashop-switch">
							<input type="radio" name="enable_filter_categories" id="enable_filter_categories_on"
                                   onclick="$('#panelCategories').show();" value="1"
                                   {if $enable_category_filter == 1}checked="checked"{/if}/>
							<label for="enable_filter_categories_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="enable_filter_categories" id="enable_filter_categories_off"
                                   onclick="$('#panelCategories').hide();" value="0"
                                   {if $enable_category_filter == 0}checked="checked"{/if}/>
							<label for="enable_filter_categories_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
            </div>
        </div>
    </div>
</div>

<div id="panelCategories" {if $enable_category_filter == 0}style="display:none;"{/if}>
    {$category_tree}
</div>

<!-- SAVE -->
<div class="panel-footer" style="height: 82px;">
    <button type="submit" value="1" id="configuration_form_submit_btn" name="submitConfiguration"
            class="btn btn-default pull-right">
        <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
    </button>
</div>
<br/>
<!-- /CONFIGURATION_MAIN.TPL -->

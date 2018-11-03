{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- DEBUG.TPL -->
<form method="post" action="{$request_uri|escape:'htmlall':'UTF-8'}">

    <div class="panel">
        <h3><i class="icon-wrench"></i> {l s='Advanced options' mod='beezup'}</h3>
        <div class="form-group">
            <label class="control-label col-sm-3">{l s='Stop Current Feed Execution:' mod='beezup'}</label>
            <a href="{$request_uri|escape:'htmlall':'UTF-8'}&stopXmlGeneration" class="btn btn-primary">{l s='Stop' mod='beezup'}</a>
        </div>


        <div class="form-group">
            <label for="BEEZUP_DEBUG_MODE" class="control-label col-lg-3">{l s='DEBUG Mode:' mod='beezup'}</label>
            <div class="col-lg-9">
                <div class="row">
                    <div class="input-group col-lg-2">
						<span class="switch prestashop-switch">
							<input type="radio" name="BEEZUP_DEBUG_MODE" id="BEEZUP_DEBUG_MODE_on" value="1"
                                   {if $beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/>
							<label for="BEEZUP_DEBUG_MODE_on">{l s='Yes' mod='beezup'}</label>
							<input type="radio" name="BEEZUP_DEBUG_MODE" id="BEEZUP_DEBUG_MODE_off" value="0"
                                   {if !$beezup_conf.BEEZUP_DEBUG_MODE}checked="checked"{/if}/>
							<label for="BEEZUP_DEBUG_MODE_off">{l s='No' mod='beezup'}</label>
							<a class="slide-button btn btn-default"></a>
						</span>
                    </div>
                    <p class="help-block">{l s='Set enabled to display error repporting (should be desabled in production shop)' mod='beezup'}</p>
                </div>
            </div>
        </div>
        <div class="clear"></div>

        <!-- MEMORY LIMIT -->
        <div class="form-group col-lg-12">
            <label for="BEEZUP_MEMORY_LIMIT" class="control-label col-lg-3">{l s='Memory limit:' mod='beezup'}</label>
            <div class="col-lg-1">
                <input type="text" name="BEEZUP_MEMORY_LIMIT"
                       value="{$beezup_conf.BEEZUP_MEMORY_LIMIT|escape:'htmlall':'UTF-8'}" size="8" maxsize="8"/>
            </div>


            <p class="help-block"><a href="http://docs.php.net/manual/ini.core.php#ini.memory-limit"
                                     target="_blank"/>{l s='Documentation' mod='beezup'}</a></p>

        </div>
        <br/>
        <div class="clear"></div>
        <!-- TIME LIMIT -->
        <div class="form-group col-lg-12">
            <label for="BEEZUP_TIME_LIMIT" class="control-label col-lg-3">{l s='Time limit:' mod='beezup'}</label>
            <div class="col-lg-1">
                <input type="text" name="BEEZUP_TIME_LIMIT"
                       value="{$beezup_conf.BEEZUP_TIME_LIMIT|escape:'htmlall':'UTF-8'}" size="8" maxsize="8"/>
            </div>

            <p class="help-block"><a href="http://docs.php.net/manual/function.set-time-limit.php"
                                     target="_blank"/>{l s='Documentation' mod='beezup'}</a></p>

        </div>
        <div class="clear"></div>
        <!-- Batch size -->
        <div class="form-group col-lg-12">
            <label for="BEEZUP_BATCH_SIZE" class="control-label col-lg-3">{l s='Batch size:' mod='beezup'}</label>
            <div class="col-lg-1">
                <input type="text" name="BEEZUP_BATCH_SIZE"
                       value="{$beezup_conf.BEEZUP_BATCH_SIZE|escape:'htmlall':'UTF-8'}" size="8" maxsize="8"/>
            </div>
            <p class="help-block">{l s='Product processing batch size' mod='beezup'}</p>

        </div>
        <br/>
        <div class="clear"></div>
        <p>
            <small>phing;beezup;Atlas;2015-02-09 18:57:32;v2.4-om;f87d039c467030ca655c145ec43f988f10718536;3.0-beta-11
            </small>
        </p>
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submitDebug"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='beezup'}
            </button>
        </div>
</form>
</div>
<!-- /DEBUG.TPL -->
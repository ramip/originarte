{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- TESTS.TPL -->
<div class="panel panel-default" id="beezup-tests">
    <h3>
        <i class="{if $time_test && $memory_test && $log_file_test && $cache_file_test}icon-check-sign alert-success{else}icon-warning-sign alert-danger{/if}"></i>&nbsp;{l s='Tests' mod='beezup'}
    </h3>

    <div class="panel-body">
        <div class="col-lg-12">
            <div class="col-lg-1" style="float: left;padding-top: 3px;">
                <i class="{if $memory_test}icon-check-sign alert-success{else}icon-warning-sign alert-danger{/if}"> </i>
            </div>
            <label for="" class="control-label col-lg-2">{l s='Memory limit test:' mod='beezup'}</label>
            <div class="col-lg-9"><strong>{$memory_limit|escape:'htmlall':'UTF-8'}o</strong> {l s='extensible to:' mod='beezup'}
                <strong>{$memory_limit_ext|escape:'htmlall':'UTF-8'}o</strong>
                {if !$memory_test}
                    <div class="help-block">
                        {l s='The generation of xml feed may require more memory than your server allows. If problems occur, change the value of "memory_limit" in php.ini' mod='beezup'}
                    </div>
                {/if}
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-lg-12">
            <div class="col-lg-1" style="float: left;padding-top: 3px;">
                <i class="{if $time_limit}icon-check-sign alert-success{else}icon-warning-sign alert-danger{/if}"> </i>
            </div>
            <label for="" class="control-label col-lg-2">{l s='Time limit test:' mod='beezup'}</label>
            <div class="col-lg-9"><strong>{$time_limit|escape:'htmlall':'UTF-8'}s</strong> {l s='extensible to:' mod='beezup'}
                <strong>{$time_limit_ext|escape:'htmlall':'UTF-8'}s</strong>
                {if !$time_limit}
                    <div class="help-block">
                        {l s='The generation of xml feed may require more time than your server allows. If problems occur, change the value of "max_execution_time" in php.ini' mod='beezup'}
                    </div>
                {/if}
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-lg-12">
            <div class="col-lg-1" style="float: left;padding-top: 3px;">
                <i class="{if $log_file_test}icon-check-sign alert-success{else}icon-warning-sign alert-danger{/if}"> </i>
            </div>
            <label for="" class="control-label col-lg-2">{l s='Log file test:' mod='beezup'}</label>
            <div class="col-lg-9">
                <strong>{if $log_file_test}{l s='Log file is writable' mod='beezup'}{else}{l s='Log file is not writable' mod='beezup'}{/if}</strong>
                {if !$log_file_test}
                    <div class="help-block">
                        {l s='Without a writable log file, beezup module can\'t log access and configuration actions' mod='beezup'}
                        <br/>
                        {l s='Set /modules/beezup/views/log/log.txt file permissions to 077 to solve this problem.' mod='beezup'}
                    </div>
                {/if}
            </div>
        </div>
        <div class="clear"></div>
        <div class="col-lg-12">
            <div class="col-lg-1" style="float: left;padding-top: 3px;">
                <i class="{if $cache_file_test}icon-check-sign alert-success{else}icon-warning-sign alert-danger{/if}"> </i>
            </div>
            <label for="" class="control-label col-lg-2">{l s='Cache directory test:' mod='beezup'}</label>
            <div class="col-lg-9">
                <strong>{if $cache_file_test}{l s='Cache directory is writable' mod='beezup'}{else}{l s='Cache directory is not writable' mod='beezup'}{/if}</strong>
                {if !$cache_file_test}
                    <div class="help-block">
                        {l s='Without a writable cache directory, beezup module can\'t use cache and CRON options' mod='beezup'}
                        <br/>
                        {l s='Set /modules/beezup/views/cache file permissions to 077 to solve this problem.' mod='beezup'}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
<!-- /TESTS.TPL -->
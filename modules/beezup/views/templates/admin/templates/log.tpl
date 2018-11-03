{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- LOG.TPL -->

{if $log}
    <div class="panel">
        <h3><i class="icon-list-alt"></i>&nbsp;{l s='Log' mod='beezup'}</h3>
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="col-lg-1">{l s='ID' mod='beezup'}</th>
                <th class="col-lg-1">{l s='Date' mod='beezup'}</th>
                <th class="col-lg-1">{l s='IP' mod='beezup'}</th>
                <th class="col-lg-1">{l s='Time' mod='beezup'}</th>
                <th class="col-lg-1">{l s='Memory' mod='beezup'}</th>
                <th class="col-lg-4">{l s='Action' mod='beezup'}</th>

            </tr>
            </thead>
            <tbody>
            {foreach item=entry from=$log key=k}
                {if $entry[2]}
                    <tr>
                        <td class="id">{if isset($entry[2])}{$entry[2]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="date">{$entry[0]|escape:'htmlall':'UTF-8'}</td>
                        <td class="ip">{$entry[1]|escape:'htmlall':'UTF-8'}</td>
                        <td class="time">{if isset($entry[4])}{$entry[4]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="memory">{if isset($entry[5])}{$entry[5]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="action">{if isset($entry[3])}{$entry[3]|escape:'htmlall':'UTF-8'}{/if}</td>
                    </tr>
                {/if}
            {/foreach}
            </tbody>
        </table>
    </div>
{/if}
<!-- /LOG.TPL -->

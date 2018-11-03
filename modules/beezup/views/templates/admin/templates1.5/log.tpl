{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

{if $log}
    <fieldset id="fieldsetLog"
              style="width: 48%; float: left; margin: 1px 1% 15px 1%; box-sizing: border-box; min-height: 350px">
        <legend><img src="../img/admin/details.gif" alt=""/>{l s='Log' mod='beezup'}</legend>
        <table class="table" id="beezup-log-table">
            <tr>
                <th class="id">{l s='ID' mod='beezup'}</th>
                <th class="date">{l s='Date' mod='beezup'}</th>
                <th class="ip">{l s='IP' mod='beezup'}</th>
                <th class="time">{l s='Time' mod='beezup'}</th>
                <th class="memory">{l s='Memory' mod='beezup'}</th>
                <th class="action">{l s='Action' mod='beezup'}</th>
            </tr>
            {assign var=first value=1}
            {foreach item=entry from=$log key=k}
                {if $entry[2]}
                    <tr class="{if $first}{assign var=first value=0}last {/if}{if $k%2==1}log-row{else}alt-log-row{/if}">
                        <td class="id">{if isset($entry[2])}{$entry[2]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="date">{$entry[0]|escape:'htmlall':'UTF-8'}</td>
                        <td class="ip">{$entry[1]|escape:'htmlall':'UTF-8'}</td>
                        <td class="time">{if isset($entry[4])}{$entry[4]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="memory">{if isset($entry[5])}{$entry[5]|escape:'htmlall':'UTF-8'}{/if}</td>
                        <td class="action">{if isset($entry[3])}{$entry[3]|escape:'htmlall':'UTF-8'}{/if}</td>
                    </tr>
                {/if}
            {/foreach}
        </table>
    </fieldset>
{/if}

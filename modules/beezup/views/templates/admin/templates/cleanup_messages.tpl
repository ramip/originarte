{*
* @author      BeezUP <support@beezup.com>
* @copyright   2018 BeezUP
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @category    BeezUP
* @package     beezup
*}

<!-- CLEANUP MESSAGES //-->
{if isset($cleanup_messages) && is_array($cleanup_messages)}
    <div class="beezup cleanup_messages">
        {foreach $cleanup_messages as  $message}
            <div class="{$message.class|escape:'htmlall':'UTF-8'}">
                {$message.text|escape:'htmlall':'UTF-8'}
                {if isset($message.action)}
                    <span class="beezup action"> :
                        {if isset($message.action_url)}<a href="{$message.action_url|escape:'htmlall':'UTF-8'}">{/if}
                            {$message.action|escape:'htmlall':'UTF-8'}
                            {if isset($message.action_url)}</a>{/if}
					</span>
                {/if}
            </div>
        {/foreach}
    </div>
{/if}
<!-- /CLEANUP MESSAGES //-->
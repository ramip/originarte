<!-- FAQ Module -->
<p>
	<a href="{$module_dir}faqs.php" title="{l s='Frequently ask questions' mod='faq'}">{l s='FAQ' mod='faq'}</a>
</p>
<ul class="submenu" style="display: none;">
{foreach from=$faqs item=faq key=key name=loop}
	<li><a href="{$module_dir}faqs.php?faq_question={$key}">{$faq}</a></li>
{/foreach}
</ul>
<!-- /FAQ Module -->

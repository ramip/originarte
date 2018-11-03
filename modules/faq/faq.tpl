<!-- FAQ Module -->
{capture name=path}{l s='FAQ' mod='faq'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Frequently ask questions' mod='faq'}</h2>

{if $faqs && count($faqs) > 0}
<script type="text/javascript" src="{$module_dir}lib/ddaccordion.js"></script>
<script type="text/javascript">
{literal}
ddaccordion.init({
	headerclass: "faq_question", //Shared CSS class name of headers group
	contentclass: "faq_answer", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: true, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
});
{/literal}
</script>
<div class="applemenu">
{foreach from=$faqs item=faq name=loop}
	<div class="faq_question">{$smarty.foreach.loop.iteration}.&nbsp;{$faq.question}</div>
	<div class="faq_answer">
		{$faq.answer}
		<br />
	</div> 
{/foreach}
</div>
{else}
	<p class="warning">{l s='None questions yet.' mod='faq'}</p>
{/if}
<!-- /FAQ Module -->

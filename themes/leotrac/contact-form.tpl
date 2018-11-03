{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Contact'}{/capture}
<h1 class="title_block">{if isset($customerThread) && $customerThread}{l s='Your reply'}{else}{l s='Contact us'}{/if}</h1>
<br />
	<div class="contact-data">
        <div class="contact-left">
               
            <iframe style="width:100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.es/maps?f=q&amp;source=s_q&amp;hl=es&amp;geocode=&amp;q=valencia+663,+barcelona&amp;sll=41.412319,2.189498&amp;sspn=0.012246,0.026007&amp;gl=es&amp;g=C%2F+Val%C3%A8ncia,+663,+08026+Barcelona&amp;ie=UTF8&amp;hq=&amp;hnear=C%2F+Val%C3%A8ncia,+663,+08026+Barcelona&amp;t=m&amp;ll=41.412093,2.189412&amp;spn=0.011265,0.018024&amp;z=15&amp;output=embed"></iframe><br /><small><a href="https://maps.google.es/maps?f=q&amp;source=embed&amp;hl=es&amp;geocode=&amp;q=valencia+663,+barcelona&amp;sll=41.412319,2.189498&amp;sspn=0.012246,0.026007&amp;gl=es&amp;g=C%2F+Val%C3%A8ncia,+663,+08026+Barcelona&amp;ie=UTF8&amp;hq=&amp;hnear=C%2F+Val%C3%A8ncia,+663,+08026+Barcelona&amp;t=m&amp;ll=41.412093,2.189412&amp;spn=0.011265,0.018024&amp;z=15" style="margin-top:-5px; font-size:0.8em; color:006991">{l s='View larger map'}</a></small><br />

        </div>
        
        <div class="contact-right">
            <p>
                <span>C/ Valencia 663 (El Clot)<br />
				08026 - Barcelona</span>
            </p>
              
            <p>
                {l s='Email'}<span>: info@originarte.com</span><br />
                {l s='Phone'}<span>: +34 934 852 652</span><br /></p>

            <p>    {l s='Opening hours'} <br />
                <span>{l s='Monday to Friday'}<br />
                {l s='From'} 10:00 {l s='to'} 14:00 {l s='and from'} 15:30 {l s='to'} 18:00.<br /></span>
            </p>
            <p>   <span> Viernes de 10:00 a 14:00 </span><br /></p>
        </div>
        <div class="clear"></div>
    </div>

{if isset($confirmation)}
    <p>{l s='Your message has been successfully sent to our team.'}</p>
    <ul class="footer_links">
        <li><a href="{$base_dir}"><img class="icon" alt="" src="{$img_dir}icon/home.gif"/></a><a href="{$base_dir}">{l s='Home'}</a></li>
    </ul>
{elseif isset($alreadySent)}
    <p>{l s='Your message has already been sent.'}</p>
    <ul class="footer_links">
        <li><a href="{$base_dir}"><img class="icon" alt="" src="{$img_dir}icon/home.gif"/></a><a href="{$base_dir}">{l s='Home'}</a></li>
    </ul>
{else}
    {include file="$tpl_dir./errors.tpl"}
    <form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std  form-horizontal" enctype="multipart/form-data">
        <fieldset>
            <p>{l s='For questions about an order or for more information about our products'}.</p>
            <div class="control-group select">
                <label class="control-label" for="id_contact">{l s='Subject Heading'}</label>
                <div class="controls">
                    {if isset($customerThread.id_contact)}
                        {foreach from=$contacts item=contact}
                            {if $contact.id_contact == $customerThread.id_contact}
                                <input class="input-xlarge" type="text" id="contact_name" name="contact_name" value="{$contact.name|escape:'htmlall':'UTF-8'}" readonly />
                                <input type="hidden" name="id_contact" value="{$contact.id_contact}" />
                            {/if}
                        {/foreach}

                    {else}
                        <select class="input-xlarge" id="id_contact" name="id_contact" onchange="showElemFromSelect('id_contact', 'desc_contact')">
                            <option value="0">{l s='-- Choose --'}</option>
                            {foreach from=$contacts item=contact}
                                <option value="{$contact.id_contact|intval}" {if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>

                        <p id="desc_contact0" class="desc_contact">&nbsp;</p>
                        {foreach from=$contacts item=contact}
                            <p id="desc_contact{$contact.id_contact|intval}" class="desc_contact" style="display:none;">
                                {$contact.description|escape:'htmlall':'UTF-8'}
                            </p>
                        {/foreach}
                    {/if}
                </div>
            </div>

            <div class="control-group text">
                <label class="control-label" for="email">{l s='Email address'}</label>
                <div class="controls">
                    {if isset($customerThread.email)}
                        <input class="input-xlarge" type="text" id="email" name="from" value="{$customerThread.email|escape:'htmlall':'UTF-8'}" readonly />
                    {else}
                        <input class="input-xlarge" type="text" id="email" name="from" value="{$email|escape:'htmlall':'UTF-8'}" />
                    {/if}
                </div>
            </div>
            {if !$PS_CATALOG_MODE}
                {if (!isset($customerThread.id_order) || $customerThread.id_order > 0)}
                    <div class="control-group text select">
                        <label class="control-label" for="id_order">{l s='Order reference'}</label>
                        <div class="controls">
                            {if !isset($customerThread.id_order) && isset($isLogged) && $isLogged == 1}
                                <select class="input-xlarge" name="id_order" >
                                    <option value="0">{l s='-- Choose --'}</option>
                                    {foreach from=$orderList item=order}
                                        <option value="{$order.value|intval}" {if $order.selected|intval}selected="selected"{/if}>{$order.label|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            {elseif !isset($customerThread.id_order) && empty($isLogged)}
                                <input class="input-xlarge" type="text" name="id_order" id="id_order" value="{if isset($customerThread.id_order) && $customerThread.id_order|intval > 0}{$customerThread.id_order|intval}{else}{if isset($smarty.post.id_order) && !empty($smarty.post.id_order)}{$smarty.post.id_order|intval}{/if}{/if}" />
                            {elseif $customerThread.id_order|intval > 0}
                                <input class="input-xlarge" type="text" name="id_order" id="id_order" value="{$customerThread.id_order|intval}" readonly />
                            {/if}
                        </div>
                    </div>
                {/if}
                {if isset($isLogged) && $isLogged}
                    <div class="control-group text select">
                        <label class="control-label" for="id_product">{l s='Product'}</label>
                        <div class="controls">
                            {if !isset($customerThread.id_product)}
                                {foreach from=$orderedProductList key=id_order item=products name=products}
                                    <select class="input-xlarge" name="id_product" id="{$id_order}_order_products" class="product_select" style="{if !$smarty.foreach.products.first} display:none; {/if}" {if !$smarty.foreach.products.first}disabled="disabled" {/if}>
                                        <option value="0">{l s='-- Choose --'}</option>
                                        {foreach from=$products item=product}
                                            <option value="{$product.value|intval}">{$product.label|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                {/foreach}
                            {elseif $customerThread.id_product > 0}
                                <input class="input-xlarge" type="text" name="id_product" id="id_product" value="{$customerThread.id_product|intval}" readonly />
                            {/if}
                        </div>
                    </div>
                {/if}
            {/if}
            {if $fileupload == 1}
                <div class="control-group text">
                    <label class="control-label" for="fileUpload">{l s='Attach File'}</label>
                    <div class="controls">
                        <input class="input-xlarge" type="hidden" name="MAX_FILE_SIZE" value="2000000" />
                        <input class="input-xlarge" type="file" name="fileUpload" id="fileUpload" />
                    </div>
                </div>
            {/if}
            <div class="control-group textarea">
                <label class="control-label" for="message">{l s='Message'}</label>
                <div class="controls ">
                    <textarea class="input-xlarge" id="message" name="message" rows="5" cols="10">{if isset($message)}{$message|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
                </div>
            </div>
            <div class="control-group submit">
                <input type="hidden" name="url" value="" class="hidden" /> {* add by rpc *}
                <input type="hidden" name="contactKey" value="{$contactKey}" />  {* add by rpc *}
                <div class="controls">
                    <input class="input-xlarge" type="submit" name="submitMessage" id="submitMessage" value="{l s='Send'}" class="button_large" />
                </div>
            </div>
        </fieldset>
    </form>
{/if}
				

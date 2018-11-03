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

{if isset($cms) && !isset($cms_category)}
	{if !$cms->active}
		<br />
		<div id="admin-action-cms">
			<p>{l s='This CMS page is not visible to your customers.'}
			<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
			<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
			</p>
			<div class="clear" ></div>
			<p id="admin-action-result"></p>
			</p>
		</div>
	{/if}
	<div class="cms-wrapper">
	
		<div class="rte{if $content_only} content_only{/if}">
			{$cms->content}
            
            {if $cms->id==9}

				<form action="" method="POST" class="std  form-horizontal presu">
                	<fieldset>
                        <div class="control-group text">
                            <label for="nombre" class="control-label">{l s='Nombre'}</label>
                            <div class="controls">
                                <input type="text" name="nombre" id="nombre" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="email" class="control-label">{l s='Email'}</label>
                            <div class="controls">
                                <input type="text" name="email" id="email" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="product" class="control-label">{l s='Product (magazine, flyer...)'}</label>
                            <div class="controls">
                                <input type="text" name="producto" id="cantidad" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="cantidad" class="control-label">{l s='Quantity'}</label>
                            <div class="controls">
                                <input type="text" name="cantidad" id="cantidad" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group select">
                            <label for="color" class="control-label">{l s='Color'}</label>
                            <div class="controls">
                                <select name="color" id="color" class="input-xlarge">
                                    <option value="4+4">{l s='Full Color both sides (4+4)'}</option>
                                    <option value="4+0">{l s='Full Color one side (4+0)'}</option>
                                    <option value="2+2">{l s='PANTONE (2+2)'}</option>
                                    <option value="2+0">{l s='PANTONE (2+0)'}</option>
                                    <option value="otro">{l s='Other'}</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="otrocolor" class="control-label">{l s='If you select Other tell us which'}</label>
                            <div class="controls">
                                <input type="text" name="otrocolor" id="otrocolor" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="formato" class="control-label">{l s='Format (open)'}</label>
                            <div class="controls">
                                <input type="text" name="formato" id="formato" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="formatocerrado" class="control-label">{l s='Format (closed, in the case of dyptichs, maps, magazines...)'}</label>
                            <div class="controls">
                                <input type="text" name="formatocerrado" id="formatocerrado" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="papel" class="control-label">{l s='Paper'}</label>
                            <div class="controls">
                                <input type="text" name="papel" id="papel" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="papelportada" class="control-label">{l s='Cover Paper'}</label>
                            <div class="controls">
                                <input type="text" name="papelportada" id="papelportada" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group text">
                            <label for="acabado" class="control-label">{l s='Finish'}</label>
                            <div class="controls">
                                <input type="text" name="acabado" id="acabado" class="input-xlarge">
                            </div>
                        </div>
                        <div class="control-group textarea">
                            <label for="mensaje" class="control-label">{l s='Message'}</label>
                            <div class="controls ">
                                <textarea cols="10" rows="5" name="mensaje" id="mensaje" class="input-xlarge"></textarea>
                            </div>
                        </div>
    
                        <span class="bots">
                            <p>{l s='Bots'}</p> 
                            <input type="text" name="bots">
                        </span>
                        <div class="control-group text">
                            <label for="newsletter" class="control-label"><input type="checkbox" name="newsletter" value="newsletter" checked="checked"></label>
                            <div class="controls newscheck">
                            	{l s='I would like to receive information about offers.'}
                            </div>
                        </div>
                        <div class="control-group submit">
                            <div class="controls">
                                <input type="submit" value="{l s='Send'}" id="customcontact" name="customcontact" class="input-xlarge">
                            </div>
                        </div>
                    </fieldset>
                </form>

			{/if}
	
		</div>
	</div>
{elseif isset($cms_category)}
	<div class="block-cms">
		<h1><a href="{if $cms_category->id eq 1}{$base_dir}{else}{$link->getCMSCategoryLink($cms_category->id, $cms_category->link_rewrite)}{/if}">{$cms_category->name|escape:'htmlall':'UTF-8'}</a></h1>
		{if isset($sub_category) && !empty($sub_category)}	
			<p class="title_block">{l s='List of sub categories in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}">{$subcategory.name|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
		{if isset($cms_pages) && !empty($cms_pages)}
		<p class="title_block">{l s='List of pages in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet">
				{foreach from=$cms_pages item=cmspages}
					<li>
						<a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'htmlall':'UTF-8'}">{$cmspages.meta_title|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	<div class="error">
		{l s='This page does not exist.'}
	</div>
{/if}
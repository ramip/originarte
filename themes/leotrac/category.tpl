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

{include file="$tpl_dir./errors.tpl"}

{if isset($category)}
	{if $category->id AND $category->active}

{*
		
		{if $scenes || $category->description || $category->id_image}
		
		<div class="content_scene_cat">
			{if $scenes}
				<!-- Scenes -->
				{include file="$tpl_dir./scenes.tpl" scenes=$scenes}
			{else}
				<!-- Category image -->
				{if $category->id_image}
				<div class="align_center">
					<img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" id="categoryImage" />
				</div>
				{/if}
			{/if}

			{if $category->description}
				<div class="cat_desc">
				{if strlen($category->description) > 120}
					<div id="category_description_short">{$description_short}</div>
					<div id="category_description_full" style="display:none;">{$category->description}</div>
					<a href="#" onclick="$('#category_description_short').hide(); $('#category_description_full').show(); $(this).hide(); return false;" class="lnk_more">{l s='More'}</a>
				{else}
					<div>{$category->description}</div>
				{/if}
				</div>
			{/if}
		</div>
		
		{/if}

		{if isset($subcategories)}
		<!-- Subcategories -->
		<div id="subcategories" class="blue">
			<h3 class="title_block"><span>{l s='Subcategories'}</span></h3>
			<div class="inline_list">
			{foreach from=$subcategories item=subcategory name=subcategories}
				{if $subcategory@iteration%4==1}
				<div class="row-fluid">
				{/if}
					
				<div class="span3 subcategories">
					<div class="subcategories-container clearfix">
						<div class="img_subcate">
						<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}" class="img">
							{if $subcategory.id_image}
								<img src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')|escape:'html'}" alt=""/>
							{else}
								<img src="{$img_cat_dir}default-medium_default.jpg" alt="" />
							{/if}
						</a>
						</div>
						<div class="right_block">
							<h3 class="s_title_block"><a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" class="cat_name">{$subcategory.name|escape:'htmlall':'UTF-8'}</a></h3>
							{if $subcategory.description}
								<div class="cat_desc">{$subcategory.description}</div>
							{/if}
						</div>
					</div>
				</div>
				{if $subcategory@iteration%4==0||$smarty.foreach.subcategories.last}
				</div>
				{/if}
			{/foreach}
			</div>
		
		</div>
		{/if}
*}		
		<h1 class="title_category">
			{strip}
				{$category->name|escape:'htmlall':'UTF-8'}
				{if isset($categoryNameComplement)}
					{$categoryNameComplement|escape:'htmlall':'UTF-8'}
				{/if}
			{/strip}
			 
			<span class="resumecat category-product-count">
			/	{include file="$tpl_dir./category-count.tpl"}
			</span>
		</h1>
{* block add by show blocklayered Block *}
{if $smarty.get.id_category != '27'}
    {hook h='displayLayeredTop'}
{/if}
<div class="clear:both"></div>	
{if $smarty.get.id_category == '27'}
	<img class="cabecera-diseno" src="https://originarte.com/img/cms/org-blog-encabezado.jpg">
{/if}	
		{if $products}
			<div class="content_sortPagiBar">
				<div class="row-fluid sortPagiBar">
					<div class="span3 hidden-phone">
						<div class="inner pull-left">
							<span class="title_view hidden-phone">View : </span>
						  <div class="btn-group" id="productsview">
							<a class="leo_btn last" href="#" rel="view-grid"><i class="icon-th active"></i></a>
							<a class="leo_btn first" href="#"  rel="view-list"><i class="icon-th-list"></i></a>
						  </div>
						</div>
					</div>
					<div class="span7 hidden-phone">
						<div class="inner">
						{include file="./product-sort.tpl"}
						
						</div>
					</div>
					 <div class="span2"><div class="inner">
					{include file="./product-compare.tpl"}
					</div></div>
				</div>
			</div>
			{include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar bottom-compare">
				<div class="sortPagiBar clearfix row-fluid">
					{include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
					<div class="span4">
						<div class="inner">
							{include file="./nbr-product-page.tpl" paginationId='bottom'}
						</div>						
					</div>
					<div class="span2 pull-right" style="display:none">
						<div class="inner">
							{include file="./product-compare.tpl" paginationId='bottom'}	
						</div>					
					</div>
				</div>
			</div>
			
		{/if}
		{elseif $category->id}
			<p class="warning">{l s='This category is currently unavailable.'}</p>
		
	{/if}


{/if}

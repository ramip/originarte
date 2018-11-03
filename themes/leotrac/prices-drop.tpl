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

{capture name=path}{l s='Price drop'}{/capture}


<h1 class="title_block title_pridrop">{l s='Price drop'}</h1>

{if $products}
	<div class="content_sortPagiBar">
		<div class="sortPagiBar row-fluid">
			<div class="span3 hidden-phone">
				<div class="inner pull-left">
					<span class="title_view">View : </span>
				  <div class="btn-group" id="productsview">
					<a class="leo_btn last" href="#" rel="view-grid"><i class="icon-th active"></i></a>
					<a class="leo_btn first" href="#"  rel="view-list"><i class="icon-th-list"></i></a>
				  </div>
				</div>
			</div>
			<div class="span7 hidden-phone">
				<div class="inner">
				{include file="./product-sort.tpl"}
				{include file="./nbr-product-page.tpl"}
				</div>
			</div>
		
			<div class="span2">
				<div class="inner">
					{include file="./product-compare.tpl"}
				</div>
			</div>
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
			<div class="span2 pull-right">
				<div class="inner">
					{include file="./product-compare.tpl" paginationId='bottom'}	
				</div>					
			</div>
		</div>
	</div>
{else}
	<p class="warning">{l s='No price drop.'}</p>
{/if}
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
<table>
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444">
                    
<table border="0">
  <tr>
    <td>
{if $formato == 'invoice'}      
    <table border="0" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td style="width:300px;border:1px solid #111;font-size:28px;" align="center"><br>&nbsp;<br>Ingresos en La Caixa: <strong>IBAN ES80 2100 3200 9122 0126 2288</strong><br>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
{/if}    
   </td>
  </tr>
  <tr>
    <td><table border="0">
      <tr>
        <td style="border-top:1px solid #111;">&nbsp;</td>
      </tr>
      <tr>
        <td>Originarte Diseño e Impresión S.L. - N.I.F: B64196413</td>
      </tr>
      <!--tr>
        <td>&nbsp;</td>
      </tr-->
      <tr>
        <td style="text-align:center; background-color:#dddddd; padding:10px;">&nbsp;<br>C/ Valencia 663 (El Clot), 08026 - Barcelona España<br />
            TELÉFONO: 934852652<br>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>


            {if $available_in_your_account}
                {l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' pdf='true'}             
    			<br />
            {/if}
{*            
			{$shop_address|escape:'htmlall':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:' pdf='true'}<br />
				{if !empty($shop_phone)}
					Tel: {$shop_phone|escape:'htmlall':'UTF-8'}
				{/if}

				{if !empty($shop_fax)}
					Fax: {$shop_fax|escape:'htmlall':'UTF-8'}
				{/if}
				<br />
			{/if}
            
            {if isset($shop_details)}
                {$shop_details|escape:'htmlall':'UTF-8'}<br />
            {/if}

            {if isset($free_text)}
    			{$free_text|escape:'htmlall':'UTF-8'}<br />
            {/if}
*}            
		</td>
	</tr>
</table>


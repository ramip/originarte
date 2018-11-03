<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class LeoSpecials extends Module
{
	private $_html = '';
	private $_postErrors = array();

    function __construct()
    {
        $this->name = 'leospecials';
        $this->tab = 'Prestashop';
        $this->version = '0.8';
		$this->author = 'leotheme';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Leo Specials products');
		$this->description = $this->l('Adds a block with current product specials.');
	}

	public function install()
	{
		return ( parent::install() AND $this->registerHook('leftColumn')  AND $this->registerHook('header') AND $this->_defaultData() );
	}
	
	private function _defaultData(){
		Configuration::updateValue( $this->name.'_limit', 3);
		Configuration::updateValue( $this->name.'_order_by', 'position-ASC', true);
		return true;
	}
	
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitSpecials'))
		{
			Configuration::updateValue( $this->name.'_limit', (int)(Tools::getValue('limit')));
			Configuration::updateValue( $this->name.'_order_by', Tools::getValue('order_by') );
			$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$order_by = array(
			'id_product-DESC' => $this->l('Id product decrease'),
			'id_product-ASC' => $this->l('Id product increase'),
			'price-DESC' => $this->l('Price decrease'),
			'price-ASC' => $this->l('Price increase'),
			'date_add-DESC' => $this->l('Date add decrease'),
			'date_add-ASC' => $this->l('Date add increase'),
			'date_upd-DESC' => $this->l('Date update decrease'),
			'date_upd-ASC' => $this->l('Date update increase'),
			'position-DESC' => $this->l('Position decrease'),
			'position-ASC' => $this->l('Position increase'),
		);
		$return = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Limit products:').'</label>
				<div class="margin-form">
					<input type="text" name="limit" value="'.(int)Tools::getValue( 'limit',Configuration::get( $this->name.'_limit' ) ).'"/>
				</div>
				<label>'.$this->l('Order By:').'</label>
				<div class="margin-form">
					<select name="order_by">';
						foreach($order_by as $key=>$row){
							$return .= ' <option value="'.$key.'" '.($key == Tools::getValue( 'order_by', Configuration::get( $this->name.'_order_by' ) ) ? ' selected="selected"' : '').'>'.$row.'</option>';
						}
		$return .= '
					</select>
				</div>
				<center><input type="submit" name="submitSpecials" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $return;
	}

	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
			
		$order_by_way = explode('-',Configuration::get( $this->name.'_order_by' ));
		$order_by = isset($order_by_way[0]) ? $order_by_way[0] : 'position';
		$order_way = isset($order_by_way[1]) ? $order_by_way[1] : 'ASC';
		
		if (!($specials = Product::getPricesDrop((int)$params['cookie']->id_lang, 0, (int)Configuration::get( $this->name.'_limit' ), false, $order_by, $order_way)))
			return;
		foreach($specials as &$special){
			$special['priceWithoutReduction_tax_excl'] = Tools::ps_round($special['price_without_reduction'], 2);
		}
		$this->smarty->assign(array(
			'specials' => $specials,
			'mediumSize' => Image::getSize(ImageType::getFormatedName('medium'))
		));

		return $this->display(__FILE__, 'leospecials.tpl');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return ;
		$this->context->controller->addCSS(($this->_path).'leospecials.css', 'all');
	}
}


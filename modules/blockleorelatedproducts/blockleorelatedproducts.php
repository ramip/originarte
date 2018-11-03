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
include_once(_PS_MODULE_DIR_.'blockleorelatedproducts/Params.php');

class BlockLeoRelatedProducts extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private $_configs = array();
	private $catids = array();
    function __construct()
    {
        $this->name = 'blockleorelatedproducts';
        $this->tab = 'landofcoder';
        $this->version = '1.1';
		$this->author = 'leotheme';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->_prepareForm();
		$this->displayName = $this->l('Leo Related Products Block');
		$this->description = $this->l('Display Products In Same Category or Related by Tag.... in Carousel.');
		$this->params =  new LeoParams( $this, 'LEOREPRODS', $this->_configs  );
 
	}
	public function _prepareForm(){
		
		$this->_configs = array(
			'modclass'=>'',
			'theme'  => 'default',
			'catids' => '2,3',
			'itemspage' => 3,
			'columns'   => 3,
			'itemstab' => 12,
			'porder'   => 'date_add',
		);		
	}	
	public function install()
	{
		$a =  (parent::install() AND $this->registerHook('displayFooterProduct')  AND $this->registerHook('header'));
		return $a;
	}
	
	public function uninstall()
	{
		return parent::uninstall();
	}
	
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitSpecials'))
		{
			$res = $this->params->batchUpdate( $this->_configs );
			$this->params->refreshConfig(); 
			$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		 $orders = array('date_add'=>$this->l('Date Add'),'date_add DESC'=>$this->l('Date Add DESC'),
                         'name'=>$this->l('Name'),'name DESC'=>$this->l('Name DESC'),
                         'quantity'=>$this->l('Quantity'),'quantity DESC'=>$this->l('Quantity DESC'),
                         'price'=>$this->l('Price'),'price DESC'=>$this->l('Price DESC'));
								
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				
			
				 
				<div class="row-form">
					'.$this->params->selectTag( $orders, "Order By", 'porder',  $this->params->get('porder') ).'
					<p class="clear">'.$this->l('The maximum number of products in each page Carousel (default: 3).').'</p>
				</div>
			
				<div class="row-form">
					'.$this->params->inputTag( 'Items Per Page', 'itemspage', $this->params->get('itemspage') ).'
					<p class="clear">'.$this->l('The maximum number of products in each page Carousel (default: 3).').'</p>
				</div>
				<div class="row-form">
					'.$this->params->inputTag( 'Colums In Each Carousel', 'columns', $this->params->get('columns') ).'
					<p class="clear">'.$this->l('The maximum column products in each page Carousel (default: 3).').'</p>
				</div>
				<div class="row-form">
					'.$this->params->inputTag( 'Items In all Carousels', 'itemstab', $this->params->get('itemstab') ).'
					<p class="clear">'.$this->l('The maximum number of products in each Carousel (default: 6).').'</p>
				</div>
				
				 
				<center><input type="submit" name="submitSpecials" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	private function getCurrentProduct($products, $id_current)
	{
		if ($products)
			foreach ($products AS $key => $product)
				if ($product['id_product'] == $id_current)
					return $key;
		return false;
	}
	public function hookDisplayFooterProduct( $params ){
		return $this->displayRightColumnProduct( $params );
	}
	public function hookDisplayLeftColumnProduct( $params ){
		return $this->displayRightColumnProduct( $params );
	}
	

	
	
	public function displayRightColumnProduct( $params )
	{
		if (Tools::getValue('controller') != "product" )
			return ;
			
		$nb =  (int)$this->params->get('itemstab');
 
		$catids = $this->params->get( 'catids', '1,2,3' );
		$catids = explode(",",$catids);
		$porder = $this->params->get('porder','date_add');
		$porder = preg_split("#\s+#",$porder);
		if( !isset($porder[1]) ) {
			$porder[1] = null;
		}
		 
		
		$items_page =  (int)$this->params->get('itemspage');
		$columns_page =  (int)$this->params->get('columns');
	 
			
		$this->catids = $catids;
		// $products = $this->getProducts((int)Context::getContext()->language->id, 1, $nb, $porder[0], $porder[1] );
		
		
		$dir = dirname(__FILE__)."/products.tpl";
		$tdir = _PS_ALL_THEMES_DIR_._THEME_NAME_.'/modules/'.$this->name.'/products.tpl';
	
		if( file_exists($tdir) ){
			$dir = $tdir;
		}
		
		
		$idProduct = (int)(Tools::getValue('id_product'));
		$product = new Product((int)($idProduct));

		/* If the visitor has came to this product by a category, use this one */
		if (isset($params['category']->id_category))
			$category = $params['category'];
		/* Else, use the default product category */
		else
		{
			if (isset($product->id_category_default) AND $product->id_category_default > 1)
				$category = New Category((int)($product->id_category_default));
		}
		
		if (!Validate::isLoadedObject($category) OR !$category->active) 
			return;

		// Get infos
		$categoryProducts = $category->getProducts($this->context->language->id, 1, $nb, $porder[0], $porder[1] ); /* 100 products max. */
		$sizeOfCategoryProducts = (int)sizeof($categoryProducts);
		$middlePosition = 0;
		
		// Remove current product from the list
		if (is_array($categoryProducts) AND sizeof($categoryProducts))
		{
			foreach ($categoryProducts AS $key => $categoryProduct){
				if ($categoryProduct['id_product'] == $idProduct)
				{
					unset($categoryProducts[$key]);
					break;
				}
			}	
		}
// Generar atributos para cada producto  by ramiro
$prodAttributes = '';  
$prodCombinations = '';
if($categoryProducts) {
    foreach($categoryProducts AS $prod) {
        $producto = new Product($prod['id_product'], true, (int)Context::getContext()->language->id);
        $arr = $this->assignAttributesGroupsHome($producto, (int)Context::getContext()->language->id);
        $prodAttributes[$prod['id_product']] = $arr['groups'];
        $prodCombinations[$prod['id_product']] = $arr['combinations'];
    }
}		
		// Display tpl
		$this->smarty->assign(array(
                        'productsAttributes' => $prodAttributes, // atributos de producto
                        'productsCombinations' => $prodCombinations, // combinaciones de producto   
			'itemsperpage'=> $items_page,
			'columnspage' => $columns_page,
			'product_tpl' => $dir,
			'products'	 => $categoryProducts,
			'scolumn'     => 12/$columns_page,
			'homeSize'  => Image::getSize(ImageType::getFormatedName('home_default'))
		//	'priceWithoutReduction_tax_excl' => Tools::ps_round($special['price_without_reduction'], 2),
		///	'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
		));
		return $this->display(__FILE__, 'blockleorelatedproducts.tpl');
	}
	
	 
 
	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockleorelatedproducts.css', 'all');
	}
	
	protected function getCacheId($name = null, $hook = '')
	{
		$cache_array = array(
			$name !== null ? $name : $this->name,
			$hook,
			date('Ymd'),
			(int)Tools::usingSecureMode(),
			(int)$this->context->shop->id,
			(int)Group::getCurrent()->id,
			(int)$this->context->language->id,
			(int)$this->context->currency->id,
			(int)$this->context->country->id
		);
		return implode('|', $cache_array);
	}
        
        // Obtiene los atributos de un producto
	protected function assignAttributesGroupsHome($product, $id_lang)
	{
		$colors = array();
		$groups = array();

		// @todo (RM) should only get groups and not all declination ?
		$attributes_groups = $product->getAttributesGroups($id_lang);
                
		if (is_array($attributes_groups) && $attributes_groups)
		{
			$combination_images = $product->getCombinationImages($id_lang);
			$combination_prices_set = array();
			foreach ($attributes_groups as $k => $row)
			{
				// Color management
				if ((isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')))
				{
					$colors[$row['id_attribute']]['value'] = $row['attribute_color'];
					$colors[$row['id_attribute']]['name'] = $row['attribute_name'];
					if (!isset($colors[$row['id_attribute']]['attributes_quantity']))
						$colors[$row['id_attribute']]['attributes_quantity'] = 0;
					$colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
				}
				if (!isset($groups[$row['id_attribute_group']]))
					$groups[$row['id_attribute_group']] = array(
						'name' => $row['public_group_name'],
						'group_type' => $row['group_type'],
						'default' => -1,
					);

				$groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
				if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
					$groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
				if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
					$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
				$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];


				$combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
				$combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
				$combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];

				// Call getPriceStatic in order to set $combination_specific_price
				if (!isset($combination_prices_set[(int)$row['id_product_attribute']]))
				{
					Product::getPriceStatic((int)$product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
					$combination_prices_set[(int)$row['id_product_attribute']] = true;
					$combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
				}
				$combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
				$combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
				$combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
				$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
				$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
				$combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
				if ($row['available_date'] != '0000-00-00')
					$combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
				else
					$combinations[$row['id_product_attribute']]['available_date'] = '';

				if (!isset($combination_images[$row['id_product_attribute']][0]['id_image']))
					$combinations[$row['id_product_attribute']]['id_image'] = -1;
				else
				{
					$combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
					if ($row['default_on'] && $id_image > 0)
					{
						if (isset($this->context->smarty->tpl_vars['images']->value))
							$product_images = $this->context->smarty->tpl_vars['images']->value;
						if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image]))
						{
							$product_images[$id_image]['cover'] = 1;
							$this->context->smarty->assign('mainImage', $product_images[$id_image]);
							if (count($product_images))
								$this->context->smarty->assign('images', $product_images);
						}
						if (isset($this->context->smarty->tpl_vars['cover']->value))
							$cover = $this->context->smarty->tpl_vars['cover']->value;
						if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images))
						{
							$product_images[$cover['id_image']]['cover'] = 0;
							if (isset($product_images[$id_image]))
								$cover = $product_images[$id_image];
							$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$id_image) : (int)$id_image);
							$cover['id_image_only'] = (int)$id_image;
							$this->context->smarty->assign('cover', $cover);
						}
					}
				}
			}

			// wash attributes list (if some attributes are unavailables and if allowed to wash it)
			if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0)
			{
				foreach ($groups as &$group)
					foreach ($group['attributes_quantity'] as $key => &$quantity)
						if (!$quantity)
							unset($group['attributes'][$key]);

				foreach ($colors as $key => $color)
					if (!$color['attributes_quantity'])
						unset($colors[$key]);
			}
			foreach ($combinations as $id_product_attribute => $comb)
			{
				$attribute_list = '';
				foreach ($comb['attributes'] as $id_attribute)
					$attribute_list .= '\''.(int)$id_attribute.'\',';
				$attribute_list = rtrim($attribute_list, ',');
				$combinations[$id_product_attribute]['list'] = $attribute_list;
			}
                        
			$arrayAttr = array(
				'groups' => $groups,
				'combinations' => $combinations,
				'colors' => (count($colors)) ? $colors : false,
				'combinationImages' => $combination_images);
                        return $arrayAttr;
		}
	}                
        
}


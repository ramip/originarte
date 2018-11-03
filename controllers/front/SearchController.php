<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SearchControllerCore extends FrontController
{
	public $php_self = 'search';
	public $instant_search;
	public $ajax_search;

	/**
	 * Initialize search controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		$this->instant_search = Tools::getValue('instantSearch');

		$this->ajax_search = Tools::getValue('ajaxSearch');

		if ($this->instant_search || $this->ajax_search)
		{
			$this->display_header = false;
			$this->display_footer = false;
		}
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$query = Tools::replaceAccentedChars(urldecode(Tools::getValue('q')));
$search_products = array();                
		if ($this->ajax_search)
		{
			$searchResults = Search::find((int)(Tools::getValue('id_lang')), $query, 1, 10, 'position', 'desc', true);
			foreach ($searchResults as &$product)
				$product['product_link'] = $this->context->link->getProductLink($product['id_product'], $product['prewrite'], $product['crewrite']);
			die(Tools::jsonEncode($searchResults));
		}

		if ($this->instant_search && !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$search = Search::find($this->context->language->id, $query, 1, 10, 'position', 'desc');
			Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
$search_products = $search['result'];                   
			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $query,
				'instant_search' => $this->instant_search,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		else if (($query = Tools::getValue('search_query', Tools::getValue('ref'))) && !is_array($query))
		{
			$this->productSort();
			$this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
			$this->p = abs((int)(Tools::getValue('p', 1)));
			$query = Tools::replaceAccentedChars(urldecode($query));			
			$search = Search::find($this->context->language->id, $query, $this->p, $this->n, $this->orderBy, $this->orderWay);
			Hook::exec('actionSearch', array('expr' => $query, 'total' => $search['total']));
			$nbProducts = $search['total'];
			$this->pagination($nbProducts);
$search_products = $search['result'];
			$this->context->smarty->assign(array(
				'products' => $search['result'], // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $search['result'],
				'nbProducts' => $search['total'],
				'search_query' => $query,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		else if (($tag = urldecode(Tools::getValue('tag'))) && !is_array($tag))
		{
			$nbProducts = (int)(Search::searchTag($this->context->language->id, $tag, true));
			$this->pagination($nbProducts);
			$result = Search::searchTag($this->context->language->id, $tag, false, $this->p, $this->n, $this->orderBy, $this->orderWay);
			Hook::exec('actionSearch', array('expr' => $tag, 'total' => count($result)));
$search_products = $result;
			$this->context->smarty->assign(array(
				'search_tag' => $tag,
				'products' => $result, // DEPRECATED (since to 1.4), not use this: conflict with block_cart module
				'search_products' => $result,
				'nbProducts' => $nbProducts,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		else
		{
			$this->context->smarty->assign(array(
				'products' => array(),
				'search_products' => array(),
				'pages_nb' => 1,
				'nbProducts' => 0));
		}
                
// Generar atributos para cada producto  
$prodAttributes = '';  
$prodCombinations = '';
if($search_products) {
    foreach($search_products AS $prod) {
        $producto = new Product($prod['id_product'], true, (int)Context::getContext()->language->id);
        $arr = $this->assignAttributesGroupsHome($producto, (int)Context::getContext()->language->id);
        $prodAttributes[$prod['id_product']] = $arr['groups'];
        $prodCombinations[$prod['id_product']] = $arr['combinations'];
    }
}
		$this->context->smarty->assign(array(
                        'productsAttributes' => $prodAttributes, // atributos de producto
                        'productsCombinations' => $prodCombinations, // combinaciones de producto                   
		));
                
		$this->context->smarty->assign(array('add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'), 'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')));

		$this->setTemplate(_PS_THEME_DIR_.'search.tpl');
	}

	public function displayHeader($display = true)
	{
		if (!$this->instant_search && !$this->ajax_search)
			parent::displayHeader();
		else
			$this->context->smarty->assign('static_token', Tools::getToken(false));
	}

	public function displayFooter($display = true)
	{
		if (!$this->instant_search && !$this->ajax_search)
			parent::displayFooter();
	}

	public function setMedia()
	{
		parent::setMedia();

		if (!$this->instant_search && !$this->ajax_search)
			$this->addCSS(_THEME_CSS_DIR_.'product_list.css');
			
		if (Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
	}
        
        // funcion añadido por ramiro
	public function assignAttributesGroupsHome($product, $id_lang)
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

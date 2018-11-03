<?php

class Homecategories extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'homecategories';
		$this->tab = 'front_office_features';
		$this->version = 1.3;
		$this->author = 'John Stocks';
		$this->need_instance = 0;



		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Homepage Categories for v1.5');
		$this->description = $this->l('Displays categories on your homepage');
	}

	function install()
	{
			return (parent::install() AND $this->registerHook('home') AND $this->registerHook('header'));
	}



  public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'homecategories.css', 'all'); 
	}

function hookHome($params)
{
  global $smarty, $cookie, $link;
 
  $id_customer = (int)$params['cookie']->id_customer;
  $id_group = $id_customer ? Customer::getDefaultGroupId($id_customer) : _PS_DEFAULT_CUSTOMER_GROUP_;
  $id_lang = (int)$params['cookie']->id_lang;
  $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
  SELECT c.*, cl.*
  FROM `'._DB_PREFIX_.'category` c
  LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.$id_lang.')
  LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
 
  LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category`)
  WHERE level_depth > 1 And level_depth < 3
  AND c.`active` = 1
  AND cg.`id_group` = '.$id_group.'
  ORDER BY `level_depth` ASC, cs.`position` ASC');
  $category = new Category(1);
  $nb = intval(Configuration::get('HOME_categories_NBR'));
 
    global $link;
                 $this->context->smarty->assign(array(
                 'categories' => $result, Category::getRootCategories(intval($params['cookie']->id_lang), true),
                 'link' => $link));
                 
  $this->context->smarty->assign(array(
   'category' => $category,
   'lang' => Language::getIsoById(intval($params['cookie']->id_lang)),
  ));
  return $this->display(__FILE__, 'homecategories.tpl');
 }
}
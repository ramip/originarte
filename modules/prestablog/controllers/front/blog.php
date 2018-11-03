<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class PrestaBlogBlogModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	private $assignPage = 0;
	private $PrestaBlog = array();
	private $News = array();
	private $NewsCountAll;
	private $path;
	private $Pagination = array();
	private $ConfigTheme;

	public function setTemplate($template)
	{
		$this->template = _PS_MODULE_DIR_.$this->module->name.'/themes/'.Configuration::get('prestablog_theme').'/tpl/'.$template;
	}
	
	public function __construct()
	{
		//$this->display_column_left = false;
		//$this->display_column_right = false;

		parent::__construct();
		
		include_once(_PS_MODULE_DIR_.'prestablog/prestablog.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/news.class.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/categories.class.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/correspondancescategories.class.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/commentnews.class.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/antispam.class.php');
		
		$this->ConfigTheme = PrestaBlog::_getConfigXmlTheme(Configuration::get('prestablog_theme'));
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->context->controller->addCSS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/css/module.css', 'all');

		if( Configuration::get('prestablog_socials_actif') ) {
			$this->context->controller->addCSS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/css/rrssb.css', 'all');
			// le js est défini directement dans le tpl pour des raisons de prise en charge de la popup
			//$this->context->controller->addJS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/js/rrssb.min.js');
		}
		
		if( Configuration::get('prestablog_pageslide_actif') ) {
			$ConfigThemeArray = objectToArray($this->ConfigTheme);
			if(is_array($ConfigThemeArray['js']))
				foreach($ConfigThemeArray['js'] As $kjs => $vjs)
					$this->context->controller->addJS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/js/'.$vjs);
			elseif($ConfigThemeArray['js'] != '')
				$this->context->controller->addJS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/js/'.$ConfigThemeArray['js']);
		}
	}

	public function canonicalRedirectionCustomController($urlReal) {
		$match_url = (Configuration::get('PS_SSL_ENABLED') && ($this->ssl || Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$match_url = rawurldecode($match_url);
		if (!preg_match('/^'.Tools::pRegexp(rawurldecode($urlReal), '/').'([&?].*)?$/', $match_url))
			Tools::redirectLink($urlReal);
	}
	
	public function init()
	{
		parent::init();
		$SecteurName = "";

		$base = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true));
		
		$base .= __PS_BASE_URI__;
		
		$this->PrestaBlog = new PrestaBlog();
		
		// assignPage (1 = 1 news page, 2 = news listes, 0 = rien) 
		$this->context->smarty->assign(
			array(
					'prestablog_config' => Configuration::getMultiple(array_keys($this->PrestaBlog->Configurations)),
					'prestablog_theme' => Configuration::get('prestablog_theme'),
					'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/',
					'md5pic' => md5(time())
				)
		);
		
		if(Tools::getValue('id') && $id_prestablog_news = (int)Tools::getValue('id')) {
			$this->assignPage = 1;
			$this->News = new NewsClass($id_prestablog_news, (int)$this->context->cookie->id_lang);
			if(!$this->News->actif)
				Tools::redirect('404.php');
			
			$this->context->smarty->assign(
				array(
						'SecteurName' => '&nbsp;>&nbsp;<a href="'.PrestaBlog::prestablog_url(
														array(
																"id"		=> $this->News->id,
																"seo"		=> $this->News->link_rewrite,
																"titre"		=> $this->News->title
															)
                              ).'">'.$this->News->title.'</a>'
					)
			);
		}
		// action page
		elseif (Tools::getValue('a') && Configuration::get('prestablog_comment_subscription')) {
			if(!$this->context->cookie->isLogged())
				Tools::redirect('index.php?controller=authentication&back='.urlencode('index.php?fc=module&module=prestablog&controller=blog&a='.Tools::getValue('a')));
			
			$this->News = new NewsClass((int)Tools::getValue('a'), (int)$this->context->cookie->id_lang);
			
			if($this->News->actif) {
				CommentNewsClass::insertCommentAbo(
										$this->News->id,
										$this->context->cookie->id_customer
									);
			}
			
			Tools::redirect(
							PrestaBlog::prestablog_url(
														array(
																"id"		=> $this->News->id,
																"seo"		=> $this->News->link_rewrite,
																"titre"		=> $this->News->title
															)
													)
							); 
		}
		// action page
		elseif (Tools::getValue('d') && Configuration::get('prestablog_comment_subscription')) {
			if($this->context->cookie->isLogged()) {
				$this->News = new NewsClass((int)Tools::getValue('d'), (int)$this->context->cookie->id_lang);
				if($this->News->actif) {
					CommentNewsClass::deleteCommentAbo(
											$this->News->id,
											$this->context->cookie->id_customer
										);
				}
			}
			
			Tools::redirect(
							PrestaBlog::prestablog_url(
														array(
																"id"		=> $this->News->id,
																"seo"		=> $this->News->link_rewrite,
																"titre"	=> $this->News->title
															)
													)
							);
		}
		else {
			$this->assignPage = 2;
			$Categorie = NULL;
			$Year = NULL;
			$Month = NULL;
			
			if(Tools::getValue('c')) {
				$Categorie = new CategoriesClass((int)Tools::getValue("c"), (int)$this->context->cookie->id_lang);

				$SecteurName = CategoriesClass::getBreadcrumb(CategoriesClass::getBranche($Categorie->id));
							
				$this->context->smarty->assign(
					array(
							'prestablog_categorie'	=> $Categorie->id,
							'prestablog_categorie_name' => $Categorie->title,
							'prestablog_categorie_link_rewrite' => ($Categorie->link_rewrite!=''?$Categorie->link_rewrite:$Categorie->title),
						)
				);
			}
			else {
				$this->context->smarty->assign(
					array(
							'prestablog_categorie'	=> Null,
							'prestablog_categorie_name' => Null,
							'prestablog_categorie_link_rewrite' => Null,
						)
				);
			}

			if(Tools::getValue('y')) {
				$Year = Tools::getValue('y');
				$SecteurName .= $Year;
			}
			
			if(Tools::getValue('m')) {
				$Month = Tools::getValue('m');
				$SecteurName .= ($SecteurName != '' ? ' > ' : '').'<a href="'.PrestaBlog::prestablog_url(
														array(
																"y"		=> $Year,
																"m"		=> $Month
															)
													).'">'.$this->PrestaBlog->MoisLangue[$Month].'</a>';
			}
			
			if(Tools::getValue('p')) {
				if ($SecteurName == "")
					$SecteurName = $this->PrestaBlog->LSecteurAll;
				$SecteurName .= ' > '.$this->PrestaBlog->LPage.' '.Tools::getValue('p');
			}
			
			$this->context->smarty->assign(
				array(
						'prestablog_month'	=> $Month,
						'prestablog_year'	=> $Year
					)
			);
			
			if(Tools::getValue('m') && Tools::getValue('y')) {
				$DateDebut = Date('Y-m-d H:i:s', mktime(0, 0, 0, $Month, +1, $Year));
				$DateFin = Date('Y-m-d H:i:s', mktime(0, 0, 0, $Month+1, +1, $Year));
				if ($DateFin > Date('Y-m-d H:i:s'))
					$DateFin = Date('Y-m-d H:i:s');
			}
			else {
				$DateDebut = NULL;
				$DateFin = Date('Y-m-d H:i:s');
			}
			
			$this->NewsCountAll = NewsClass::getCountListeAll(
											(int)($this->context->cookie->id_lang), 
											1, // actif only
											0, // homeslide
											$DateDebut, // date début
											$DateFin, // date fin
											(isset($Categorie->id)?$Categorie->id:null),
											1 // langue active sur news
										);
			
			$this->News = NewsClass::getListe(
											(int)($this->context->cookie->id_lang), 
											1, // actif only
											0, // homeslide
											$this->ConfigTheme, 
											(int)Tools::getValue('start'), // limit start
											(int)Configuration::get('prestablog_nb_liste_page'), // limit stop
											'n.`date`', 
											'desc',
											$DateDebut, // date début
											$DateFin, // date fin
											(isset($Categorie->id)?$Categorie->id:null),
											1, // langue active sur news
											(int)Configuration::get('prestablog_news_title_length'),
											(int)Configuration::get('prestablog_news_intro_length')
										);
			
			$this->context->smarty->assign(
				array(
						'SecteurName' => $SecteurName
					)
			);
		}
		
		if($this->assignPage == 1)
			$this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsOnly((int)$this->context->cookie->id_lang, (int)Tools::getValue('id')));
		elseif($this->assignPage == 2 && Tools::getValue('c'))
			$this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsCat((int)$this->context->cookie->id_lang, (int)Tools::getValue('c')));
		elseif($this->assignPage == 2 && (Tools::getValue('y') || Tools::getValue('m')))
			$this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsNewsDate());
		else $this->context->smarty->assign(PrestaBlog::getPrestaBlogMetaTagsPage((int)$this->context->cookie->id_lang));

		$this->gestionRedirectionCanonical((int)$this->assignPage);
	}

	private function gestionRedirectionCanonical($assignPage) {
		switch ($assignPage) {
			case 1:
				// nous sommes dans une page unique
				$News = new NewsClass((int)Tools::getValue("id"), (int)$this->context->cookie->id_lang);
				if(!Tools::getValue('submitComment'))
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"id"		=> $News->id,
						"seo"		=> $News->link_rewrite,
						"titre"	=> $News->title
					)));
				break;

			case 2:
				// nous sommes dans un listing
				if(	Tools::getValue("start") && Tools::getValue("p") 
					&& !Tools::getValue("c") && !Tools::getValue("m") && !Tools::getValue("y")) {
					// listing blog simple pagination
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"start"	=> (int)Tools::getValue("start"),
						"p"		=> (int)Tools::getValue("p")
					)));
				}
				if(Tools::getValue("c") && !Tools::getValue("start") && !Tools::getValue("p")) {
					// listing catégorie	première page
					$Categorie = new CategoriesClass((int)Tools::getValue("c"), (int)$this->context->cookie->id_lang);
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"c"			=> $Categorie->id,
						"categorie"	=> ($Categorie->link_rewrite!=''?$Categorie->link_rewrite:CategoriesClass::getCategoriesName((int)$this->context->cookie->id_lang, (int)Tools::getValue("c"))),
					)));
				}
				if(Tools::getValue("c") && Tools::getValue("start") && Tools::getValue("p")) {
					// listing catégorie	pagignation
					$Categorie = new CategoriesClass((int)Tools::getValue("c"), (int)$this->context->cookie->id_lang);
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"c"			=> $Categorie->id,
						"start"		=> (int)Tools::getValue("start"),
						"p"			=> (int)Tools::getValue("p"),
						"categorie"	=> ($Categorie->link_rewrite!=''?$Categorie->link_rewrite:CategoriesClass::getCategoriesName((int)$this->context->cookie->id_lang, (int)Tools::getValue("c"))),
					)));
				}
				if(	Tools::getValue("m") && Tools::getValue("y")
					&& !Tools::getValue("start") && !Tools::getValue("p")) {
					// listing date première page
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"y"		=> (int)Tools::getValue("y"),
						"m"		=> (int)Tools::getValue("m")
					)));
				}			
				if(	Tools::getValue("m") && Tools::getValue("y")
					&& Tools::getValue("start") && Tools::getValue("p")) {
					// listing date pagination
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array(
						"y"		=> (int)Tools::getValue("y"),
						"m"		=> (int)Tools::getValue("m"),
						"start"	=> (int)Tools::getValue("start"),
						"p"		=> (int)Tools::getValue("p")
					)));
				}
				if(	!Tools::getValue("m") && !Tools::getValue("y")
					&& !Tools::getValue("c")
					&& !Tools::getValue("start") && !Tools::getValue("p")) {
					// page d'accueil du blog
					$this->canonicalRedirectionCustomController(PrestaBlog::prestablog_url(array()));
				}
				break;
		}
	}
	
	public function initContent() 
	{
		parent::initContent();
		
		/** affichage du menu cat */
		if($this->assignPage == 1 && Configuration::get('prestablog_menu_cat_blog_article')) // page article unique
			$this->VoirListeCatMenu();
		if($this->assignPage == 2) { // page liste articles
			if(		Configuration::get('prestablog_menu_cat_blog_index')
				&&	!Tools::getValue('c')
				&&	!Tools::getValue('y')
				&&	!Tools::getValue('m')
				&&	!Tools::getValue('p')
				)
				$this->VoirListeCatMenu();
			elseif(
					Configuration::get('prestablog_menu_cat_blog_list')
					&&	(
						Tools::getValue('c')
					||	Tools::getValue('y')
					||	Tools::getValue('m')
					||	Tools::getValue('p')
					)
				)
				$this->VoirListeCatMenu();
		}
		/** /affichage du menu cat */
		
		if($this->assignPage == 1) {
			$this->News->categories = 	CorrespondancesCategoriesClass::getCategoriesListeName((int)$this->News->id, (int)$this->context->cookie->id_lang, 1);
			$products_liaison		= 	NewsClass::getProductLinkListe((int)$this->News->id, true);
			
			if(sizeof($products_liaison)) {
				foreach($products_liaison As $ProductLink) {
					$product = new Product((int)$ProductLink, false, (int)$this->context->cookie->id_lang);
					$productCover = Image::getCover($product->id);
					$image_product = new Image((int)$productCover["id_image"]);
					$imageThumbPath = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$image_product->getExistingImgPath().'.jpg', 'product_mini_2_'.$product->id.'.jpg', 215, 'jpg');
					
					$this->News->products_liaison[$ProductLink] = Array(
						"name" => $product->name,
						"description_short" => $product->description_short,
						"thumb" => $imageThumbPath,
						"link" => $product->getLink($this->context)
					);
				}
			}
			
			if(file_exists(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$this->News->id.'.jpg'))
				$this->context->smarty->assign('News_Image', 'modules/prestablog/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$this->News->id.'.jpg');
			$this->context->smarty->assign(
				array(
						'LinkReal' 		=> PrestaBlog::getBaseUrlFront().'?fc=module&module=prestablog&controller=blog',
						'News' 			=> $this->News,
						'prestablog_current_url' => PrestaBlog::prestablog_url(
														array(
																"id"		=> $this->News->id,
																"seo"		=> $this->News->link_rewrite,
																"titre"	=> $this->News->title
															)
													)
					)
			);
			
			$this->context->smarty->assign(
					array(
						'tpl_unique'			=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_page-unique.tpl')
					)
			);
			
			if($this->PrestaBlog->gestComment($this->News->id)) {
				
				if(Configuration::get('prestablog_antispam_actif')) {
					$AntiSpamLoad = $this->PrestaBlog->gestAntiSpam();
					
					if($AntiSpamLoad != false)
						$this->context->smarty->assign(
							array(
									'AntiSpam'			=> $AntiSpamLoad
								)
						);
				}
				$this->context->smarty->assign(
					array(
							'Is_Subscribe'		=> in_array($this->context->cookie->id_customer, CommentNewsClass::listeCommentAbo($this->News->id)),
						)
				);
				
				$this->context->smarty->assign(
					array(
							'tpl_comment'		=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_page-comment.tpl')
						)
				);
			}
		}
		elseif($this->assignPage == 2) {
			if(Configuration::get('prestablog_pageslide_actif') 
					&& !Tools::getValue('c')
					&& !Tools::getValue('y')
					&& !Tools::getValue('m')
					&& !Tools::getValue('p')
					)
				if($this->PrestaBlog->slideNews()) {
					$this->context->smarty->assign(
						array(
							'tpl_slide'		=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_slide.tpl')
						)
					);
				}

			/** affichage de la description catégorie */
			if(
				(
						Configuration::get('prestablog_view_cat_desc')
					||	Configuration::get('prestablog_view_cat_thumb')
					||	Configuration::get('prestablog_view_cat_img')
				)
				&&	Tools::getValue('c')
				&&	!Tools::getValue('y')
				&&	!Tools::getValue('m')
				&&	!Tools::getValue('p')
				) {
				$objCategorie = new CategoriesClass((int)Tools::getValue('c'), (int)$this->context->cookie->id_lang);

				if (file_exists(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/up-img/c/'.$objCategorie->id.'.jpg'))
					$objCategorie->image_presente = true;
				else
					$objCategorie->image_presente = false; 

				$this->context->smarty->assign(
					array(
							'prestablog_categorie_obj'		=> $objCategorie,
						)
				);

				$this->context->smarty->assign(
					array(
						'tpl_cat'		=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_category.tpl')
					)
				);
			}		
			/** /affichage de la description catégorie */
			
			$this->Pagination = PrestaBlog::getPagination(	$this->NewsCountAll,
															NULL,
															(int)Configuration::get('prestablog_nb_liste_page'),
															(int)Tools::getValue('start'), 
															(int)Tools::getValue('p')
														);
			$this->context->smarty->assign(
				array(
						'Pagination'		=> $this->Pagination,
						'News'				=> $this->News,
						'NbNews'				=> $this->NewsCountAll
					)
			);
			
			$this->context->smarty->assign(
					array(
						'tpl_all'			=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_page-all.tpl')
					)
			);
		}
		
			// echo '<pre style="font-size:11px;text-align:left">';
			// 	print_r($this->News);
			// echo '</pre>';

		$this->setTemplate('module_page.tpl');
	}
	
	private function VoirListeCatMenu() {
		$ListeCat = CategoriesClass::getListe((int)$this->context->cookie->id_lang, 1);
		
		if(sizeof($ListeCat)) {
			$this->context->smarty->assign(
					array(
						'MenuCatNews' => $this->_displayMenuCategories($ListeCat),
					)
			);
				
			$this->context->smarty->assign(
					array(
						'tpl_menu_cat'			=> $this->context->smarty->fetch(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/tpl/module_page-menucat.tpl')
					)
			);
		}
	}

   public function _displayMenuCategories($Liste, $first=true) {
      $_html = '<ul>';
      if($first && Configuration::get('prestablog_menu_cat_home_link')) {
      	$PrestaBlog = new PrestaBlog();
			$_html .= '	<li>
								<a href="'.PrestaBlog::prestablog_url(array()).'">
									'.(Configuration::get('prestablog_menu_cat_home_img')?'<img src="'._MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/img/home.gif" alt="" />':$PrestaBlog->MessageCallBack['Blog']).'
								</a>
							</li>';
			$first=false;
      }
      foreach($Liste As $Key => $Value) {
      	//$nombre_news = CategoriesClass::getNombreNewsDansCat((int)$Value["id_prestablog_categorie"]);
      	//$branche = CategoriesClass::getBranche((int)$Value["id_prestablog_categorie"]);

      	if(!Configuration::get('prestablog_menu_cat_blog_empty') && (int)$Value["nombre_news_recursif"] == 0) {
      		$_html .= '';
      	}
      	else {
	      	$_html .= '	<li>
									<a href="'.PrestaBlog::prestablog_url(
														array(
																"c"		=> (int)$Value["id_prestablog_categorie"],
																"titre"	=> ($Value["link_rewrite"]!=''?$Value["link_rewrite"]:$Value["title"]),
															)
													).'" '.(sizeof($Value["children"])?'class="mparent"':'').'>'.$Value["title"].(Configuration::get('prestablog_menu_cat_blog_nbnews') && (int)$Value["nombre_news_recursif"] > 0 ?'&nbsp;<span>('.(int)$Value["nombre_news_recursif"].')</span>':'').'</a>';

	         if(sizeof($Value["children"])) { 
	            $_html .= $this->_displayMenuCategories($Value["children"], $first);
	         }
	         $_html .= '	</li>'; 		
      	}
      }
      $_html .= '</ul>';

      return $_html;
   }
}

?>

<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

if (!defined('_PS_VERSION_'))
	exit;
	
class PrestaBlog extends Module
{
	/****************************/
	/******* DEMO MODE **********/
	/****************************/
	// true or false, if false, all upload files 
	// and critical hoster informations are disable
	// feature @HDClic demo reserved
	protected $demoMode = false; 
	/****************************/
	
	public $ModulePath = "";
	public $LPage = "";
	public $LSecteurAll = "";
	public $MoisLangue = array();
	public $RssLangue = array();
	public $fixCssLabel16 = "";
			
	private $checksum = "";
	
	protected $InPost = Array();
	protected $checkSlide;
	protected $checkActive;
	
	protected $checkCommentState = -2;
	
	protected $NormalImageSizeWidth = 1024;
	protected $NormalImageSizeHeight = 1024;
	
	protected $AdminCropImageSizeWidth = 400;
	protected $AdminCropImageSizeHeight = 400;
	
	protected $AdminThumbImageSizeWidth = 40;
	protected $AdminThumbImageSizeHeight = 40;
	
	protected $maxImageSize = 25510464;
	protected $default_theme = "default";
	
	protected $PathModuleConf;
	
	protected $url_hdclic = "http://www.hdclic.com";
	protected $url_faq_hdclic = "http://boutique.hdclic.com/fr/content/10-foire-aux-questions-prestablog";
	protected $url_tuto_hdclic = "http://boutique.hdclic.com/fr/content/9-tutoriel-installation-blog-prestashop";
	
	protected $htaccessFileProtect = 'Order deny,allow
Deny from all
';
	protected $indexFileProtect = '<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
						
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
						
header("Location: ../");
exit;';

	static public function IsPSVersion($compare, $version) {
		return version_compare(_PS_VERSION_, $version, $compare);
	}
	
	public function __construct()
	{
		$this->name = 'prestablog';
		$this->tab = 'front_office_features';
		$this->version = '3.4';
		$this->author = 'HDClic';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->module_key = '7aafe030447c17f08629e0319107b62b';
		
		parent::__construct();
		
		$this->displayName = $this->l('PrestaBlog');
		$this->description = $this->l('A module to add a blog on your web store.');
		
		$this->confirmUninstall = $this->l('Are you sure you want to delete this module ?');
		
		$this->PathModuleConf = 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getValue('token');
		$this->LangueDefaultStore = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$path = dirname(__FILE__);
		if (strpos(__FILE__, 'Module.php') !== false)
			$path .= '/../modules/'.$this->name;
		
		$this->MoisLangue = Array( // important pour les traductions langues
			1 => $this->l('January'), 2 => $this->l('February'), 3 => $this->l('March'), 4 => $this->l('April'), 5 => $this->l('May'), 6 => $this->l('June'), 7 => $this->l('July'), 8 => $this->l('August'), 9 => $this->l('September'), 10 => $this->l('October'), 11 => $this->l('November'), 12 => $this->l('December')
		);
		
		$this->LPage = $this->l('Page');
		$this->LSecteurAll = $this->l('All news');
		
		$this->ModulePath = $path;
		
		include_once($path.'/class/news.class.php');
		include_once($path.'/class/categories.class.php');
		include_once($path.'/class/correspondancescategories.class.php');
		include_once($path.'/class/commentnews.class.php');
		include_once($path.'/class/antispam.class.php');
		
		$this->MessageCallBack = array (
			"Blog"						=> $this->l('Blog'),
			"no_result_search"		=> $this->l('No results found'),
			"no_result_linked"		=> $this->l('No product linked'),
			"total_results"		=> $this->l('Total results'),
			"next_results"		=> $this->l('Next'),
			"prev_results"		=> $this->l('Previous'),
			
			"blocRss"			=> $this->l('Block Rss all news'),
			"blocDateListe"		=> $this->l('Block date news'),
			"blocLastListe"		=> $this->l('Block last news'),
			"blocCatListe"		=> $this->l('Block categories news'),
			
			//pour les traductions des messages retours après traitement
			"2pTtfC78"			=> $this->l('The restore was successfull'),
			"6R9ba6mR"			=> $this->l('The backup was successfull'),
			"87Yu8thV"			=> $this->l('No backup to restore'),
			"Yu3Tr9r7"			=> $this->l('The import XML was successfull'),
			"2yt6wEK7"			=> $this->l('No import selected'),
			"EwB89w9u"			=> '',
			"Txz7p55R"			=> '',
			"gAbYd675"			=> '',
			"BN8gf4y9"			=> '',
			"V6f5Hga2"			=> '',
		);

		if($this->IsPSVersion('>=','1.6'))
			$this->default_theme = "default";
		else
			$this->default_theme = "default-1-5";
		
		$this->Configurations = array(
			/** Thèmes et slide **************************/
			// Thème
			$this->name.'_theme'							=> $this->default_theme,
			// Slideshow
			$this->name.'_homenews_actif'				=> 0,
			$this->name.'_pageslide_actif'			=> 1,
			$this->name.'_homenews_limit'				=> 5,
			$this->name.'_slide_picture_width'		=> 555,
			$this->name.'_slide_picture_height'		=> 246,
			$this->name.'_slide_title_length'		=> 80,
			$this->name.'_slide_intro_length'		=> 160,
			/** /Thèmes et slide *************************/
			
			/** Blocs ************************************/
			// Bloc derniers articles
			$this->name.'_lastnews_limit'			=> 5,
			$this->name.'_lastnews_showall'		=> 1,
			$this->name.'_lastnews_actif'			=> 0,
			$this->name.'_lastnews_showintro'	=> 0,
			$this->name.'_lastnews_showthumb'	=> 1,
			$this->name.'_lastnews_title_length'	=> 80,
			$this->name.'_lastnews_intro_length'	=> 120,
			// Bloc d'articles par date
			$this->name.'_datenews_order'			=> "desc",
			$this->name.'_datenews_showall'		=> 0,
			$this->name.'_datenews_actif'			=> 0,
			// Bloc Rss pour tous les articles
			$this->name.'_allnews_rss'				=> 0,
			$this->name.'_rss_title_length'		=> 80,
			$this->name.'_rss_intro_length'		=> 200,
			// Dernières actualités en footer
			$this->name.'_footlastnews_actif'		=> 0,
			$this->name.'_footlastnews_limit'		=> 3,
			$this->name.'_footlastnews_showall'		=> 1,
			$this->name.'_footlastnews_intro'		=> 0,
			$this->name.'_footer_title_length'		=> 80,
			$this->name.'_footer_intro_length'		=> 120,
			// Ordre des blocs dans les colonnes
			$this->name.'_sbr'	=> serialize(array(
													0 => ""
												)),
			$this->name.'_sbl'	=> serialize(array(
													0 => "blocRss",
													1 => "blocLastListe",
													2 => "blocCatListe",
													3 => "blocDateListe"
												)),
			/** /Blocs ***********************************/
			
			/** Commentaires *****************************/
			$this->name.'_comment_actif'				=> 1,
			$this->name.'_comment_only_login'		=> 0,
			$this->name.'_comment_auto_actif'		=> 0,
			$this->name.'_comment_nofollow'			=> 1,
			$this->name.'_comment_alert_admin'		=> 1,
			$this->name.'_comment_admin_mail'		=> Configuration::get('PS_SHOP_EMAIL'),
			$this->name.'_comment_subscription'		=> 1,
			$this->name.'_comment_autoshow'			=> 1,
			/** /Commentaires ****************************/
			
			/** Categorie ********************************/
			// Menu catégories dans la page du blog
			$this->name.'_menu_cat_blog_index'		=> 1,
			$this->name.'_menu_cat_blog_list'		=> 0,
			$this->name.'_menu_cat_blog_article'	=> 0,
			$this->name.'_menu_cat_blog_empty'		=> 0,
			$this->name.'_menu_cat_home_link'		=> 1,
			$this->name.'_menu_cat_home_img'			=> 1,
			//$this->name.'_menu_cat_blog_rss'			=> 0,
			$this->name.'_menu_cat_blog_nbnews'		=> 0,
			// Bloc de catégories d'articles
			$this->name.'_catnews_showall'			=> 0,
			$this->name.'_catnews_rss'					=> 0,
			$this->name.'_catnews_actif'				=> 0,
			$this->name.'_catnews_empty'				=> 0,
			$this->name.'_catnews_tree'				=> 0,
			// page liste d'articles
			$this->name.'_catnews_shownbnews'		=> 1,
			$this->name.'_catnews_showthumb'			=> 1,
			$this->name.'_catnews_showintro'			=> 1,
			// liste des categories
			$this->name.'_thumb_cat_width'			=> 150,
			$this->name.'_thumb_cat_height'			=> 150,
			$this->name.'_full_cat_width'				=> 535,
			$this->name.'_full_cat_height'			=> 236,
			$this->name.'_cat_title_length'			=> 80,
			$this->name.'_cat_intro_length'			=> 120,
			/** /Categorie *******************************/
			
			/** Globales *********************************/
			// Configuration du rewrite
			$this->name.'_rewrite_actif'				=> 0,
			// Configuration générale du front-office
			$this->name.'_nb_liste_page'				=> 5,
			$this->name.'_producttab_actif'			=> 1,
			$this->name.'_socials_actif'				=> 1,
			$this->name.'_uniqnews_rss'				=> 0,
			$this->name.'_view_cat_desc'				=> 1,
			$this->name.'_view_cat_thumb'				=> 0,
			$this->name.'_view_cat_img'				=> 1,
			$this->name.'_view_news_img'				=> 0,
			// liste d'articles
			$this->name.'_thumb_picture_width'		=> 215,
			$this->name.'_thumb_picture_height'		=> 190,
			$this->name.'_news_title_length'			=> 80,
			$this->name.'_news_intro_length'			=> 200,
			// Configuration globale de l'administration
			$this->name.'_nb_car_min_linkprod'		=> 2,
			$this->name.'_nb_list_linkprod'			=> 5,
			$this->name.'_nb_news_pl'					=> 20,
			$this->name.'_nb_comments_pl'				=> 20,
			$this->name.'_comment_div_visible'		=> 0,
			/** /Globales ********************************/
			
			/** Outils ***********************************/
			// Anitspam
			$this->name.'_antispam_actif'				=> 0,
			// Importation depuis un XML de WordPress
			$this->name.'_import_xml'					=> "",
			/** /Outils **********************************/
		);
		
		// configurations liées aux langues de la boutique
		// ceci passe bien dans le système backup 
		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
			$this->Configurations[$this->name.'_titlepageblog_'.$language['id_lang']] = $this->l('Blog');

		$this->fixCssLabel16 = (self::IsPSVersion('>=','1.6')?'fixLabel16':'');
	}
	
	private function registerHookPosition($hook_name, $position)
	{
		if($this->registerHook($hook_name))
			$this->updatePosition((int)Hook::getIdByName($hook_name), 0, (int)$position);
		else
			return false;
		return true;
	}
	
	public function InitLangueModule($id_lang) {
		$this->RssLangue["id_lang"]			=	$id_lang;
		$this->RssLangue["channel_title"]	=	Configuration::get('PS_SHOP_NAME').' '.$this->l('news feed');
	}
	
	public function RegisterAdminTab()
	{
		// Prepare tab AdminPrestaBlogAjaxController
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminPrestaBlogAjax';
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'PrestaBlogAjax';
		$tab->id_parent = -1;
		$tab->module = $this->name;
		
		return $tab->add();
	}	

	public function DeleteAdminTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminPrestaBlogAjax');
		if ($id_tab)
		{
			$tab = new Tab($id_tab);
			return $tab->delete();
		}
	}

	public function install()
	{
		// si multiboutique, alors activer le contexte pour installe le module
		// sur toutes les boutiques
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		$News = new NewsClass();
		$Categories = new CategoriesClass();
		$CorrespondancesCategories = new CorrespondancesCategoriesClass();
		$CommentNews = new CommentNewsClass();
		$AntiSpam = new AntiSpamClass();
		
		@unlink(_PS_MODULE_DIR_.$this->name.'/override/classes/Dispatcher.php');
		if (version_compare(_PS_VERSION_,'1.5.3.1','<')) {
			if(copy(	
						_PS_ROOT_DIR_.'/override/classes/Dispatcher.php', 
						_PS_MODULE_DIR_.$this->name.'/backup_override/Dispatcher_'.md5(date("YmdHis")).'.php'
						)) {
				if(!copy(
							_PS_MODULE_DIR_.$this->name.'/override_before_1531/Dispatcher.php', 
							_PS_MODULE_DIR_.$this->name.'/override/classes/Dispatcher.php'
							))
					return false;
			}
			else
				return false;
		}
		
		$this->installQuickAccess();
		
		if (
					!parent::install() 
				// ACCROCHES TEMPLATE
				||	!$this->registerHookPosition('displayHeader', 1)
				||	!$this->registerHookPosition('displayHome', 1)
				||	!$this->registerHook('displayTop')
				||	!$this->registerHookPosition('displayRightColumn', 1)
				||	!$this->registerHookPosition('displayLeftColumn', 1)
				||	!$this->registerHook('displayFooter')
				||	!$this->registerHook('ModuleRoutes')

				// ACCROCHES TEMPLATE PRESTASHOP 1.5
				|| !$this->installHookPS15()

				// ACCROCHES TEMPLATE PRESTASHOP 1.6
				|| !$this->installHookPS16()
				
				// CONFIGURATION & INTEGRATION BASE DE DONNEES
				||	!$this->UpdateConfiguration("add")
				
				||	!$this->MetaTitlePageBlog("add")
				
				// STRUCTURE BASE DE DONNEES
				||	!$News->registerTablesBdd()
				||	!$Categories->registerTablesBdd()
				||	!$CorrespondancesCategories->registerTablesBdd()
				||	!$CommentNews->registerTablesBdd()
				||	!$AntiSpam->registerTablesBdd()

				// ADMIN CONTROLLERS
				|| !$this->RegisterAdminTab()
			)
			return false;
		return true;
	}

	public function installHookPS15() {
		if($this->IsPSVersion("<","1.6"))
			if(
						!$this->registerHook('displayProductTab')
					||	!$this->registerHook('displayProductTabContent')
				)
				return false;
		return true;
	}	

	public function installHookPS16() {
		if($this->IsPSVersion(">=","1.6"))
			if(
						!$this->registerHook('displayNav')
					|| !$this->registerHook('displayFooterProduct')
				)
				return false;
		return true;
	}
	
	public function uninstall()
	{
		$News = new NewsClass();
		$Categories = new CategoriesClass();
		$CorrespondancesCategories = new CorrespondancesCategoriesClass();
		$CommentNews = new CommentNewsClass();
		$AntiSpam = new AntiSpamClass();
		
		$this->uninstallQuickAccess();
		
		@unlink(_PS_MODULE_DIR_.$this->name.'/override/classes/Dispatcher.php');
		
		if (
					!parent::uninstall()

				// CONFIGURATION & INTEGRATION BASE DE DONNEES
				||	!$this->UpdateConfiguration("del")
				
				||	!$this->MetaTitlePageBlog("del")
				
				// STRUCTURE BASE DE DONNEES
				||	!$News->deleteTablesBdd()
				||	!$Categories->deleteTablesBdd()
				||	!$CorrespondancesCategories->deleteTablesBdd()
				||	!$CommentNews->deleteTablesBdd()
				||	!$AntiSpam->deleteTablesBdd()

				// ADMIN CONTROLLERS
				|| !$this->DeleteAdminTab()
			)
			return false;
			
		return true;
	}
	
	public function installQuickAccess()
	{
		$QA = new QuickAccess;
		foreach (Language::getLanguages(true) as $language)
			$QA->name[(int)$language['id_lang']] = $this->displayName;
		$QA->link = 'index.php?controller=AdminModules&configure='.$this->name.'&module_name='.$this->name;
		$QA->new_window = 0;
		$QA->Add();
		Configuration::updateValue($this->name.'_QuickAccess', $QA->id);
	
		return true;
	}
	
	public function uninstallQuickAccess()
	{
		$QA = new QuickAccess((int)Configuration::get($this->name.'_QuickAccess'));
		$QA->delete();
		Configuration::deleteByName($this->name.'_QuickAccess');
		return true;
	}
	
	private function UpdateConfiguration($action) {
		switch($action) {
			case "add" :
				//if (Shop::isFeatureActive()) {
					$Shops = Shop::getShops();
					foreach ($Shops as $keyShop => $valueShop)
						foreach ($this->Configurations as $ConfigurationKey => $ConfigurationValue)
							Configuration::updateValue($ConfigurationKey, $ConfigurationValue, false, null, $keyShop);			
				//}
				//else {
					foreach ($this->Configurations as $ConfigurationKey => $ConfigurationValue)
						Configuration::updateValue($ConfigurationKey, $ConfigurationValue);
				//}
				break;
			case "del" :
				foreach ($this->Configurations as $ConfigurationKey => $ConfigurationValue)
					Configuration::deleteByName($ConfigurationKey);
				break;
		}
		return true;
	}
	
	private function CheckConfiguration() {
		foreach ($this->Configurations as $ConfigurationKey => $ConfigurationValue) {
			if(!Configuration::getIdByName($ConfigurationKey, null, $this->context->shop->id))
				Configuration::updateValue($ConfigurationKey, $ConfigurationValue, false, null, $this->context->shop->id);
			if(!Configuration::getIdByName($ConfigurationKey))
				Configuration::updateValue($ConfigurationKey, $ConfigurationValue);
		}
	}
	
	private function MetaTitlePageBlog($action) {
		switch($action) {
			case "add" :
				$languages = Language::getLanguages(true);
				foreach ($languages as $language)
					Configuration::updateValue($this->name.'_titlepageblog_'.$language['id_lang'], $this->Configurations[$this->name.'_titlepageblog_'.$language['id_lang']]);
				break;
			case "del" :
				$languages = Language::getLanguages();
				foreach ($languages as $language)
					Configuration::deleteByName($this->name.'_titlepageblog_'.$language['id_lang']);
				break;
		}
		return true;
	}
	
	private function _postForm()
	{
		if (_PS_MAGIC_QUOTES_GPC_) {
			$process = array(&$_GET, &$_POST);
			while (list($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][Tools::stripslashes($k)] = $v;
						$process[] = &$process[$key][Tools::stripslashes($k)];
					} else {
						$process[$key][Tools::stripslashes($k)] = Tools::stripslashes($v);
					}
				}
			}
			unset($process);
		}  
		
		$errors = array();
		$postEnCours = false;
		//$defaultLanguage = $this->LangueDefaultStore;
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		$languages = Language::getLanguages();
		
		$this->checkSlide = 0;
		$this->checkActive = 0;
		
		if (Tools::getValue('submitFiltreNews')) {
			if(Tools::getValue('slide')) $this->checkSlide = 1;
			else $this->checkSlide = 0;
			if(Tools::getValue('activeNews')) $this->checkActive = 1;
			else $this->checkActive = 0;
		}
		else {
			if(Tools::getValue('slideget')==1) $this->checkSlide = 1;
			else $this->checkSlide = 0;
			if(Tools::getValue('activeget')==1) $this->checkActive = 1;
			else $this->checkActive = 0;
		}
		
		if (Tools::getValue('submitFiltreComment')) {
			$this->checkCommentState = Tools::getValue('activeComment') ;
		}
		else {
			if(Tools::getValue('activeCommentget')) $this->checkCommentState = Tools::getValue('activeCommentget');
			else $this->checkCommentState = -2;
		}
		
		$this->PathModuleConf .= '&activeget='.$this->checkActive.'&slideget='.$this->checkSlide.'&activeCommentget='.$this->checkCommentState;
		
		if (Tools::isSubmit('deleteNews') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			$News = new NewsClass((int)(Tools::getValue('idN')));
			if(!$News->delete())
				$errors[] = $this->l('An error occurred while delete news.');
			else {
				$this->deleteAllImagesThemes((int)$News->id);
				CorrespondancesCategoriesClass::delAllCategoriesNews((int)$News->id);
				Tools::redirectAdmin($this->PathModuleConf.'&newsListe');
			}
		}
		elseif (Tools::isSubmit('deleteCat') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$Categorie = new CategoriesClass((int)(Tools::getValue('idC')));
			if(!$Categorie->delete())
				$errors[] = $this->l('An error occurred while delete categorie.');
			else {
				$this->deleteAllImagesThemesCat((int)$Categorie->id);
				CorrespondancesCategoriesClass::delAllCorrespondanceNewsAfterDelCat((int)$Categorie->id);
				Tools::redirectAdmin($this->PathModuleConf.'&catListe');
			}
		}
		elseif (Tools::isSubmit('deleteAntiSpam') && Tools::getValue('idAS'))
		{
			$postEnCours = true;
			$AntiSpam = new AntiSpamClass((int)(Tools::getValue('idAS')));
			if(!$AntiSpam->delete())
				$errors[] = $this->l('An error occurred while delete antispam question.');
			else
				Tools::redirectAdmin($this->PathModuleConf.'&configAntiSpam');
		}
		elseif (Tools::isSubmit('etatNews') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			$News = new NewsClass((int)(Tools::getValue('idN')));
			if(!$News->changeEtat('actif'))
				$errors[] = $this->l('An error occurred while change status of news.');
			else {
				Tools::redirectAdmin($this->PathModuleConf.'&newsListe');
			}
		}
		elseif (Tools::isSubmit('slideNews') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			$News = new NewsClass((int)(Tools::getValue('idN')));
			if(!$News->changeEtat('slide'))
				$errors[] = $this->l('An error occurred while change status of slide.');
			else {
				Tools::redirectAdmin($this->PathModuleConf.'&newsListe');
			}
		}
		elseif (Tools::isSubmit('etatCat') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$Categories = new CategoriesClass((int)(Tools::getValue('idC')));
			if(!$Categories->changeEtat('actif'))
				$errors[] = Tools::displayError('An error occurred while change status object.').' <b>'.mysql_error().'</b>';
			else {
				Tools::redirectAdmin($this->PathModuleConf.'&catListe');
			}
		}
		elseif (Tools::isSubmit('etatAntiSpam') && Tools::getValue('idAS'))
		{
			$postEnCours = true;
			$AntiSpam = new AntiSpamClass((int)(Tools::getValue('idAS')));
			if(!$AntiSpam->changeEtat('actif'))
				$errors[] = $this->l('An error occurred while change status of antispam question.');
			else {
				Tools::redirectAdmin($this->PathModuleConf.'&configAntiSpam');
			}
		}
		elseif (Tools::isSubmit('submitAddNews'))
		{
			$postEnCours = true;
			
			if (!sizeof(Tools::getValue('languesup')))
				$errors[] = $this->l('You must activate at least one language');
			else {
				foreach ($languages as $language) {
					if (!Tools::getValue('title_'.$language['id_lang']) && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The title must be specified');
					if (!Tools::getValue('link_rewrite_'.$language['id_lang']) && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The url rewrite must be specified');
						
					$Summary=Tools::getValue('paragraph_'.$language['id_lang']);
					$Content=Tools::getValue('content_'.$language['id_lang']);
					
					if (!$Summary && !$Content && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The content or introduction must be specified');
				}
			}
			
			if(!sizeof($errors)) {
				$News = new NewsClass();
				$News->id_shop = (int)$this->context->shop->id;
				$News->copyFromPost();
				$News->langues = serialize(Tools::getValue('languesup'));
				if(!$News->add())
					$errors[] = $this->l('An error occurred while add object.');
				
				NewsClass::RemoveAllProductsLinkNews((int)$News->id);
				if(Tools::getValue('productsLink')) {
					foreach(Tools::getValue('productsLink') As $productLink)
						NewsClass::updateProductLinkNews((int)$News->id, (int)$productLink);
				}
				
				$News->razEtatLangue((int)$News->id);
				foreach ($languages as $language) {
					if (in_array($language['id_lang'], Tools::getValue('languesup')))
						$News->changeActiveLangue((int)$News->id, (int)$language['id_lang']);
				}
				if(!$this->demoMode)
					if($_FILES['homepage_logo']["name"]) {
						if(!$this->UploadImage($_FILES['homepage_logo'], $News->id, $this->NormalImageSizeWidth, $this->NormalImageSizeHeight))
							$errors[] = $this->l('An error occurred while upload image.');
						else {
							foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
								$ConfigTheme = $this->_getConfigXmlTheme($ValueTheme);
								$this->ImageResize(	dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/'.$News->id.'.jpg', 
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/admincrop_'.$News->id.'.jpg', 
													$this->AdminCropImageSizeWidth, 
													$this->AdminCropImageSizeHeight); // pour le crop
													
								$this->AutoCropImage(
													$News->id.'.jpg',
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
													$this->AdminThumbImageSizeWidth, 
													$this->AdminThumbImageSizeHeight,
													"adminth_",
													NULL);
								
								$ConfigThemeArray = objectToArray($ConfigTheme);
								foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
									$this->AutoCropImage(
														$News->id.'.jpg',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
														$ValueThemeArray["width"],
														$ValueThemeArray["height"],
														$KeyThemeArray."_",
														NULL);
								}
							}
						}
					}
				
				if(!sizeof($errors)) {
					if(!Tools::getValue('categories'))
						CorrespondancesCategoriesClass::delAllCategoriesNews($News->id);
					else {
						CorrespondancesCategoriesClass::delAllCategoriesNews($News->id);
						CorrespondancesCategoriesClass::updateCategoriesNews(Tools::getValue('categories'), $News->id);
					}
					Tools::redirectAdmin($this->PathModuleConf.'&newsListe');
				}
			}
		}
		elseif (Tools::isSubmit('submitAddCat'))
		{
			$postEnCours = true;
			
			if (!Tools::getValue('title_'.$this->LangueDefaultStore))
				$errors[] = '<img src="'._PS_IMG_.'l/'.$this->LangueDefaultStore.'.jpg" alt="" title="" /> '.$this->l('The title must be specified');
			
			$Categories = new CategoriesClass();
			$Categories->id_shop = (int)$this->context->shop->id;
			$Categories->copyFromPost();

			if(!sizeof($errors))
				if(!$Categories->add())
					$errors[] = $this->l('An error occurred while add object.');
				else
					if(!$this->demoMode)
						if($_FILES['imageCategory']["name"]) {
							if(!$this->UploadImage($_FILES['imageCategory'], $Categories->id, $this->NormalImageSizeWidth, $this->NormalImageSizeHeight, 'c'))
								$errors[] = $this->l('An error occurred while upload image.');
							else {
								foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
									$this->ImageResize(	dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/'.$Categories->id.'.jpg', 
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/admincrop_'.$Categories->id.'.jpg', 
														$this->AdminCropImageSizeWidth, 
														$this->AdminCropImageSizeHeight); // pour le crop
									
									$this->AutoCropImage(
														$Categories->id.'.jpg',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
														$this->AdminThumbImageSizeWidth, 
														$this->AdminThumbImageSizeHeight,
														"adminth_",
														NULL);
										
									$ConfigThemeArray = objectToArray($ConfigTheme);
									foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
										$this->AutoCropImage(
														$Categories->id.'.jpg',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
														$ValueThemeArray["width"],
														$ValueThemeArray["height"],
														$KeyThemeArray."_",
														NULL);
									}
								}
							}
						}
				
			if(!sizeof($errors))
					Tools::redirectAdmin($this->PathModuleConf.'&catListe');
		}
		elseif (Tools::isSubmit('submitAddAntiSpam'))
		{
			$postEnCours = true;
			
			if (!Tools::getValue('question_'.$this->LangueDefaultStore))
				$errors[] = '<img src="'._PS_IMG_.'l/'.$this->LangueDefaultStore.'.jpg" alt="" title="" /> '.$this->l('The question must be specified');
			if (!Tools::getValue('reply_'.$this->LangueDefaultStore))
				$errors[] = '<img src="'._PS_IMG_.'l/'.$this->LangueDefaultStore.'.jpg" alt="" title="" /> '.$this->l('The reply must be specified');

			
			if(!sizeof($errors)) {
				$AntiSpam = new AntiSpamClass();
				$AntiSpam->id_shop = (int)$this->context->shop->id;
				$AntiSpam->copyFromPost();
				
				if(!$AntiSpam->add())
					$errors[] = $this->l('An error occurred while add object.');
				else {
					$AntiSpam->reloadChecksum();
					Tools::redirectAdmin($this->PathModuleConf.'&configAntiSpam');
				}
			}
		}
		elseif (Tools::isSubmit('addProductLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
			$postEnCours = true;
			
			NewsClass::updateProductLinkNews((int)Tools::getValue('idN'), (int)Tools::getValue('idP'));
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'#productLinkTable');
		}
		elseif (Tools::isSubmit('removeProductLink') && Tools::getValue('idN') && Tools::getValue('idP')) {
			$postEnCours = true;
			
			NewsClass::removeProductLinkNews((int)Tools::getValue('idN'), (int)Tools::getValue('idP'));
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'#productLinkTable');
		}
		elseif (Tools::isSubmit('submitUpdateNews') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			
			if (!sizeof(Tools::getValue('languesup')))
				$errors[] = $this->l('You must activate at least one language');
			else {
				foreach ($languages as $language) {
					if (!Tools::getValue('title_'.$language['id_lang']) && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The title must be specified');
					if (!Tools::getValue('link_rewrite_'.$language['id_lang']) && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The url rewrite must be specified');
					
					$Summary=Tools::getValue('paragraph_'.$language['id_lang']);
					$Content=Tools::getValue('content_'.$language['id_lang']);
					
					if (!$Summary && !$Content && in_array($language['id_lang'], Tools::getValue('languesup')))
						$errors[] = '<img src="'._PS_IMG_.'l/'.$language['id_lang'].'.jpg" alt="" title="" /> '.$this->l('The content or introduction must be specified');
				}
			}
			
			if(!sizeof($errors)) {
				$News = new NewsClass((int)(Tools::getValue('idN')));
				$News->id_shop = (int)$this->context->shop->id;
				$News->copyFromPost();
				$News->langues = serialize(Tools::getValue('languesup'));
				if(!$News->update())
					$errors[] = $this->l('An error occurred while update object.');
				
				NewsClass::RemoveAllProductsLinkNews((int)$News->id);
				if(Tools::getValue('productsLink')) {
					foreach(Tools::getValue('productsLink') As $productLink)
						NewsClass::updateProductLinkNews((int)$News->id, (int)$productLink);
				}
				
				$News->razEtatLangue((int)$News->id);
				foreach ($languages as $language) {
					if (in_array($language['id_lang'], Tools::getValue('languesup')))
						$News->changeActiveLangue((int)$News->id, (int)$language['id_lang']);
				}
				
				if(!$this->demoMode)
					if($_FILES['homepage_logo']["name"]) {
						if(!$this->UploadImage($_FILES['homepage_logo'], Tools::getValue('idN'), $this->NormalImageSizeWidth, $this->NormalImageSizeHeight))
							$errors[] = $this->l('An error occurred while upload image.');
						else {
							foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
								$ConfigTheme = $this->_getConfigXmlTheme($ValueTheme);
								$this->ImageResize(	dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/'.Tools::getValue('idN').'.jpg', 
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/admincrop_'.Tools::getValue('idN').'.jpg', 
													$this->AdminCropImageSizeWidth, 
													$this->AdminCropImageSizeHeight); // pour le crop
													
								$this->AutoCropImage(
													Tools::getValue('idN').'.jpg',
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
													dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
													$this->AdminThumbImageSizeWidth, 
													$this->AdminThumbImageSizeHeight,
													"adminth_",
													NULL);
								
								$ConfigThemeArray = objectToArray($ConfigTheme);
								foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
									$this->AutoCropImage(
														Tools::getValue('idN').'.jpg',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
														dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/',
														$ValueThemeArray["width"],
														$ValueThemeArray["height"],
														$KeyThemeArray."_",
														NULL);
								}
							}
						}
					}
				
				if(!sizeof($errors)) {
					if(!Tools::getValue('categories'))
						CorrespondancesCategoriesClass::delAllCategoriesNews((int)Tools::getValue('idN'));
					else {
						CorrespondancesCategoriesClass::delAllCategoriesNews((int)Tools::getValue('idN'));
						CorrespondancesCategoriesClass::updateCategoriesNews(Tools::getValue('categories'), (int)Tools::getValue('idN'));
					}
				}
			}
		}
		elseif (Tools::isSubmit('submitUpdateCat') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			
			$Categories = new CategoriesClass((int)(Tools::getValue('idC')));
			$Categories->id_shop = (int)$this->context->shop->id;
			$Categories->copyFromPost();
			
			if(!$Categories->update())
				$errors[] = $this->l('An error occurred while update object.');
			
			if(!$this->demoMode)
				if($_FILES['imageCategory']["name"]) {
					if(!$this->UploadImage($_FILES['imageCategory'], $Categories->id, $this->NormalImageSizeWidth, $this->NormalImageSizeHeight, 'c'))
						$errors[] = $this->l('An error occurred while upload image.');
					else {
						foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
							$this->ImageResize(	dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/'.$Categories->id.'.jpg', 
												dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/admincrop_'.$Categories->id.'.jpg', 
												$this->AdminCropImageSizeWidth, 
												$this->AdminCropImageSizeHeight); // pour le crop
							
							$this->AutoCropImage(
												$Categories->id.'.jpg',
												dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
												dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
												$this->AdminThumbImageSizeWidth, 
												$this->AdminThumbImageSizeHeight,
												"adminth_",
												NULL);
								
							$ConfigThemeArray = objectToArray($ConfigTheme);
							foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
								$this->AutoCropImage(
												$Categories->id.'.jpg',
												dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
												dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/c/',
												$ValueThemeArray["width"],
												$ValueThemeArray["height"],
												$KeyThemeArray."_",
												NULL);
							}
						}
					}
				}
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&catListe');
		}
		elseif (Tools::isSubmit('submitUpdateAntiSpam') && Tools::getValue('idAS'))
		{
			$postEnCours = true;
			
			if(!sizeof($errors)) {
				$AntiSpam = new AntiSpamClass((int)(Tools::getValue('idAS')));
				$AntiSpam->id_shop = (int)$this->context->shop->id;
				$AntiSpam->copyFromPost();
				
				if(!$AntiSpam->update())
					$errors[] = $this->l('An error occurred while update object.');
				else {
					$AntiSpam->reloadChecksum();
					Tools::redirectAdmin($this->PathModuleConf.'&configAntiSpam');
				}
			}
		}
		elseif (Tools::isSubmit('submitUpdateComment') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			if (!Tools::getValue('name'))
				$errors[] = $this->l('The name must be specified');
				
			if(!sizeof($errors)) {
				$Comment = new CommentNewsClass((int)(Tools::getValue('idC')));
				$Comment->copyFromPost();
				
				if(!$Comment->update())
					$errors[] = $this->l('An error occurred while update object.');
			}
		}
		elseif (Tools::isSubmit('deleteComment') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$CommentNews = new CommentNewsClass((int)(Tools::getValue('idC')));
			if(!$CommentNews->delete())
				$errors[] = $this->l('An error occurred while delete object.');
			else
				if(Tools::getValue('idN'))
					Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'&showComments');
				else
					Tools::redirectAdmin($this->PathModuleConf.'&commentListe');
		}
		elseif (Tools::isSubmit('enabledComment') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$CommentNews = new CommentNewsClass((int)(Tools::getValue('idC')));
			if(!$CommentNews->changeEtat('actif', 1))
				$errors[] = $this->l('An error occurred while update object.');
			else {
				$NewsId = CommentNewsClass::getNewsFromComment($CommentNews->id);
				$ListeAbo = CommentNewsClass::listeCommentMailAbo($NewsId);
				
				if (
							Configuration::get($this->name.'_comment_subscription')
						&&	sizeof($ListeAbo)
					) {
						
					$News = new NewsClass($NewsId, $this->LangueDefaultStore);
					$content_form=array();
					$content_form["title_news"] = $News->title;
					
					foreach($ListeAbo As $ValueAbo) {
						Mail::Send(
							$this->LangueDefaultStore,				// langue
							'feedback-subscribe', 									// template
							$this->l('New comment').' / '.$content_form["title_news"],	// sujet
							array(													// templatevars
									'{news}'				=> $NewsId, 
									'{title_news}'			=> $content_form["title_news"], 
									'{url_reponse}'			=> Tools::getShopDomainSsl(true).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&id='.$content_form["news"],
									'{url_desabonnement}'	=> Tools::getShopDomainSsl(true).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&d='.$content_form["news"]
								), 
							$ValueAbo, 												// destinataire mail
							NULL, 													// destinataire nom
							(Configuration::get('PS_SHOP_EMAIL')),			// expéditeur
							(Configuration::get('PS_SHOP_NAME')),				// expéditeur nom
							NULL,													// fichier joint
							NULL,													// mode smtp
							dirname(__FILE__).'/mails/'								// répertoire des mails templates
						);
					}
				}
				
				if(Tools::getValue('idN'))
					Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'&showComments');
				else
					Tools::redirectAdmin($this->PathModuleConf.(Tools::isSubmit('commentListe') ? '&commentListe' : ''));
			}
		}
		elseif (Tools::isSubmit('pendingComment') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$CommentNews = new CommentNewsClass((int)(Tools::getValue('idC')));
			if(!$CommentNews->changeEtat('actif', -1))
				$errors[] = $this->l('An error occurred while update object.');
			else
				Tools::redirectAdmin($this->PathModuleConf.(Tools::isSubmit('commentListe') ? '&commentListe' : ''));
		}
		elseif (Tools::isSubmit('disabledComment') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			$CommentNews = new CommentNewsClass((int)(Tools::getValue('idC')));
			if(!$CommentNews->changeEtat('actif', 0))
				$errors[] = $this->l('An error occurred while update object.');
			else
				if(Tools::getValue('idN'))
					Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'&showComments');
				else
					Tools::redirectAdmin($this->PathModuleConf.(Tools::isSubmit('commentListe') ? '&commentListe' : ''));
		}
		elseif (Tools::isSubmit('deleteImageBlog') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			if (!file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.Tools::getValue('idN').'.jpg'))
				$errors[] = $this->l('This action cannot be taken.');
			else
			{
				$this->deleteAllImagesThemes(Tools::getValue('idN'));
			}
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN'));
		}
		elseif (Tools::isSubmit('deleteImageBlog') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			if (!file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.Tools::getValue('idC').'.jpg'))
				$errors[] = $this->l('This action cannot be taken.');
			else
			{
				$this->deleteAllImagesThemesCat(Tools::getValue('idC'));
			}
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editCat&idC='.Tools::getValue('idC'));
		}
		elseif (Tools::isSubmit('submitCrop') && Tools::getValue('idN'))
		{
			$postEnCours = true;
			if (!file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg'))
				$errors[] = $this->l('This action cannot be taken.');
			else
			{
				$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
				$ConfigThemeArray = objectToArray($ConfigTheme);
				
				//list($W_Image_Base, $H_Image_Base, $type, $attr) = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg');
				list($W_Image_Base, $H_Image_Base) = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg');
				
				$this->CropImage(
								Tools::getValue('idN').'.jpg',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/',
								$W_Image_Base, // width de l'image sur lequel le crop a été selectionné
								$H_Image_Base, // heigth de l'image sur lequel le crop a été selectionné
								$ConfigThemeArray["images"][Tools::getValue('pfx')]["width"], // width de l'image sur lequel le crop a été selectionné
								$ConfigThemeArray["images"][Tools::getValue('pfx')]["height"], // heigth de l'image sur lequel le crop a été selectionné
								Tools::getValue('x'), // position horizontal du point de départ du crop selectionné
								Tools::getValue('y'), // position vertical du point de départ du crop selectionné
								Tools::getValue('w'), // width de la selection du crop
								Tools::getValue('h'), // heigth de la selection du crop
								Tools::getValue('pfx').'_',
								NULL
							);
				
				// on met aussi à jour le crop de l'adminth
				if(Tools::getValue('pfx') == 'thumb')
					$this->AutoCropImage(
								Tools::getValue('idN').'.jpg',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/',
								$this->AdminThumbImageSizeWidth, 
								$this->AdminThumbImageSizeHeight,
								'adminth_',
								NULL
							);

			}
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editNews&idN='.Tools::getValue('idN').'&pfx='.Tools::getValue('pfx'));
		}
		elseif (Tools::isSubmit('submitCrop') && Tools::getValue('idC'))
		{
			$postEnCours = true;
			if (!file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg'))
				$errors[] = $this->l('This action cannot be taken.');
			else
			{
				$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
				$ConfigThemeArray = objectToArray($ConfigTheme);

				//list($W_Image_Base, $H_Image_Base, $type, $attr) = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg');
				list($W_Image_Base, $H_Image_Base) = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg');
				
				$this->CropImage(
								Tools::getValue('idC').'.jpg',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/',
								$W_Image_Base, // width de l'image sur lequel le crop a été selectionné
								$H_Image_Base, // heigth de l'image sur lequel le crop a été selectionné
								$ConfigThemeArray["categories"][Tools::getValue('pfx')]["width"], // width de l'image sur lequel le crop a été selectionné
								$ConfigThemeArray["categories"][Tools::getValue('pfx')]["height"], // heigth de l'image sur lequel le crop a été selectionné
								Tools::getValue('x'), // position horizontal du point de départ du crop selectionné
								Tools::getValue('y'), // position vertical du point de départ du crop selectionné
								Tools::getValue('w'), // width de la selection du crop
								Tools::getValue('h'), // heigth de la selection du crop
								Tools::getValue('pfx').'_',
								NULL
							);
				
				// on met aussi à jour le crop de l'adminth
				if(Tools::getValue('pfx') == 'thumb')
					$this->AutoCropImage(
								Tools::getValue('idC').'.jpg',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/',
								dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/',
								$this->AdminThumbImageSizeWidth, 
								$this->AdminThumbImageSizeHeight,
								'adminth_',
								NULL
							);

			}
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&editCat&idC='.Tools::getValue('idC').'&pfx='.Tools::getValue('pfx'));
		}
		elseif (Tools::isSubmit('submitAntiSpamConfig'))	{
			if(is_numeric(Tools::getValue($this->name.'_antispam_actif')))
				Configuration::updateValue($this->name.'_antispam_actif', (int)Tools::getValue($this->name.'_antispam_actif'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configAntiSpam');
		}
		elseif (Tools::isSubmit('submitTheme'))	{
			Configuration::updateValue($this->name.'_theme', Tools::getValue('theme'));
			Tools::redirectAdmin($this->PathModuleConf.'&configTheme');
		}
		elseif (Tools::isSubmit('submitWizard'))	{
			Tools::redirectAdmin($this->PathModuleConf.'&configWizard');
		}
		elseif (Tools::isSubmit('submitPageBlog')) {
			if(is_numeric(Tools::getValue($this->name.'_pageslide_actif')))
				Configuration::updateValue($this->name.'_pageslide_actif', (int)Tools::getValue($this->name.'_pageslide_actif'));
			
			$languages = Language::getLanguages(true);
			foreach ($languages as $language)
				Configuration::updateValue($this->name.'_titlepageblog_'.$language['id_lang'], Tools::getValue('meta_title_'.$language['id_lang']));
			
			Tools::redirectAdmin($this->PathModuleConf.'&pageBlog');
		}
		elseif (Tools::isSubmit('submitConfSlideNews')) { // uniquement pour la configuration du slideshow
			if(is_numeric(Tools::getValue($this->name.'_homenews_limit')))
				Configuration::updateValue($this->name.'_homenews_limit', (int)Tools::getValue($this->name.'_homenews_limit'));
			if(is_numeric(Tools::getValue($this->name.'_homenews_actif')))
				Configuration::updateValue($this->name.'_homenews_actif', (int)Tools::getValue($this->name.'_homenews_actif'));
			if(is_numeric(Tools::getValue($this->name.'_pageslide_actif')))
				Configuration::updateValue($this->name.'_pageslide_actif', (int)Tools::getValue($this->name.'_pageslide_actif'));
			if(is_numeric(Tools::getValue($this->name.'_slide_title_length')))
				Configuration::updateValue($this->name.'_slide_title_length', (int)Tools::getValue($this->name.'_slide_title_length'));
			if(is_numeric(Tools::getValue($this->name.'_slide_intro_length')))
				Configuration::updateValue($this->name.'_slide_intro_length', (int)Tools::getValue($this->name.'_slide_intro_length'));
			
			$xml = Tools::file_get_contents($configFile = _PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get($this->name.'_theme').'/config.xml');
			$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
			$ConfigThemeArray = objectToArray($ConfigTheme);

			$remplacement = '
		<thumb> <!--Image prevue pour les miniatures dans les listes -->
			<width>'.(int)$ConfigThemeArray["images"]['thumb']["width"].'</width>
			<height>'.(int)$ConfigThemeArray["images"]['thumb']["height"].'</height>
		</thumb>
		<slide> <!--Image prevue pour les slides -->
			<width>'.Tools::getValue('slide_picture_width').'</width>
			<height>'.Tools::getValue('slide_picture_height').'</height>
		</slide>
	';
			
			$xml=preg_replace('#<images[^>]*>.*?</images>#si','<images>'.$remplacement.'</images>',$xml);
			
			if (is_writable(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/')) {
				file_put_contents(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/config.xml', utf8_encode($xml));
				Tools::redirectAdmin($this->PathModuleConf.'&configTheme');
			}
		}
		elseif (Tools::isSubmit('submitConfListeArticles')) {
			if(is_numeric(Tools::getValue($this->name.'_news_title_length')))
				Configuration::updateValue($this->name.'_news_title_length', (int)Tools::getValue($this->name.'_news_title_length'));
			if(is_numeric(Tools::getValue($this->name.'_news_intro_length')))
				Configuration::updateValue($this->name.'_news_intro_length', (int)Tools::getValue($this->name.'_news_intro_length'));
			
			$xml = Tools::file_get_contents($configFile = _PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get($this->name.'_theme').'/config.xml');
			$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
			$ConfigThemeArray = objectToArray($ConfigTheme);
			
			$remplacement = '
		<thumb> <!--Image prevue pour les miniatures dans les listes -->
			<width>'.Tools::getValue('thumb_picture_width').'</width>
			<height>'.Tools::getValue('thumb_picture_height').'</height>
		</thumb>
		<slide> <!--Image prevue pour les slides -->
			<width>'.(int)$ConfigThemeArray["images"]['slide']["width"].'</width>
			<height>'.(int)$ConfigThemeArray["images"]['slide']["height"].'</height>
		</slide>
	';
			
			$xml=preg_replace('#<images[^>]*>.*?</images>#si','<images>'.$remplacement.'</images>',$xml);

			if (is_writable(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/')) {
				file_put_contents(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/config.xml', utf8_encode($xml));
				Tools::redirectAdmin($this->PathModuleConf.'&configCategories');
			}
		}
		elseif (Tools::isSubmit('submitConfBlocRss')) {
			if(is_numeric(Tools::getValue($this->name.'_allnews_rss')))
				Configuration::updateValue($this->name.'_allnews_rss', (int)Tools::getValue($this->name.'_allnews_rss'));
			if(is_numeric(Tools::getValue($this->name.'_rss_title_length')))
				Configuration::updateValue($this->name.'_rss_title_length', (int)Tools::getValue($this->name.'_rss_title_length'));
			if(is_numeric(Tools::getValue($this->name.'_rss_intro_length')))
				Configuration::updateValue($this->name.'_rss_intro_length', (int)Tools::getValue($this->name.'_rss_intro_length'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configBlocs');
		}
		elseif (Tools::isSubmit('submitConfBlocLastNews')) {
			if(is_numeric(Tools::getValue($this->name.'_lastnews_limit')))
				Configuration::updateValue($this->name.'_lastnews_limit', (int)Tools::getValue($this->name.'_lastnews_limit'));
			if(is_numeric(Tools::getValue($this->name.'_lastnews_actif')))
				Configuration::updateValue($this->name.'_lastnews_actif', (int)Tools::getValue($this->name.'_lastnews_actif'));
			if(is_numeric(Tools::getValue($this->name.'_lastnews_showintro')))
				Configuration::updateValue($this->name.'_lastnews_showintro', (int)Tools::getValue($this->name.'_lastnews_showintro'));
			if(is_numeric(Tools::getValue($this->name.'_lastnews_showthumb')))
				Configuration::updateValue($this->name.'_lastnews_showthumb', (int)Tools::getValue($this->name.'_lastnews_showthumb'));
			if(is_numeric(Tools::getValue($this->name.'_lastnews_showall')))
				Configuration::updateValue($this->name.'_lastnews_showall', (int)Tools::getValue($this->name.'_lastnews_showall'));
			if(is_numeric(Tools::getValue($this->name.'_lastnews_title_length')))
				Configuration::updateValue($this->name.'_lastnews_title_length', (int)Tools::getValue($this->name.'_lastnews_title_length'));		
			if(is_numeric(Tools::getValue($this->name.'_lastnews_intro_length')))
				Configuration::updateValue($this->name.'_lastnews_intro_length', (int)Tools::getValue($this->name.'_lastnews_intro_length'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configBlocs');
		}
		elseif (Tools::isSubmit('submitConfFooterLastNews')) {
			if(is_numeric(Tools::getValue($this->name.'_footlastnews_limit')))
				Configuration::updateValue($this->name.'_footlastnews_limit', (int)Tools::getValue($this->name.'_footlastnews_limit'));
			if(is_numeric(Tools::getValue($this->name.'_footlastnews_actif')))
				Configuration::updateValue($this->name.'_footlastnews_actif', (int)Tools::getValue($this->name.'_footlastnews_actif'));
			if(is_numeric(Tools::getValue($this->name.'_footlastnews_showall')))
				Configuration::updateValue($this->name.'_footlastnews_showall', (int)Tools::getValue($this->name.'_footlastnews_showall'));
			if(is_numeric(Tools::getValue($this->name.'_footlastnews_intro')))
				Configuration::updateValue($this->name.'_footlastnews_intro', (int)Tools::getValue($this->name.'_footlastnews_intro'));
			if(is_numeric(Tools::getValue($this->name.'_footer_title_length')))
				Configuration::updateValue($this->name.'_footer_title_length', (int)Tools::getValue($this->name.'_footer_title_length'));
			if(is_numeric(Tools::getValue($this->name.'_footer_intro_length')))
				Configuration::updateValue($this->name.'_footer_intro_length', (int)Tools::getValue($this->name.'_footer_intro_length'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configBlocs');
		}
		elseif (Tools::isSubmit('submitConfBlocDateNews')) {
			if(is_numeric(Tools::getValue($this->name.'_datenews_actif')))
				Configuration::updateValue($this->name.'_datenews_actif', (int)Tools::getValue($this->name.'_datenews_actif'));
			if(is_numeric(Tools::getValue($this->name.'_datenews_showall')))
				Configuration::updateValue($this->name.'_datenews_showall', (int)Tools::getValue($this->name.'_datenews_showall'));
			Configuration::updateValue($this->name.'_datenews_order', Tools::getValue($this->name.'_datenews_order'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configBlocs');
		}
		elseif (Tools::isSubmit('submitConfBlocCatNews')) {
			if(is_numeric(Tools::getValue($this->name.'_catnews_actif')))
				Configuration::updateValue($this->name.'_catnews_actif', (int)Tools::getValue($this->name.'_catnews_actif'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_showall')))
				Configuration::updateValue($this->name.'_catnews_showall', (int)Tools::getValue($this->name.'_catnews_showall'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_empty')))
				Configuration::updateValue($this->name.'_catnews_empty', (int)Tools::getValue($this->name.'_catnews_empty'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_tree')))
				Configuration::updateValue($this->name.'_catnews_tree', (int)Tools::getValue($this->name.'_catnews_tree'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_shownbnews')))
				Configuration::updateValue($this->name.'_catnews_shownbnews', (int)Tools::getValue($this->name.'_catnews_shownbnews'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_showthumb')))
				Configuration::updateValue($this->name.'_catnews_showthumb', (int)Tools::getValue($this->name.'_catnews_showthumb'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_showintro')))
				Configuration::updateValue($this->name.'_catnews_showintro', (int)Tools::getValue($this->name.'_catnews_showintro'));
			if(is_numeric(Tools::getValue($this->name.'_cat_title_length')))
				Configuration::updateValue($this->name.'_cat_title_length', (int)Tools::getValue($this->name.'_cat_title_length'));
			if(is_numeric(Tools::getValue($this->name.'_cat_intro_length')))
				Configuration::updateValue($this->name.'_cat_intro_length', (int)Tools::getValue($this->name.'_cat_intro_length'));
			if(is_numeric(Tools::getValue($this->name.'_catnews_rss')))
				Configuration::updateValue($this->name.'_catnews_rss', (int)Tools::getValue($this->name.'_catnews_rss'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configCategories');
		}
		elseif (Tools::isSubmit('submitConfRewrite')) {
			if(is_numeric(Tools::getValue($this->name.'_rewrite_actif')))
				Configuration::updateValue($this->name.'_rewrite_actif', (int)Tools::getValue($this->name.'_rewrite_actif'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configModule');
		}
		elseif (Tools::isSubmit('submitConfGobalFront')) {
			if(is_numeric(Tools::getValue($this->name.'_nb_liste_page')))
				Configuration::updateValue($this->name.'_nb_liste_page', (int)Tools::getValue($this->name.'_nb_liste_page'));
			if(is_numeric(Tools::getValue($this->name.'_producttab_actif')))
				Configuration::updateValue($this->name.'_producttab_actif', (int)Tools::getValue($this->name.'_producttab_actif'));
			if(is_numeric(Tools::getValue($this->name.'_socials_actif')))
				Configuration::updateValue($this->name.'_socials_actif', (int)Tools::getValue($this->name.'_socials_actif'));
			if(is_numeric(Tools::getValue($this->name.'_uniqnews_rss')))
				Configuration::updateValue($this->name.'_uniqnews_rss', (int)Tools::getValue($this->name.'_uniqnews_rss'));
			if(is_numeric(Tools::getValue($this->name.'_view_news_img')))
				Configuration::updateValue($this->name.'_view_news_img', (int)Tools::getValue($this->name.'_view_news_img'));

				
			Tools::redirectAdmin($this->PathModuleConf.'&configModule');
		}
		elseif (Tools::isSubmit('submitConfCategory')) {
			if(is_numeric(Tools::getValue($this->name.'_view_cat_desc')))
				Configuration::updateValue($this->name.'_view_cat_desc', (int)Tools::getValue($this->name.'_view_cat_desc'));
			if(is_numeric(Tools::getValue($this->name.'_view_cat_thumb')))
				Configuration::updateValue($this->name.'_view_cat_thumb', (int)Tools::getValue($this->name.'_view_cat_thumb'));
			if(is_numeric(Tools::getValue($this->name.'_view_cat_img')))
				Configuration::updateValue($this->name.'_view_cat_img', (int)Tools::getValue($this->name.'_view_cat_img'));
				
			$xml = Tools::file_get_contents($configFile = _PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get($this->name.'_theme').'/config.xml');

			$remplacement = '
		<thumb> <!--Image prevue pour les miniatures dans les listes -->
			<width>'.Tools::getValue('thumb_cat_width').'</width>
			<height>'.Tools::getValue('thumb_cat_height').'</height>
		</thumb>
		<full> <!--Image prevue pour la description de la catégorie en liste 1ère page -->
			<width>'.Tools::getValue('full_cat_width').'</width>
			<height>'.Tools::getValue('full_cat_height').'</height>
		</full>
	';
			
			$xml=preg_replace('#<categories[^>]*>.*?</categories>#si','<categories>'.$remplacement.'</categories>',$xml);
			
			if (is_writable(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/')) {
				file_put_contents(_PS_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/config.xml', utf8_encode($xml));
				Tools::redirectAdmin($this->PathModuleConf.'&configCategories');
			}
		}
		elseif (Tools::isSubmit('submitConfGobalAdmin')) {
			if(is_numeric(Tools::getValue($this->name.'_nb_car_min_linkprod')))
				Configuration::updateValue($this->name.'_nb_car_min_linkprod', (int)Tools::getValue($this->name.'_nb_car_min_linkprod'));
			if(is_numeric(Tools::getValue($this->name.'_nb_list_linkprod')))
				Configuration::updateValue($this->name.'_nb_list_linkprod', (int)Tools::getValue($this->name.'_nb_list_linkprod'));
			if(is_numeric(Tools::getValue($this->name.'_nb_news_pl')))
				Configuration::updateValue($this->name.'_nb_news_pl', (int)Tools::getValue($this->name.'_nb_news_pl'));
			if(is_numeric(Tools::getValue($this->name.'_nb_comments_pl')))
				Configuration::updateValue($this->name.'_nb_comments_pl', (int)Tools::getValue($this->name.'_nb_comments_pl'));
			if(is_numeric(Tools::getValue($this->name.'_comment_div_visible')))
				Configuration::updateValue($this->name.'_comment_div_visible', (int)Tools::getValue($this->name.'_comment_div_visible'));
				
			Tools::redirectAdmin($this->PathModuleConf.'&configModule');
		}
		elseif (Tools::isSubmit('submitConfMenuCatBlog')) {
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_index')))
				Configuration::updateValue($this->name.'_menu_cat_blog_index', (int)Tools::getValue($this->name.'_menu_cat_blog_index'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_list')))
				Configuration::updateValue($this->name.'_menu_cat_blog_list', (int)Tools::getValue($this->name.'_menu_cat_blog_list'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_article')))
				Configuration::updateValue($this->name.'_menu_cat_blog_article', (int)Tools::getValue($this->name.'_menu_cat_blog_article'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_empty')))
				Configuration::updateValue($this->name.'_menu_cat_blog_empty', (int)Tools::getValue($this->name.'_menu_cat_blog_empty'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_home_link')))
				Configuration::updateValue($this->name.'_menu_cat_home_link', (int)Tools::getValue($this->name.'_menu_cat_home_link'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_home_img')))
				Configuration::updateValue($this->name.'_menu_cat_home_img', (int)Tools::getValue($this->name.'_menu_cat_home_img'));
			// if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_rss')))
			// 	Configuration::updateValue($this->name.'_menu_cat_blog_rss', (int)Tools::getValue($this->name.'_menu_cat_blog_rss'));
			if(is_numeric(Tools::getValue($this->name.'_menu_cat_blog_nbnews')))
				Configuration::updateValue($this->name.'_menu_cat_blog_nbnews', (int)Tools::getValue($this->name.'_menu_cat_blog_nbnews'));
				
			Tools::redirectAdmin($this->PathModuleConf.'&configCategories');
		}
		elseif (Tools::isSubmit('submitConfComment')) {
			if(is_numeric(Tools::getValue($this->name.'_comment_actif')))
				Configuration::updateValue($this->name.'_comment_actif', (int)Tools::getValue($this->name.'_comment_actif'));
			if(is_numeric(Tools::getValue($this->name.'_comment_only_login')))
				Configuration::updateValue($this->name.'_comment_only_login', (int)Tools::getValue($this->name.'_comment_only_login'));
			if(is_numeric(Tools::getValue($this->name.'_comment_auto_actif')))
				Configuration::updateValue($this->name.'_comment_auto_actif', (int)Tools::getValue($this->name.'_comment_auto_actif'));
			if(is_numeric(Tools::getValue($this->name.'_comment_autoshow')))
				Configuration::updateValue($this->name.'_comment_autoshow', (int)Tools::getValue($this->name.'_comment_autoshow'));
			if(is_numeric(Tools::getValue($this->name.'_comment_nofollow')))
				Configuration::updateValue($this->name.'_comment_nofollow', (int)Tools::getValue($this->name.'_comment_nofollow'));
			if(is_numeric(Tools::getValue($this->name.'_comment_alert_admin')))
				Configuration::updateValue($this->name.'_comment_alert_admin', (int)Tools::getValue($this->name.'_comment_alert_admin'));
			if(is_numeric(Tools::getValue($this->name.'_comment_subscription')))
				Configuration::updateValue($this->name.'_comment_subscription', (int)Tools::getValue($this->name.'_comment_subscription'));

			
			Configuration::updateValue($this->name.'_comment_admin_mail', Tools::getValue($this->name.'_comment_admin_mail'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&configComments');
		}
		elseif (Tools::isSubmit('submitImportBackup')) {
			if(!$this->demoMode)
				if (isset($_FILES[$this->name.'_import_backup']) && is_uploaded_file($_FILES[$this->name.'_import_backup']['tmp_name']))
				{
					if ($_FILES[$this->name.'_import_backup']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$errors[] = sprintf(
							$this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is: %2$d kB.'),
							(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
							number_format(($_FILES[$this->name.'_import_backup']['size'] / 1024), 2, '.', '')
						);
					else
					{
						do $uniqid = md5(date("YmdHis"));
						while (file_exists(_PS_MODULE_DIR_.'prestablog/backup/prestablog_backup_'.$uniqid));
						if (!copy($_FILES[$this->name.'_import_backup']['tmp_name'], _PS_MODULE_DIR_.'prestablog/backup/prestablog_backup_'.$uniqid.'.zip'))
							$errors[] = $this->l('File copy failed');
						
						@unlink($_FILES[$this->name.'_import_backup']['tmp_name']);
					}
				}
				else
					Tools::redirectAdmin($this->PathModuleConf.'&backup'."&feedback=87Yu8thV");
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&restoreBackup&file=prestablog_backup_'.$uniqid.'.zip');
		}
		elseif (Tools::isSubmit('restoreBackup') && Tools::getValue('file')) {
			$postEnCours = true;
			
			//$checksumBackupFile = preg_replace(array('/prestablog_backup_/','/.zip/') , array('','') , Tools::getValue('file'));
			
			if(!Tools::ZipExtract(_PS_MODULE_DIR_.'prestablog/backup/'.Tools::getValue('file'), _PS_MODULE_DIR_.'prestablog/backup/'))
				$errors[] = $this->l('Error extract backup file to restore.');
			else {
				// import thèmes
				$checksumBackupFile = $this->ScanDirectory(_PS_MODULE_DIR_.'prestablog/backup');
				$checksumBackupFile = $checksumBackupFile[0];
				
				if(file_exists(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/themes')) {
					$dirThemes = $this->ScanFilesDirectory(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/themes');
					foreach($dirThemes As $KeyBackupTheme => $ValueBackupTheme) {
						$ThemeName = preg_replace(array('/.zip/') , array('') , $ValueBackupTheme);
						
						$this->rrmdir(_PS_MODULE_DIR_.'prestablog/themes/'.$ThemeName);
						
						if(!Tools::ZipExtract(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/themes/'.$ValueBackupTheme, _PS_MODULE_DIR_.'prestablog/themes/'))
							$errors[] = sprintf( $this->l('Folder backup theme %s copy failed') , $ThemeName );
					}
				}

				if(file_exists(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/mails')) {
					$dirMails = $this->ScanFilesDirectory(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/mails');
					foreach($dirMails As $KeyBackupMail => $ValueBackupMail) {
						$MailName = preg_replace(array('/.zip/') , array('') , $ValueBackupMail);
						
						$this->rrmdir(_PS_MODULE_DIR_.'prestablog/mails/'.$MailName);
						
						if(!Tools::ZipExtract(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/mails/'.$ValueBackupMail, _PS_MODULE_DIR_.'prestablog/mails/'))
							$errors[] = sprintf( $this->l('Folder backup mail %s copy failed') , $MailName );
					}
				}

				if(file_exists(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/translations')) {
					$dirTranslations = $this->ScanFilesDirectory(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/translations');
					foreach($dirTranslations As $KeyBackupTranslation => $ValueBackupTranslation) {
						$trad_origine = '';
						if(file_exists(_PS_MODULE_DIR_.'prestablog/translations/'.$ValueBackupTranslation))
							$trad_origine = Tools::file_get_contents(_PS_MODULE_DIR_.'prestablog/translations/'.$ValueBackupTranslation);
						file_put_contents( // attention, les modifications faites dans la vesrion du backup prennent le dessus sur les traductions d'origines du module
								_PS_MODULE_DIR_.'prestablog/translations/'.$ValueBackupTranslation, // nouveau fichier à restaurer
								$this->compareTraductionsEtMergeAll(
										Tools::file_get_contents(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/translations/'.$ValueBackupTranslation), // fichier contenu dans le backup
										$trad_origine // fichier d'origine du module
								)
						);
					}
				}
				
				// parse SQL
				$fileToParse = _PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/blog.sql';
				if(file_exists($fileToParse)) {
					include_once($this->ModulePath.'/class/sqlparse.php');
					$sql_loader = new SqlParse();
					if(!$sql_loader->parse_file($fileToParse))
						$errors[] = $this->l('The parse sql is failed.');
				}
        
				// import configuration
				if(file_exists(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/blog.conf')) {
					$configBackupFile = Tools::file_get_contents(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile.'/blog.conf');
					$arrayConfigBackup = unserialize($configBackupFile);
					
					// $arrayKeysConfig['multishop'] = $Shops;
					// $arrayKeysConfig['languages'] = Language::getLanguages();

					$Shops = Shop::getShops();
					foreach ($Shops as $keyShop => $valueShop) {
						foreach($arrayConfigBackup['configuration'][$keyShop] As $ConfigurationKey => $ConfigurationValue)
							Configuration::updateValue($ConfigurationKey, $ConfigurationValue, false, null, (int)$keyShop);			
					}
				}
				
				// supprimer le dossier temporaire
				$this->rrmdir(_PS_MODULE_DIR_.'prestablog/backup/'.$checksumBackupFile);
			}
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&backup'."&feedback=2pTtfC78");
		}
		elseif (Tools::isSubmit('deleteBackup') && Tools::getValue('file')) {
			unlink(_PS_MODULE_DIR_.'prestablog/backup/'.Tools::getValue('file'));
			
			Tools::redirectAdmin($this->PathModuleConf.'&backup');
		}
		elseif (Tools::isSubmit('submitBackup') && is_array(Tools::getValue($this->name.'_backup_conf'))) {
			$postEnCours = true;
			if(!$this->demoMode) {
				//$this->rrmdir(_PS_MODULE_DIR_.'prestablog/backup/');
				/** les fichiers backup seront uniques */
				$dossierBackupTmp = md5(date("YmdHis"));
				$configBackup = Tools::getValue($this->name.'_backup_conf');
				if(!is_dir(_PS_MODULE_DIR_.'prestablog/backup'))
					if(!mkdir(_PS_MODULE_DIR_.'prestablog/backup/'))
						$errors[] = $this->l('Error creating temporary main backup folder.');
				
				/** protegeons le dossier des backups */
				if(!is_file(_PS_MODULE_DIR_.'prestablog/backup/index.php'))
					file_put_contents(_PS_MODULE_DIR_.'prestablog/backup/index.php', $this->indexFileProtect);
				
				if(!mkdir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp))
					$errors[] = $this->l('Error creating temporary backup folder.');
				
				if(!sizeof($errors)) {
					include_once($this->ModulePath.'/class/HZip.php');
					if(in_array("config", $configBackup)) {
						$Shops = Shop::getShops();
						$arrayKeysConfig=array();
						foreach(Language::getLanguages() as $key => $value) {
							$arrayKeysConfig['languages'][$value["id_lang"]] = $value["iso_code"];
						}
						foreach($Shops as $key => $value) {
							$arrayKeysConfig['multishop'][$value["id_shop"]] = $value["domain"];
							$arrayKeysConfig['configuration'][$key] = Configuration::getMultiple(array_keys($this->Configurations), null, null, $key);
						}
						file_put_contents(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/blog.conf', serialize($arrayKeysConfig));
					}
					if(in_array("bdd", $configBackup)) {
						file_put_contents(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/blog.sql', $this->dumpMySQLBlog());
					}
					if(sizeof($configBackup["themes"]))
						if(!mkdir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/themes'))
							$errors[] = $this->l('Error creating backup themes folder.');
						foreach($configBackup["themes"] As $keyTheme => $valueTheme) {
							HZip::zipDir(_PS_MODULE_DIR_.'prestablog/themes/'.$valueTheme, _PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/themes/'.$valueTheme.'.zip');
						}
					if(sizeof($configBackup["translations"]))
						if(!mkdir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/translations'))
							$errors[] = $this->l('Error creating backup translations folder.');
						foreach($configBackup["translations"] As $KeyTranslation => $ValueTranslation) {
							if(!copy(_PS_MODULE_DIR_.'prestablog/translations/'.$ValueTranslation, _PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/translations/'.$ValueTranslation))
								$errors[] = $this->l('Error creating backup translations for '.$ValueTranslation);
						}
					if(sizeof($configBackup["mails"]))
						if(!mkdir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/mails'))
							$errors[] = $this->l('Error creating backup mails folder.');
						foreach($configBackup["mails"] As $keyMail => $valueMail) {
							HZip::zipDir(_PS_MODULE_DIR_.'prestablog/mails/'.$valueMail, _PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp.'/mails/'.$valueMail.'.zip');
						}

					HZip::zipDir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp, _PS_MODULE_DIR_.'prestablog/backup/prestablog_backup_'.md5(date("YmdHis")).'.zip');
					$this->rrmdir(_PS_MODULE_DIR_.'prestablog/backup/'.$dossierBackupTmp);
				}
			}
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&backup'."&feedback=6R9ba6mR");
		}
		elseif (Tools::isSubmit('submitParseXml')) {
			include_once($this->ModulePath.'/class/Xml.php');
			$xmlString = Tools::file_get_contents(_PS_UPLOAD_DIR_.Configuration::get($this->name.'_import_xml'));
			$xmlArray = Xml::toArray(Xml::build($xmlString));
		
			// echo '<pre style="font-size:11px;text-align:left">';
			//  	print_r($xmlArray["rss"]["channel"]["wp:category"]);
			// echo '</pre>';

			if(sizeof($xmlArray["rss"]["channel"]["wp:category"])) {
				$langue = (int)Tools::getValue('import_xml_langue');
				// créons d'abord toutes les cat si elles n'existent pas
				// sans principe d'arborescence
				$ModifCategoriesParents = array();
				$CategoriesTitle = array();
				foreach ($xmlArray["rss"]["channel"]["wp:category"] as $key => $value) {
					$CategoriesTitle[$value["wp:category_nicename"]] = $value["wp:cat_name"];
					if($id_import_category = (int)CategoriesClass::isCategoriesExist((int)$langue, $value["wp:cat_name"]) > 0) {
						 $id_import_category = $id_import_category;
					}
					else {
						$Categorie = new CategoriesClass();
						$Categorie->id_shop = (int)$this->context->shop->id;
						$Categorie->title[(int)$langue] = $value["wp:cat_name"];
						$Categorie->add();
						$id_import_category = $Categorie->id;
					}
					if($value["wp:category_parent"] != "") {
						$ModifCategoriesParents[$value["wp:category_nicename"]]["id_import_category"] = $id_import_category;
						$ModifCategoriesParents[$value["wp:category_nicename"]]["parent"] = $value["wp:category_parent"];
						//$ModifCategoriesParents[$value["wp:category_nicename"]]["parent_title"] = $value["wp:category_parent"];
						$ModifCategoriesParents[$value["wp:category_nicename"]]["title"] = $value["wp:cat_name"];
					}
				}

				// echo '<pre style="font-size:11px;text-align:left">';
				//  	print_r($ModifCategoriesParents);
				// echo '</pre>';

				// on recherche toutes les cat qui sont rangés dans des parents
				// et on leur assigne l'id de leur parent
				foreach ($ModifCategoriesParents as $key => $value) {
						// on recherche l'id parent précédement préparé dans $ModifCategoriesParents
						$id_import_category = (int)CategoriesClass::isCategoriesExist((int)$langue, $value["title"]);

						$Categorie = new CategoriesClass((int)$id_import_category);
						$Categorie->parent = (int)CategoriesClass::isCategoriesExist((int)$langue, $CategoriesTitle[$value["parent"]]);

						// echo '<pre style="font-size:11px;text-align:left">';
						//  	print_r($Categorie);
						// echo '</pre>';

						$Categorie->save();

				}
			}

			// echo '<pre style="font-size:11px;text-align:left">';
			//  	print_r($this->importCategoriesArborescence);
			// echo '</pre>';

			if(sizeof($xmlArray["rss"]["channel"]["item"])) {
				foreach($xmlArray["rss"]["channel"]["item"] As $kItem => $vItem) {
					if($vItem["wp:post_type"] == "post") {
						$Post = new NewsClass();
						$Post->id_shop = (int)$this->context->shop->id;
						$Post->date					= $vItem["wp:post_date"];
						$Post->langues				= serialize(
																array(
																		0 => (int)Tools::getValue('import_xml_langue')
																	)
																);
						
						if($vItem["wp:status"] == "publish")
							$Post->actif			= 1;
						else
							$Post->actif			= 0;
										
						$Post->title[(int)Tools::getValue('import_xml_langue')]			= $vItem["title"];
						$Post->paragraph[(int)Tools::getValue('import_xml_langue')]		= $vItem["excerpt:encoded"];
						$Post->content[(int)Tools::getValue('import_xml_langue')]		= $vItem["content:encoded"];
						$Post->meta_title[(int)Tools::getValue('import_xml_langue')]	= $vItem["title"];
						$Post->link_rewrite[(int)Tools::getValue('import_xml_langue')]	= $vItem["wp:post_name"];
						
						/** gestion des catégories et tags */
						if(isset($vItem["category"]) && sizeof($vItem["category"])) {
							$importCategories = array();
							$importCategoriesId = array();
							if(isset($vItem["category"]["@domain"])) { // il n'y a qu'un seul comment
								/** gestion des catégories */
								if($vItem["category"]["@domain"] == "category")
									$importCategories[] = $vItem["category"]["@"];
								
								/** gestion des tags > keywords */
								if($vItem["category"]["@domain"] == "post_tag")
									$keyWords = $vItem["category"]["@"];
							}
							else {
								/** gestion des catégories */
								if(sizeof($vItem["category"])) {
									foreach($vItem["category"] As $kCategory => $vCategory)
										if($vCategory["@domain"] == "category")
											$importCategories[] = $vCategory["@"];
									$importCategories = array_unique($importCategories);
								}
								
								/** gestion des tags > keywords */
								$importTags = array();
								if(sizeof($vItem["category"])) {
									foreach($vItem["category"] As $kTag => $vTag)
										if($vTag["@domain"] == "post_tag")
											$importTags[] = $vTag["@"];
									$importTags = array_unique($importTags);
								}
								$keyWords="";
								if(sizeof($importTags)) {
									foreach($importTags As $kImportTag => $vImportTag)
										$keyWords .= $vImportTag.', ';
								}
								$keyWords = rtrim($keyWords, ', ');
							}
							
							if(sizeof($importCategories)) {
								foreach($importCategories As $kImportCategorie => $vImportCategorie) {
									if($id_import_category = CategoriesClass::isCategoriesExist((int)Tools::getValue('import_xml_langue'), $vImportCategorie)) {
										$importCategoriesId[] = $id_import_category;
									}
									else {
										$Categorie = new CategoriesClass();
										$Categorie->id_shop = (int)$this->context->shop->id;
										$Categorie->title[(int)Tools::getValue('import_xml_langue')] = $vImportCategorie;
										$Categorie->add();
										$importCategoriesId[] = $Categorie->id;
									}
								}
							}
							
							$Post->meta_keywords[(int)Tools::getValue('import_xml_langue')]	= $keyWords;
						}
						
						$Post->add();
						if($Post->id) {
							$Post->razEtatLangue((int)$Post->id);
							$Post->changeActiveLangue((int)$Post->id, (int)Tools::getValue('import_xml_langue'));
							
							/** gestion des commentaires */
							if(isset($vItem["wp:comment"]) && sizeof($vItem["wp:comment"])) {
								$Comment = new CommentNewsClass();
								if(isset($vItem["wp:comment"]["wp:comment_author"])) { // il n'y a qu'un seul comment
									$Comment->news		= $Post->id;
									$Comment->name		= Tools::substr($vItem["wp:comment"]["wp:comment_author"], 0, 254);
									$Comment->url		= $vItem["wp:comment"]["wp:comment_author_url"];
									$Comment->comment	= $vItem["wp:comment"]["wp:comment_content"];
									$Comment->date		= $vItem["wp:comment"]["wp:comment_date"];
									
									if((int)$vItem["wp:comment"]["wp:comment_approved"] == 1)
										$Comment->actif		= 1;
									else
										$Comment->actif		= 0;
									
									$Comment->add();
								}
								else {
									foreach($vItem["wp:comment"] As $kComment => $vComment) { // il y a plusieurs comments
										$Comment = new CommentNewsClass();
										
										$Comment->news		= $Post->id;
										$Comment->name		= Tools::substr($vComment["wp:comment_author"], 0, 254);
										$Comment->url		= $vComment["wp:comment_author_url"];
										$Comment->comment	= $vComment["wp:comment_content"];
										$Comment->date		= $vComment["wp:comment_date"];
										
										if((int)$vComment["wp:comment_approved"] == 1)
											$Comment->actif		= 1;
										else
											$Comment->actif		= 0;
										
										$Comment->add();
									}
								}
							}
							/** liaison des catégories aux articles */
							if(sizeof($importCategoriesId))
								CorrespondancesCategoriesClass::updateCategoriesNews($importCategoriesId, $Post->id);
						}
					} // fin if [wp:post_type] => post
				} // fin foreach
			}
			else {
				$errors[] = $this->l('No items to import');
			}
			
			if(!sizeof($errors)) {
				@unlink(_PS_UPLOAD_DIR_.Configuration::get($this->name.'_import_xml'));
				Configuration::updateValue($this->name.'_import_xml', NULL);
				Tools::redirectAdmin($this->PathModuleConf.'&import'."&feedback=Yu3Tr9r7");
			}
		}
		elseif (Tools::isSubmit('submitImportXml')) {
			if(!$this->demoMode)
				if (isset($_FILES[$this->name.'_import_xml']) && is_uploaded_file($_FILES[$this->name.'_import_xml']['tmp_name']))
				{
					if ($_FILES[$this->name.'_import_xml']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$errors[] = sprintf(
							$this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is: %2$d kB.'),
							(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
							number_format(($_FILES[$this->name.'_import_xml']['size'] / 1024), 2, '.', '')
						);
					else
					{
						do $uniqid = sha1(microtime());
						while (file_exists(_PS_UPLOAD_DIR_.$uniqid));
						if (!copy($_FILES[$this->name.'_import_xml']['tmp_name'], _PS_UPLOAD_DIR_.$uniqid))
							$errors[] = $this->l('File copy failed');
						
						@unlink($_FILES[$this->name.'_import_xml']['tmp_name']);
						@unlink(_PS_UPLOAD_DIR_.Configuration::get($this->name.'_import_xml'));
						Configuration::updateValue($this->name.'_import_xml', $uniqid);
					}
				}
				else
					Tools::redirectAdmin($this->PathModuleConf.'&import'."&feedback=2yt6wEK7");
			
			if(!sizeof($errors))
				Tools::redirectAdmin($this->PathModuleConf.'&import');
		}
		
		if($postEnCours) {
			if(sizeof($errors))
				$this->_html .= $this->displayError(implode('<br />', $errors));
			else
				$this->_html .= $this->displayConfirmation($this->l('Settings updated successfully'));
		}
	}
	
	public function displayError($error) {
		if($this->IsPSVersion(">=","1.6")) {
			$output = '	<div class="bootstrap">
								<div class="alert alert-danger">
									<button class="close" data-dismiss="alert" type="button">×</button>
									<strong>'.$this->l('Error').'</strong><br>
									'.$error.'
								‌</div>
							</div>';
		}
		else {
			$output = '	<div class="error">
							‌	<span style="float:right">
							‌			<a id="hideError" href="#"><img src="../img/admin/close.png" alt="X"></a>
								</span>
								<strong>'.$this->l('Error').'</strong><br>
								'.$error.'
							</div>';
		}

		$this->error = true;
		return $output;
	}
	
	public function displayWarning($warn) {
		if($this->IsPSVersion(">=","1.6")) {
			$output = '	<div class="bootstrap">
								<div class="alert alert-warning">
									<button class="close" data-dismiss="alert" type="button">×</button>
									<strong>'.$this->l('Warning').'</strong><br/>
									'.$warn.'
								‌</div>
							</div>';
		}
		else {
			$output = '	<div class="warn">
								<span style="float:right">
									<a id="hideWarn" href="">
										<img src="../img/admin/close.png" alt="X">
									</a>
								</span>
								<strong>'.$this->l('Warning').'</strong><br/>
								'.$warn.'
							</div>';
		}

		return $output;
	}	

	public function displayInfo($info) {
		if($this->IsPSVersion(">=","1.6")) {
			$output = '	<div class="bootstrap">
								<div class="alert alert-info">
									<strong>'.$this->l('Information').'</strong><br/>
									'.$info.'
								‌</div>
							</div>';
		}
		else {
			$output = '	<div class="hint" style="display:block;">
								<strong>'.$this->l('Information').'</strong><br/>
								'.$info.'
							</div>';
		}

		return $output;
	}
	
	public function _getVerification() {
		$errors = $warnings = array();
		if(sizeof($this->CopyFiles))
			foreach($this->CopyFiles As $Folder => $File) {
				if(!file_exists(_PS_ROOT_DIR_.$Folder.$File)) {
					$errors[] = $this->l('The file').' <span style="color:#FF0000;">'._PS_ROOT_DIR_.$Folder.$File.'</span> '.$this->l('is not present');
					$warnings[] = $this->l('Copy this source file :').' <span style="color:#0000FF;">'._PS_MODULE_DIR_.$this->name.'/cpy'.$Folder.$File.'</span></br>'.$this->l('to this destination :').' <span style="color:#009000;">'._PS_ROOT_DIR_.$Folder.$File.'</span>';
				}
			}
			
		if(sizeof($errors)) {
			$MessageBefore = $this->l('Be carefull ! Those files are not present after install, due to permissions write.').'<br /><br />';
			$this->_html .= $this->displayError($MessageBefore.implode('<br />', $errors));
			$this->_html .= $this->displayWarning($this->l('To solve this problem, you must copy/paste those file manually :').'<br /><br />'.implode('<br /><br />', $warnings));
		}
	}
	
	private function ModuleDatepicker($class, $time)
	{
		$return = "";
		if ($time)
			$return = '
			var dateObj = new Date();
			var hours = dateObj.getHours();
			var mins = dateObj.getMinutes();
			var secs = dateObj.getSeconds();
			if (hours < 10) { hours = "0" + hours; }
			if (mins < 10) { mins = "0" + mins; }
			if (secs < 10) { secs = "0" + secs; }
			var time = " "+hours+":"+mins+":"+secs;';
		$return .= '
		$(function() {
			$("#'.Tools::htmlentitiesUTF8($class).'").datepicker({
				prevText:"",
				nextText:"",
				dateFormat:"yy-mm-dd"'.($time ? '+time' : '').'});
		});';
	 
		return '<script type="text/javascript">'.$return.'</script>';
	}
	
	public function CheckPresenceFoldersCritiques() {
		$errors = array();
		$success = array();
		
		// check les thèmes
		if(!is_dir(_PS_MODULE_DIR_.$this->name.'/themes')) {
			$errors[] = $this->l('No existing the module\'s templates root folder.');
			if(!mkdir(_PS_MODULE_DIR_.$this->name.'/themes'))
				$errors[] = $this->l('Error creating the module\'s templates root folder.');
			else
				$success[] = $this->l('Creating the module\'s templates root folder successfull.');
		}
		else {
			$listFolderTheme = $this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes');
			if(!in_array(Configuration::get($this->name.'_theme'), $listFolderTheme )) {
				$errors[] = sprintf($this->l('The template folder in the configuration called "%1$s" doesn\'t exists in the module\'s templates root folder.'), Configuration::get($this->name.'_theme'));
				if(!in_array($this->default_theme, $listFolderTheme )) {
					$errors[] = sprintf($this->l('The template folder in the default configuration called "%1$s" doesn\'t exists in the module\'s templates root folder.'), $this->default_theme);
					if(sizeof($listFolderTheme)) {
						$success[] = sprintf($this->l('The first folder template in the module\'s templates root folder called "%1$s" was activated.'), $listFolderTheme[0]);
						Configuration::updateValue($this->name.'_theme', $listFolderTheme[0]);
					}
					else {
						$errors[] = $this->l('No existing template in the module\'s templates root folder.');
						$errors[] = sprintf('<strong>'.$this->l('Please restore one folder template minimum and %1$srefresh this module page.').'</strong>', '<img src="../modules/'.$this->name.'/img/refresh.png" alt="" />');
					}
				}
				else {
					$success[] = sprintf($this->l('The template in the default configuration called "%1$s" was activated.'), $this->default_theme);
					$success[] = sprintf('<strong>'.$this->l('You can %1$srefresh this module page.').'</strong>', '<img src="../modules/'.$this->name.'/img/refresh.png" alt="" />');
					Configuration::updateValue($this->name.'_theme', $this->default_theme);
				}
			}
		}
		
		// check les translates mails
		if(!is_dir(_PS_MODULE_DIR_.$this->name.'/mails/en')) {
			$errors[] = $this->l('No existing the module\'s default "en" mails folder.');
			if(!Tools::ZipExtract(_PS_MODULE_DIR_.$this->name.'/mails/en.zip', _PS_MODULE_DIR_.$this->name.'/mails/'))
				$errors[] = $this->l('Error extract the module\'s default "en" mails folder.');
			else
				$success[] = $this->l('Restore the module\'s default "en" mails folder successfull.');
		}
		
		if(sizeof($errors)) {
			$this->_html = $this->displayError(implode('<br />', $errors));
			if(sizeof($success))
				$this->_html .= $this->displayConfirmation(implode('<br />', $success));
			$this->_html .= '<a href="'.$this->PathModuleConf.'" class="button"><img src="../modules/'.$this->name.'/img/refresh.png" alt="" />&nbsp;'.$this->l('Refresh this page to enter again on the configuration of module.').'</a>';
			
			return $this->_html;
		}
	}
	
	public function getContent()
	{
		// echo '<pre style="font-size:11px;text-align:left">';
		// 	print_r(unserialize('str'));
		// echo '</pre>';
		// if (1==1)
		// 	throw new PrestaShopException('a category cannot be it\'s own parent');
		
		if($ErrorCritique = $this->CheckPresenceFoldersCritiques())
			return $ErrorCritique;
		
		$this->CheckConfiguration();
		
		$this->_postForm();
		
		if(Tools::getValue("feedback")) {
			$this->_html .= '<script type="text/javascript">
			$(document).ready(function() { 
				jAlert( "'.$this->MessageCallBack[Tools::getValue("feedback")].'", "'.$this->l('Information').'" );
			});
			</script>';
		}
		
		$this->context->controller->addJqueryUI('ui.datepicker');
		
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		$this->_html .= '<link type="text/css" rel="stylesheet" href="'._MODULE_DIR_.$this->name.'/css/admin.css" />'."\n";
		$this->_html .= '<link type="text/css" rel="stylesheet" href="'.(Configuration::get('PS_SSL_ENABLED')?'https':'http').'://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />'."\n";
		$this->_html .= '<div id="hdclicconfiguration">';

		$this->_html .= '
		<nav>
			<ul >
				<li class="logohdclicmini">
					<a href="'.$this->url_hdclic.'" target="_blank"><img src="../modules/'.$this->name.'/img/logohdclicmini.jpg" alt="" /></a>
				</li>
				<li>
					<a href="'.$this->PathModuleConf.'"><img src="../modules/'.$this->name.'/img/home.png" alt="" />&nbsp;'.$this->l('Home').'</a>
				</li>';
		$this->_html .= '
				<li>
					<a href="#"><img src="../modules/'.$this->name.'/img/content.png" alt="" />&nbsp;'.$this->l('Manage content').'</a>
					<ul>
						<li>
							<a href="'.$this->PathModuleConf.'&newsListe"><img src="../modules/'.$this->name.'/img/copy_files.gif" alt="" />&nbsp;'.$this->l('News').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&commentListe"><img src="../modules/'.$this->name.'/img/comments.png" alt="" />&nbsp;'.$this->l('Comments').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&catListe"><img src="../modules/'.$this->name.'/img/categories.png" alt="" />&nbsp;'.$this->l('Categories').'</a>
						</li>
					</ul>
				</li>';
		$this->_html .= '
				<li>
					<a href="#"><img src="../modules/'.$this->name.'/img/tools.png" alt="" />&nbsp;'.$this->l('Tools').'</a>
					<ul>
						<li>
							<a href="'.$this->PathModuleConf.'&configAntiSpam"><img src="../modules/'.$this->name.'/img/shield.png" alt="" />&nbsp;'.$this->l('Anti-spam').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&import"><img src="../modules/'.$this->name.'/img/import.png" alt="" />&nbsp;'.$this->l('Import WordPress XML').'</a>
						</li>';
		// BETA DEV pour v3.1
		// $this->_html .= '				<li>
		// 					<a href="'.$this->PathModuleConf.'&backup"><img src="../modules/'.$this->name.'/img/backup.png" alt="" />&nbsp;'.$this->l('Backup / Restore').'</a>
		// 				</li>';
		$this->_html .= '			</ul>
				</li>';

		$this->_html .= '
				<li>
					<a href="#"><img src="../modules/'.$this->name.'/img/cog.gif" alt="" />&nbsp;'.$this->l('Configuration').'</a>
					<ul>';

		// BETA DEV pour v3.1
		// $this->_html .= '				<li>
		// 					<a href="'.$this->PathModuleConf.'&configWizard"><img src="../modules/'.$this->name.'/img/wizard.png" alt="" />&nbsp;'.$this->l('Wizard templating').'</a>
		// 				</li>';
		
		$this->_html .= '				<li>
							<a href="'.$this->PathModuleConf.'&configTheme"><img src="../modules/'.$this->name.'/img/theme.png" alt="" />&nbsp;'.$this->l('Theme and slide').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&pageBlog"><img src="../modules/'.$this->name.'/img/blog.png" alt="" />&nbsp;'.$this->l('Blog page').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&configCategories"><img src="../modules/'.$this->name.'/img/categories.png" alt="" />&nbsp;'.$this->l('Categories').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&configBlocs"><img src="../modules/'.$this->name.'/img/blocs.png" alt="" />&nbsp;'.$this->l('Blocks').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&configComments"><img src="../modules/'.$this->name.'/img/comments.png" alt="" />&nbsp;'.$this->l('Comments').'</a>
						</li>
						<li>
							<a href="'.$this->PathModuleConf.'&configModule"><img src="../modules/'.$this->name.'/img/globalconf.png" alt="" />&nbsp;'.$this->l('Global').'</a>
						</li>
					</ul>
				</li>';
		$this->_html .= '
				<li>
					<a href="#"><img src="../modules/'.$this->name.'/img/help.png" alt="" />&nbsp;'.$this->l('Help').'</a>
					<ul>
						<li>
							<a href="'.$this->PathModuleConf.'&informations"><img src="../modules/'.$this->name.'/img/informations.png" alt="" />&nbsp;'.$this->l('Informations').'</a>
						</li>
						<li>
							<a href="'.$this->url_faq_hdclic.'" target="_blank"><img src="../modules/'.$this->name.'/img/faq.png" alt="" />&nbsp;'.$this->l('Faq').'</a>
						</li>
						<li>
							<a href="'.$this->url_tuto_hdclic.'" target="_blank"><img src="../modules/'.$this->name.'/img/tutoriel.png" alt="" />&nbsp;'.$this->l('Tutorial').'</a>
						</li>
					</ul>
				</li>';
				
						//~ <li>
							//~ <a href="'.$this->PathModuleConf.'&debug" title="'.$this->l('Debug').'"><img src="../modules/'.$this->name.'/img/debug.png" alt="" />&nbsp;'.$this->l('Debug').'</a>
						//~ </li>

		$this->_html .= '
					<li id="nav-version">
						'.$this->l('Version').' : '.$this->version.($this->demoMode ? ' / '.$this->l('Demo mode') : '').'
					</li>
				</ul>
			</nav>
			<div id="contenu_config_prestablog">';
		
		if ( 	Tools::isSubmit('addNews') 
			||	Tools::isSubmit('editNews') 
			||	Tools::isSubmit('submitAddNews') 
			|| ( Tools::isSubmit('submitUpdateNews') && Tools::getValue('idN') ) 
			) {
			$this->_displayFormNews();
		}
		elseif ( 	Tools::isSubmit('addCat') 
				||	Tools::isSubmit('editCat') 
				||	Tools::isSubmit('submitAddCat') 
				|| ( Tools::isSubmit('submitUpdateCat') && Tools::getValue('idC') ) 
				) {
			$this->_displayFormCategories();
		}
		elseif ( 	Tools::isSubmit('addAntiSpam') 
				||	Tools::isSubmit('editAntiSpam') 
				||	Tools::isSubmit('submitAddAntiSpam') 
				|| ( Tools::isSubmit('submitUpdateAntiSpam') && Tools::getValue('idAS') ) 
				) {
			$this->_displayFormAntiSpam();
		}
		elseif (	Tools::isSubmit('editComment') 
				|| ( Tools::isSubmit('submitUpdateComment') && Tools::getValue('idC') ) 
				) {
			$this->_displayFormComments();
		}
		elseif (Tools::isSubmit('pageBlog')) {
			$this->_displayPageBlog();
		}
		elseif (Tools::isSubmit('configAntiSpam')) {
			$this->_displayConfigAntiSpam();
		}
		elseif (Tools::isSubmit('configModule')) {
			$this->_displayConf();
		}
		elseif (Tools::isSubmit('configTheme')) {
			$this->_displayConfTheme();
			$this->_displayConfSlide();
		}
		elseif (Tools::isSubmit('configWizard')) {
			$this->_displayConfWizard();
		}
		elseif (Tools::isSubmit('configCategories')) {
			$this->_displayConfCategories();
		}
		elseif (Tools::isSubmit('configBlocs')) {
			$this->_displayConfBlocs();
		}
		elseif (Tools::isSubmit('configProductTab')) {
			$this->_displayConfProductTab();
		}
		elseif (Tools::isSubmit('configComments')) {
			$this->_displayConfComments();
		}
		elseif (Tools::isSubmit('debug')) {
			$this->_displayDebug();
		}
		elseif (Tools::isSubmit('informations')) {
			$this->_displayInformations();
		}
		elseif (Tools::isSubmit('version')) {
			//$this->_displayVersion();
		}
		elseif (Tools::isSubmit('import')) {
			$this->_displayImport();
		}
		elseif (Tools::isSubmit('backup')) {
			$this->_displayBackup();
		}
		elseif (Tools::isSubmit('catListe')) {
			$this->_displayListeCategories($ConfigTheme);
		}
		elseif (Tools::isSubmit('newsListe')) {
			$this->_displayListeNews($ConfigTheme);
		}
		elseif (Tools::isSubmit('commentListe')) {
			$this->_displayListeComments();
		}
		else {
			$this->_displayHome($ConfigTheme);
		}
		$this->_html .= '
			</div>';
		$this->_html .= '</div>';
		
		return $this->_html;
	}
	
	private function compareTraductionsEtMergeAll($new , $old) {
		$trad_=array();
		if(preg_match_all(	'#\$\_MODULE\[\'\<\{prestablog\}prestashop\>(.*)\'\][ =]*[ ]*\'(.*)\'[ ]*\;#', 
									$new,
									$matchs
									)) {
			foreach ($matchs[1] as $key => $value) {
				$trad_[$value]["new"] = $matchs[2][$key];
			}
		}
		if(preg_match_all(	'#\$\_MODULE\[\'\<\{prestablog\}prestashop\>(.*)\'\][ =]*[ ]*\'(.*)\'[ ]*\;#', 
									$old,
									$matchs
									)) {
			foreach ($matchs[1] as $key => $value) {
				$trad_[$value]["old"] = $matchs[2][$key];
			}
		}
		$trad=array();
		foreach ($trad_ as $key => $value) {
			if(isset($value['new']) && $value['new'] != '')
				$trad[$key] = $value['new'];
			elseif(isset($value['old']) && $value['old'] != '')
				$trad[$key] = $value['old'];
		}
		$_file_trad = '<?php'."\n"."\n";
		$_file_trad .= 'global $_MODULE;'."\n";
		$_file_trad .= '$_MODULE = array();'."\n";
		foreach ($trad as $key => $value) {
			$_file_trad .= '$_MODULE[\'<{prestablog}prestashop>'.$key.'\'] = \''.$value.'\';'."\n";
		}
		return $_file_trad;
	}

	private function _displayHome($ConfigTheme) {
		$CommentsNonLu		= 	CommentNewsClass::getListeNonLu();
		$this->_html .= '
		<div id="comments">'."\n";
		$this->_html .= '
			<div class="blocs col-sm-4 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
				<h3><img src="../modules/'.$this->name.'/img/question.gif" alt="'.$this->l('Pending').'" />'.count($CommentsNonLu).'&nbsp;'.sprintf($this->l('comment%1$s pending'),(count($CommentsNonLu)>1 ? 's':'')).'</h3>'."\n";
		if(sizeof($CommentsNonLu)) {
			$this->_html .= '<div class="wrap">'."\n";
			foreach($CommentsNonLu As $KeyC => $ValueC) {
				$News = new NewsClass((int)($ValueC["news"]), (int)($this->context->language->id));
				$this->_html .= '<div>'."\n";
				$this->_html .= '	<h2>
				<a href="'.$this->PathModuleConf.'&deleteComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" style="float:right;"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /><span style="display:none;">'.$this->l('Delete').'</span></a>
				<a href="'.$this->PathModuleConf.'&editComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" style="float:right;"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /><span style="display:none;">'.$this->l('Edit').'</span></a>
				<a href="'.$this->PathModuleConf.'&editNews&idN='.$ValueC["news"].'">'.$News->title.'</a></h2>'."\n";
				$this->_html .= '	<h4>'.ToolsCore::displayDate($ValueC["date"], null, true).', '.$this->l('by').' <strong>'.$ValueC["name"].'</strong></h4>'."\n";
				if ($ValueC["url"]!="")
					$this->_html .= '	<h5><a href="'.$ValueC["url"].'" target="_blank">'.$ValueC["url"].'</a></h5>'."\n";
				$this->_html .= '	<p>'.$ValueC["comment"].'</p>'."\n";
				$this->_html .= '	
				<p class="center">
					<a href="'.$this->PathModuleConf.'&enabledComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../img/admin/enabled.gif" alt="'.$this->l('Approuved').'" /><span style="display:none;">'.$this->l('Approuved').'</span></a>
					<a href="'.$this->PathModuleConf.'&disabledComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" /><span style="display:none;">'.$this->l('Disabled').'</span></a>
				</p>'."\n";
				$this->_html .= '</div>'."\n";
			}
			$this->_html .= '</div>'."\n";
		}
		$this->_html .= '
			</div>'."\n";
		
		$ListeNews = NewsClass::getListe(
										(int)($this->context->language->id), 
										1, // actif only
										0, // slide
										$ConfigTheme, 
										0, // limit start
										(int)Configuration::get($this->name.'_lastnews_limit'), // limit stop
										'n.`date`', 
										'desc',
										NULL, // date début
										NULL, // date fin
										NULL,
										0,
										(int)Configuration::get('prestablog_news_title_length'),
										(int)Configuration::get('prestablog_news_intro_length')
									);
		
		$this->_html .= '
			<div class="blocs col-sm-4 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
				<h3><img src="../modules/'.$this->name.'/img/lastnews.png" alt="'.$this->l('News').'" />'.(int)Configuration::get($this->name.'_lastnews_limit').' '.$this->l('latest news').'</h3>'."\n";
		if(sizeof($ListeNews)) {
			$this->_html .= '<div class="wrap">'."\n";
			foreach($ListeNews As $KeyN => $ValueN) {
				$this->_html .= '<div class="homeblog">'."\n";
				if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/adminth_'.$ValueN['id_'.$this->name.'_news'].'.jpg'))
					$this->_html .= '	<img src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/adminth_'.$ValueN['id_'.$this->name.'_news'].'.jpg?'.md5(time()).'" class="thumb"/>'."\n";
				$this->_html .= '	<h2><a href="'.$this->PathModuleConf.'&editNews&idN='.$ValueN['id_'.$this->name.'_news'].'" class="hrefComment" style="float:right;"><img src="../img/admin/edit.gif" alt="'.$this->l('Edit').'" /><span style="display:none;">'.$this->l('Edit').'</span></a>'.$ValueN["title"].'</h2>'."\n";
				$this->_html .= '	<h4>'.ToolsCore::displayDate($ValueN["date"], null, true).'</h4>'."\n";
				$this->_html .= '	<p>'."\n";
				$this->_html .= ($ValueN["paragraph_crop"] ? $ValueN["paragraph_crop"] : '<span style="color:red">'.$this->l('... empty content ...').'</span>');
				$this->_html .= '	</p>'."\n";
				$this->_html .= '	<div class="clear"></div>'."\n";
				$this->_html .= '</div>'."\n";
			}
			$this->_html .= '</div>'."\n";
		}
		$this->_html .= '
			</div>'."\n";
		
		$Stats = self::Statistiques();
		$Langue = LanguageCore::getLanguage((int)($this->context->language->id));
		$this->_html .= '
			<div class="blocs col-sm-3 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
				<h3><img src="../modules/'.$this->name.'/img/stats.png" alt="'.$this->l('Statistics').'" />'.$this->l('Statistics').'</h3>
				<h2>'.$this->l('Total news').' : <span style="color:#000;"><strong>'.$Stats["allnews"].'</strong> <span style="color:#7F7F7F;">&rArr; <small>(<img src="../modules/'.$this->name.'/img/enabled.gif" width="10px;" />'.$Stats["allnewsactives"].' | <img src="../modules/'.$this->name.'/img/disabled.gif" width="10px;" />'.$Stats["allnewsinactives"].')</small></span></span></h2>
				<h2>'.$this->l('Total slides').' : <span style="color:#000;"><strong>'.$Stats["allslides"].'</strong> <span style="color:#7F7F7F;">&rArr; <small>(<img src="../modules/'.$this->name.'/img/enabled.gif" width="10px;" />'.$Stats["allslidesactives"].' | <img src="../modules/'.$this->name.'/img/disabled.gif" width="10px;" />'.$Stats["allslidesinactives"].')</small></span></span></h2>
				<h2>'.$this->l('Total comments').' : <span style="color:#000;"><strong>'.$Stats["allcomments"].'</strong> <span style="color:#7F7F7F;">&rArr; <small>(<img src="../modules/'.$this->name.'/img/enabled.gif" width="10px;" />'.$Stats["allcommentsactives"].' | <img src="../modules/'.$this->name.'/img/disabled.gif" width="10px;" />'.$Stats["allcommentsinactives"].' | <img src="../modules/'.$this->name.'/img/question.gif" width="10px;" />'.$Stats["allcommentspending"].')</small></span></span></h2>
				<h2>'.$this->l('Total categories').' : <span style="color:#000;"><strong>'.$Stats["allcategories"].'</strong> <span style="color:#7F7F7F;">&rArr; <small>(<img src="../modules/'.$this->name.'/img/enabled.gif" width="10px;" />'.$Stats["allcategoriesactives"].' | <img src="../modules/'.$this->name.'/img/disabled.gif" width="10px;" />'.$Stats["allcategoriesinactives"].')</small></span></span></h2>
				<h2>'.$this->l('Total subscribe news').' : <span style="color:#000;"><strong>'.$Stats["allabonnements"].'</strong> <span style="color:#7F7F7F;"><small>('.$this->l('only registered user').')</small></span></span></h2>
			</div>'."\n";

		$this->_html .= '
		</div>
		<div class="clear"></div>'."\n";
		$this->_html .= '
			<script type="text/javascript">
				$(document).ready(function() { 
					$("a.hrefComment").mouseenter(function() { 
						$("span:first", this).show(\'slow\'); 
					}).mouseleave(function() { 
						$("span:first", this).hide(); 
					});
				});
			</script>'."\n";
	}
	
	static public function Statistiques() {
		$context = Context::getContext();
		$Stats=array();
		$Stats["allnews"] 					= NewsClass::getCountListeAllNoLang(0, 0, NULL, NULL, NULL);
		$Stats["allnewsactives"]			= NewsClass::getCountListeAllNoLang(1, 0, NULL, NULL, NULL);
		$Stats["allnewsinactives"]			= $Stats["allnews"] - $Stats["allnewsactives"];
		
		$Stats["allslides"] 				= NewsClass::getCountListeAllNoLang(0, 1, NULL, NULL, NULL);
		$Stats["allslidesactives"]			= NewsClass::getCountListeAllNoLang(1, 1, NULL, NULL, NULL);
		$Stats["allslidesinactives"]		= $Stats["allslides"] - $Stats["allslidesactives"];
		
		$Stats["allcomments"] 				= count(CommentNewsClass::getListe(-2, 0));
		$Stats["allcommentsactives"]		= count(CommentNewsClass::getListe(1, 0));
		$Stats["allcommentspending"]		= count(CommentNewsClass::getListe(-1, 0));
		$Stats["allcommentsinactives"]		= count(CommentNewsClass::getListe(0, 0));
		
		$Stats["allcategories"] 			= count(CategoriesClass::getListeNoArbo(0, (int)$context->language->id));
		$Stats["allcategoriesactives"]	= count(CategoriesClass::getListeNoArbo(1, (int)$context->language->id));
		$Stats["allcategoriesinactives"]	= $Stats["allcategories"] - $Stats["allcategoriesactives"];
		
		$Stats["allabonnements"] 			= count(CommentNewsClass::listeCommentAbo());
		
		return $Stats;
	}
	
	private function _displayListeNews($ConfigTheme) {
		$languages_shop = array();
		$Shops = Shop::getShops();
		foreach(Language::getLanguages() as $key => $value) {
			$languages_shop[$value["id_lang"]] = $value["iso_code"];
		}

		// echo '<pre style="font-size:11px;text-align:left">';
		// 	print_r($languages_shop);
		// echo '</pre>';


		$NbParPage = (int)Configuration::get($this->name.'_nb_news_pl');
		
		
		$tri_champ = 'n.`date`';
		$tri_ordre = 'desc';
		$languages = Language::getLanguages(true);
		
		if(Tools::getValue('c') && (int)Tools::getValue('c') > 0) {
			$Categorie = (int)Tools::getValue('c');
			$this->PathModuleConf .= $this->PathModuleConf.'&c='.$Categorie;
		}
		else
			$Categorie = NULL;
		
		$CountListe = NewsClass::getCountListeAll(
								0, 
								(int)$this->checkActive, // actif
								(int)$this->checkSlide, // slide
								NULL, // date début
								NULL, // date fin
								$Categorie,
								0
							);
		
		$Liste = NewsClass::getListe(
										0, 
										(int)$this->checkActive, // actif
										(int)$this->checkSlide, // slide
										$ConfigTheme, 
										(int)Tools::getValue('start'), // limit start
										$NbParPage, // limit stop
										$tri_champ, 
										$tri_ordre,
										NULL, // date début
										NULL, // date fin
										$Categorie,
										0,
										(int)Configuration::get('prestablog_news_title_length'),
										(int)Configuration::get('prestablog_news_intro_length')
									);
		
		$Pagination = self::getPagination(
											$CountListe,
											NULL,
											$NbParPage,
											(int)Tools::getValue('start'), 
											(int)Tools::getValue('p')
										);
		
		$Categories = CategoriesClass::getListe((int)($this->context->language->id), 0);
		
		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '
				<div class="panel">
					<form method="post" action="'.$this->PathModuleConf.'&newsListe" enctype="multipart/form-data">
						<fieldset>
							<input type="hidden" name="submitFiltreNews" value="1" />
							<div class="col-sm-3">
								<a class="btn btn-primary" href="'.$this->PathModuleConf.'&addNews">
									<i class="icon-plus"></i>
									'.$this->l('Add a news').'
								</a>
							</div>
							<div class="col-sm-2">
								<img src="../modules/'.$this->name.'/img/filter.png" alt="" />
								'.$this->l('Filter list').' : 
							</div>'."\n";
							if(sizeof($Categories)) {
								$Categories = new CategoriesClass();
								$ListeCategories = CategoriesClass::getListe((int)($this->context->language->id), 0);
								$this->_html .= '<div class="col-sm-2">'."\n";
								$this->_html .= $Categories->_displaySelectArboCategories($ListeCategories, 0, 0, $this->l("Aucun"), "c", "form.submit();", (int)Tools::getValue('c'))."\n";
								$this->_html .= '</div>'."\n";
							}
				$this->_html .= '
							<div class="col-sm-2">
								<input type="checkbox" name="activeNews" '.($this->checkActive == 1 ? 'checked' : '').' onchange="form.submit();"> '.$this->l('Active').'
							</div>'."\n";
				$this->_html .= '
							<div class="col-sm-2">
								<input type="checkbox" name="slide" '.($this->checkSlide == 1 ? 'checked' : '').' onchange="form.submit();"> '.$this->l('Slide').'
							</div>'."\n";

					$this->_html .= '
						</fieldset>
					</form>
				</div>';
		}
		else {
			$this->_html .= '
				<form method="post" action="'.$this->PathModuleConf.'&newsListe" enctype="multipart/form-data">
				<input type="hidden" name="submitFiltreNews" value="1" />
				<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center;">
					<tr style="height:30px">
						<th>
							<img src="../modules/'.$this->name.'/img/add.gif" alt="" />
							<a href="'.$this->PathModuleConf.'&addNews" title="'.$this->l('Add a news').'">'.$this->l('Add a news').'</a>
						</th>
						<th style="border-left:3px solid #A0A0A0;">
							<img src="../modules/'.$this->name.'/img/filter.png" alt="" />
							'.$this->l('Filter list').' :
						</th>'."\n";

			if(sizeof($Categories)) {
				$Categories = new CategoriesClass();
				$ListeCategories = CategoriesClass::getListe((int)($this->context->language->id), 0);
				$this->_html .= '
							<th style="border-left:1px solid #A0A0A0;">
								'.$Categories->_displaySelectArboCategories($ListeCategories, 0, 0, $this->l("Aucun"), "c", "form.submit();", (int)Tools::getValue('c')).'
							</th>'."\n";
			}

			$this->_html .= '
						<th style="border-left:1px solid #A0A0A0;">
							<input type="checkbox" name="activeNews" '.($this->checkActive == 1 ? 'checked' : '').' onchange="form.submit();"> '.$this->l('Active').'
						</th>'."\n";
			$this->_html .= '
						<th style="border-left:1px solid #A0A0A0;">
							<input type="checkbox" name="slide" '.($this->checkSlide == 1 ? 'checked' : '').' onchange="form.submit();"> '.$this->l('Slide').'
						</th>'."\n";

			$this->_html .= '
					</tr>
				</table>
				</form>';
		}

		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '<div class="panel">';
		}
		else
			$this->_html .= '<br/>';

		$this->_html .= '<fieldset>';
		
		$this->_html .= '<legend style="margin-bottom:10px;">'.$this->l('News').' :
							<span style="color: green;">'.($Categorie ? sprintf($this->l('%1$s currents items on %2$s'), $CountListe, CategoriesClass::getCategoriesName((int)$this->context->language->id, (int)$Categorie)) : sprintf($this->l('%1$s currents items'), $CountListe)).'
							</span>
						</legend>';
		$this->_html .= '<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">';
		$this->_html .= '	<thead class="center">';
		$this->_html .= '		<tr>';
		$this->_html .= '			<th>Id</th>';
		$this->_html .= '			<th>'.$this->l('Date').'</th>';
		$this->_html .= '			<th>'.$this->l('Image').'</th>';
		$this->_html .= '			<th width="400px">'.$this->l('Title').'</th>';
		$this->_html .= '			<th>'.$this->l('Comments').'</th>';
		$this->_html .= '			<th>'.$this->l('Products linked').'</th>';
		$this->_html .= '			<th>'.$this->l('Slide').'</th>';
		$this->_html .= '			<th>'.$this->l('Activate').'</th>';
		$this->_html .= '			<th>'.$this->l('Actions').'</th>';
		$this->_html .= '		</tr>';
		$this->_html .= '	</thead>';
		if(sizeof($Liste)) {
			foreach($Liste As $Key => $Value) {
				$this->_html .= '	<tr>';
				$this->_html .= '		<td class="center">'.$Value["id_prestablog_news"].'</td>';
				$this->_html .= '		<td class="center">'.(($dateC = new DateTime($Value["date"])) > ($now = new DateTime()) ? '<img src="../modules/'.$this->name.'/img/postdate.gif" alt="'.$this->l('Post Date').'" />' : '').ToolsCore::displayDate($Value["date"], null, true).'</td>';
				if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/adminth_'.$Value['id_'.$this->name.'_news'].'.jpg'))
					$this->_html .= '		<td class="center"><img src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/adminth_'.$Value['id_'.$this->name.'_news'].'.jpg?'.md5(time()).'" /></td>';
				else
					$this->_html .= '		<td class="center">-</td>';
				
				$LangListeNews = unserialize($Value["langues"]);
				$this->_html .= '		<td>';
				foreach($LangListeNews As $ValLangue)
					$this->_html .= ((sizeof($languages) > 1 && array_key_exists((int)$ValLangue, $languages_shop)) ? '<img src="../img/l/'.(int)($ValLangue).'.jpg" />' : '').NewsClass::getTitleNews((int)$Value['id_'.$this->name.'_news'], $ValLangue).'<br/>';
				$this->_html .= '		</td>';
				
				$this->_html .= '		<td class="center">';
				$CommentsActif = CommentNewsClass::getListe(1, (int)$Value['id_'.$this->name.'_news']);
				$CommentsAll = CommentNewsClass::getListe(-2, (int)$Value['id_'.$this->name.'_news']);
				if(sizeof($CommentsAll))
					$this->_html .= count($CommentsActif).' '.$this->l('of').' '.count($CommentsAll).' '.$this->l('active');
				else
					$this->_html .= '-';
				$this->_html .= '		</td>';
				
				$this->_html .= '		<td class="center">';
				// récupérer nb product en liaison
				$ProductsLink = NewsClass::getProductLinkListe((int)$Value["id_prestablog_news"]);
				
				$this->_html .= (count($ProductsLink) > 0 ? count($ProductsLink) : '-');
				
				$this->_html .= '		</td>';
				
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&slideNews&idN='.$Value['id_'.$this->name.'_news'].'">
					'.($Value["slide"]? '<img src="../modules/'.$this->name.'/img/enabled.gif" alt="" />':'<img src="../modules/'.$this->name.'/img/disabled.gif" alt="" />').'
					</a>
				</td>';
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&etatNews&idN='.$Value['id_'.$this->name.'_news'].'">
					'.($Value["actif"]? '<img src="../modules/'.$this->name.'/img/enabled.gif" alt="" />':'<img src="../modules/'.$this->name.'/img/disabled.gif" alt="" />').'
					</a>
				</td>';
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&editNews&idN='.$Value['id_'.$this->name.'_news'].'" title="'.$this->l('Edit').'"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /></a>
					<a href="'.$this->PathModuleConf.'&deleteNews&idN='.$Value['id_'.$this->name.'_news'].'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>
				</td>';
				$this->_html .= '	</tr>';
			}
			$PageType = "newsListe";
			if ((int)($Pagination["NombreTotalPages"]) > 1) {
				$this->_html .= '<tfooter>';
				$this->_html .= '	<tr>';
				$this->_html .= '	<td colspan="6">';
				$this->_html .= '<div class="prestablog_pagination">'."\n";
				if ((int)($Pagination["PageCourante"] > 1)) {
					$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$Pagination["StartPrecedent"].'&p='.$Pagination["PagePrecedente"].'">&lt;&lt;</a>'."\n";
				}
				else $this->_html .= '<span class="disabled">&lt;&lt;</span>'."\n";
				if ($Pagination["PremieresPages"]) {
					foreach($Pagination["PremieresPages"] As $key_page => $value_page) {
						if (((int)(Tools::getValue('p')) == $key_page) || ((Tools::getValue('p') == '') && $key_page == 1)) {
							$this->_html .= '<span class="current">'.$key_page.'</span>'."\n";
						}
						else {
							if ($key_page == 1) $this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'">'.$key_page.'</a>'."\n";
							else {
								$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$value_page.'&p='.$key_page.'">'.$key_page.'</a>'."\n";
							}
						}
					}
				}
				if (isset($Pagination["Pages"]) && $Pagination["Pages"]) {
					$this->_html .= '<span class="more">...</span>'."\n";
				
					foreach($Pagination["Pages"] As $key_page => $value_page) {
						if (!in_array($value_page, $Pagination["PremieresPages"])) {
							if (((int)(Tools::getValue('p')) == $key_page) || ((Tools::getValue('p') == '') && $key_page == 1)) {
								$this->_html .= '<span class="current">'.$key_page.'</span>'."\n";
							}
							else {
								$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$value_page.'&p='.$key_page.'">'.$key_page.'</a>'."\n";
							}
						}
					}
				}
				if ($Pagination["PageCourante"] < $Pagination["NombreTotalPages"]) {
					$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$Pagination["StartSuivant"].'&p='.$Pagination["PageSuivante"].'">&gt;&gt;</a>'."\n";
				}
				else $this->_html .= '<span class="disabled">&gt;&gt;</span>'."\n";
				$this->_html .= '</div>'."\n";
				$this->_html .= '	</td>';
				$this->_html .= '	</tr>';
				$this->_html .= '</tfooter>';
			}
		}
		else {
			$this->_html .= '<tr><td colspan="8" class="center">'.$this->l('No content registered').'</td></tr>';
		}
		$this->_html .= '</table>';
		$this->_html .= '</fieldset>';

		if($this->IsPSVersion(">=","1.6"))
			$this->_html .= '</div>';
		
	}
	
	private function _displayListeComments() {
		$NbParPage = (int)Configuration::get($this->name.'_nb_comments_pl');
		
		$tri_champ = 'cn.`date`';
		$tri_ordre = 'desc';
		
		if(Tools::getValue('n') && (int)Tools::getValue('n') > 0) {
			$News = (int)Tools::getValue('n');
			$this->PathModuleConf .= $this->PathModuleConf.'&n='.$News;
		}
		else
			$News = NULL;
		
		$CountListe = CommentNewsClass::getCountListeAll(
								$this->checkCommentState, // active
								$News // only_news
							);
		
		$Liste = CommentNewsClass::getListeNavigate(
										$this->checkCommentState, // active
										(int)Tools::getValue('start'), 
										$NbParPage // limit stop
									);
		
		$Pagination = self::getPagination(
											$CountListe,
											NULL,
											$NbParPage,
											(int)Tools::getValue('start'), 
											(int)Tools::getValue('p')
										);
		
		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '
				<div class="panel">
					<form method="post" action="'.$this->PathModuleConf.'&commentListe" enctype="multipart/form-data">
						<fieldset>
							<input type="hidden" name="submitFiltreComment" value="1" />
							<div class="col-sm-2">
								<img src="../modules/'.$this->name.'/img/filter.png" alt="" />
								'.$this->l('Filter list').' : 
							</div>
							<div class="col-sm-2">
								<input type="radio" name="activeComment" '.($this->checkCommentState == -2 ? 'checked' : '').' onchange="form.submit();" value="-2" > <img src="../modules/'.$this->name.'/img/refresh.png" /> '.$this->l('All').'
							</div>
							<div class="col-sm-2">
								<input type="radio" name="activeComment" '.($this->checkCommentState == -1 ? 'checked' : '').' onchange="form.submit();" value="-1" > <img src="../modules/'.$this->name.'/img/question.gif" /> '.$this->l('Pending').'
							</div>
							<div class="col-sm-2">
								<input type="radio" name="activeComment" '.($this->checkCommentState == 1 ? 'checked' : '').' onchange="form.submit();" value="1"> <img src="../modules/'.$this->name.'/img/enabled.gif" /> '.$this->l('Enabled').'
							</div>
							<div class="col-sm-2">
								<input type="radio" name="activeComment" '.(is_numeric($this->checkCommentState) && ($this->checkCommentState == 0) ? 'checked' : '').' onchange="form.submit();" value="0" > <img src="../modules/'.$this->name.'/img/disabled.gif" /> '.$this->l('Disabled').'
							</div>
						</fieldset>
					</form>
				</div>';
		}	
		else {
			$this->_html .= '
				<form method="post" action="'.$this->PathModuleConf.'&commentListe" enctype="multipart/form-data">
				<input type="hidden" name="submitFiltreComment" value="1" />
				<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center;">
					<tr style="height:30px">
						<th style="border-left:3px solid #A0A0A0;">
							<img src="../modules/'.$this->name.'/img/filter.png" alt="" />
							'.$this->l('Filter list').' :
						</th>'."\n";

			$this->_html .= '
						<th style="border-left:1px solid #A0A0A0;">
							<input type="radio" name="activeComment" '.($this->checkCommentState == -2 ? 'checked' : '').' onchange="form.submit();" value="-2" > <img src="../modules/'.$this->name.'/img/refresh.png" /> '.$this->l('All').'
						</th>
						<th style="border-left:1px solid #A0A0A0;">
							<input type="radio" name="activeComment" '.($this->checkCommentState == -1 ? 'checked' : '').' onchange="form.submit();" value="-1" > <img src="../modules/'.$this->name.'/img/question.gif" /> '.$this->l('Pending').'
						</th>
						<th style="border-left:1px solid #A0A0A0;">
							<input type="radio" name="activeComment" '.($this->checkCommentState == 1 ? 'checked' : '').' onchange="form.submit();" value="1"> <img src="../modules/'.$this->name.'/img/enabled.gif" /> '.$this->l('Enabled').'
						</th>
						<th style="border-left:1px solid #A0A0A0;">
							<input type="radio" name="activeComment" '.(is_numeric($this->checkCommentState) && ($this->checkCommentState == 0) ? 'checked' : '').' onchange="form.submit();" value="0" > <img src="../modules/'.$this->name.'/img/disabled.gif" /> '.$this->l('Disabled').'
						</th>'."\n";

			$this->_html .= '
					</tr>
				</table>
				</form>';
		}
		
		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '<div class="panel">';
		}
		else
			$this->_html .= '<br/>';

		$this->_html .= '<fieldset>';
		$this->_html .= '<legend style="margin-bottom:10px;">'.$this->l('Comments').' :</legend>';
		$this->_html .= '<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">';
		$this->_html .= '	<thead class="center">';
		$this->_html .= '		<tr>';
		$this->_html .= '			<th>Id</th>';
		$this->_html .= '			<th>'.$this->l('Date').'</th>';
		$this->_html .= '			<th>'.$this->l('News').'</th>';
		$this->_html .= '			<th>'.$this->l('Name').'</th>';
		$this->_html .= '			<th>'.$this->l('Url').'</th>';
		$this->_html .= '			<th>'.$this->l('Comment').'</th>';
		$this->_html .= '			<th class="center" style="width:70px;">'.$this->l('Status').'</th>';
		$this->_html .= '			<th class="center">'.$this->l('Actions').'</th>';
		$this->_html .= '		</tr>';
		$this->_html .= '	</thead>';
		if(sizeof($Liste)) {
			foreach($Liste As $Key => $Value) {
				$this->_html .= '	<tr>';
				$this->_html .= '		<td class="center">'.($Value["id_prestablog_commentnews"]).'</td>';
				$this->_html .= '		<td class="center">'.ToolsCore::displayDate($Value["date"], null, true).'</td>';
				$TitleNews = NewsClass::getTitleNews((int)($Value["news"]), (int)($this->context->language->id));
				//$this->_html .= '		<td><a href="'.$this->PathModuleConf.'&editNews&idN='.$Value["news"].'">'.(Tools::strlen($TitleNews) >= 20 ? Tools::substr($TitleNews, 0, 20).'...' : $TitleNews).'</a></td>';
				
				$this->_html .= '		<td><a href="'.$this->PathModuleConf.'&editNews&idN='.$Value["news"].'">'.self::cleanCut($TitleNews,40,'...').'</a></td>';
				
				$this->_html .= '		<td>'.$Value["name"].'</td>';
				$this->_html .= '		<td><a href="'.$Value["url"].'" target="_blank">'.$Value["url"].'</a></td>';
				$this->_html .= '		<td><small>'.self::cleanCut($Value["comment"],120,'...').'</small></td>';
				$this->_html .= '		<td class="status">
					<a href="'.$this->PathModuleConf.'&enabledComment&commentListe&idC='.$Value["id_prestablog_commentnews"].'" '.((int)$Value["actif"] != 1 ? 'style="display:none;"' : 'rel="1"').' >
						<img src="../modules/'.$this->name.'/img/enabled.gif" title="'.$this->l('Approuved').'" />
					</a>
					<a href="'.$this->PathModuleConf.'&disabledComment&commentListe&idC='.$Value["id_prestablog_commentnews"].'" '.((int)$Value["actif"] != 0 ? 'style="display:none;"' : 'rel="1"').' >
						<img src="../modules/'.$this->name.'/img/disabled.gif" title="'.$this->l('Disabled').'" />
					</a>
					<a href="'.$this->PathModuleConf.'&pendingComment&commentListe&idC='.$Value["id_prestablog_commentnews"].'" '.((int)$Value["actif"] != -1 ? 'style="display:none;"' : 'rel="1"').' >
						<img src="../modules/'.$this->name.'/img/question.gif" title=""'.$this->l('Pending').'" />
					</a>
				</td>
				<script language="javascript" type="text/javascript">
					$(document).ready(function() {
						$("td.status").mouseenter(function() { 
							$(this).find("a").fadeIn(); 
						}).mouseleave(function() { 
							$(this).find("a").hide(function() {
								if ($(this).attr(\'rel\') == 1) 
									$(this).fadeIn();
							}); 
						});
					});
				</script>
				';
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&editComment&idC='.$Value["id_prestablog_commentnews"].'" title="'.$this->l('Edit').'"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /></a>
					<a href="'.$this->PathModuleConf.'&deleteComment&idC='.$Value["id_prestablog_commentnews"].'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>
				</td>';
				$this->_html .= '	</tr>';
			}
			$PageType = "commentListe";
			if ((int)($Pagination["NombreTotalPages"]) > 1) {
				$this->_html .= '<tfooter>';
				$this->_html .= '	<tr>';
				$this->_html .= '	<td colspan="6">';
				$this->_html .= '<div class="prestablog_pagination">'."\n";
				if ((int)($Pagination["PageCourante"] > 1)) {
					$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$Pagination["StartPrecedent"].'&p='.$Pagination["PagePrecedente"].'">&lt;&lt;</a>'."\n";
				}
				else $this->_html .= '<span class="disabled">&lt;&lt;</span>'."\n";
				if ($Pagination["PremieresPages"]) {
					foreach($Pagination["PremieresPages"] As $key_page => $value_page) {
						if (((int)(Tools::getValue('p')) == $key_page) || ((Tools::getValue('p') == '') && $key_page == 1)) {
							$this->_html .= '<span class="current">'.$key_page.'</span>'."\n";
						}
						else {
							if ($key_page == 1) $this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'">'.$key_page.'</a>'."\n";
							else {
								$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$value_page.'&p='.$key_page.'">'.$key_page.'</a>'."\n";
							}
						}
					}
				}
				if (isset($Pagination["Pages"]) && $Pagination["Pages"]) {
					$this->_html .= '<span class="more">...</span>'."\n";
				
					foreach($Pagination["Pages"] As $key_page => $value_page) {
						if (!in_array($value_page, $Pagination["PremieresPages"])) {
							if (((int)(Tools::getValue('p')) == $key_page) || ((Tools::getValue('p') == '') && $key_page == 1)) {
								$this->_html .= '<span class="current">'.$key_page.'</span>'."\n";
							}
							else {
								$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$value_page.'&p='.$key_page.'">'.$key_page.'</a>'."\n";
							}
						}
					}
				}
				if ($Pagination["PageCourante"] < $Pagination["NombreTotalPages"]) {
					$this->_html .= '<a href="'.$this->PathModuleConf.'&'.$PageType.'&start='.$Pagination["StartSuivant"].'&p='.$Pagination["PageSuivante"].'">&gt;&gt;</a>'."\n";
				}
				else $this->_html .= '<span class="disabled">&gt;&gt;</span>'."\n";
				$this->_html .= '</div>'."\n";
				$this->_html .= '	</td>';
				$this->_html .= '	</tr>';
				$this->_html .= '</tfooter>';
			}
		}
		else {
			$this->_html .= '<tr><td colspan="8" class="center">'.$this->l('No content registered').'</td></tr>';
		}
		$this->_html .= '</table>';
		$this->_html .= '</fieldset>';

		if($this->IsPSVersion(">=","1.6"))
			$this->_html .= '</div>';
	}
	
	private function _displayListeCategories($ConfigTheme) {
		$Liste = CategoriesClass::getListe((int)($this->context->language->id), 0);

		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '
				<div class="panel">
					<fieldset>
						<div class="col-sm-3">
							<a class="btn btn-primary" href="'.$this->PathModuleConf.'&addCat">
								<i class="icon-plus"></i>
								'.$this->l('Add a category').'
							</a>
						</div>
					</fieldset>
				</div>';
		}
		else {
			$this->_html .= '
				<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center">
					<tr>
						<th>
								<img src="../modules/'.$this->name.'/img/add.gif" alt="" />
								<a href="'.$this->PathModuleConf.'&addCat" title="'.$this->l('Add a category').'">'.$this->l('Add a category').'</a>
						</th>
					</tr>
				</table>';
		}

		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '<div class="panel">';
		}

		$this->_html .= '<fieldset>';
		$this->_html .= '<legend style="margin-bottom:10px;">'.$this->l('Categories').'</legend>';
		$this->_html .= '<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;width:100%;">';
		$this->_html .= '	<thead class="center">';
		$this->_html .= '		<tr>';
		$this->_html .= '			<th>Id</th>';
		$this->_html .= '			<th>'.$this->l('Image').'</th>';
		$this->_html .= '			<th>'.$this->l('Title').'</th>';
		$this->_html .= '			<th>'.$this->l('Title Meta').'</th>';
		$this->_html .= '			<th>'.$this->l('Use in articles').'</th>';
		$this->_html .= '			<th class="center">'.$this->l('Activate').'</th>';
		$this->_html .= '			<th class="center">'.$this->l('Actions').'</th>';
		$this->_html .= '		</tr>';
		$this->_html .= '	</thead>';
		
		if(sizeof($Liste)) {
			$this->_html .= $this->_displayListeArborescenceCategories($Liste);
		}
		else {
			$this->_html .= '<tr><td colspan="5" class="center">'.$this->l('No content registered').'</td></tr>';
		}
		$this->_html .= '</table>';
		$this->_html .= '</fieldset>';

		if($this->IsPSVersion(">=","1.6"))
			$this->_html .= '</div>';
	}

	static public function array_delete_value($array, $search) {
		$temp = array();
		foreach($array as $key => $value) {
	 		if($value!=$search) $temp[$key] = $value;
		}
		return $temp;
	}

	private function _displayListeArborescenceCategoriesNews($ListeCat, $Decalage = 0, $ListeIdBranchDeploy = array()) {
		$defaultCat = 1;
		$_html = '';
		foreach($ListeCat As $Key => $Value) {
			$active=false;
			if(
					(Tools::getValue('idN') && in_array((int)$Value["id_prestablog_categorie"], CorrespondancesCategoriesClass::getCategoriesListe((int)Tools::getValue('idN'))))
				||
					(Tools::getValue('categories') && in_array((int)$Value["id_prestablog_categorie"], Tools::getValue('categories')))
				||
					( (isset($defaultCat) && !Tools::getValue('categories')) && ((int)$Value["id_prestablog_categorie"] == (int)$defaultCat) )
				) {
				$active=true;
			}
			$_html .= '
			<tr class="prestablog_branch'.($Decalage>0?' childs':'').($active?' alt_row':'').'" rel="'.$Value["branch"].'" id="prestablog_categorie_'.(int)$Value["id_prestablog_categorie"].'">
				<td>
					<input type="checkbox" name="categories[]" value="'.(int)$Value["id_prestablog_categorie"].'" '.($active? 'checked=checked' : '' ).' />
				</td>
				<td>'.$Value["id_prestablog_categorie"].'</td>';
				if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/adminth_'.$Value['id_'.$this->name.'_categorie'].'.jpg'))
					$_html .= '		<td class="center"><img src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/adminth_'.$Value['id_'.$this->name.'_categorie'].'.jpg?'.md5(time()).'" /></td>';
				else
					$_html .= '		<td class="center">-</td>';

			$_html .= '<td>';

			$ListeCatLang = CategoriesClass::getListeNoArbo();
			$languages = Language::getLanguages(true);

			foreach ($languages as $language) {
				foreach ($ListeCatLang as $CatLang) {
					if(		(int)$CatLang["id_prestablog_categorie"] == (int)$Value["id_prestablog_categorie"]
						&&	(int)$CatLang["id_lang"] == (int)$language['id_lang']) {
						$_html .= '<div class="catlang" rel="'.(int)$language['id_lang'].'">
												<span style="'.($Decalage > 0 ? 'padding-left:'.($Decalage*20).'px;background: url(../modules/'.$this->name.'/img/decalage.png) no-repeat right center;':'').'"></span>';

						if(sizeof($Value["children"]) && in_array((int)$Value["id_prestablog_categorie"],$ListeIdBranchDeploy))
							$_html .= '<img src="'.$this->_path.'img/collapse.gif" class="expand-cat" rel="'.$Value["branch"].'" />';
						elseif(sizeof($Value["children"]) && !in_array((int)$Value["id_prestablog_categorie"],$ListeIdBranchDeploy))
							$_html .= '<img src="'.$this->_path.'img/expand.gif" class="expand-cat" rel="'.$Value["branch"].'" />';

						if($active)
							$_html .= '<strong>'.$CatLang["title"].'</strong>';
						else
							$_html .= $CatLang["title"];

						$_html .= '</div>';
					}
				}
			}
			$_html .= '
				</td>
			</tr>';
			if(sizeof($Value["children"]))
				$_html .= $this->_displayListeArborescenceCategoriesNews($Value["children"], $Decalage+1, $ListeIdBranchDeploy);
		}
		return $_html;
	}

	private function _displayListeArborescenceCategories($Liste, $Decalage = 0) {
		$_html='';
		foreach($Liste As $Key => $Value) {
			$_html .= '	<tr>';
			$_html .= '		<td class="center">'.($Value['id_'.$this->name.'_categorie']).'</td>';
			if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/adminth_'.$Value['id_'.$this->name.'_categorie'].'.jpg'))
				$_html .= '		<td class="center"><img src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/adminth_'.$Value['id_'.$this->name.'_categorie'].'.jpg?'.md5(time()).'" /></td>';
			else
				$_html .= '		<td class="center">-</td>';
			$_html .= '		<td><span style="'.($Decalage > 0 ? 'padding-left:'.($Decalage*20).'px;background: url(../modules/'.$this->name.'/img/decalage.png) no-repeat right center;':'').'"></span>'.$Value["title"].'</td>';
			$_html .= ($Value["meta_title"] ? '<td style="font-size:90%;">'.$Value["meta_title"].'</td>' : '<td style="text-align:center;">-</td>');
			$_html .= '<td style="text-align:center;">'.CategoriesClass::getNombreNewsDansCat((int)$Value['id_'.$this->name.'_categorie']).'</td>';
			$_html .= '		<td class="center">
				<a href="'.$this->PathModuleConf.'&etatCat&idC='.$Value['id_'.$this->name.'_categorie'].'">
				'.($Value["actif"]? '<img src="../modules/'.$this->name.'/img/enabled.gif" alt="" />':'<img src="../modules/'.$this->name.'/img/disabled.gif" alt="" />').'
				</a>
			</td>';
			$_html .= '		<td class="center">
				<a href="'.$this->PathModuleConf.'&editCat&idC='.$Value['id_'.$this->name.'_categorie'].'" title="'.$this->l('Edit').'"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /></a>';
				if($Value['id_'.$this->name.'_categorie']>1 && !sizeof($Value["children"]))
					$_html .= '		<a href="'.$this->PathModuleConf.'&deleteCat&idC='.$Value['id_'.$this->name.'_categorie'].'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>';
				else
					$_html .= '		<a href="#" onclick="return alert(\''.$this->l('For delete parent category, you should delete all child before !').'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>';
			$_html .= '		</td>';
			$_html .= '	</tr>';
			if(sizeof($Value["children"]))
				$_html .= $this->_displayListeArborescenceCategories($Value["children"], $Decalage+1);
		}
		return $_html;
	}	

	private function _displayDebug() {
		
		$this->_html .= '<fieldset style="margin:auto;">';
		$this->_html .= '<legend style="margin-bottom:10px;"><img src="../modules/'.$this->name.'/img/debug.png" alt="" /> '.$this->l('Debug module').'</legend>';
		$this->_html .= $this->displayWarning('
				<p>'.$this->l('This debug is expected to reveal the errors after an installation or upgrade.').'</p>
				<p>'.$this->l('It is based on the knowledge base of customer feedback, bugs or simple errors.').'</p>
			');
		$this->_html .= '</fieldset>';
	}
	
	private function _displayInformations() {
		$informations = array(
			'host' => array(
				'version' => array(
					'php' => phpversion(),
					'server' => $_SERVER['SERVER_SOFTWARE'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
					'uname' => function_exists('php_uname') ? php_uname('s').' '.php_uname('v').' '.php_uname('m') : '',
					'memory_limit' => ini_get('memory_limit'),
					'max_execution_time' => ini_get('max_execution_time'),
					'display_errors' => ini_get('display_errors'),
					'magic_quotes' => _PS_MAGIC_QUOTES_GPC_,
				),
				'database' => array(
					'version' => Db::getInstance()->getVersion(),
					'prefix' => _DB_PREFIX_,
					'engine' => _MYSQL_ENGINE_,
					'ps_version' => Configuration::get('PS_VERSION_DB'),
				),
			),
			'prestashop' => array(
				'ps_version' => _PS_VERSION_,
				'ps_version_install' => Configuration::get('PS_INSTALL_VERSION'),
				'ps_ssl' => Configuration::get('PS_SSL_ENABLED'),
				'url_front' => Tools::getHttpHost(true).__PS_BASE_URI__,
				'url_admin' => $_SERVER['PHP_SELF'],
				'domain' => Configuration::get('PS_SHOP_DOMAIN'),
				'domain_ssl' => Configuration::get('PS_SHOP_DOMAIN_SSL'),
				'theme' => _THEME_NAME_,
				'mobile' => Configuration::get('PS_ALLOW_MOBILE_DEVICE'),
				'mail' => Configuration::get('PS_MAIL_METHOD') == 1, // 3 pas d'envoi, 1 sendmail, 2 smtp
				'ps_rewrite' => Configuration::get('PS_REWRITING_SETTINGS'),
				'accented_chars_url' => Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
				'css_cache' => Configuration::get('PS_CSS_THEME_CACHE'),
				'js_cache' => Configuration::get('PS_JS_THEME_CACHE'),
				'html_compression' => Configuration::get('PS_HTML_THEME_COMPRESSION'),
				'js_html_compression' => Configuration::get('PS_JS_HTML_THEME_COMPRESSION'),
				'mode_dev' => _PS_MODE_DEV_,
				'debug_sql' => _PS_DEBUG_SQL_,
				'display_compatibility_warning' => _PS_DISPLAY_COMPATIBILITY_WARNING_,
				'mode_demo' => _PS_MODE_DEMO_,
			),
			'prestablog' => array(
				'core' => array(
					'version' => $this->version,
					'module_key' =>	$this->module_key,
				),
			),
		);
		
		$Shops = Shop::getShops();
		foreach ($Shops as $keyShop => $valueShop) {
			foreach ($valueShop as $keyS => $valueS) {
				$informations['shop'.$keyShop][$keyS] = $valueS;
			}
			foreach ($this->Configurations as $ConfigurationKey => $ConfigurationValue) {
				$informations['shop'.$keyShop]['config'][$ConfigurationKey] = Configuration::get($ConfigurationKey, false, null, $keyShop);
			}
		}
		
		$this->_html .= $this->_displayFormOpen("icon-info", $this->l('Informations'), $this->PathModuleConf);

			$infos = '';
			foreach($informations As $kcore => $vcore) {
				if(is_array($vcore))
					foreach($vcore As $kth => $vth)
						if(is_array($vth))
							foreach($vth As $kinfo => $vinfo)
								$infos .= $kcore.'_'.$kth.'_'.$kinfo.' : '.$vinfo."\n";
						else
							$infos .= $kcore.'_'.$kth.' : '.$vth."\n";
				else
					$infos .= $kcore.' : '.$vcore."\n";
				$infos .= "\n";
			}
			
			if(!$this->demoMode)
				$htmlLibre = '<textarea '.(self::IsPSVersion('<','1.6')?'style="width:500px;height:300px;"':'	style="height:300px;"').'>'.$infos.'</textarea></p>';
			else
				$htmlLibre = $this->displayWarning($this->l('Feature disabled on the demo mode'));
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Informations'), $htmlLibre, "col-lg-7");

		$this->_html .= $this->_displayFormClose();
	}
	
	private function _displayBackup() {
		//$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		$languagesIso = array();
		foreach (Language::getIsoIds() as $key => $value) {
			$languagesIso[] = $value["iso_code"];
		}
		
		if($this->demoMode)
			$this->_html .= $this->displayWarning($this->l('Feature disabled on the demo mode'));			
		
		$this->_html .= '<div class="demi">';
		$this->_html .= '	<fieldset style="margin:auto;">
								<legend style="margin-bottom:10px;"><img src="../modules/'.$this->name.'/img/backup.png" alt="" />&nbsp;'.$this->l('Backup blog').'</legend>
								<form method="post" action="'.$this->PathModuleConf.'" enctype="multipart/form-data">
									<label style="width: 115px;">'.$this->l('Select backup options').'</label>
									<div class="margin-form" style="padding: 0 0 0 130px;">
										<p><input type="checkbox" name="'.$this->name.'_backup_conf[]" checked=checked value="config" />&nbsp;'.$this->l('Module\'s configuration').'</p>
										<p>
											<input type="checkbox" name="'.$this->name.'_backup_conf[]" checked=checked value="bdd" />&nbsp;'.$this->l('Module\'s database tables').'
											<div class="clear">'.$this->l('All content : articles and attributes, categories, comments, antispam').'</div>
										</p>
									</div>
									<label style="width: 115px;">'.$this->l('Module\'s themes').'</label>
									<div class="margin-form" style="padding: 0 0 0 130px;">
										<p>';
		foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
			$sizeFolder = $this->dirSize(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme);
			$sizeFolder = number_format($sizeFolder / 1048576, 3).' MB';
			$this->_html .= '			<input type="checkbox" name="'.$this->name.'_backup_conf[themes][]" '.(Configuration::get($this->name.'_theme') == $ValueTheme ? 'checked="checked"' : "").' value="'.$ValueTheme.'" />&nbsp;<span style="color:green;">'.$ValueTheme.'</span>&nbsp;<small style="color:blue;">'.$sizeFolder.'</small>'.(Configuration::get($this->name.'_theme') == $ValueTheme ? '&nbsp;<strong>('.$this->l('activate').')</strong>' : '').'<br />';
		}
		
		$this->_html .= '					<div class="clear"><strong>'.$this->l('Important').'</strong>, '.$this->l('The theme includes all main pictures of articles, for thumbs and slide\'s pictures').'</div>
										</p>
									</div>
									<div class="clear"></div>
									<label style="width: 115px;">'.$this->l('General translations').'</label>
									<div class="margin-form" style="padding: 0 0 0 130px;">
										<p>';

		
		foreach($this->ScanFilesDirectory(_PS_MODULE_DIR_.$this->name.'/translations') As $KeyTranslation => $ValueTranslation) {
			$sizeFile = filesize(_PS_MODULE_DIR_.$this->name.'/translations/'.$ValueTranslation);
			$sizeFile = number_format($sizeFile / 1048576, 3).' MB';
			$flagIsoLang = Tools::strtolower(preg_replace('/.php/', '', $ValueTranslation));
			$flagLang = self::getImgFlagByIso($flagIsoLang);
			$this->_html .= '			<input type="checkbox" name="'.$this->name.'_backup_conf[translations][]" '.(in_array($flagIsoLang, $languagesIso) ? 'checked="checked"' : "").' value="'.$ValueTranslation.'" />&nbsp;<span style="color:green;">'.$flagLang.' '.$ValueTranslation.'</span>&nbsp;<small style="color:blue;">'.$sizeFile.'</small>'.(!in_array($flagIsoLang, $languagesIso) ? '&nbsp;<small>'.$this->l('Not installed').'</small>' : "").'<br />';
		}
		
		$this->_html .= '
										</p>
									</div>
									<div class="clear"></div>
									<label style="width: 115px;">'.$this->l('Translations of mail templates').'</label>
									<div class="margin-form" style="padding: 0 0 0 130px;">
										<p>';
		foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/mails') As $KeyMail => $ValueMail) {
			$sizeFolder = $this->dirSize(_PS_MODULE_DIR_.$this->name.'/mails/'.$ValueMail);
			$sizeFolder = number_format($sizeFolder / 1048576, 3).' MB';
			$flagLang = self::getImgFlagByIso($ValueMail);
			$this->_html .= '			<input type="checkbox" name="'.$this->name.'_backup_conf[mails][]" '.(in_array($ValueMail, $languagesIso) ? 'checked="checked"' : "").' value="'.$ValueMail.'" />&nbsp;<span style="color:green;">'.$flagLang.' '.$ValueMail.'</span>&nbsp;<small style="color:blue;">'.$sizeFolder.'</small>'.(!in_array($ValueMail, $languagesIso) ? '&nbsp;<small>'.$this->l('Not installed').'</small>' : "").'<br />';
		}
		
		$this->_html .= '					
										</p>
									</div>
									<div class="clear"></div>
									<div class="margin-form clear">
										<input type="submit" name="submitBackup" value="'.$this->l('Backup').'" class="button" />
									</div>
								</form>
							</fieldset>';
		$this->_html .= '</div>';
		
		$this->_html .= '<div class="demi">';
		$this->_html .= '	<fieldset style="margin:auto;">
								<legend style="margin-bottom:10px;"><img src="../modules/'.$this->name.'/img/restore.png" alt="" />&nbsp;'.$this->l('Restore blog from backup file').'</legend>
								<label style="width: 115px;">'.$this->l('Import file to backup').'</label>
								<div class="margin-form" style="padding: 0 0 0 130px;">
									<input type="file" name="'.$this->name.'_import_backup" />
									<div class="clear">'.$this->l('Only backup files from this PrestaBlog module work.').'</div>
								</div>
								<div class="margin-form clear">
									<input type="submit" name="submitImportBackup" value="'.$this->l('Send backup file').'" class="button" onclick="if(confirm(\''.$this->l('Restoring will erase and replace the entire contents of your current blog. Are you sure?', __CLASS__, true, false).'\')) { return confirm(\''.$this->l('Be carefull !!! Restore will erase and will replace the entire contents of your current blog. It depends your backup options of the themes, of the content sql, and of configurations. Are you really sure?', __CLASS__, true, false).'\')} else {return false;}" />
								</div>
							</fieldset>';
		
		$this->_html .= '	<p class="clear"></p>';
		$listeBackup = $this->ScanFilesDirectory(_PS_MODULE_DIR_.$this->name.'/backup', array('index.php','.htaccess'));
		if(sizeof($listeBackup)) {
			
			$this->_html .= '	<fieldset style="margin:auto;">
									<legend style="margin-bottom:10px;"><img src="../modules/'.$this->name.'/img/copy_files.gif" alt="" />&nbsp;'.$this->l('Backup files list availables to restore').'</legend>';
			$this->_html .= $this->displayWarning($this->l('Be very careful with the backups to restore. Depending on your backup archive all your content, configuration, and your themes can be replaced. Consider making a backup of your current website, before making a new restoration.'));
			$this->_html .= '		<ul id="listeBackup">';
									$backupList = array();
									foreach($listeBackup As $KeyFile => $ValueFile) {
										$sizeFile = filesize(_PS_MODULE_DIR_.$this->name.'/backup/'.$ValueFile);
										$timeunixBackup = filemtime(_PS_MODULE_DIR_.$this->name.'/backup/'.$ValueFile);
										$dateFile = ToolsCore::displayDate(date("Y-m-d H:i:s" , $timeunixBackup), null, true);
										$sizeFile = number_format($sizeFile / 1048576, 3).' MB';
										$backupList[$timeunixBackup]["date"] = $dateFile;
										$backupList[$timeunixBackup]["size"] = $sizeFile;
										$backupList[$timeunixBackup]["file"] = $ValueFile;
										if(Tools::strlen($ValueFile) > 40)
											$backupList[$timeunixBackup]["file_short"] = Tools::substr($ValueFile, 0, 25).'[...]'.Tools::substr($ValueFile, -10);
										else
											$backupList[$timeunixBackup]["file_short"] = $backupList[$timeunixBackup]["file"];
									}
									krsort($backupList);
									foreach($backupList As $kB => $vB)
										$this->_html .= '<li>
															<span style="color:#D76300;">'.$vB["date"].'</span>, '.$this->l('size').' : <span style="color:#00006F;font-style:italic;">'.$vB["size"].'</span><br />
															<a href="../modules/prestablog/backup/'.$vB["file"].'" title="'.$this->l('Download this backup on your desktop').'"><img src="../modules/'.$this->name.'/img/import.png" alt="'.$this->l('Download this backup on your desktop').'" style="vertical-align: middle;"/>&nbsp;'.$vB["file_short"].'</a>
															<div class="actionRestore">
																<a href="'.$this->PathModuleConf.'&restoreBackup&file='.$vB["file"].'" title="'.$this->l('Restore this backup').'" onclick="if(confirm(\''.$this->l('Restoring will erase and replace the entire contents of your current blog. Are you sure?', __CLASS__, true, false).'\')) { return confirm(\''.$this->l('Be carefull !!! Restore will erase and will replace the entire contents of your current blog. It depends your backup options of the themes, of the content sql, and of configurations. Are you really sure?', __CLASS__, true, false).'\')} else {return false;}"><img src="../modules/'.$this->name.'/img/restore_3.png" alt="'.$this->l('Restore this backup').'" /></a>
																<a href="'.$this->PathModuleConf.'&deleteBackup&file='.$vB["file"].'" title="'.$this->l('Delete this backup').'" onclick="return confirm(\''.$this->l('The deletion is permanent.', __CLASS__, true, false).'\n\n'.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete this backup').'" /></a>
															</div>
														</li>';
			$this->_html .= '		</ul>';
			$this->_html .= '	</fieldset>';
		}
		$this->_html .= '</div>';
	}
	
	private function _displayImport() {
		//$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		
		if($this->demoMode)
			$this->_html .= $this->displayWarning($this->l('Feature disabled on the demo mode'));
		
		$this->_html .= $this->_displayFormOpen("icon-upload", $this->l('Import from Wordpress XML file'), $this->PathModuleConf);

			$this->_html .= $this->displayWarning($this->l('Be carrefull ! Select only "Articles" exportation on your WordPress.'));

			$this->_html .= $this->_displayFormFile("col-lg-2", $this->l('Upload file'), $this->name.'_import_xml', "col-lg-5", $this->l('Format:').' *.XML');

			$this->_html .= $this->_displayFormSubmit("submitImportXml", "icon-cloud-upload", $this->l('Send file'));

		$this->_html .= $this->_displayFormClose();
		
		if(Configuration::get($this->name.'_import_xml')) {
			if(!file_exists(_PS_UPLOAD_DIR_.Configuration::get($this->name.'_import_xml'))) {
				$this->_html .= $this->displayError(
									$this->l('The XML file in the configuration is not locate in the ./download directory').'<br/>'.$this->l('You must upload a new import XML file.')
								);
			}
			else {
				$fileContent = Tools::file_get_contents(_PS_UPLOAD_DIR_.Configuration::get($this->name.'_import_xml'));
				if (strpos($fileContent, '<?xml') === false)
					$this->_html .= $this->displayError(
										$this->l('The file is not an XML content').'<br/>'.$this->l('You must upload a new import XML file.')
									);
				else {
					$this->_html .= $this->_displayFormOpen("icon-gear", $this->l('Import the XML file to language'), $this->PathModuleConf);
				
						$htmlLibre = $this->l('Current XML import file in configuration :').' '.Configuration::get($this->name.'_import_xml');
						$this->_html .= $this->_displayFormLibre("col-lg-2", '', $htmlLibre, "col-lg-7");

						$htmlLibre = '';
						foreach($languages As $language)
							$htmlLibre .= '	<input type="radio" name="import_xml_langue" value="'.(int)($language["id_lang"]).'" '.($this->LangueDefaultStore==(int)($language["id_lang"]) ? 'checked':'').'>
												<img src="../img/l/'.(int)($language["id_lang"]).'.jpg" />&nbsp;&nbsp;&nbsp;';

						$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Select language'), $htmlLibre, "col-lg-7");

						$this->_html .= $this->_displayFormSubmit("submitParseXml", "icon-gears", $this->l('Import the current file'));

					$this->_html .= $this->_displayFormClose();
				}
			}
		}
	}
	
	private function _displayConfigAntiSpam() {
		$Liste = AntiSpamClass::getListe((int)($this->context->language->id), 0);
		
		$this->_html .= $this->_displayFormOpen("icon-shield", $this->l('Antispam questions'), $this->PathModuleConf);

			$this->_html .= $this->displayInfo('
				<p>'.$this->l('This Antispam option can protect you to comments of spammers robots.').'</p>
				<p>'.$this->l('A question from all those you recorded will be placed randomly in the submission of a comment.'));
		
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-2", $this->l('Antispam activation'), $this->name.'_antispam_actif');

			$this->_html .= $this->_displayFormSubmit("submitAntiSpamConfig", "icon-save", $this->l('Update the configuration'));

		$this->_html .= $this->_displayFormClose();

		if($this->IsPSVersion(">=","1.6")) {
			$this->_html .= '
				<div class="panel">
					<fieldset>
						<div class="col-sm-3">
							<a class="btn btn-primary" href="'.$this->PathModuleConf.'&addAntiSpam">
								<i class="icon-plus"></i>
								'.$this->l('Add an antispam question').'
							</a>
						</div>
					</fieldset>
				</div>';
		}
		else {
			$this->_html .= '
				<table class="table" cellpadding="0" cellspacing="0" style="margin:auto;text-align:center">
					<tr>
						<th>
								<img src="../modules/'.$this->name.'/img/add.gif" alt="" />
								<a href="'.$this->PathModuleConf.'&addAntiSpam" title="'.$this->l('Add an antispam question').'">'.$this->l('Add an antispam question').'</a>
						</th>
					</tr>
				</table>';
		}

		$this->_html .= '<div class="panel">
								<table class="table" cellpadding="0" cellspacing="0" style="width:100%;margin:auto;">';
		$this->_html .= '	<thead class="center">';
		$this->_html .= '		<tr>';
		$this->_html .= '			<th></th>';
		$this->_html .= '			<th>'.$this->l('Question').'</th>';
		$this->_html .= '			<th>'.$this->l('Expected reply').'</th>';
		$this->_html .= '			<th class="center">'.$this->l('Activate').'</th>';
		$this->_html .= '			<th class="center">'.$this->l('Actions').'</th>';
		$this->_html .= '		</tr>';
		$this->_html .= '	</thead>';
		if(sizeof($Liste)) {
			foreach($Liste As $Key => $Value) {
				$this->_html .= '	<tr>';
				$this->_html .= '		<td class="center">'.($Key+1).'</td>';
				$this->_html .= '		<td>'.$Value["question"].'</td>';
				$this->_html .= '		<td>'.$Value["reply"].'</td>';
				
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&etatAntiSpam&idAS='.$Value['id_'.$this->name.'_antispam'].'">
					'.($Value["actif"]? '<img src="../modules/'.$this->name.'/img/enabled.gif" alt="" />':'<img src="../modules/'.$this->name.'/img/disabled.gif" alt="" />').'
					</a>
				</td>';
				$this->_html .= '		<td class="center">
					<a href="'.$this->PathModuleConf.'&editAntiSpam&idAS='.$Value['id_'.$this->name.'_antispam'].'" title="'.$this->l('Edit').'"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /></a>';
				$this->_html .= '		<a href="'.$this->PathModuleConf.'&deleteAntiSpam&idAS='.$Value['id_'.$this->name.'_antispam'].'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /></a>';
				$this->_html .= '		</td>';
				$this->_html .= '	</tr>';
			}
		}
		else {
			$this->_html .= '<tr><td colspan="5" class="center">'.$this->l('No content registered').'</td></tr>';
		}
		$this->_html .= '</table>';
		$this->_html .= '</div>';
	}
	
	private function _displayPageBlog() {
		//$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		$iso = Language::getIsoById((int)($this->context->language->id));
		$divLangName = 'meta_title';

		$this->_html .= '<script type="text/javascript">id_language = Number('.$this->LangueDefaultStore.');</script>';

		$this->_html .= $this->_displayFormOpen("blog.png", $this->l('Blog page configuration'), $this->PathModuleConf);

			$info =	'<p>'.$this->l('If you have a custom menu, or if you want to make an acces to your blog page, you can use this link :').'</p>
						<ul>';
						$multilang = (Language::countActiveLanguages() > 1);
						
						if ($multilang) {
							$languages = Language::getLanguages(true);
							foreach ($languages as $language) {
								if((int)(Configuration::get('prestablog_rewrite_actif')))
									if((int)(Configuration::get('PS_REWRITING_SETTINGS')))
										$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.Language::getIsoById((int)$language['id_lang']).'/blog';
									else
										$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&id_lang='.(int)$language['id_lang'];
								else
									if((int)(Configuration::get('PS_REWRITING_SETTINGS')))
										$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.Language::getIsoById((int)$language['id_lang']).'/?fc=module&module=prestablog&controller=blog';
									else
										$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&id_lang='.(int)$language['id_lang'];
								
								$info .= '<li><img src="../../img/l/'.$language['id_lang'].'.jpg" style="vertical-align:middle;" />
									<a href="'.$url_page_blog.'" target="_blank">'.$url_page_blog.'</a>
								</li>';
							}
						}
						else {
								if((int)(Configuration::get('PS_REWRITING_SETTINGS')) && (int)(Configuration::get('prestablog_rewrite_actif')))
									$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.'blog';
								else
									$url_page_blog = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog';
								
								$info .= '<li>
									<a href="'.$url_page_blog.'" target="_blank">'.$url_page_blog.'</a>
								</li>';
						}
							
			$info .= '</ul>';

			$this->_html .= $this->displayInfo($info);

			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-2", $this->l('Slide on blogpage'), $this->name.'_pageslide_actif');

			/***********************************************************/
			$htmlLibre = '';				
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->LangueDefaultStore ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.Configuration::get($this->name.'_titlepageblog_'.$language['id_lang']).'" />
					</div>';
				}			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Title Meta'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_title", $divLangName));
			/***********************************************************/
					
			$this->_html .= $this->_displayFormSubmit("submitPageBlog", "icon-save", $this->l('Update'));
		$this->_html .= $this->_displayFormClose();
	}
	
	private function _displayConfWizard() {
		$this->_html .= $this->_displayFormOpen("wizard.png", $this->l('Wizard templating'), $this->PathModuleConf);
		$this->_html .= $this->_displayFormSubmit("submitWizard", "icon-save", $this->l('Update'));
		$this->_html .= $this->_displayFormClose();
	}

	private function _displayConfTheme() {
		$this->_html .= $this->_displayFormOpen("theme.png", $this->l('Theme'), $this->PathModuleConf);
			$themes = array();
			foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme)
				$themes[$ValueTheme] = $ValueTheme;

			$this->_html .= $this->_displayFormSelect("col-lg-5", 
										$this->l('Choose your module theme :'), 
										'theme', 
										Configuration::get($this->name.'_theme'), 
										$themes, 
										null, 
										"col-lg-5");
			$this->_html .= '
				<script language="javascript" type="text/javascript">
					$(document).ready(function() {
						$("#theme").change(function() {
							var src = $(this).val();
							$("#imagePreview").hide();
							$("#imagePreview").html(src ? "<img src=\'../modules/'.$this->name.'/themes/" + src + "/preview.jpg\'>" : "");
							$("#imagePreview").fadeIn();
						});
					});
				</script>

				<label>'.$this->l('Preview :').'</label>
				<div class="margin-form">
					<div id="imagePreview" style="border: 1px #ccc solid;text-align:center;padding:10px;">
						<img src="../modules/'.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/preview.jpg" />
					</div>
					<div class="clear"></div>
				</div>';
			$this->_html .= $this->_displayFormSubmit("submitTheme", "icon-save", $this->l('Update'));
		$this->_html .= $this->_displayFormClose();
	}
	
	private function _displayConfSlide()
	{
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		
		$this->_html .= $this->_displayFormOpen("slide.png", $this->l('Slideshow'), $this->PathModuleConf);
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Slide on homepage'), $this->name.'_homenews_actif', $this->l('The slide will be displayed in the center column of the home page of your shop.'));
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Slide on blogpage'), $this->name.'_pageslide_actif', $this->l('The slide will be displayed in the top of first page articles list of blog.'));
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of slide to display'), $this->name.'_homenews_limit', Configuration::get($this->name.'_homenews_limit'), 10, "col-lg-2");
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_slide_title_length', Configuration::get($this->name.'_slide_title_length'), 10, "col-lg-2", $this->l('caracters'));
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Introduction length'), $this->name.'_slide_intro_length', Configuration::get($this->name.'_slide_intro_length'), 10, "col-lg-2", $this->l('caracters'));
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Slide picture width'), 'slide_picture_width', $ConfigTheme->images->slide->width, 10, "col-lg-2", $this->l('px'));
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Slide picture height'), 'slide_picture_height', $ConfigTheme->images->slide->height, 10, "col-lg-2", $this->l('px'));
			$this->_html .= $this->_displayFormSubmit("submitConfSlideNews", "icon-save", $this->l('Update'));
		$this->_html .= $this->_displayFormClose();
	}
	
	private function _displayConfBlocs() {
		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';
		
			$this->_html .= $this->_displayFormOpen("blocs.png", $this->l('Block last news'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_lastnews_actif');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show introduction text'), $this->name.'_lastnews_showintro', $this->l('This option may penalize your SEO.'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show thumb'), $this->name.'_lastnews_showthumb');
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_lastnews_title_length', Configuration::get($this->name.'_lastnews_title_length'), 10, "col-lg-4", $this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Introduction length'), $this->name.'_lastnews_intro_length', Configuration::get($this->name.'_lastnews_intro_length'), 10, "col-lg-4", $this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of news to display'), $this->name.'_lastnews_limit', Configuration::get($this->name.'_lastnews_limit'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Link "show all"'), $this->name.'_lastnews_showall');
				$this->_html .= $this->_displayFormSubmit("submitConfBlocLastNews", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();	
			
			$this->_html .= $this->_displayFormOpen("blocs.png", $this->l('Block date news'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_datenews_actif');
				$this->_html .= $this->_displayFormSelect("col-lg-5", 
										$this->l('Order news'), 
										$this->name.'_datenews_order', 
										Configuration::get($this->name.'_datenews_order'), 
										array("desc" => $this->l('Desc'), "asc" => $this->l('Asc')), 
										null, 
										"col-lg-5");
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Link "show all"'), $this->name.'_datenews_showall');
				$this->_html .= $this->_displayFormSubmit("submitConfBlocDateNews", "icon-save", $this->l('Update'));		
			$this->_html .= $this->_displayFormClose();		

			$this->_html .= $this->_displayFormOpen("blocs.png", $this->l('Block categories news'), $this->PathModuleConf);
				$this->_html .= '<p class="center"><a class="button" href="'.$this->PathModuleConf.'&configCategories">'.$this->l('Go to the categories configuration').'</a></p>';
			$this->_html .= $this->_displayFormClose();
		
			$this->_html .= $this->_displayFormOpen("rss.png", $this->l('Block Rss all news'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_allnews_rss');
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_rss_title_length', Configuration::get($this->name.'_rss_title_length'), 10, "col-lg-4", $this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Introduction length'), $this->name.'_rss_intro_length', Configuration::get($this->name.'_rss_intro_length'), 10, "col-lg-4", $this->l('caracters'));
				$this->_html .= $this->_displayFormSubmit("submitConfBlocRss", "icon-save", $this->l('Update'));		
			$this->_html .= $this->_displayFormClose();		

		$this->_html .= '</div>';
		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';

			$this->_html .= $this->_displayFormOpen("order.png", $this->l('Order of the blocks on columns'), $this->PathModuleConf);
				$this->_html .= '		<ul id="sortblocLeft" class="connectedSortable">
												<li class="ui-state-default ui-state-disabled">Left</li>';
												$sbl = unserialize(Configuration::get($this->name.'_sbl'));
												if(sizeof($sbl))
													foreach($sbl as $vs) {
														if($vs != '')
															$this->_html .= '				<li rel="'.$vs.'" class="ui-state-default ui-move">'.$this->MessageCallBack[$vs].'</li>';
													}
				$this->_html .= '		</ul>
											<ul id="sortblocRight" class="connectedSortable">
												<li class="ui-state-default ui-state-disabled">Right</li>';
												$sbr = unserialize(Configuration::get($this->name.'_sbr'));
												if(sizeof($sbr))
													foreach($sbr as $vs) {
														if($vs != '')
															$this->_html .= '				<li rel="'.$vs.'" class="ui-state-default ui-move">'.$this->MessageCallBack[$vs].'</li>';
													}

				$this->_html .= '		</ul>
											<script src="'.(Configuration::get('PS_SSL_ENABLED')?'https':'http').'://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
											<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/jquery.mjs.nestedSortable.js"></script>
											
											<script type="text/javascript">
												$(function() {
													$("#sortblocLeft, #sortblocRight").sortable({
															placeholder: "ui-state-highlight",
															connectWith: ".connectedSortable",
															items: "li:not(.ui-state-disabled)",
															update: function( event, ui ) {
																$.ajax({
																	url: \''.$this->context->link->getAdminLink('AdminPrestaBlogAjax').'\',
																	type: "GET",
																	data: {
																		ajax: true,
																		action: \'prestablogrun\',
																		do: \'sortBlocs\',
																		sortblocLeft: $("#sortblocLeft").sortable("toArray", { attribute: "rel" }),
																		sortblocRight: $("#sortblocRight").sortable("toArray", { attribute: "rel" }),
																		id_shop: \''.$this->context->shop->id.'\'
																	},
																	success:function(data){}
																});
															}
													}).disableSelection();
												});
											</script>';
			$this->_html .= $this->_displayFormClose();
		
			$this->_html .= $this->_displayFormOpen("blocs.png", $this->l('Footer last news'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_footlastnews_actif');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show introduction text'), $this->name.'_footlastnews_intro', $this->l('This option may penalize your SEO.'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of news to display'), $this->name.'_footlastnews_limit', Configuration::get($this->name.'_footlastnews_limit'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_footer_title_length', Configuration::get($this->name.'_footer_title_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Introduction length'), $this->name.'_footer_intro_length', Configuration::get($this->name.'_footer_intro_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Link "show all"'), $this->name.'_footlastnews_showall');
				$this->_html .= $this->_displayFormSubmit("submitConfFooterLastNews", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();

		$this->_html .= '</div>';
	}

	private function _displayConfCategories() {
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));

		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';

			$this->_html .= $this->_displayFormOpen("blocs.png", $this->l('Block categories news'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_catnews_actif');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('View empty categories'), $this->name.'_catnews_empty', $this->l('Supports the count of items in the categories recursive children.'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show news count by category'), $this->name.'_catnews_shownbnews', $this->l('Does not display zero values.'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Tree view'), $this->name.'_catnews_tree');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show thumb'), $this->name.'_catnews_showthumb');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show crop description'), $this->name.'_catnews_showintro');
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_cat_title_length', Configuration::get($this->name.'_cat_title_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Description length'), $this->name.'_cat_intro_length', Configuration::get($this->name.'_cat_intro_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Link "show all"'), $this->name.'_catnews_showall');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", '<img src="../modules/'.$this->name.'/img/rss.png" alt="" align="absmiddle" /> '.$this->l('Rss feed'), $this->name.'_catnews_rss',$this->l('List only for selected category'));
				$this->_html .= $this->_displayFormSubmit("submitConfBlocCatNews", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();	

			$this->_html .= $this->_displayFormOpen("categories.png", $this->l('Category menu in blog pages'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate menu on blog index'), $this->name.'_menu_cat_blog_index');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate menu on blog list'), $this->name.'_menu_cat_blog_list');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate menu on article'), $this->name.'_menu_cat_blog_article');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show blog link'), $this->name.'_menu_cat_home_link');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show blog image link'), $this->name.'_menu_cat_home_img', $this->l('Only if blog link is activated').'<br/>'.sprintf($this->l('Show %1$s instead %2$s'), '<img style="vertical-align:top;background-color:#383838;padding:4px;" src="'._MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/img/home.gif" alt="" />' , '"<strong>'.$this->l('Blog').'</strong>"' ) );
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('View empty categories'), $this->name.'_menu_cat_blog_empty', $this->l('Supports the count of items in the categories recursive children.'));
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show news count by category'), $this->name.'_menu_cat_blog_nbnews', $this->l('Does not display zero values.'));
				$this->_html .= $this->_displayFormSubmit("submitConfMenuCatBlog", "icon-save", $this->l('Update'));		
			$this->_html .= $this->_displayFormClose();	

		$this->_html .= '</div>';
		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';

			$this->_html .= $this->_displayFormOpen("top-category.png", $this->l('Top of first page of category'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show description'), $this->name.'_view_cat_desc');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show thumbnail'), $this->name.'_view_cat_thumb');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show picture'), $this->name.'_view_cat_img');
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Thumb picture width for categories'), 'thumb_cat_width', $ConfigTheme->categories->thumb->width, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Thumb picture height for categories'), 'thumb_cat_height', $ConfigTheme->categories->thumb->height, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Full size picture width for category'), 'full_cat_width', $ConfigTheme->categories->full->width, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Full size picture height for category'), 'full_cat_height', $ConfigTheme->categories->full->height, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormSubmit("submitConfCategory", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();

		
			$this->_html .= $this->_displayFormOpen("textoptions.png", $this->l('Options in articles list'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Thumb picture width for news'), 'thumb_picture_width', $ConfigTheme->images->thumb->width, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Thumb picture height for news'), 'thumb_picture_height', $ConfigTheme->images->thumb->height, 10, "col-lg-4",$this->l('px'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Title length'), $this->name.'_news_title_length', Configuration::get($this->name.'_news_title_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Description length'), $this->name.'_news_intro_length', Configuration::get($this->name.'_news_intro_length'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormSubmit("submitConfListeArticles", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();		

		$this->_html .= '</div>';
	}
	
	private function _displayConf()
	{
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));

		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';

			$this->_html .= $this->_displayFormOpen("rewrite.png", $this->l('Rewrite configuration'), $this->PathModuleConf);
				if(!Configuration::get('PS_REWRITING_SETTINGS') && Configuration::get('prestablog_rewrite_actif'))
					$this->_html .= $this->displayError($this->l('The general rewrite option (Friendly URL) of your PrestaShop is not activate.').'<br />'.$this->l('You must enable this general option to it works.'));

				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Enable rewrite (Friendly URL)'), $this->name.'_rewrite_actif', $this->l('Enable only if your server allows URL rewriting (recommended)'));
				$this->_html .= $this->_displayFormSubmit("submitConfRewrite", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();

			$this->_html .= $this->_displayFormOpen("frontoffice.png", $this->l('Global front configuration'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of news per page'), $this->name.'_nb_liste_page', Configuration::get($this->name.'_nb_liste_page'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Show thumbnail image in article page'), $this->name.'_view_news_img');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Add a new tab with associated blog posts directly in your product page'), $this->name.'_producttab_actif');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Socials buttons share'), $this->name.'_socials_actif');
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", '<img src="../modules/'.$this->name.'/img/rss.png" alt="" align="absmiddle" /> '.$this->l('Rss link for categories news'), $this->name.'_uniqnews_rss',$this->l('Rss link for categories in the news page.'));
				$this->_html .= $this->_displayFormSubmit("submitConfGobalFront", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();	

		$this->_html .= '</div>';
		$this->_html .= '<div class="'.($this->IsPSVersion(">=","1.6")?'col-md-6':'demi').'">';

			$this->_html .= $this->_displayFormOpen("backoffice.png", $this->l('Global admin configuration'), $this->PathModuleConf);
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of characters to search on related products for article edited'), $this->name.'_nb_car_min_linkprod', Configuration::get($this->name.'_nb_car_min_linkprod'), 10, "col-lg-4",$this->l('caracters'));
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Number of results in search off related products for article edited'), $this->name.'_nb_list_linkprod', Configuration::get($this->name.'_nb_list_linkprod'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('items/page on admin list news'), $this->name.'_nb_news_pl', Configuration::get($this->name.'_nb_news_pl'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('items/page on admin list comments'), $this->name.'_nb_comments_pl', Configuration::get($this->name.'_nb_comments_pl'), 10, "col-lg-4");
				$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Always show comments in article edition'), $this->name.'_comment_div_visible');
				$this->_html .= $this->_displayFormSubmit("submitConfGobalAdmin", "icon-save", $this->l('Update'));
			$this->_html .= $this->_displayFormClose();

		$this->_html .= '</div>';
	}
	
	private function _displayConfComments() {
		$this->_html .= $this->_displayFormOpen("comments.png", $this->l('Comments'), $this->PathModuleConf);
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Activate'), $this->name.'_comment_actif');
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Only registered can publish'), $this->name.'_comment_only_login');
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Always show comments on blog post'), $this->name.'_comment_autoshow', $this->l('If hidden, must click "on show comment" link in blog posts to toggle on.'));
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Auto approve comments'), $this->name.'_comment_auto_actif');
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Link href nofollow'), $this->name.'_comment_nofollow', $this->l('Indicates search engines not to follow the link'));
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Mail to admin on new comment'), $this->name.'_comment_alert_admin');
			$this->_html .= $this->_displayFormInput("col-lg-5", $this->l('Admin Mail'), $this->name.'_comment_admin_mail', Configuration::get($this->name.'_comment_admin_mail'), 40, "col-lg-4", NULL, NULL, '<i class="icon-envelope-o"></i>');
			$this->_html .= $this->_displayFormEnableItemConfiguration("col-lg-5", $this->l('Mail user subscription'), $this->name.'_comment_subscription', $this->l('Only registered can subscribe'));
			$this->_html .= $this->_displayFormSubmit("submitConfComment", "icon-save", $this->l('Update'));
		$this->_html .= $this->_displayFormClose();
	}
	
	private function _displayFormOpen($icon_legend = "cog.gif", $label_legend = "New Form", $action) {
		if($this->IsPSVersion(">=","1.6"))
			return '
			<div class="panel">
				<fieldset>
					<legend>
						'.(strpos($icon_legend, 'icon-') !== false?'<i class="'.$icon_legend.'"></i>':'<img src="../modules/'.$this->name.'/img/'.$icon_legend.'" />').'&nbsp;'.$label_legend.'
					</legend>
					<form method="post" class="form-horizontal" action="'.$action.'" enctype="multipart/form-data">';
		else
			return '
			<fieldset>
				<legend>
					'.(strpos($icon_legend, 'icon-') !== false?'<img src="../modules/'.$this->name.'/img/cog.gif" />':'<img src="../modules/'.$this->name.'/img/'.$icon_legend.'" />').'&nbsp;'.$label_legend.'
				</legend>
				<form method="post" class="form-horizontal" action="'.$action.'" enctype="multipart/form-data">';
	}

	private function _displayFormClose() {
		if($this->IsPSVersion(">=","1.6"))
			return '</form>
				</fieldset>
			</div>';
		else
			return '</form>
				</fieldset>
			<br />';
	}

	private function _displayFormSelect($label_bootstrap = "col-lg-5", $labelText, $nameItem, $value="", $options, $sizecar=20, $size_bootstrap="col-lg-5", $infoSpan=null, $help=null, $infoSpanBefore=null) {
		$select='';
		if($this->IsPSVersion(">=","1.6")) {
			$select .= '<div class="form-group ">
								<label class="control-label '.$label_bootstrap.'" for="'.$nameItem.'">'.$labelText.'</label>
								<div class="'.$size_bootstrap.'">
									<div class="input-group">
										'.($infoSpanBefore?'<span class="input-group-addon">'.$infoSpanBefore.'</span>':'').'
										<select name="'.$nameItem.'" id="'.$nameItem.'" '.($sizecar?'size="'.$sizecar.'"':"").'>';
											if(sizeof($options))
												foreach($options As $key => $val)
													$select .= '<option value="'.$key.'" '.($value==$key? ' selected' : '').'>'.$val.'</option>';
			$select .= '			</select>
										'.($infoSpan?'<span class="input-group-addon">'.$infoSpan.'</span>':'').'
									</div>
									'.($help?'<p class="help-block">'.$help.'</p>':'').'
								</div>
							</div>';
			return $select;
		}
		else {
			$select .= '	<div class="form-ligne">
									<label for="'.$nameItem.'">'.$labelText.'</label>
									<div class="margin-form">
										<select name="'.$nameItem.'" id="'.$nameItem.'" '.($sizecar?'size="'.$sizecar.'"':"").'>';
											if(sizeof($options))
												foreach($options As $key => $val)
													$select .= '<option value="'.$key.'" '.($value==$key? ' selected' : '').'>'.$val.'</option>';
						$select .= '</select>
										'.($help?'<p class="help-block">'.$help.'</p>':'').'
									</div>
									<div class="clear"></div>
								</div>';
			return $select;
		}
	}

	private function _displayFormSubmit($submitName, $icon, $label) {
		if($this->IsPSVersion(">=","1.6"))
			return '		<div class="form-actions">						
								<button class="btn btn-primary" name="'.$submitName.'" type="submit"><i class="'.$icon.'"></i>&nbsp;'.$label.'</button>
							</div>';
		else
			return '		<div class="form-ligne">
								<div class="margin-form">
									<input type="submit" name="'.$submitName.'" value="'.$label.'" class="button" />
								</div>
								<div class="clear"></div>
							</div>';
	}	

	private function _displayFormLibre($label_bootstrap = "col-lg-5", $labelText, $libreHtml, $size_bootstrap="col-lg-5", $langFlags=null) {
		//$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);

		if($this->IsPSVersion(">=","1.6"))
			return '		<div class="form-group ">
								<label class="control-label '.$label_bootstrap.'">'.$labelText.'</label>
								<div class="'.$size_bootstrap.'">
									'.$libreHtml.'
								</div>
								'.($langFlags?'<div class="col-lg-1">'.$langFlags.'</div>':'').'
							</div>';
		else
			return '	<div class="form-ligne">
							<label>'.$labelText.'</label>
							<div class="margin-form">
								<div style="float:left;">'.$libreHtml.'</div>
								'.($langFlags?$langFlags:'').'
							</div>
							<div class="clear"></div>
						</div>';
	}

	private function _displayFormFile($label_bootstrap="col-lg-5", $labelText, $nameItem, $size_bootstrap="col-lg-5", $help=null) {
		if($this->IsPSVersion(">=","1.6"))
			return '		<div class="form-group ">
								<label class="control-label '.$label_bootstrap.'" for="'.$nameItem.'">'.$labelText.'</label>
								<div class="'.$size_bootstrap.'">
									<input id="'.$nameItem.'" type="file" name="'.$nameItem.'" class="hide" />
									<div class="dummyfile input-group">
										<span class="input-group-addon"><i class="icon-file"></i></span>
										<input id="'.$nameItem.'-name" type="text" class="disabled" name="filename" readonly />
										<span class="input-group-btn">
											<button id="'.$nameItem.'-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
												<i class="icon-folder-open"></i> '.$this->l('Choose a file').'
											</button>
										</span>
									</div>
									'.($help?'<p class="help-block">'.$help.'</p>':'').'
								</div>
							</div>
							<script>
								$(document).ready(function(){
									$(\'#'.$nameItem.'-selectbutton\').click(function(e){
										$(\'#'.$nameItem.'\').trigger(\'click\');
									});
									$(\'#'.$nameItem.'\').change(function(e){
										var val = $(this).val();
										var file = val.split(/[\\/]/);
										$(\'#'.$nameItem.'-name\').val(file[file.length-1]);
									});
								});
							</script>';
		else
			return '		<div class="form-ligne">
								<label for="'.$nameItem.'">'.$labelText.'</label>
								<div class="margin-form">
									<input type="file" name="'.$nameItem.'"  id="'.$nameItem.'" />'.($help?' '.$help:'').'
								</div>
								<div class="clear"></div>
							</div>';
	}

	private function _displayFormFileNoLabel($nameItem, $size_bootstrap="col-lg-5", $help=null) {
		if($this->IsPSVersion(">=","1.6"))
			return '		
								<input id="'.$nameItem.'" type="file" name="'.$nameItem.'" class="hide" />
								<div class="dummyfile input-group '.$size_bootstrap.'" >
									<span class="input-group-addon"><i class="icon-file"></i></span>
									<input id="'.$nameItem.'-name" type="text" class="disabled" name="filename" readonly />
									<span class="input-group-btn">
										<button id="'.$nameItem.'-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
											<i class="icon-folder-open"></i> '.$this->l('Choose a file').'
										</button>
									</span>
								</div>
								'.($help?'<p class="help-block">'.$help.'</p>':'').'
								<script>
									$(document).ready(function(){
										$(\'#'.$nameItem.'-selectbutton\').click(function(e){
											$(\'#'.$nameItem.'\').trigger(\'click\');
										});
										$(\'#'.$nameItem.'\').change(function(e){
											var val = $(this).val();
											var file = val.split(/[\\/]/);
											$(\'#'.$nameItem.'-name\').val(file[file.length-1]);
										});
									});
								</script>
							';
		else
			return '		<input type="file" name="'.$nameItem.'"  id="'.$nameItem.'" />'.($help?' '.$help:'');
	}	

	private function _displayFormInput($label_bootstrap="col-lg-5", $labelText, $nameItem, $value="", $sizecar=20, $size_bootstrap="col-lg-5", $infoSpan=null, $help=null, $infoSpanBefore=null) {
		if($this->IsPSVersion(">=","1.6"))
			return '		<div class="form-group ">
								<label class="control-label '.$label_bootstrap.'" for="'.$nameItem.'">'.$labelText.'</label>
								<div class="'.$size_bootstrap.'">
									<div class="input-group">
										'.($infoSpanBefore?'<span class="input-group-addon">'.$infoSpanBefore.'</span>':'').'
										<input id="'.$nameItem.'" '.($sizecar?'size="'.$sizecar.'"':"").' type="text" value="'.$value.'" name="'.$nameItem.'">
										'.($infoSpan?'<span class="input-group-addon">'.$infoSpan.'</span>':'').'
									</div>
									'.($help?'<p class="help-block">'.$help.'</p>':'').'
								</div>
							</div>';
		else
			return '		<div class="form-ligne">
								<label for="'.$nameItem.'">'.$labelText.'</label>
								<div class="margin-form">
									<input type="text" name="'.$nameItem.'" '.($sizecar?'size="'.$sizecar.'"':"").' value="'.$value.'" />'.($infoSpan?'&nbsp;<strong>'.$infoSpan.'</strong>':'').'
									'.($help?'<p class="help-block">'.$help.'</p>':'').'
								</div>
								<div class="clear"></div>
							</div>';
	}	

	private function _displayFormDate($label_bootstrap="col-lg-5", $labelText, $nameItem, $value, $time) {
		if(!$value) {
			if($time)
				$value = date("Y-m-d H:i:s");
			else
				$value = date("Y-m-d");
		}

		if($this->IsPSVersion(">=","1.6"))
			return '		<div class="form-group ">
								<label class="control-label '.$label_bootstrap.'" for="'.$nameItem.'">'.$labelText.'</label>
								<div class="'.($time?'col-lg-3':'col-lg-2').'">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input id="'.$nameItem.'" '.($time?'size="20"':'size="10"').' type="text" value="'.$value.'" name="'.$nameItem.'">
									</div>
									<p class="help-block">'.$this->l('Format: YYYY-MM-DD').($time?' '.$this->l('HH:MM:SS'):'').'</p>
								</div>
							</div>'.$this->ModuleDatepicker($nameItem, true);
		else
			return '	<div class="form-ligne">
							<label for="'.$nameItem.'">'.$labelText.'</label>
							<div class="margin-form">
								<input id="'.$nameItem.'" type="text" name="'.$nameItem.'" '.($time?'size="20"':'size="10"').' value="'.htmlentities($value, ENT_COMPAT, 'UTF-8').'" />
								'.$this->l('Format: YYYY-MM-DD').($time?' '.$this->l('HH:MM:SS'):'').'
							</div>
							<div class="clear"></div>
						</div>'.$this->ModuleDatepicker($nameItem, true);
	}

	private function _displayFormEnableItemConfiguration($label_bootstrap="col-lg-5", $labelText, $nameItem, $help=null) {
		if($this->IsPSVersion(">=","1.6"))
			return '	<div class="form-group">
							<label for="'.$nameItem.'" class="control-label '.$label_bootstrap.'">
								<span>'.$labelText.'</span>
							</label>
							<div class="col-lg-7">
								<span class="switch prestashop-switch fixed-width-lg">
									<input name="'.$nameItem.'" id="'.$nameItem.'_on" value="1" '.(Tools::getValue($nameItem, Configuration::get($nameItem)) ? 'checked="checked" ' : '').' type="radio">
									<label for="'.$nameItem.'_on">'.$this->l('Yes').'</label>
									<input name="'.$nameItem.'" id="'.$nameItem.'_off" value="0" '.(!Tools::getValue($nameItem, Configuration::get($nameItem)) ? 'checked="checked" ' : '').' type="radio">
									<label for="'.$nameItem.'_off">'.$this->l('No').'</label>
									<a class="slide-button btn"></a>
								</span>
								'.($help?'<p class="help-block">'.$help.'</p>':'').'
							</div>
						</div>';
		else
			return '	<div class="form-ligne">
							<label>'.$labelText.'</label>
							<div class="margin-form">
								<input type="radio" name="'.$nameItem.'" value="1" '.(Tools::getValue($nameItem, Configuration::get($nameItem)) ? 'checked="checked" ' : '').'/>
								<label class="t" > <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
								<input type="radio" name="'.$nameItem.'" value="0" '.(!Tools::getValue($nameItem, Configuration::get($nameItem)) ? 'checked="checked" ' : '').'/>
								<label class="t" > <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
								'.($help?'<br /><span>'.$help.'</span>':'').'
							</div>
							<div class="clear"></div>
						</div>';
	}

	private function _displayFormEnableItem($label_bootstrap="col-lg-5", $labelText, $nameItem, $value, $help=null) {
		if($this->IsPSVersion(">=","1.6"))
			return '	<div class="form-group">
							<label for="'.$nameItem.'" class="control-label '.$label_bootstrap.'">
								<span>'.$labelText.'</span>
							</label>
							<div class="col-lg-7">
								<span class="switch prestashop-switch fixed-width-lg">
									<input name="'.$nameItem.'" id="'.$nameItem.'_on" value="1" '.($value ? 'checked="checked" ' : '').' type="radio">
									<label for="'.$nameItem.'_on">'.$this->l('Yes').'</label>
									<input name="'.$nameItem.'" id="'.$nameItem.'_off" value="0" '.(!$value ? 'checked="checked" ' : '').' type="radio">
									<label for="'.$nameItem.'_off">'.$this->l('No').'</label>
									<a class="slide-button btn"></a>
								</span>
								'.($help?'<p class="help-block">'.$help.'</p>':'').'
							</div>
						</div>';
		else
			return '	<div class="form-ligne">
							<label>'.$labelText.'</label>
							<div class="margin-form">
								<input type="radio" name="'.$nameItem.'" value="1" '.($value ? 'checked="checked" ' : '').'/>
								<label class="t" > <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
								<input type="radio" name="'.$nameItem.'" value="0" '.(!$value ? 'checked="checked" ' : '').'/>
								<label class="t" > <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
								'.($help?'<br /><span>'.$help.'</span>':'').'
							</div>
							<div class="clear"></div>
						</div>';
	}

	private function displayFlagsFor($item, $divLangName) {
		//$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		return $this->displayFlags($languages, $this->LangueDefaultStore, $divLangName, $item, true);
	}
	
	private function _displayFormNews()
	{
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		
		$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		$iso = Language::getIsoById((int)($this->context->language->id));
		$divLangName = 'title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1¤cpara2';
		
		$legend_title = $this->l('Add news');
		if(Tools::getValue('idN')) {
			$News = new NewsClass((int)(Tools::getValue('idN')));
			$LangListeNews = unserialize($News->langues);
			$legend_title = $this->l('Edit news').' #'.$News->id;;
		}
		else {
			$News = new NewsClass();
			$defaultCat = 1;
		}
		
		if($_POST){
			$News->id_shop = (int)$this->context->shop->id;
			$News->copyFromPost();
		}
		
		// TinyMCE
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
			<script type="text/javascript">
			'.(Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL') ? 'var PS_ALLOW_ACCENTED_CHARS_URL = 1;' : 'var PS_ALLOW_ACCENTED_CHARS_URL = 0;').'
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			'.(self::IsPSVersion('>=','1.6')?'
			<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.1.6.js"></script>
			<script type="text/javascript">
				$(function() {
					tinySetup({ editor_selector :"autoload_rte" });
				});
			</script>
				':'
			<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.js"></script>
				').'
			<script type="text/javascript">
				id_language = Number('.$defaultLanguage.');
				
				function copy2friendlyURLPrestaBlog() {
					if(!$(\'#slink_rewrite_\'+id_language).attr(\'disabled\')) {
						$(\'#slink_rewrite_\'+id_language).val(str2url($(\'input#title_\'+id_language).val().replace(/^[0-9]+\./, \'\'), \'UTF-8\')); 
					}
				}
				function updateFriendlyURLPrestaBlog() { 
					$(\'#slink_rewrite_\'+id_language).val(str2url($(\'#slink_rewrite_\'+id_language).val().replace(/^[0-9]+\./, \'\'), \'UTF-8\')); 
				}
				
				function RetourLangueCheckUp(ArrayCheckedLang, idLangEnCheck, idLangDefaut) {
					if(ArrayCheckedLang.length > 0)
						return ArrayCheckedLang[0];
					else
						return idLangDefaut;
				}
				
				$(function() {
					';

					if(Tools::getValue('idN')) {
						if(!Tools::getValue('languesup') && sizeof($LangListeNews) == 1) 
							$this->_html .= 'changeTheLanguage(\'title\', \''.$divLangName.'\', '.(int)$LangListeNews[0].', \'\');';
					}
					else {
						$ArrayCheckLang = array();
						if(Tools::getValue('languesup'))
							$ArrayCheckLang = Tools::getValue('languesup');

						if(sizeof($ArrayCheckLang) == 1)
							$this->_html .= 'changeTheLanguage(\'title\', \''.$divLangName.'\', '.(int)$ArrayCheckLang[0].', \'\');';
						else
							$this->_html .= 'changeTheLanguage(\'title\', \''.$divLangName.'\', '.(int)$defaultLanguage.', \'\');';
					}

		$this->_html .= '
					$(".catlang").hide();
					$(".catlang[rel="+id_language+"]").show();
					
					$("div.language_flags img, #check_lang_prestablog img").click(function() {
						$(".catlang").hide();
						$(".catlang[rel="+id_language+"]").show();
						$("#imgCatLang").attr("src", "../img/l/" + id_language + ".jpg");
					});
					
					$("input[name=\'languesup[]\']").click(function() {
						if(this.checked)
							changeTheLanguage(\'title\', \''.$divLangName.'\', this.value, \'\');
						else {
							selectedL = new Array();
							$("input[name=\'languesup[]\']:checked").each(function() {selectedL.push($(this).val());});
							changeTheLanguage(\'title\', \''.$divLangName.'\', RetourLangueCheckUp(selectedL, this.value, '.$defaultLanguage.'), \'\');
						}
					});
					
					$("#submitForm").click(function() {';
					foreach ($languages as $language) {
						$this->_html .= '$(\'#slink_rewrite_'.$language['id_lang'].'\').removeAttr("disabled");';
					}
						$this->_html .= '
						selectedLangues = new Array();
						$("input[name=\'languesup[]\']:checked").each(function() {selectedLangues.push($(this).val());});
						
						if (selectedLangues.length == 0) {
							alert("'.$this->l('You must choose at least one language !').'");
							$("html, body").animate({scrollTop: $("#menu_config_prestablog").offset().top}, 300);
							$("#check_lang_prestablog").css("background-color", "#FFA300");
							return false;
						}
						else return true;
					});
					
					$("#control").toggle( 
						function () { 
							$(\'#slink_rewrite_\'+id_language).removeAttr("disabled");
							$(\'#slink_rewrite_\'+id_language).css("background-color", "#fff");
							$(\'#slink_rewrite_\'+id_language).css("color", "#000");
							$(this).html("'.$this->l('Disable this rewrite').'");
						},
						function () { 
							$(\'#slink_rewrite_\'+id_language).attr("disabled", true);
							$(\'#slink_rewrite_\'+id_language).css("background-color", "#e0e0e0");
							$(\'#slink_rewrite_\'+id_language).css("color", "#7F7F7F");
							$(this).html("'.$this->l('Enable this rewrite').'");
						} 
					);
					';
					
				foreach ($languages as $language) {
					$this->_html .= '
					if ($("#slink_rewrite_'.$language['id_lang'].'").val() == \'\') { 
						$("#slink_rewrite_'.$language['id_lang'].'").removeAttr("disabled");
						$("#slink_rewrite_'.$language['id_lang'].'").css("background-color", "#fff");
						$("#slink_rewrite_'.$language['id_lang'].'").css("color", "#000");
						$("#control").html("'.$this->l('Disable this rewrite').'");
					}
					
					$("#paragraph_'.$language['id_lang'].'").keyup(function(){  
						var limit = parseInt($(this).attr("maxlength"));
						var text = $(this).val();
						var chars = text.length;
						if(chars > limit){
							var new_text = text.substr(0, limit);
							$(this).val(new_text);
						}
						$("#compteur-texte-'.$language['id_lang'].'").html(chars+" / "+limit);
					});';
				}
		
		$this->_html .= '
					$("#productLinkSearch").bind("keyup click focusin", function() {
						ReloadLinkedSearchProducts();
					});
					
					ReloadLinkedProducts();
				});
				
				function ReloadLinkedSearchProducts(start) {
					var listLinkedProducts="";
					$("input[name^=productsLink]").each(function() {
						listLinkedProducts += $(this).val() + ";";
					});
					
					if($("#productLinkSearch").val() != \'\' && $("#productLinkSearch").val().length >= '.(int)Configuration::get($this->name.'_nb_car_min_linkprod').') {
						$.ajax({
							url: \''.$this->context->link->getAdminLink('AdminPrestaBlogAjax').'\',
							type: "GET",
							data: {
								ajax: true,
								action: \'prestablogrun\',
								do: \'searchProducts\',
								listLinkedProducts: listLinkedProducts,
								start: start,
								req: $("#productLinkSearch").attr("value"),
								id_shop: \''.$this->context->shop->id.'\'
							},
							success:function(data){
								$("#productLinkResult").empty();
								$("#productLinkResult").append(data);
							}
						});
					}
					else {
						$("#productLinkResult").empty();
						$("#productLinkResult").append(\'<tr><td colspan="4" class="center">'.$this->l('You must search before').' ('.(int)Configuration::get($this->name.'_nb_car_min_linkprod').' '.$this->l('caract. minimum').')</td></tr>\');
					}
				}
				
				function ReloadLinkedProducts() {
					var req="";
					$("input[name^=productsLink]").each(function() {
						req += $(this).val() + ";";
					});
					$.ajax({
						url: \''.$this->context->link->getAdminLink('AdminPrestaBlogAjax').'\',
						type: "GET",
						data: {
							ajax: true,
							action: \'prestablogrun\',
							do: \'loadProductsLink\',
							req: req,
							id_shop: \''.$this->context->shop->id.'\'
						},
						success:function(data){
							$("#productLinked").empty();
							$("#productLinked").append(data);
						}
					});
				}

				function changeTheLanguage(title, divLangName, id_lang, iso) {
					$("#imgCatLang").attr("src", "../img/l/" + id_lang + ".jpg");
					return changeLanguage(title, divLangName, id_lang, iso);
				}
			</script>';
	
		$this->_html .= $this->_displayFormOpen("icon-edit", $legend_title, $this->PathModuleConf);
			if(Tools::getValue('idN'))
				$this->_html .= '<input type="hidden" name="idN" value="'.Tools::getValue('idN').'" />';

			/***********************************************************/
			$htmlLibre = '<span id="check_lang_prestablog">'.(sizeof($languages) == 1 ? '' : '<input type="checkbox" name="checkmelang" class="noborder" onclick="checkDelBoxes(this.form, \'languesup[]\', this.checked)" /> '.$this->l('All').' | ');
								foreach ($languages as $language) {
									$htmlLibre .= '<input type="checkbox" name="languesup[]" value="'.$language['id_lang'].'" '.
										(			(Tools::getValue('idN') && in_array((int)$language['id_lang'], $LangListeNews))
											||		(Tools::getValue('languesup') && in_array((int)$language['id_lang'], Tools::getValue('languesup')))
											||		( ( !Tools::getValue('idN') && !Tools::getValue('languesup')) && ((int)$language['id_lang'] == (int)$defaultLanguage) )
											 ? 'checked=checked' : '' ).' '.(sizeof($languages) == 1 ? 'style="display:none;"' : '').' /><img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeTheLanguage(\'title\', \''.$divLangName.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');"  />';
								}
			$htmlLibre .= '</span>';
			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Language'), $htmlLibre, "col-lg-7");
			/***********************************************************/
			$htmlLibre = '';
								foreach ($languages as $language) {
									$htmlLibre .= '<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
										<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="title_'.$language['id_lang'].'" id="title_'.$language['id_lang'].'" maxlength="'.(int)Configuration::get('prestablog_news_title_length').'" value="'.(isset($News->title[$language['id_lang']]) ? $News->title[$language['id_lang']] : '').'" onkeyup="if (isArrowKey(event)) return; copy2friendlyURLPrestaBlog();" onchange="copy2friendlyURLPrestaBlog();" />
									</div>';
								}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Main title'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("title", $divLangName));
			/***********************************************************/
			$this->_html .= $this->_displayFormEnableItem("col-lg-2", $this->l('Activate'), 'actif', $News->actif);
			/***********************************************************/
			$this->_html .= $this->_displayFormEnableItem("col-lg-2", $this->l('Silde'), 'slide', $News->slide);
			/***********************************************************/
			if(Tools::getValue('idN')) {
				$CommentsActif 	= 	CommentNewsClass::getListe(1, $News->id);
				$CommentsAll 		= 	CommentNewsClass::getListe(-2, $News->id);
				$CommentsNonLu		= 	CommentNewsClass::getListe(-1, $News->id);
				$CommentsDisabled	= 	CommentNewsClass::getListe(0, $News->id);

				$htmlLibre = '
					<div id="labelComments">
						'.((sizeof($CommentsAll)) ? '<strong>'.count($CommentsActif).'</strong> '.$this->l('approuved').' '.$this->l('of').' <strong>'.count($CommentsAll).'</strong>' : $this->l('No comment')).((sizeof($CommentsNonLu)) ? '&nbsp;&mdash;-&nbsp;<span style="color:green;font-weight:bold;">'.count($CommentsNonLu).' '.$this->l('Comments pending').'</span>' : '').'<br />
						'.((sizeof($CommentsAll)) ? '<span onclick="$(\'#comments\').slideToggle();" style="cursor: pointer" class="link"><img src="../img/admin/cog.gif" alt="'.$this->l('Comments').'" title="'.$this->l('Comments').'" />'.$this->l('Click here to manage comments').'</span>' : '').'
					</div>'."\n";

				$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Comments'), $htmlLibre, "col-lg-10");

				if(sizeof($CommentsAll)) {
					$htmlLibre='';
					if(Tools::isSubmit('showComments')) {
						$htmlLibre .= '<div id="comments">'."\n";
						$htmlLibre .= '<script type="text/javascript">$(document).ready(function() { $("html, body").animate({scrollTop: $("#labelComments").offset().top}, 750); });</script>'."\n";
					}
					else
						$htmlLibre .= '<div id="comments" style="'.(Configuration::get($this->name.'_comment_div_visible') ? '' : 'display: none;').'">'."\n";
					
					$htmlLibre .= '
						<div class="blocs col-sm-4 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
							<h3><img src="../modules/'.$this->name.'/img/question.gif" alt="'.$this->l('Pending').'" title="'.$this->l('Pending').'" />'.count($CommentsNonLu).'&nbsp;'.$this->l('Comments pending').'</h3>'."\n";
					if(sizeof($CommentsNonLu)) {
						$htmlLibre .= '<div class="wrap">'."\n";
						foreach($CommentsNonLu As $KeyC => $ValueC) {
							$htmlLibre .= '<div>'."\n";
							$htmlLibre .= '
								<h4>
									<a href="'.$this->PathModuleConf.'&deleteComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" style="float:right;"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /><span style="display:none;">'.$this->l('Delete').'</span></a>
									'.ToolsCore::displayDate($ValueC["date"], null, true).'<br />'.$this->l('by').' <strong>'.$ValueC["name"].'</strong>
								</h4>'."\n";
							if ($ValueC["url"]!="")
								$htmlLibre .= '	<h5><a href="'.$ValueC["url"].'" target="_blank">'.$ValueC["url"].'</a></h5>'."\n";
							$htmlLibre .= '	<p>'.$ValueC["comment"].'</p>'."\n";
							$htmlLibre .= '	
							<p class="center">
								<a href="'.$this->PathModuleConf.'&enabledComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../img/admin/enabled.gif" alt="'.$this->l('Approuved').'" /><span style="display:none;">'.$this->l('Approuved').'</span></a>
								<a href="'.$this->PathModuleConf.'&editComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /><span style="display:none;">'.$this->l('Edit').'</span></a>
								<a href="'.$this->PathModuleConf.'&disabledComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" /><span style="display:none;">'.$this->l('Disabled').'</span></a>
							</p>'."\n";
							$htmlLibre .= '</div>'."\n";
						}
						$htmlLibre .= '</div>'."\n";
					}
					$htmlLibre .= '
						</div>'."\n";

					$htmlLibre.= '
						<div class="blocs col-sm-4 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
							<h3><img src="../img/admin/enabled.gif" alt="'.$this->l('Approuved').'" title="'.$this->l('Approuved').'" />'.count($CommentsActif).'&nbsp;'.$this->l('Comments approuved').'</h3>'."\n";
					if(sizeof($CommentsActif)) {
						$htmlLibre .= '<div class="wrap">'."\n";
						foreach($CommentsActif As $KeyC => $ValueC) {
							$htmlLibre .= '<div>'."\n";
							$htmlLibre .= '
								<h4>
									<a href="'.$this->PathModuleConf.'&deleteComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" style="float:right;"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'"/><span style="display:none;">'.$this->l('Delete').'</span></a>
									'.ToolsCore::displayDate($ValueC["date"], null, true).'<br />'.$this->l('by').' <strong>'.$ValueC["name"].'</strong>
								</h4>'."\n";
							if ($ValueC["url"]!="")
								$htmlLibre .= '	<h5><a href="'.$ValueC["url"].'" target="_blank">'.$ValueC["url"].'</a></h5>'."\n";
							$htmlLibre .= '	<p>'.$ValueC["comment"].'</p>'."\n";
							$htmlLibre .= '	
							<p class="center">
								<a href="'.$this->PathModuleConf.'&editComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /><span style="display:none;">'.$this->l('Edit').'</span></a>
								<a href="'.$this->PathModuleConf.'&disabledComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../img/admin/disabled.gif" alt="'.$this->l('Deleted').'" /><span style="display:none;">'.$this->l('Disabled').'</span></a>
							</p>'."\n";
							$htmlLibre .= '</div>'."\n";
						}
						$htmlLibre .= '</div>'."\n";
					}
					$htmlLibre .= '
						</div>'."\n";
						
					$htmlLibre .= '
						<div class="blocs col-sm-3 '.(self::IsPSVersion('<','1.6')?'fixBloc15':'').'">
							<h3><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'.count($CommentsDisabled).'&nbsp;'.$this->l('Comments disabled').'</h3>'."\n";
					if(sizeof($CommentsDisabled)) {
						$htmlLibre .= '<div class="wrap">'."\n";
						foreach($CommentsDisabled As $KeyC => $ValueC) {
							$htmlLibre .= '<div>'."\n";
							$htmlLibre .= '
								<h4>
									<a href="'.$this->PathModuleConf.'&deleteComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" style="float:right;"><img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'"/><span style="display:none;">'.$this->l('Delete').'</span></a>
									'.ToolsCore::displayDate($ValueC["date"], null, true).'<br />'.$this->l('by').' <strong>'.$ValueC["name"].'</strong>
								</h4>'."\n";
							if ($ValueC["url"]!="")
								$htmlLibre .= '	<h5><a href="'.$ValueC["url"].'" target="_blank">'.$ValueC["url"].'</a></h5>'."\n";
							$htmlLibre .= '	<p>'.$ValueC["comment"].'</p>'."\n";
							$htmlLibre .= '	
							<p class="center">
								<a href="'.$this->PathModuleConf.'&editComment&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment"><img src="../modules/'.$this->name.'/img/edit.gif" alt="" /><span style="display:none;">'.$this->l('Edit').'</span></a>
								<a href="'.$this->PathModuleConf.'&enabledComment&idN='.Tools::getValue('idN').'&idC='.$ValueC['id_'.$this->name.'_commentnews'].'" class="hrefComment" ><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" /><span style="display:none;">'.$this->l('Approuved').'</span></a>
							</p>'."\n";
							$htmlLibre .= '</div>'."\n";
						}
						$htmlLibre .= '</div>'."\n";
					}
					$htmlLibre .= '
						</div>'."\n";

					$htmlLibre .= '
						</div>
						<div class="clear"></div>
						'."\n";
					$htmlLibre .= '
						<script type="text/javascript">
							$(document).ready(function() { 
								$("a.hrefComment").mouseenter(function() { 
									$("span:first", this).show(\'slow\'); 
								}).mouseleave(function() { 
									$("span:first", this).hide(); 
								});
							});
						</script>'."\n";
					
					$this->_html .= $this->_displayFormLibre("col-lg-2", '', $htmlLibre, "col-lg-10");
				}
			}
			/***********************************************************/
			// DEBUT SEO
			$htmlLibre = '<span onclick="$(\'#seo\').slideToggle();" style="cursor: pointer" class="link">
						<img src="../img/admin/cog.gif" alt="'.$this->l('SEO').'" title="'.$this->l('SEO').'" />'.$this->l('Click here to improve SEO').'
					</span>';

			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('SEO'), $htmlLibre, "col-lg-7");

			$this->_html .= '<div id="seo" style="display: none;">';
			/***********************************************************/
			$htmlLibre = '';
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="link_rewrite_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="link_rewrite_'.$language['id_lang'].'" id="slink_rewrite_'.$language['id_lang'].'" value="'.(isset($News->link_rewrite[$language['id_lang']]) ? $News->link_rewrite[$language['id_lang']] : '').'" 
						onkeyup="if (isArrowKey(event)) return ;updateFriendlyURLPrestaBlog();" onchange="updateFriendlyURLPrestaBlog();" 
						'.(isset($News->id) ? ' style="color:#7F7F7F;background-color:#e0e0e0;" disabled="true"' :'').'
						/><sup> *</sup>
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", 
																	$this->l('Url Rewrite').'<br/><a href="#" id="control" />'.(isset($News->id) ? $this->l('Enable this rewrite') : $this->l('Disable this rewrite')).'</a>', 
																	$htmlLibre, "col-lg-7", $this->displayFlagsFor("link_rewrite", $divLangName));
			/***********************************************************/
			$htmlLibre = '';					
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.(isset($News->meta_title[$language['id_lang']]) ? $News->meta_title[$language['id_lang']] : '').'" />
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Title'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_title", $divLangName));
			/***********************************************************/
			$htmlLibre = '';				
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_description_'.$language['id_lang'].'" id="meta_description_'.$language['id_lang'].'" value="'.(isset($News->meta_description[$language['id_lang']]) ? $News->meta_description[$language['id_lang']] : '').'" />
					</div>';
				}			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Description'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_description", $divLangName));
			/***********************************************************/
			$htmlLibre = '';
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_keywords_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_keywords_'.$language['id_lang'].'" id="meta_keywords_'.$language['id_lang'].'" value="'.(isset($News->meta_keywords[$language['id_lang']]) ? $News->meta_keywords[$language['id_lang']] : '').'" />
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Keywords'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_keywords", $divLangName));
			/***********************************************************/
			$this->_html .= '</div>';
			// FIN SEO
			/***********************************************************/

			/***********************************************************/
			// DEBUT IMAGE
			$htmlLibre = '';
				if($this->demoMode)
					$htmlLibre .= $this->displayWarning($this->l('Feature disabled on the demo mode'));
				if(		Tools::getValue('idN') 
					&&	file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg')) {
					$htmlLibre .= '<span id="labelPicture"></span>';
					$ConfigThemeArray = objectToArray($ConfigTheme);
					if(Tools::getValue('pfx')) {
						$htmlLibre .= '<script type="text/javascript">$(document).ready(function() { $("html, body").animate({scrollTop: $("#labelPicture").offset().top}, 750); });</script>'."\n";
					}
					$htmlLibre .= '
					<script src="'.$this->_path.'js/Jcrop/jquery.Jcrop.prestablog.js"></script>
					<link rel="stylesheet" href="'.$this->_path.'js/Jcrop/css/jquery.Jcrop.css" type="text/css" />
					<script language="Javascript">'."\n";
					
					$htmlLibre .= '							var ratioValue = new Array();'."\n";
					foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
						$htmlLibre .= '							ratioValue[\''.$KeyThemeArray.'\'] = '.$ValueThemeArray["width"]/$ValueThemeArray["height"].';'."\n";
					}
					
					$htmlLibre .= '
						var monRatio;
						var monImage;
						
						$(function(){
							$("div.togglePreview").hide();'."\n";
							if(Tools::getValue('pfx'))
								$htmlLibre .= '
								$(\'input[name$="imageChoix"]\').filter(\'[value="'.Tools::getValue('pfx').'"]\').attr(\'checked\', true);
								$(\'input[name$="imageChoix"]\').filter(\'[value="'.Tools::getValue('pfx').'"]\').parent().next(1).slideDown();
								$("#pfx").val(\''.Tools::getValue('pfx').'\');
								$("#ratio").val(ratioValue[\''.Tools::getValue('pfx').'\']);
								monRatio = ratioValue[\''.Tools::getValue('pfx').'\'];
								$(\'#cropbox\').Jcrop({
									\'aspectRatio\' : monRatio,
									\'onSelect\' : updateCoords
								});
								nomImage = \''.$this->l('Resize').' '.Tools::getValue('pfx').'\';
								'.($this->IsPSVersion(">=","1.6")?'$("#resizeText").html(nomImage);':'$("#resizeBouton").val(nomImage);').'
								'."\n";
							$htmlLibre .= '
							$(\'input[name$="imageChoix"]\').change(function () {
								$("div.togglePreview").slideUp();
								$(this).parent().next().slideDown();
								$("#pfx").val($(this).val());
								$("#ratio").val(ratioValue[$(this).val()]);
								monRatio = ratioValue[$(this).val()];
								$(\'#cropbox\').Jcrop({
									\'aspectRatio\' : monRatio,
									\'onSelect\' : updateCoords
								});
								nomImage = \''.$this->l('Resize').' \'+$("#pfx").val();
								'.($this->IsPSVersion(">=","1.6")?'$("#resizeText").html(nomImage);':'$("#resizeBouton").val(nomImage);').'
							});
						});
						
						function updateCoords(c)
						{
							$(\'#x\').val(c.x);
							$(\'#y\').val(c.y);
							$(\'#w\').val(c.w);
							$(\'#h\').val(c.h);
						};
						function checkCoords()
						{
							if (!$(\'input[name="imageChoix"]:checked\').val()) {
								alert(\''.$this->l('Please select a picture to crop.').'\');
								return false;
							}
							else {
								if (parseInt($(\'#w\').val())) 
									return true;
								alert(\''.$this->l('Please select a crop region then press submit.').'\');
								return false;
							}
						};
					</script>';
					if($this->IsPSVersion(">=","1.6")) {
						$htmlLibre .= '
							<div id="image" class="col-md-7">
								<div class="panel">
									<img id="cropbox" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg?'.md5(time()).'" />
									<p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.Tools::getValue('idN').'.jpg') / 1000).'kb</p>
									<p>
										<a href="'.$this->PathModuleConf.'&deleteImageBlog&idN='.Tools::getValue('idN').'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
											<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'
										</a>
									</p>
									<p>'.$this->_displayFormFileNoLabel('homepage_logo', "col-lg-10", $this->l('Format:').' .jpg .png').'</p>
								</div>
							</div>
							<div class="col-md-5">'."\n";
							foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
								$widthForce = '';
								if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg')) {
									$attribImage = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg');
									if ((int)$attribImage[0] > 200) // si l'image est sup à 200 elle loge pas dans le système crop, donc il faut la passer à 200 max
										$widthForce = 'width="200"';
								}
								
								$labelPic = $KeyThemeArray;
								Switch($KeyThemeArray) {
									case "thumb" :
										$labelPic = $this->l('thumb for articles list');
										break;
									case "slide" :
										$labelPic = $this->l('slide picture (home / blog page)');
										break;
								}
								$htmlLibre .= '
									<div class="panel">
										<p><input type="radio" name="imageChoix" value="'.$KeyThemeArray.'" />&nbsp;'.$labelPic.' <span style="font-size: 80%;">('.($widthForce ? $this->l('Real size : ') : '').$ValueThemeArray["width"].' * '.$ValueThemeArray["height"].')</span></p>
										<div class="togglePreview" style="text-align:center;">
											<img style="border:1px solid #4D4D4D;padding:0px;" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg?'.md5(time()).'" '.$widthForce.' />
										</div>
									</div>'."\n";
							}
							$htmlLibre .= '
									<div class="panel">
										<a class="btn btn-default" onclick="if (checkCoords()) {formCrop.submit();}"  >
											<i class="icon-crop"></i>&nbsp;<span id="resizeText">'.$this->l('Resize').'</span>
										</a>
									</div>
							</div>'."\n";	
					}
					else {
						$htmlLibre .= '
							<div id="image" style="width:400px;float:left;margin-right:5px;">
								<img style="" id="cropbox" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/admincrop_'.Tools::getValue('idN').'.jpg?'.md5(time()).'" />
								<p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.Tools::getValue('idN').'.jpg') / 1000).'kb</p>
								<p>
									<a href="'.$this->PathModuleConf.'&deleteImageBlog&idN='.Tools::getValue('idN').'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
										<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'
									</a>
								</p>
								<p>'.$this->_displayFormFileNoLabel('homepage_logo', "col-lg-10", $this->l('Format:').' .jpg .png').'</p>
							</div>
							<div>'."\n";
							
							foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
								$widthForce = '';
								if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg')) {
									$attribImage = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg');
									if ((int)$attribImage[0] > 200) // si l'image est sup à 200 elle loge pas dans le système crop, donc il faut la passer à 200 max
										$widthForce = 'width="200"';
								}
								
								$labelPic = $KeyThemeArray;
								Switch($KeyThemeArray) {
									case "thumb" :
										$labelPic = $this->l('thumb for articles list');
										break;
									case "slide" :
										$labelPic = $this->l('slide picture (home / blog page)');
										break;
								}
								$htmlLibre .= '
									<div style="float:left;width:250px;border:1px solid #ccc;background-color:#fff;padding:5px;margin-bottom:10px;">
										<p><input type="radio" name="imageChoix" value="'.$KeyThemeArray.'" />&nbsp;'.$labelPic.' <span style="font-size: 80%;">('.($widthForce ? $this->l('Real size : ') : '').$ValueThemeArray["width"].' * '.$ValueThemeArray["height"].')</span></p>
										<div class="togglePreview" style="text-align:center;">
											<img style="border:1px solid #4D4D4D;padding:0px;" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/'.$KeyThemeArray.'_'.Tools::getValue('idN').'.jpg?'.md5(time()).'" '.$widthForce.' />
										</div>
									</div>'."\n";
							}
							$htmlLibre .= '
									<div style="text-align:center;float:left;width:250px;border:1px solid #ccc;background-color:#fff;padding:5px;margin-bottom:10px;">
										<input type="button" value="'.$this->l('Resize').'" id="resizeBouton" class="button" onclick="if (checkCoords()) {formCrop.submit();}" />
									</div>
								</div>'."\n";
					}					
			}
			else {
				$htmlLibre .= $this->_displayFormFileNoLabel('homepage_logo', "col-lg-5", $this->l('Format:').' .jpg .png');
			}

			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Main picture'), $htmlLibre, "col-lg-10");
			// FIN IMAGE
			/***********************************************************/


			/***********************************************************/
			// DEBUT INTRO
			$htmlLibre = '';					
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="cpara1_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<textarea '.(self::IsPSVersion('<','1.6')?'style="width:500px;height:100px;"':'').' maxlength="'.(int)Configuration::get('prestablog_news_intro_length').'" id="paragraph_'.$language['id_lang'].'" name="paragraph_'.$language['id_lang'].'">'.(isset($News->paragraph[$language['id_lang']]) ? $News->paragraph[$language['id_lang']] : '').'</textarea>
						<p>'.$this->l('Caracters remaining').' : <span id="compteur-texte-'.$language['id_lang'].'" style="color:red;">'.Tools::strlen($News->paragraph[$language['id_lang']]).' / '.(int)Configuration::get('prestablog_news_intro_length').'</span>
						<br/>'.$this->l('You can configure the max length in the general configuration of the module theme.').'</p>
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Introduction'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("cpara1", $divLangName));
			// FIN INTRO
			/***********************************************************/
			
			/***********************************************************/
			// DEBUT CONTENU
			$htmlLibre = '';					
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="cpara2_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<textarea class="rte '.(self::IsPSVersion('>=','1.6')?'autoload_rte rte':'').'" id="content_'.$language['id_lang'].'" name="content_'.$language['id_lang'].'">'.(isset($News->content[$language['id_lang']]) ? $News->content[$language['id_lang']] : '').'</textarea>
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Content'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("cpara2", $divLangName));
			// FIN CONTENU
			/***********************************************************/

			/***********************************************************/
			// DEBUT CATEGORIES
			$htmlLibre = '';
			$htmlLibre .= '
			<div class="panel">
				<table cellspacing="0" cellpadding="0" class="table" '.(self::IsPSVersion('<','1.6')?'style="width:60%"':'').'>
					<thead>
						<tr>
							<th style="width:20px;"><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categories[]\', this.checked)" /></th>
							<th style="width:20px;">'.$this->l('ID').'</th>
							<th style="width:60px;">'.$this->l('Image').'</th>
							<th>'.$this->l('Name').'&nbsp;<img id="imgCatLang" src="../img/l/'.$defaultLanguage.'.jpg" style="vertical-align:middle;" /></th>
						</tr>
					</thead>';

			$ListeCat = CategoriesClass::getListe((int)($this->context->language->id), 0);
			$ListeCatNoArbre = CategoriesClass::getListeNoArbo();
			$ListeCatBranchesActives = array();

			foreach (CorrespondancesCategoriesClass::getCategoriesListe((int)$News->id) as $key => $value) {
				$ListeCatBranchesActives = array_unique(array_merge($ListeCatBranchesActives , preg_split('/\./', CategoriesClass::getBranche((int)$value))));
			}
			$htmlLibre .= $this->_displayListeArborescenceCategoriesNews($ListeCat, $Decalage = 0, $ListeCatBranchesActives);
			
			$htmlLibre .= '
				</table>
			</div>
			<script language="javascript" type="text/javascript">
				$(document).ready(function() {';
					foreach ($ListeCatBranchesActives as $key => $value) {
						$htmlLibre .= '$("tr#prestablog_categorie_'.$value.'").show();';
					}
					foreach ($ListeCatNoArbre as $key => $value) {
						if(in_array((int)$value["parent"], $ListeCatBranchesActives)) {
							$htmlLibre .= '$("tr#prestablog_categorie_'.$value["id_prestablog_categorie"].'").show();';
						}
					}

			$htmlLibre .= '
					$("img.expand-cat").click(function() {
						BranchClick=$(this).attr("rel"); // version de la branch parent
						BranchClickSplit = BranchClick.split(\'.\');
						fixBranchClickSplit = "0,"+BranchClickSplit.toString();
						
						switch ( $(this).attr("src") ) {
							case "/modules/prestablog/img/expand.gif":
								$("tr.prestablog_branch").each(function() {
									BranchParent = $(this).attr("rel");
									BranchParentSplit = BranchParent.split(\'.\');
									fixBranchParentSplit = "0,"+BranchParentSplit.toString();

									if(		$.isSubstring( fixBranchParentSplit , fixBranchClickSplit )
											&& BranchClick != BranchParent
											&& BranchClickSplit.length+1 == BranchParentSplit.length
										) {
											$(this).show();
									}
								});
								$(this).attr("src", "/modules/prestablog/img/collapse.gif");
								break;
							
							case "/modules/prestablog/img/collapse.gif":
								$("tr.prestablog_branch").each(function() {
									BranchParent = $(this).attr("rel");
									BranchParentSplit = BranchParent.split(\'.\');
									fixBranchParentSplit = "0,"+BranchParentSplit.toString();
									
									if( 		$.isSubstring( fixBranchParentSplit , fixBranchClickSplit )
											&&	BranchClick != BranchParent
										) {
											$(this).hide();
											$(this).find("img.expand-cat").each(function() {
												$(this).attr("src", "/modules/prestablog/img/expand.gif");
											});
									}
								});
								$(this).attr("src", "/modules/prestablog/img/expand.gif");
								break;
						}
					});
				});
				jQuery.isSubstring = function(haystack, needle) {
				    return haystack.indexOf(needle) !== -1;
				};
			</script>';
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Categories'), $htmlLibre, "col-lg-5");
			// FIN CATEGORIES
			/***********************************************************/
			
			/***********************************************************/
			// DEBUT PRODUITS LIES
			$htmlLibre = '';
			$htmlLibre .= '
				<div id="currentProductLink" style="display:none;">'."\n";
				
				if(Tools::getValue('idN')) {
					$ProductsLink = NewsClass::getProductLinkListe((int)Tools::getValue('idN'));
					
					if(sizeof($ProductsLink))
						foreach($ProductsLink As $ProductLink) {
							$htmlLibre .= '<input type="text" name="productsLink[]" value="'.(int)$ProductLink.'" class="linked_'.(int)$ProductLink.'" />'."\n";
						}
				}
				if(		Tools::getValue('productsLink') 
					&&	!Tools::getValue('idN')
					) {
					foreach(Tools::getValue('productsLink') As $ProductLink) {
						$htmlLibre .= '<input type="text" name="productsLink[]" value="'.(int)$ProductLink["id_product"].'" class="linked_'.(int)$ProductLink["id_product"].'" />'."\n";
					}
				}
				
			$htmlLibre .= '</div>';

			if($this->IsPSVersion(">=","1.6")) {
				$htmlLibre .= '<div class="panel col-sm-4">';
			}
			else {
				$htmlLibre .= '
					<table cellspacing="0" cellpadding="0" id="productLinkTable">
						<tr>
							<td style="padding-right:3px;width:50%;vertical-align:top;">';					
			}
			$htmlLibre .= '
							<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
								<thead>
									<tr>
										<th class="center" style="width:30px;">'.$this->l('ID').'</th>
										<th class="center" style="width:50px;">'.$this->l('Image').'</th>
										<th class="center">'.$this->l('Name').'</th>
										<th class="center" style="width:40px;">'.$this->l('Delink').'</th>
									</tr>
								</thead>
								<tbody id="productLinked">
									<tr>
										<td colspan="4" class="center">'.$this->l('No product linked').'</td>
									</tr>
								</tbody>
							</table>';
			if($this->IsPSVersion(">=","1.6")) {
				$htmlLibre .= '</div>';
				$htmlLibre .= '<div class="col-sm-1"></div>';
				$htmlLibre .= '<div class="panel col-sm-5">';
			}
			else {
				$htmlLibre .= '		</td>
						<td style="padding-left:3px;width:50%;vertical-align:top;">';
			}
			$htmlLibre .= '	
							<p class="center">'.$this->l('Search').' : <input type="text" size="20" id="productLinkSearch" name="productLinkSearch" /></p>
							<table cellspacing="0" cellpadding="0" class="table" style="width:100%">
								<thead>
									<tr>
										<th class="center" style="width:40px;">'.$this->l('Link').'</th>
										<th class="center" style="width:30px;">'.$this->l('ID').'</th>
										<th class="center" style="width:50px;">'.$this->l('Image').'</th>
										<th class="center">'.$this->l('Name').'</th>
									</tr>
								</thead>
								<tbody id="productLinkResult">
									<tr>
										<td colspan="4" class="center">'.$this->l('You must search before').' ('.(int)Configuration::get($this->name.'_nb_car_min_linkprod').' '.$this->l('caract. minimum').')</td>
									</tr>
								</tbody>
							</table>';

			if($this->IsPSVersion(">=","1.6")) {
				$htmlLibre .= '</div>';
			}
			else {
				$htmlLibre .= '	
							</td>
						</tr>
					</table>';
			}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Products links'), $htmlLibre, "col-lg-10");
			// FIN PRODUITS LIES
			/***********************************************************/

			$this->_html .= $this->_displayFormDate("col-lg-2", $this->l('Date'), 'date', $News->date, true);

			$this->_html .= '<div class="margin-form">';
			
			if($this->IsPSVersion(">=","1.6")) {
				if(Tools::getValue('idN'))
					$this->_html .= '<button class="btn btn-primary" id="submitForm" name="submitUpdateNews" name="submit" type="submit"><i class="icon-save"></i>&nbsp;'.$this->l('Update the content news').'</button>';		
				else
					$this->_html .= '<button class="btn btn-primary" id="submitForm" name="submitAddNews" name="submit" type="submit"><i class="icon-plus"></i>&nbsp;'.$this->l('Add content').'</button>';
			}
			else {
				if(Tools::getValue('idN'))
					$this->_html .= '<input type="submit" id="submitForm" name="submitUpdateNews" value="'.$this->l('Update the content news').'" class="button" />';
				else
					$this->_html .= '<input type="submit" id="submitForm" name="submitAddNews" value="'.$this->l('Add content').'" class="button" />';
			}
			$this->_html .= '</div>';

		$this->_html .= $this->_displayFormClose();
		
		$this->_html .= '
			<form name="formCrop" id="formCrop" action="'.$this->PathModuleConf.'" method="post" onsubmit="return checkCoords();">
				<input type="hidden" name="idN" value="'.Tools::getValue('idN').'" />
				<input type="hidden" id="pfx" name="pfx" value="'.Tools::getValue('pfx').'" />
				<input type="hidden" id="x" name="x" />
				<input type="hidden" id="y" name="y" />
				<input type="hidden" id="w" name="w" />
				<input type="hidden" id="h" name="h" />
				<input type="hidden" id="ratio" name="ratio" />
				<input type="hidden" name="submitCrop" value="submitCrop" />
			</form>';
	}
	
	private function _displayFormCategories()
	{
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		
		$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		$iso = Language::getIsoById((int)($this->context->language->id));
		$divLangName = 'title¤link_rewrite¤meta_title¤meta_description¤meta_keywords¤cpara1';
		
		$legend_title = $this->l('Add a category');
		if(Tools::getValue('idC')) {
			$Categories = new CategoriesClass((int)(Tools::getValue('idC')));
			$legend_title = $this->l('Update the category').' #'.$Categories->id;
		}
		else {
			$Categories = new CategoriesClass();
		}

		if($_POST){
			$Categories->id_shop = (int)$this->context->shop->id;
			$Categories->copyFromPost();
		}

		// TinyMCE
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
			<script type="text/javascript">
			'.(Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL') ? 'var PS_ALLOW_ACCENTED_CHARS_URL = 1;' : 'var PS_ALLOW_ACCENTED_CHARS_URL = 0;').'
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			'.(self::IsPSVersion('>=','1.6')?'
			<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.1.6.js"></script>
			<script type="text/javascript">
				$(function() {
					tinySetup({ editor_selector :"autoload_rte" });
				});
			</script>
				':'
			<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.js"></script>
				').'
			<script type="text/javascript">
				id_language = Number('.$defaultLanguage.');
				
				function copy2friendlyURLPrestaBlog() {
					if(!$(\'#slink_rewrite_\'+id_language).attr(\'disabled\')) {
						$(\'#slink_rewrite_\'+id_language).val(str2url($(\'input#title_\'+id_language).val().replace(/^[0-9]+\./, \'\'), \'UTF-8\')); 
					}
				}
				function updateFriendlyURLPrestaBlog() { 
					$(\'#slink_rewrite_\'+id_language).val(str2url($(\'#slink_rewrite_\'+id_language).val().replace(/^[0-9]+\./, \'\'), \'UTF-8\')); 
				}
				
				$(function() {
					$("#submitForm").click(function() {';
					foreach ($languages as $language) {
						$this->_html .= '$(\'#slink_rewrite_'.$language['id_lang'].'\').removeAttr("disabled");';
					}
						$this->_html .= '
					});
					
					$("#control").toggle( 
						function () { 
							$(\'#slink_rewrite_\'+id_language).removeAttr("disabled");
							$(\'#slink_rewrite_\'+id_language).css("background-color", "#fff");
							$(\'#slink_rewrite_\'+id_language).css("color", "#000");
							$(this).html("'.$this->l('Disable this rewrite').'");
						},
						function () { 
							$(\'#slink_rewrite_\'+id_language).attr("disabled", true);
							$(\'#slink_rewrite_\'+id_language).css("background-color", "#e0e0e0");
							$(\'#slink_rewrite_\'+id_language).css("color", "#7F7F7F");
							$(this).html("'.$this->l('Enable this rewrite').'");
						} 
					);
					';
					
				foreach ($languages as $language) {
					$this->_html .= '
					if ($("#slink_rewrite_'.$language['id_lang'].'").val() == \'\') { 
						$("#slink_rewrite_'.$language['id_lang'].'").removeAttr("disabled");
						$("#slink_rewrite_'.$language['id_lang'].'").css("background-color", "#fff");
						$("#slink_rewrite_'.$language['id_lang'].'").css("color", "#000");
						$("#control").html("'.$this->l('Disable this rewrite').'");
					}';
				}
		
		$this->_html .= '
				});
			</script>'."\n";

		$this->_html .= $this->_displayFormOpen("icon-edit", $legend_title, $this->PathModuleConf);
			if(Tools::getValue('idC'))
				$this->_html .= '<input type="hidden" name="idC" value="'.Tools::getValue('idC').'" />';
			/***********************************************************/
			$this->_html .= $this->_displayFormEnableItem("col-lg-2", $this->l('Activate'), 'actif', $Categories->actif);
			/***********************************************************/
			$htmlLibre = '';
								foreach ($languages as $language) {
									$htmlLibre .= '<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
										<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="title_'.$language['id_lang'].'" id="title_'.$language['id_lang'].'" maxlength="'.(int)Configuration::get('prestablog_news_title_length').'" value="'.(isset($Categories->title[$language['id_lang']]) ? $Categories->title[$language['id_lang']] : '').'" onkeyup="if (isArrowKey(event)) return; copy2friendlyURLPrestaBlog();" onchange="copy2friendlyURLPrestaBlog();" />
									</div>';
								}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Title'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("title", $divLangName));
			/***********************************************************/
			$htmlLibre = $Categories->_displaySelectArboCategories(CategoriesClass::getListe((int)($this->context->language->id), 0), (int)$Categories->parent, 0, $this->l("Top level"),"parent");
								
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Parent category'), $htmlLibre, "col-lg-7");
			/***********************************************************/


			/***********************************************************/
			// DEBUT SEO
			$htmlLibre = '<span onclick="$(\'#seo\').slideToggle();" style="cursor: pointer" class="link">
						<img src="../img/admin/cog.gif" alt="'.$this->l('SEO').'" title="'.$this->l('SEO').'" />'.$this->l('Click here to improve SEO').'
					</span>';

			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('SEO'), $htmlLibre, "col-lg-7");

			$this->_html .= '<div id="seo" style="display: none;">';
			/***********************************************************/
			$htmlLibre = '';
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="link_rewrite_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="link_rewrite_'.$language['id_lang'].'" id="slink_rewrite_'.$language['id_lang'].'" value="'.(isset($Categories->link_rewrite[$language['id_lang']]) ? $Categories->link_rewrite[$language['id_lang']] : '').'" 
						onkeyup="if (isArrowKey(event)) return ;updateFriendlyURLPrestaBlog();" onchange="updateFriendlyURLPrestaBlog();" 
						'.(isset($Categories->id) ? ' style="color:#7F7F7F;background-color:#e0e0e0;" disabled="true"' :'').'
						/><sup> *</sup>
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", 
																	$this->l('Url Rewrite').'<br/><a href="#" id="control" />'.(isset($Categories->id) ? $this->l('Enable this rewrite') : $this->l('Disable this rewrite')).'</a>', 
																	$htmlLibre, "col-lg-7", $this->displayFlagsFor("link_rewrite", $divLangName));
			/***********************************************************/
			$htmlLibre = '';					
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_title_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" value="'.(isset($Categories->meta_title[$language['id_lang']]) ? $Categories->meta_title[$language['id_lang']] : '').'" />
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Title'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_title", $divLangName));
			/***********************************************************/
			$htmlLibre = '';				
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_description_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_description_'.$language['id_lang'].'" id="meta_description_'.$language['id_lang'].'" value="'.(isset($Categories->meta_description[$language['id_lang']]) ? $Categories->meta_description[$language['id_lang']] : '').'" />
					</div>';
				}			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Description'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_description", $divLangName));
			/***********************************************************/
			$htmlLibre = '';
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="meta_keywords_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="meta_keywords_'.$language['id_lang'].'" id="meta_keywords_'.$language['id_lang'].'" value="'.(isset($Categories->meta_keywords[$language['id_lang']]) ? $Categories->meta_keywords[$language['id_lang']] : '').'" />
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Meta Keywords'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("meta_keywords", $divLangName));
			/***********************************************************/
			$this->_html .= '</div>';
			// FIN SEO
			/***********************************************************/

			/***********************************************************/
			// DEBUT IMAGE
			$htmlLibre = '';					
				if($this->demoMode)
					$htmlLibre .= $this->displayWarning($this->l('Feature disabled on the demo mode'));
				if(		Tools::getValue('idC') 
					&&	file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg')) {
					$htmlLibre .= '<span id="labelPicture"></span>';
					$ConfigThemeArray = objectToArray($ConfigTheme);
					if(Tools::getValue('pfx')) {
						$htmlLibre .= '<script type="text/javascript">$(document).ready(function() { $("html, body").animate({scrollTop: $("#labelPicture").offset().top}, 750); });</script>'."\n";
					}
					$htmlLibre .= '
					<script src="'.$this->_path.'js/Jcrop/jquery.Jcrop.prestablog.js"></script>
					<link rel="stylesheet" href="'.$this->_path.'js/Jcrop/css/jquery.Jcrop.css" type="text/css" />
					<script language="Javascript">'."\n";
					
					$htmlLibre .= '							var ratioValue = new Array();'."\n";
					foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
						$htmlLibre .= '							ratioValue[\''.$KeyThemeArray.'\'] = '.$ValueThemeArray["width"]/$ValueThemeArray["height"].';'."\n";
					}
					
					$htmlLibre .= '
						var monRatio;
						var monImage;
						
						$(function(){
							$("div.togglePreview").hide();'."\n";
							if(Tools::getValue('pfx'))
								$htmlLibre .= '
								$(\'input[name$="imageChoix"]\').filter(\'[value="'.Tools::getValue('pfx').'"]\').attr(\'checked\', true);
								$(\'input[name$="imageChoix"]\').filter(\'[value="'.Tools::getValue('pfx').'"]\').parent().next(1).slideDown();
								$("#pfx").val(\''.Tools::getValue('pfx').'\');
								$("#ratio").val(ratioValue[\''.Tools::getValue('pfx').'\']);
								monRatio = ratioValue[\''.Tools::getValue('pfx').'\'];
								$(\'#cropbox\').Jcrop({
									\'aspectRatio\' : monRatio,
									\'onSelect\' : updateCoords
								});
								nomImage = \''.$this->l('Resize').' '.Tools::getValue('pfx').'\';
								'.($this->IsPSVersion(">=","1.6")?'$("#resizeText").html(nomImage);':'$("#resizeBouton").val(nomImage);').'
								'."\n";
							$htmlLibre .= '
							$(\'input[name$="imageChoix"]\').change(function () {
								$("div.togglePreview").slideUp();
								$(this).parent().next().slideDown();
								$("#pfx").val($(this).val());
								$("#ratio").val(ratioValue[$(this).val()]);
								monRatio = ratioValue[$(this).val()];
								$(\'#cropbox\').Jcrop({
									\'aspectRatio\' : monRatio,
									\'onSelect\' : updateCoords
								});
								nomImage = \''.$this->l('Resize').' \'+$("#pfx").val();
								'.($this->IsPSVersion(">=","1.6")?'$("#resizeText").html(nomImage);':'$("#resizeBouton").val(nomImage);').'
							});
						});
						
						function updateCoords(c)
						{
							$(\'#x\').val(c.x);
							$(\'#y\').val(c.y);
							$(\'#w\').val(c.w);
							$(\'#h\').val(c.h);
						};
						function checkCoords()
						{
							if (!$(\'input[name="imageChoix"]:checked\').val()) {
								alert(\''.$this->l('Please select a picture to crop.').'\');
								return false;
							}
							else {
								if (parseInt($(\'#w\').val())) 
									return true;
								alert(\''.$this->l('Please select a crop region then press submit.').'\');
								return false;
							}
						};
					</script>';
					if($this->IsPSVersion(">=","1.6")) {
						$htmlLibre .= '							
							<div id="image" class="col-md-7">
								<div class="panel">
									<img id="cropbox" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg?'.md5(time()).'" />
									<p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.Tools::getValue('idC').'.jpg') / 1000).'kb</p>
									<p>
										<a href="'.$this->PathModuleConf.'&deleteImageBlog&idC='.Tools::getValue('idC').'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
											<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'
										</a>
									</p>
									<p>'.$this->_displayFormFileNoLabel('imageCategory', "col-lg-10", $this->l('Format:').' .jpg .png').'</p>
								</div>
							</div>
							<div class="col-md-5">'."\n";
							foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
								$widthForce = '';
								if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg')) {
									$attribImage = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg');
									if ((int)$attribImage[0] > 200) // si l'image est sup à 200 elle loge pas dans le système crop, donc il faut la passer à 200 max
										$widthForce = 'width="200"';
								}
								
								$labelPic = $KeyThemeArray;
								Switch($KeyThemeArray) {
									case "thumb" :
										$labelPic = $this->l('thumb for category list');
										break;
									case "full" :
										$labelPic = $this->l('full picture for description category list');
										break;
								}
								$htmlLibre .= '
									<div class="panel">
										<p><input type="radio" name="imageChoix" value="'.$KeyThemeArray.'" />&nbsp;'.$labelPic.' <span style="font-size: 80%;">('.($widthForce ? $this->l('Real size : ') : '').$ValueThemeArray["width"].' * '.$ValueThemeArray["height"].')</span></p>
										<div class="togglePreview" style="text-align:center;">
											<img style="border:1px solid #4D4D4D;padding:0px;" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg?'.md5(time()).'" '.$widthForce.' />
										</div>
									</div>'."\n";
							}
							$htmlLibre .= '
									<div class="panel">
										<a class="btn btn-default" onclick="if (checkCoords()) {formCrop.submit();}"  >
											<i class="icon-crop"></i>&nbsp;<span id="resizeText">'.$this->l('Resize').'</span>
										</a>
									</div>
							</div>'."\n";
					}
					else {
						$htmlLibre .= '							
							<div id="image" style="width:400px;float:left;margin-right:5px;">
								<img style="" id="cropbox" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/admincrop_'.Tools::getValue('idC').'.jpg?'.md5(time()).'" />
								<p align="center">'.$this->l('Filesize').' '.(filesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.Tools::getValue('idC').'.jpg') / 1000).'kb</p>
								<p>
									<a href="'.$this->PathModuleConf.'&deleteImageBlog&idC='.Tools::getValue('idC').'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
										<img src="../img/admin/delete.gif" alt="'.$this->l('Delete').'" /> '.$this->l('Delete').'
									</a>
								</p>
								<p>'.$this->_displayFormFileNoLabel('imageCategory', "col-lg-10", $this->l('Format:').' .jpg .png').'</p>
							</div>
							<div>'."\n";
						
							foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
								$widthForce = '';
								if(file_exists(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg')) {
									$attribImage = getimagesize(dirname(__FILE__).'/themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg');
									if ((int)$attribImage[0] > 200) // si l'image est sup à 200 elle loge pas dans le système crop, donc il faut la passer à 200 max
										$widthForce = 'width="200"';
								}
								
								$labelPic = $KeyThemeArray;
								Switch($KeyThemeArray) {
									case "thumb" :
										$labelPic = $this->l('thumb for category list');
										break;
									case "full" :
										$labelPic = $this->l('full picture for description category list');
										break;
								}
								$htmlLibre .= '
									<div style="float:left;width:250px;border:1px solid #ccc;background-color:#fff;padding:5px;margin-bottom:10px;">
										<p><input type="radio" name="imageChoix" value="'.$KeyThemeArray.'" />&nbsp;'.$labelPic.' <span style="font-size: 80%;">('.($widthForce ? $this->l('Real size : ') : '').$ValueThemeArray["width"].' * '.$ValueThemeArray["height"].')</span></p>
										<div class="togglePreview" style="text-align:center;">
											<img style="border:1px solid #4D4D4D;padding:0px;" src="'.$this->_path.'themes/'.Configuration::get($this->name.'_theme').'/up-img/c/'.$KeyThemeArray.'_'.Tools::getValue('idC').'.jpg?'.md5(time()).'" '.$widthForce.' />
										</div>
									</div>'."\n";
							}
							$htmlLibre .= '
									<div style="text-align:center;float:left;width:250px;border:1px solid #ccc;background-color:#fff;padding:5px;margin-bottom:10px;">
										<input type="button" value="'.$this->l('Resize').'" id="resizeBouton" class="button" onclick="if (checkCoords()) {formCrop.submit();}" />
									</div>
								</div>'."\n";
					}			
			}
			else {
				$htmlLibre .= $this->_displayFormFileNoLabel('imageCategory', "col-lg-5", $this->l('Format:').' .jpg .png');
			}

			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Picture'), $htmlLibre, "col-lg-10");
			// FIN IMAGE
			/***********************************************************/

			/***********************************************************/
			// DEBUT description
			$htmlLibre = '';					
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="cpara1_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<textarea class="rte '.(self::IsPSVersion('>=','1.6')?'autoload_rte rte':'').'" id="description_'.$language['id_lang'].'" name="description_'.$language['id_lang'].'">'.(isset($Categories->description[$language['id_lang']]) ? $Categories->description[$language['id_lang']] : '').'</textarea>
					</div>';
				}
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Description'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("cpara1", $divLangName));
			// FIN description
			/***********************************************************/
			
			$this->_html .= '		
			<div class="margin-form">';

			if($this->IsPSVersion(">=","1.6")) {
				if(Tools::getValue('idC'))
					$this->_html .= '<button class="btn btn-primary" name="submitUpdateCat" name="submit" type="submit"><i class="icon-save"></i>&nbsp;'.$this->l('Update the category').'</button>';		
				else
					$this->_html .= '<button class="btn btn-primary" name="submitAddCat" name="submit" type="submit"><i class="icon-plus"></i>&nbsp;'.$this->l('Add the category').'</button>';
			}
			else {
				if(Tools::getValue('idC'))
					$this->_html .= '<input type="submit" name="submitUpdateCat" value="'.$this->l('Update the category').'" class="button" />';
				else
					$this->_html .= '<input type="submit" name="submitAddCat" value="'.$this->l('Add the category').'" class="button" />';
			}				
			$this->_html .= '</div>';

		$this->_html .= $this->_displayFormClose();

		$this->_html .= '
			<form name="formCrop" id="formCrop" action="'.$this->PathModuleConf.'" method="post" onsubmit="return checkCoords();">
				<input type="hidden" name="idC" value="'.Tools::getValue('idC').'" />
				<input type="hidden" id="pfx" name="pfx" value="'.Tools::getValue('pfx').'" />
				<input type="hidden" id="x" name="x" />
				<input type="hidden" id="y" name="y" />
				<input type="hidden" id="w" name="w" />
				<input type="hidden" id="h" name="h" />
				<input type="hidden" id="ratio" name="ratio" />
				<input type="hidden" name="submitCrop" value="submitCrop" />
			</form>';
	}
	
	private function _displayFormAntiSpam()
	{
		$defaultLanguage = $this->LangueDefaultStore;
		$languages = Language::getLanguages(true);
		$iso = Language::getIsoById((int)($this->context->language->id));
		$divLangName = 'question¤reply';
		
		$legend_title = $this->l('Add an AntiSpam question');
		if(Tools::getValue('idAS')) {
			$AntiSpam = new AntiSpamClass((int)(Tools::getValue('idAS')));
			$legend_title = $this->l('Update the AntiSpam question');
		}
		else {
			$AntiSpam = new AntiSpamClass();
		}

		if($_POST){
			$AntiSpam->id_shop = (int)$this->context->shop->id;
			$AntiSpam->copyFromPost();
		}

		$this->_html .= '<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>';

		$this->_html .= $this->_displayFormOpen("icon-edit", $legend_title, $this->PathModuleConf);
			if(Tools::getValue('idAS'))
				$this->_html .= '<input type="hidden" name="idAS" value="'.Tools::getValue('idAS').'" />';
			/***********************************************************/
			$htmlLibre = '';				
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="question_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="question_'.$language['id_lang'].'" id="question_'.$language['id_lang'].'" value="'.(isset($AntiSpam->question[$language['id_lang']]) ? $AntiSpam->question[$language['id_lang']] : '').'" />
					</div>';
				}			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Question'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("question", $divLangName));
			/***********************************************************/
			$htmlLibre = '';				
				foreach ($languages as $language) {
					$htmlLibre .= '<div id="reply_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';">
						<input '.(self::IsPSVersion('<','1.6')?'class="fixInput15"':'').' type="text" name="reply_'.$language['id_lang'].'" id="question_'.$language['id_lang'].'" value="'.(isset($AntiSpam->reply[$language['id_lang']]) ? $AntiSpam->reply[$language['id_lang']] : '').'" />
					</div>';
				}			
			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Expected reply'), $htmlLibre, "col-lg-7", $this->displayFlagsFor("reply", $divLangName));
			/***********************************************************/
			$this->_html .= $this->_displayFormEnableItem("col-lg-2", $this->l('Activate'), 'actif', $AntiSpam->actif);
			/***********************************************************/					
			
			$this->_html .= '
			<div class="margin-form">';

			if($this->IsPSVersion(">=","1.6")) {
				if(Tools::getValue('idAS'))
					$this->_html .= '<button class="btn btn-primary" name="submitUpdateAntiSpam" name="submit" type="submit"><i class="icon-save"></i>&nbsp;'.$this->l('Update the AntiSpam question').'</button>';		
				else
					$this->_html .= '<button class="btn btn-primary" name="submitAddAntiSpam" name="submit" type="submit"><i class="icon-plus"></i>&nbsp;'.$this->l('Add the AntiSpam question').'</button>';
			}
			else {
				if(Tools::getValue('idAS'))
					$this->_html .= '<input type="submit" name="submitUpdateAntiSpam" value="'.$this->l('Update the AntiSpam question').'" class="button" />';
				else
					$this->_html .= '<input type="submit" name="submitAddAntiSpam" value="'.$this->l('Add the AntiSpam question').'" class="button" />';
			}				
						
			$this->_html .= '</div>';
		$this->_html .= $this->_displayFormClose();
	}

	private function _displayFormComments()
	{
		$legend_title = $this->l('Add a comment');
		if(Tools::getValue('idC')){
			$legend_title = $this->l('Update the comment');
			$Comment = new CommentNewsClass((int)(Tools::getValue('idC')));
		}
		else {
			$Comment = new CommentNewsClass();
			$Comment->copyFromPost();
		}

		$this->_html .= $this->_displayFormOpen("icon-edit", $legend_title, $this->PathModuleConf);

			if(Tools::getValue('idC'))
				$this->_html .= '<input type="hidden" name="idC" value="'.Tools::getValue('idC').'" />';

			$TitleNews = NewsClass::getTitleNews((int)($Comment->news), (int)($this->context->language->id));

			$this->_html .= $this->_displayFormLibre("col-lg-2", $this->l('Parent news'), '<a href="'.$this->PathModuleConf.'&editNews&idN='.$Comment->news.'" onclick="return confirm(\''.$this->l('You will leave this page. Are you sure ?').'\');" >'.$TitleNews.'</a>', $size_bootstrap="col-lg-5");
			$this->_html .= $this->_displayFormInput("col-lg-2", $this->l('Name'), 'name', $Comment->name, 50, "col-lg-4");
			$this->_html .= $this->_displayFormInput("col-lg-2", $this->l('Url'), 'url', $Comment->url, 80, "col-lg-6", null, null, '<i class="icon-external-link"></i>');
								
			$iso = Language::getIsoById((int)($this->context->language->id));
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
			$ad = dirname($_SERVER["PHP_SELF"]);
			$this->_html .=  '
			<script type="text/javascript">
				var iso = \''.$isoTinyMCE.'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>';
			
			if($this->IsPSVersion(">=","1.6")) {
				$this->_html .= '
				<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.1.6.js"></script>
				<script type="text/javascript">$(function() { tinySetup({ editor_selector :"autoload_rte" }); });</script>
				<div class="form-group ">
					<label class="control-label col-lg-2" for="comment">'.$this->l('Comment').'</label>
					<div class="col-lg-8">
						<textarea class="autoload_rte" id="comment" name="comment">'.$Comment->comment.'</textarea>
					</div>
				</div>';
			}
			else {
				$this->_html .= '
				<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/prestablog/js/tinymce.inc.js"></script>
				<label>'.$this->l('Comment').' </label>
				<div class="margin-form">
					<textarea class="rte" id="comment" name="comment">'.$Comment->comment.'</textarea>
				</div>
				<div class="clear pspace"></div>'."\n";
			}

			$this->_html .= $this->_displayFormDate("col-lg-2", $this->l('Date'), 'date', $Comment->date, true);		
			$this->_html .= $this->_displayFormSelect("col-lg-2", 
									$this->l('Status'), 
									'actif', 
									$Comment->actif, 
									array(
												"-1" => $this->l('Pending'), 
												"1" => $this->l('Enabled'),
												"0" => $this->l('Disabled'),
											), 
									null, 
									"col-lg-3",
									null, null, '<i class="icon-eye"></i>');
			
			$this->_html .= '<div class="margin-form">';
			if($this->IsPSVersion(">=","1.6")) {
				if(Tools::getValue('idC')) {
					$this->_html .= '<div class="col-lg-3">';
					$this->_html .= '<button class="btn btn-primary" name="submitUpdateComment" name="submit" type="submit"><i class="icon-save"></i>&nbsp;'.$this->l('Update the comment').'</button>';		
					$this->_html .= '</div>';
					$this->_html .= '<div class="col-lg-2">';
					$this->_html .= '<a class="btn btn-default" href="'.$this->PathModuleConf.'&deleteComment&idC='.$Comment->id.'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" >
								<i class="icon-trash-o"></i>&nbsp;'.$this->l('Delete the comment').'
							</a>';
					$this->_html .= '</div>';
				}
				else {
					$this->_html .= '<div class="col-lg-2">';
					$this->_html .= '<button class="btn btn-primary" name="submitAddComment" name="submit" type="submit"><i class="icon-plus"></i>&nbsp;'.$this->l('Add the comment').'</button>';
					$this->_html .= '</div>';
				}
			}
			else {
				if(Tools::getValue('idC')) {
					$this->_html .= '	<input type="submit" name="submitUpdateComment" value="'.$this->l('Update the comment').'" class="button" />';
					$this->_html .= '	<a href="'.$this->PathModuleConf.'&deleteComment&idC='.$Comment->id.'" title="'.$this->l('Delete the comment').'" class="button" style="margin-left:10px;padding:4px;" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');" />'.$this->l('Delete the comment').'</a>';
				}
				else {
					$this->_html .= '<input type="submit" name="submitAddComment" value="'.$this->l('Add the comment').'" class="button" />';
				}
			}
			$this->_html .= '</div>';		
			
		 $this->_html .= $this->_displayFormClose();
	}
	
	private function deleteAllImagesThemes($id)
	{
		foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
			$ConfigTheme = $this->_getConfigXmlTheme($ValueTheme);
			$ConfigThemeArray = objectToArray($ConfigTheme);
			foreach($ConfigThemeArray["images"] As $KeyThemeArray => $ValueThemeArray) {
				@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/'.$KeyThemeArray.'_'.$id.'.jpg');
			}
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/'.$id.'.jpg');
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/admincrop_'.$id.'.jpg');
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/adminth_'.$id.'.jpg');
		}
		
		return true;
	}
	
	private function deleteAllImagesThemesCat($id)
	{
		foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
			$ConfigTheme = $this->_getConfigXmlTheme($ValueTheme);
			$ConfigThemeArray = objectToArray($ConfigTheme);
			foreach($ConfigThemeArray["categories"] As $KeyThemeArray => $ValueThemeArray) {
				@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/c/'.$KeyThemeArray.'_'.$id.'.jpg');
			}
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/c/'.$id.'.jpg');
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/c/admincrop_'.$id.'.jpg');
			@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/c/adminth_'.$id.'.jpg');
		}
		
		return true;
	}
	
	private function deleteAllThemesConfig()
	{
		foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
			self::effacementDossier(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/up-img/');
			//@unlink(_PS_MODULE_DIR_.$this->name.'/themes/'.$ValueTheme.'/config.xml');
		}
		return true;
	}
	
	private function effacementDossier($dossier) {
		$ouverture=@opendir($dossier);
		if (!$ouverture)
			return;
		while($fichier=readdir($ouverture)) {
			if ($fichier == '.' || $fichier == '..')
				continue;
			if (is_dir($dossier."/".$fichier)) {
				$r=clearDir($dossier."/".$fichier);
				if (!$r) return false;
			}
			else {
				$r=@unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
		}
		closedir($ouverture);
		if (!$r) return false;
			return true;
	}
	
	private function UploadImage($file_image, $id, $w, $h, $folder=null)
	{
		if (isset($file_image) AND isset($file_image['tmp_name']) AND !empty($file_image['tmp_name']))
		{
				$tmpName=false;
				Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
				if ($error = ImageManager::validateUpload($file_image, $this->maxImageSize))
					return false;
				elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($file_image['tmp_name'], $tmpName))
					return false;
				else {
					foreach($this->ScanDirectory(_PS_MODULE_DIR_.$this->name.'/themes') As $KeyTheme => $ValueTheme) {
						$ConfigTheme = $this->_getConfigXmlTheme($ValueTheme);
						if (!$this->ImageResize(
										$tmpName, 
										dirname(__FILE__).'/themes/'.$ValueTheme.'/up-img/'.($folder?$folder.'/':'').$id.'.jpg', 
										$w,
										$h
									)
								)
							return false;
					}
				}
				if (isset($tmpName))
					unlink($tmpName);
		}
		
		return true;
	}
	
	private function ImageResize($fichier_avant, $fichier_apres, $dest_width, $dest_height)
	{
		//list($image_width, $image_height, $type, $attr) = getimagesize($fichier_avant);
		list($image_width, $image_height, $type) = getimagesize($fichier_avant);
		$sourceImage = ImageManager::create($type, $fichier_avant);
		
		if($image_width>$dest_width || $image_height>$dest_height) {
			$proportion = $dest_width / $image_width;
			$dest_height = $image_height * $proportion;
			$dest_width = $dest_width;
		}
		else {
			$dest_height=$image_height;
			$dest_width=$image_width;
		}
		$destImage = imagecreatetruecolor($dest_width, $dest_height);
		imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $dest_width + 1, $dest_height + 1, $image_width, $image_height);
		return (ImageManager::write('jpg', $destImage, $fichier_apres));
	}
	
	private function ScanDirectory($Directory)
	{
		$output = array();
		$MyDirectory = opendir($Directory);// or die('Erreur');
		
		while($Entry = @readdir($MyDirectory)) {
			if($Entry != '.' && $Entry != '..') {
				if(is_dir($Directory.'/'.$Entry)) {
					$output[] = $Entry;
				}
			}
		}
		closedir($MyDirectory);
		return $output;
	}
	
	private function ScanFilesDirectory($Directory, $expections=null)
	{
		$output = array();
		if(!is_dir($Directory))
			return array();
		
		$MyDirectory = opendir($Directory);// or die('Erreur');
		
		while($Entry = @readdir($MyDirectory)) {
			if($Entry != '.' && $Entry != '..') {
				if(sizeof($expections)) {
					if(
						is_file($Directory.'/'.$Entry)
						&& !in_array($Entry , $expections)
						) {
						$output[] = $Entry;
					}
				}
				elseif(is_file($Directory.'/'.$Entry)) {
					$output[] = $Entry;
				}
				
			}
		}
		closedir($MyDirectory);
		return $output;
	}
	
	static public function _getConfigXmlTheme($theme)
	{
		$configFile = _PS_MODULE_DIR_.'prestablog/themes/'.$theme.'/config.xml';
		$xml_exist = file_exists($configFile);
		
		if ($xml_exist) {
			return simplexml_load_file($configFile);
		}
		else {
			self::_generateConfigXmlTheme($theme);
			return self::_getConfigXmlTheme($theme);
		}
	}
	
	private function RetourneTexteBalise($text, $debut, $fin)
	{
		$debut = strpos( $text, $debut ) + Tools::strlen( $debut );     
		$fin = strpos( $text, $fin );
		return Tools::substr( $text, $debut, $fin - $debut );
	} 
	
	protected static function _generateConfigXmlTheme($theme)
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>
<theme>
	<!-- root js path is your theme prestablog folder -->
	<!-- modules/prestablog/themes/[theme]/js/ -->
	<js>slide.js</js>
	<images>
		<thumb> <!--Image prevue pour les miniatures dans les listes -->
			<width>'.Configuration::get('prestablog_thumb_picture_width').'</width>
			<height>'.Configuration::get('prestablog_thumb_picture_height').'</height>
		</thumb>
		<slide> <!--Image prevue pour les slides -->
			<width>'.Configuration::get('prestablog_slide_picture_width').'</width>
			<height>'.Configuration::get('prestablog_slide_picture_height').'</height>
		</slide>
	</images>
	<categories>
		<thumb> <!--Image prevue pour les miniatures des categories -->
			<width>'.Configuration::get('prestablog_thumb_cat_width').'</width>
			<height>'.Configuration::get('prestablog_thumb_cat_height').'</height>
		</thumb>
		<full> <!--Image prevue pour la description de la catégorie en liste 1ère page -->
			<width>'.Configuration::get('prestablog_full_cat_width').'</width>
			<height>'.Configuration::get('prestablog_full_cat_height').'</height>
		</full>
	</categories>
</theme>';
		if (is_writable(_PS_MODULE_DIR_.'prestablog/themes/'.$theme.'/'))
			file_put_contents(_PS_MODULE_DIR_.'prestablog/themes/'.$theme.'/config.xml', utf8_encode($xml));
	}
	
	private function _cleanMetaKeywords($keywords)
	{
		if (!empty($keywords) && $keywords != '')
		{
			$out = array();
			$words = explode(',', $keywords);
			foreach($words as $word_item)
			{
				$word_item = trim($word_item);
				if (!empty($word_item) && $word_item != '')
					$out[] = $word_item;
			}
			return ((count($out) > 0) ? implode(',', $out) : '');
		}
		else
			return '';
	}
	
	static public function prestablog_ajax_search_url($params) {
		$base = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true));
		
		$base .= __PS_BASE_URI__.'modules/prestablog/prestablog-ajax.php';
		
		return $base;
	}
	
	static public function prestablog_url($params) {
		$param=NULL;
		$ok_rewrite = '';
		$ok_rewrite_id = '';
		$ok_rewrite_do = '';
		$ok_rewrite_cat = '';
		$ok_rewrite_categorie = '';
		$ok_rewrite_page = '';
		$ok_rewrite_titre = '';
		$ok_rewrite_seo = '';
		$ok_rewrite_year = '';
		$ok_rewrite_month = '';
		
		$ko_rewrite = '';
		$ko_rewrite_id = '';
		$ko_rewrite_do = '';
		$ko_rewrite_cat = '';
		$ko_rewrite_page = '';
		$ko_rewrite_year = '';
		$ko_rewrite_month = '';
		
		if (isset($params["do"]) && $params["do"] != "") {
			$ko_rewrite_do = 'do='.$params["do"];
			$ok_rewrite_do = $params["do"];
			$param+=1;
		}
		if (isset($params["id"]) && $params["id"] != "") {
			$ko_rewrite_id = '&id='.$params["id"];
			$ok_rewrite_id = '-n'.$params["id"];
			$param+=1;
		}
		if (isset($params["c"]) && $params["c"] != "") {
			$ko_rewrite_cat = '&c='.$params["c"];
			$ok_rewrite_cat = '-c'.$params["c"];
			$param+=1;
		}
		if (isset($params["start"]) && isset($params["p"]) && $params["start"] != "" && $params["p"] != "") {
			$ko_rewrite_page = '&start='.$params["start"].'&p='.$params["p"];
			$ok_rewrite_page = $params["start"].'p'.$params["p"];
			$param+=1;
		}
		if (isset($params["titre"]) && $params["titre"] != "") {
			$ok_rewrite_titre = PrestaBlog::prestablog_filter(Tools::link_rewrite($params["titre"]));
			$param+=1;
		}
		if (isset($params["categorie"]) && $params["categorie"] != "") {
			$ok_rewrite_categorie = PrestaBlog::prestablog_filter(Tools::link_rewrite($params["categorie"])).(isset($params["start"]) && isset($params["p"]) && $params["start"] != "" && $params["p"] != "" ? '-' : '');
			$param+=1;
		}
		if (isset($params["seo"]) && $params["seo"] != "") {
			$ok_rewrite_titre = PrestaBlog::prestablog_filter(Tools::link_rewrite($params["seo"]));
			$param+=1;
		}
		if (isset($params["y"]) && $params["y"] != "") {
			$ko_rewrite_year = '&y='.$params["y"];
			$ok_rewrite_year = 'y'.$params["y"];
			$param+=1;
		}
		if (isset($params["m"]) && $params["m"] != "") {
			$ko_rewrite_month = '&m='.$params["m"];
			$ok_rewrite_month = '-m'.$params["m"];
			$param+=1;
		}
		if (isset($params["seo"]) && $params["seo"] != "") {
			$ok_rewrite_seo = $params["seo"];
			$ok_rewrite_titre = "";
			$param+=1;
		}
		
		if(sizeof($param) && !isset($params["rss"])) {
			$ok_rewrite = 'blog/'.$ok_rewrite_do.$ok_rewrite_categorie.$ok_rewrite_page.$ok_rewrite_year.$ok_rewrite_month.$ok_rewrite_titre.$ok_rewrite_seo.$ok_rewrite_cat.$ok_rewrite_id;
			$ko_rewrite = '?fc=module&module=prestablog&controller=blog&'.ltrim($ko_rewrite_do.$ko_rewrite_id.$ko_rewrite_cat.$ko_rewrite_page.$ko_rewrite_year.$ko_rewrite_month, "&");
		}
		elseif(isset($params["rss"])) {
			if($params["rss"] == "all") {
				$ok_rewrite = 'rss';
				$ko_rewrite = '?fc=module&module=prestablog&controller=rss';
			}
			else {
				$ok_rewrite = 'rss/'.$params["rss"];
				$ko_rewrite = '?fc=module&module=prestablog&controller=rss&rss='.$params["rss"];				
			}
		}
		else {
			$ok_rewrite = 'blog';
			$ko_rewrite = '?fc=module&module=prestablog&controller=blog';
		}
		
		if((int)(Configuration::get('PS_REWRITING_SETTINGS')) && (int)(Configuration::get('prestablog_rewrite_actif')))
			return self::getBaseUrlFront().$ok_rewrite;
		else
			return self::getBaseUrlFront().$ko_rewrite;
	}
	
	static public function getBaseUrlFront()
	{
		$base = ((Configuration::get('PS_SSL_ENABLED')) ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true));
		
		$base .= __PS_BASE_URI__.self::getLangLink();
		
		return $base;
	}
	
	static public function getLangLink($id_lang = NULL)
	{
		$context = Context::getContext();
		
		if (!Configuration::get('PS_REWRITING_SETTINGS'))
			return '';
		if (Language::countActiveLanguages() <= 1)
			return '';
		if (!$id_lang)
			$id_lang = $context->language->id;
		
		return Language::getIsoById((int)$id_lang).'/';
	}

	static public function prestablog_filter($retourne) {
		$search = array (
						'/--+/'
						);
		$replace = array (
						'-'
						);
		
		$retourne = Tools::strtolower(preg_replace($search, $replace, $retourne));
		
		// cyrillic conversion characters
		$url_replace = array(
		 '/А/' => 'A', '/а/' => 'a',
		 '/Б/' => 'B', '/б/' => 'b',
		 '/В/' => 'V', '/в/' => 'v',
		 '/Г/' => 'G', '/г/' => 'g',
		 '/Д/' => 'D', '/д/' => 'd',
		 '/Е/' => 'E', '/е/' => 'e',
		 '/Ж/' => 'J', '/ж/' => 'j',
		 '/З/' => 'Z', '/з/' => 'z',
		 '/И/' => 'I', '/и/' => 'i',
		 '/Й/' => 'Y', '/й/' => 'y',
		 '/К/' => 'K', '/к/' => 'k',
		 '/Л/' => 'L', '/л/' => 'l',
		 '/М/' => 'M', '/м/' => 'm',
		 '/Н/' => 'N', '/н/' => 'n',
		 '/О/' => 'O', '/о/' => 'o',
		 '/П/' => 'P', '/п/' => 'p',
		 '/Р/' => 'R', '/р/' => 'r',
		 '/С/' => 'S', '/с/' => 's',
		 '/Т/' => 'T', '/т/' => 't',
		 '/У/' => 'U', '/у/' => 'u',
		 '/Ф/' => 'F', '/ф/' => 'f',
		 '/Х/' => 'H', '/х/' => 'h',
		 '/Ц/' => 'C', '/ц/' => 'c',
		 '/Ч/' => 'CH', '/ч/' => 'ch',
		 '/Ш/' => 'SH', '/ш/' => 'sh',
		 '/Щ/' => 'SHT', '/щ/' => 'sht',
		 '/Ъ/' => 'A', '/ъ/' => 'a',
		 '/Ь/' => 'X', '/ь/' => 'x',
		 '/Ю/' => 'YU', '/ю/' => 'yu',
		 '/Я/' => 'YA','/я/' => 'ya',   
		);
		$cyrillic_find = array_keys($url_replace);
		$cyrillic_replace = array_values($url_replace);
		
		$retourne = Tools::strtolower(preg_replace($cyrillic_find, $cyrillic_replace, $retourne));
		
		return $retourne;
	}
	
	static public function getPrestaBlogMetaTagsNewsOnly($id_lang, $id=null)
	{
		if($id) {
			$row=array();
			
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `title`, `meta_title`, `meta_description`, `meta_keywords`
			FROM `'._DB_PREFIX_.'prestablog_news_lang`
			WHERE id_lang = '.(int)($id_lang).' AND id_prestablog_news = '.(int)($id));
		}
		if($row) {
			return self::completeMetaTags($row, $row['title']);
		}
	}
	
	static public function getPrestaBlogMetaTagsNewsCat($id_lang, $id=null)
	{
		if($id) {
			$row=array();
			
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `title`, `meta_title`, `meta_description`, `meta_keywords`
			FROM `'._DB_PREFIX_.'prestablog_categorie_lang`
			WHERE id_lang = '.(int)($id_lang).' AND id_prestablog_categorie = '.(int)($id));
		}
		if($row) {
			return self::completeMetaTags($row, $row['title']);
		}
	}
	
	static public function getPrestaBlogMetaTagsPage($id_lang)
	{
		return self::completeMetaTags(NULL, Configuration::get('prestablog_titlepageblog_'.$id_lang));
	}
	
	static public function getPrestaBlogMetaTagsNewsDate()
	{
		return self::completeMetaTags(NULL, NULL);
	}
	
	public static function completeMetaTags($metaTags, $defaultValue)
	{
		$context = Context::getContext();
		
		$PrestaBlog = new PrestaBlog();
		
		if (empty($metaTags['meta_title']))
			$metaTags['meta_title'] = ($defaultValue ? $defaultValue.' - ' : '').Configuration::get('PS_SHOP_NAME');
		if (empty($metaTags['meta_description']))
			$metaTags['meta_description'] = Configuration::get('PS_META_DESCRIPTION', (int)($context->language->id)) ? Configuration::get('PS_META_DESCRIPTION', (int)($context->language->id)) : '';
		if (empty($metaTags['meta_keywords']))
			$metaTags['meta_keywords'] = Configuration::get('PS_META_KEYWORDS', (int)($context->language->id)) ? Configuration::get('PS_META_KEYWORDS', (int)($context->language->id)) : '';
			
		$metaTags['meta_title'] .= (Tools::getValue('p') ? ' - '.$PrestaBlog->l('page').' '.Tools::getValue('p') : '');
		$metaTags['meta_title'] .= (Tools::getValue('y') ? ' - '.Tools::getValue('y') : '');
		$metaTags['meta_title'] .= (Tools::getValue('m') ? ' - '.$PrestaBlog->MoisLangue[Tools::getValue('m')] : '');
		
		return $metaTags;
	}
	
	static public function getPagination(	$CountListe, // nombre total d'entités
											$EntitesEnMoins=0, // enlever les premières entités
											$End=10,
											$Start=0, 
											$p=1
											) {
		$Pagination = array();
		
		$Pagination["NombreTotalEntites"] = ($CountListe-$EntitesEnMoins);
		
			$Pagination["NombreTotalPages"] = ceil((int)($Pagination["NombreTotalEntites"]) / (int)($End));
			
			if ($Pagination["NombreTotalEntites"] > 0) {
				if($p) {
					$Pagination["PageCourante"] = (int)($p);
					$Pagination["PagePrecedente"] = (int)($p) - 1;
					$Pagination["PageSuivante"] = (int)($p) + 1;
				}
				else {
					$Pagination["PageCourante"] = 1;
					$Pagination["PagePrecedente"] = 0;
					$Pagination["PageSuivante"] = 2;
				}
				
				if($Start) {
					$Pagination["StartCourant"] = (int)($Start);
					$Pagination["StartPrecedent"] = (int)($Start) - (int)($End);
					$Pagination["StartSuivant"] = (int)($Start) + (int)($End);
				}
				else {
					$Pagination["StartCourant"] = 0;
					$Pagination["StartPrecedent"] = 0;
					$Pagination["StartSuivant"] = (int)($End);
				}
				for($icount = 1; $icount <= (int)($Pagination["NombreTotalPages"]); $icount++) {
					$Pagination["Pages"][$icount] = ($icount-1) * (int)($End);
				}
				
				if(count($Pagination["Pages"]) <= 5) {
					$Pagination["PremieresPages"] = array_slice($Pagination["Pages"], 0, 5, true);
					unset($Pagination["Pages"]);
				}
				else {
					$Pagination["PremieresPages"] = array_slice($Pagination["Pages"], 0, 1, true);
					if ($Pagination["PageCourante"] == 1) {
						$Pagination["Pages"] = array_slice($Pagination["Pages"], $Pagination["PageCourante"]-1, 6, true);
					}
					else {
						if ($Pagination["PageCourante"]+4 >= $Pagination["NombreTotalPages"])
							$Pagination["Pages"] = array_slice($Pagination["Pages"], ($Pagination["NombreTotalPages"]-5) , 5, true);
						else
							$Pagination["Pages"] = array_slice($Pagination["Pages"], $Pagination["PageCourante"]-1, 5, true);
					}
				}
			}
		
		return $Pagination;
	}
	
	public function AutoCropImage(
							$ImageSource,
							$RepSource,
							$RepDest,
							$tl, // width
							$th, // height
							$Prefixe,
							$ChangeNom) {
		$tl = (int)($tl);
		$th = (int)($th);
		$tr = $tl/$th;
		
		$full_path = $RepSource.$ImageSource;
		$basefilename = preg_replace("/(.*)\.([^.]+)$/","\\1", $ImageSource);
		$extensionsource = preg_replace("/.*\.([^.]+)$/","\\1", $ImageSource);

		switch ($extensionsource) {
			case 'png':
				$imagesource = imagecreatefrompng($full_path);
				break;
				
			case 'jpg':
				$imagesource = imagecreatefromjpeg($full_path);
				break;
				
			case 'jpeg':
				$imagesource = imagecreatefromjpeg($full_path);
				break;

			default:
				//$this->message_err('ATTENTION ! La librairie GD ne supporte pas cette extension => '.$ext, '', '', '');
				break;
		}

		$sl = imagesx($imagesource);
		$sh = imagesy($imagesource);
		
		$sr = $sl/$sh;
		
		if($sr > $tr) {
			$nh = $th;
			$nl = (int)((($nh*$sl)/$sh));
		}
		elseif ($sr < $tr) {
			$nl = $tl;
			$nh = (int)((($nl*$sh)/$sl));
		}
		elseif ($sr == $tr) {
			$nh = $th;
			$nl = $tl;
		}
		
		if($tr > 1) {
			$nx = 0;
			$ny = (int)((($nh - $th) / 2));
		}
		elseif ($tr < 1) {
			$ny = 0;
			$nx = (int)((($nl - $tl) / 2));
		}
		elseif ($tr == 1) {
			if ($sr > 1) {
				$ny = 0;
				$nx = (int)((($nl - $tl) / 2));
			}
			elseif ($sr < 1) {
				$nx = 0;
				$ny = (int)((($nh - $th) / 2));
			}
			elseif ($sr == 1) {
				$nx = 0;
				$ny = 0;
			}
		}
		
		$image_avant_crop = imagecreatetruecolor($nl, $nh);
		
		imagecopyresampled(
							$image_avant_crop, 
							$imagesource,
							0,
							0, 
							0,
							0,
							$nl,
							$nh,
							$sl,
							$sh
							);
		
		$dest_crop = imagecreatetruecolor($tl, $th);
		
		imagecopyresampled(
							$dest_crop, 
							$image_avant_crop,
							0,
							0,
							$nx,
							$ny,
							$tl,
							$th,
							$tl,
							$th
							);
		
		if($ChangeNom) $ImageSource = $ChangeNom.'.jpg';
		
		switch ($extensionsource) {
			case 'png':
				imagepng($dest_crop, $RepDest.$Prefixe.$ImageSource, 100);
				break;
			case 'jpg':
				imagejpeg($dest_crop, $RepDest.$Prefixe.$ImageSource, 100);
				break;
			case 'jpeg':
				imagejpeg($dest_crop, $RepDest.$Prefixe.$ImageSource, 100);
				break;
		}
		imagedestroy($image_avant_crop);
		imagedestroy($dest_crop);
	}
	
	public function CropImage(
								$ImageSource,
								$RepSource,
								$RepDest,
								$W_Image_Base, // width de l'image sur lequel le crop a été selectionné
								$H_Image_Base, // heigth de l'image sur lequel le crop a été selectionné
								$W_Image_Dest, // width du crop final
								$H_Image_Dest, // heigth du crop final
								$X_Crop_Base, // position horizontal du point de départ du crop selectionné
								$Y_Crop_Base, // position vertical du point de départ du crop selectionné
								$W_Crop_Base, // width de la selection du crop
								$H_Crop_Base, // heigth de la selection du crop
								$Prefixe,
								$ChangeNom
							) {
		$full_path = $RepSource.$ImageSource;
		$ext = preg_replace("/.*\.([^.]+)$/","\\1", $ImageSource);
		$dst_r = ImageCreateTrueColor($W_Image_Dest, $H_Image_Dest);
		
		//list($W_Image_Source, $H_Image_Source, $type, $attr) = getimagesize($full_path);
		list($W_Image_Source, $H_Image_Source) = getimagesize($full_path);
		
		$W_Ratio = $W_Image_Source / $W_Image_Base;
		$H_Ratio = $H_Image_Source / $H_Image_Base;
		
		$X_Crop_Base = (int)($W_Ratio * $X_Crop_Base);
		$Y_Crop_Base = (int)($H_Ratio * $Y_Crop_Base);
		$W_Crop_Base = (int)($W_Ratio * $W_Crop_Base);
		$H_Crop_Base = (int)($H_Ratio * $H_Crop_Base);
		
		switch ($ext) {
			case 'png':
				$image = imagecreatefrompng($full_path);
				break;
			case 'jpg':
				$image = imagecreatefromjpeg($full_path);
				break;
			case 'jpeg':
				$image = imagecreatefromjpeg($full_path);
				break;
			default:
				break;
		}
		imagecopyresampled(
							$dst_r,
							$image,
							0,
							0,
							$X_Crop_Base,
							$Y_Crop_Base,
							$W_Image_Dest,
							$H_Image_Dest,
							$W_Crop_Base,
							$H_Crop_Base
							);
							
		if($ChangeNom) $ImageSource = $ChangeNom.'.jpg';
		
		switch ($ext) {
			case 'png':
				imagepng($dst_r, $RepDest.$Prefixe.$ImageSource, 100);
				break;
			case 'jpg':
				imagejpeg($dst_r, $RepDest.$Prefixe.$ImageSource, 100);
				break;
			case 'jpeg':
				imagejpeg($dst_r, $RepDest.$Prefixe.$ImageSource, 100);
				break;
		}
		imagedestroy($dst_r);
	}
	
	public function gestAntiSpam() {
		if($this->checksum != '')
			return AntiSpamClass::getAntiSpamByChecksum($this->checksum);
		else {
			$Liste = AntiSpamClass::getListe((int)($this->context->language->id), 1);
			if(sizeof($Liste))
				return $Liste[array_rand($Liste, 1)];
			else
				return false;
		}
	}
	
	public function gestComment($news) {
		if(!Configuration::get($this->name.'_comment_actif'))
			return false;
		
		$errors=array();
		$isSubmit=true;
		$content_form = Array(
						"news"		=> (int)$news,
						"name"		=> trim(Tools::getValue('name')),
						"url"		=> trim(Tools::getValue('url')),
						"comment"	=> trim(Tools::getValue('comment')),
						"date"		=> Date("Y-m-d H:i:s"),
						"actif"		=> (Configuration::get($this->name.'_comment_auto_actif') ? 1 : -1 ),
						"antispam_checksum"		=> '',
					);
		
		if(Tools::getValue('submitComment')) {
			if(Configuration::get('prestablog_antispam_actif')) {
				$ListeAS = AntiSpamClass::getListe((int)($this->context->language->id), 1);
				if(sizeof($ListeAS))
					foreach($ListeAS As $KeyAS => $ValueAS)
						if(Tools::getIsset($ValueAS["checksum"])) {
							$content_form["antispam_checksum"] = Tools::getValue($ValueAS["checksum"]);
							$this->checksum = $ValueAS["checksum"];
							if(Tools::getValue($ValueAS["checksum"]) != $ValueAS["reply"])
								$errors[$ValueAS["checksum"]] = $this->l('Your antispam reply is not correct.');
						}
			}
			
			$EregUrl = "#^\b(((http|https)\:\/\/)[^\s()]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))$#";
			if(Tools::strlen($content_form["name"]) < 3)
				$errors["name"]		= $this->l('Your name cannot be empty or inferior at 3 characters.');
			if(Tools::strlen($content_form["comment"]) < 5)
				$errors["comment"]	= $this->l('Your comment cannot be empty or inferior at 5 characters.');
			if(Tools::strlen($content_form["url"]) 
				!= "" && !preg_match($EregUrl, $content_form["url"]))
				$errors["url"]		= $this->l('Make sure the url is correct.');
				
			if(sizeof($errors))
				$isSubmit = false;
			else {
				CommentNewsClass::insertComment(
								$content_form["news"],
								$content_form["date"],
								$content_form["name"],
								$content_form["url"],
								$content_form["comment"],
								$content_form["actif"]
							);
				
				if (Configuration::get($this->name.'_comment_alert_admin')) {
					$News = new NewsClass($content_form["news"], $this->LangueDefaultStore);
					$content_form["title_news"] = $News->title;
					
					Mail::Send(
						$this->LangueDefaultStore,				// langue
						'feedback-admin', 										// template
						$this->l('New comment').' / '.$content_form["title_news"],	// sujet
						array(													// templatevars
								'{news}'				=> $content_form["news"], 
								'{title_news}'			=> $content_form["title_news"], 
								'{date}'				=> ToolsCore::displayDate($content_form["date"], null, true), 
								'{name}'				=> $content_form["name"], 
								'{url}'					=> $content_form["url"], 
								'{comment}'				=> $content_form["comment"], 
								'{url_news}'			=> Tools::getShopDomainSsl(true).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&id='.$content_form["news"],
								'{actif}'				=> $content_form["actif"]
							), 
						Configuration::get($this->name.'_comment_admin_mail'), 	// destinataire mail
						NULL, 													// destinataire nom
						(Configuration::get('PS_SHOP_EMAIL')),			// expéditeur
						(Configuration::get('PS_SHOP_NAME')),				// expéditeur nom
						NULL,													// fichier joint
						NULL,													// mode smtp
						dirname(__FILE__).'/mails/'								// répertoire des mails templates
					);
				}
				
				$ListeAbo = CommentNewsClass::listeCommentMailAbo($content_form["news"]);
				
				if (
							Configuration::get($this->name.'_comment_subscription')
						&&	sizeof($ListeAbo)
						&&	Configuration::get($this->name.'_comment_auto_actif')
					) {
						
					$News = new NewsClass($content_form["news"], $this->LangueDefaultStore);
					$content_form["title_news"] = $News->title;
					
					foreach($ListeAbo As $ValueAbo) {
						Mail::Send(
							$this->LangueDefaultStore,				// langue
							'feedback-subscribe', 									// template
							$this->l('New comment').' / '.$content_form["title_news"],	// sujet
							array(													// templatevars
									'{news}'				=> $content_form["news"], 
									'{title_news}'			=> $content_form["title_news"], 
									'{url_news}'			=> Tools::getShopDomainSsl(true).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&id='.$content_form["news"],
									'{url_desabonnement}'	=> Tools::getShopDomainSsl(true).__PS_BASE_URI__.'?fc=module&module=prestablog&controller=blog&d='.$content_form["news"]
								), 
							$ValueAbo, 												// destinataire mail
							NULL, 													// destinataire nom
							(Configuration::get('PS_SHOP_EMAIL')),			// expéditeur
							(Configuration::get('PS_SHOP_NAME')),				// expéditeur nom
							NULL,													// fichier joint
							NULL,													// mode smtp
							dirname(__FILE__).'/mails/'								// répertoire des mails templates
						);
					}
				}
				
				$isSubmit = true;
			}
		}
		else
			$isSubmit = false;
		
		$this->context->smarty->assign(
			array(
					'prestablog_config'		=> Configuration::getMultiple(array_keys($this->Configurations)),
					'isSubmit'				=> $isSubmit,
					'errors'				=> $errors,
					'content_form'			=> $content_form,
					'comments'				=> CommentNewsClass::getListe(1, $news),
				)
		);
		
		return true;
	}
	
	public function blocDateListe() {
		if(Configuration::get($this->name.'_datenews_actif')) {
			//$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
			
			$ResultDateListe = Array();
			
			$FinReel = 'TIMESTAMP(n.`date`) <= \''.Date("Y/m/d H:i:s").'\'';
			
			$ResultAnnee = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT	DISTINCT YEAR(n.`date`) AS `annee`
				FROM `'._DB_PREFIX_.NewsClass::$table_static.'_lang` As nl
				LEFT JOIN `'._DB_PREFIX_.NewsClass::$table_static.'` As n
					ON (n.id_prestablog_news = nl.id_prestablog_news)
				WHERE n.`actif` = 1
				AND nl.`id_lang` = '.(int)$this->context->language->id.'
				AND nl.`actif_langue` = 1
				AND '.$FinReel.'
				ORDER BY annee '.Configuration::get($this->name.'_datenews_order'));
			
			if(sizeof($ResultAnnee)){
				foreach($ResultAnnee As $ValueAnnee) {
					$ResultCountAnnee = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
						SELECT COUNT(DISTINCT nl.`id_prestablog_news`) AS `value`
						FROM `'._DB_PREFIX_.NewsClass::$table_static.'_lang` As nl
						LEFT JOIN `'._DB_PREFIX_.NewsClass::$table_static.'` As n
							ON (n.id_prestablog_news = nl.id_prestablog_news)
						WHERE n.`actif` = 1
						AND nl.`id_lang` = '.(int)$this->context->language->id.'
						AND nl.`actif_langue` = 1
						AND '.$FinReel.'
						AND YEAR(n.`date`) = \''.$ValueAnnee["annee"].'\'');
					
					$ResultDateListe[$ValueAnnee["annee"]]["nombre_news"] = $ResultCountAnnee["value"];
					
					$ResultMois = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
						SELECT	DISTINCT MONTH(n.`date`) AS `mois`
						FROM `'._DB_PREFIX_.NewsClass::$table_static.'_lang` As nl
						LEFT JOIN `'._DB_PREFIX_.NewsClass::$table_static.'` As n
							ON (n.id_prestablog_news = nl.id_prestablog_news)
						WHERE n.`actif` = 1
						AND nl.`id_lang` = '.(int)$this->context->language->id.'
						AND nl.`actif_langue` = 1
						AND YEAR(n.`date`) = '.$ValueAnnee["annee"].'
						AND '.$FinReel.'
						ORDER BY mois '.Configuration::get($this->name.'_datenews_order'));
					
					if(sizeof($ResultMois)){
						foreach($ResultMois As $ValueMois) {
							$ResultCountMois = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
								SELECT COUNT(DISTINCT n.`id_prestablog_news`) AS `value`
								FROM `'._DB_PREFIX_.NewsClass::$table_static.'_lang` As nl
								LEFT JOIN `'._DB_PREFIX_.NewsClass::$table_static.'` As n
									ON (n.id_prestablog_news = nl.id_prestablog_news)
								WHERE n.`actif` = 1
								AND nl.`id_lang` = '.(int)$this->context->language->id.'
								AND nl.`actif_langue` = 1
								AND '.$FinReel.'
								AND YEAR(n.`date`) = '.$ValueAnnee["annee"].' AND MONTH(n.`date`) = '.$ValueMois["mois"]);
							
							$ResultDateListe[$ValueAnnee["annee"]]["mois"][$ValueMois["mois"]]["nombre_news"] = $ResultCountMois["value"];
							$ResultDateListe[$ValueAnnee["annee"]]["mois"][$ValueMois["mois"]]["mois_value"] = $this->MoisLangue[$ValueMois["mois"]];
						}
					}
				}
			}
			
			$this->context->smarty->assign(
					array(
						'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
						'md5pic' => md5(time()),
						'ResultDateListe' => $ResultDateListe,
						'prestablog_annee' => Tools::getValue("prestablog_annee"),
						)
				);
			
			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_bloc-dateliste.tpl');
		}
	}
	
	public function blocLastListe() {
		if(Configuration::get($this->name.'_lastnews_actif')) {
			$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
			
			$tri_champ = 'n.`date`';
			$tri_ordre = 'desc';
			$Liste = NewsClass::getListe(
											(int)($this->context->language->id), 
											1, // actif only
											0, // slide
											$ConfigTheme, 
											0, // limit start
											(int)Configuration::get($this->name.'_lastnews_limit'), // limit stop
											$tri_champ, 
											$tri_ordre,
											NULL, // date début
											Date("Y/m/d H:i:s"), // date fin
											NULL,
											1,
											(int)Configuration::get('prestablog_lastnews_title_length'),
											(int)Configuration::get('prestablog_lastnews_intro_length')
										);
			
			$this->context->smarty->assign(
					array(
						'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
						'md5pic' => md5(time()),
						'ListeBlocLastNews' => $Liste,
						'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/'
						)
				);
		
			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_bloc-lastliste.tpl');
		}
	}
	
	public function blocCatListe() {
		if(Configuration::get($this->name.'_catnews_actif')) {
			$CategorieCourante = new CategoriesClass((int)Tools::getValue("c"), (int)$this->context->cookie->id_lang);
			$CategorieParente = new CategoriesClass((int)$CategorieCourante->parent, (int)$this->context->cookie->id_lang);
			
			
			//_catnews_tree

			$Liste = CategoriesClass::getListe((int)($this->context->language->id), 1, (int)$CategorieCourante->id);
			
			if(sizeof($Liste)) {
				foreach($Liste As $Key => $Value) {
					//$nombre_news = CategoriesClass::getNombreNewsDansCat((int)$Value["id_prestablog_categorie"]);
					
					if(!Configuration::get($this->name.'_catnews_empty') && (int)$Value["nombre_news_recursif"] == 0)
						unset($Liste[$Key]);
					else
						$Liste[$Key]["nombre_news"] = (int)$Value["nombre_news"];
				}

				$this->context->smarty->assign(
						array(
							'prestablog_categorie_courante' => $CategorieCourante,
							'prestablog_categorie_parent' => $CategorieParente,
							'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
							'md5pic' => md5(time()),
							'ListeBlocCatNews' => $Liste,
							'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/'
							)
					);
				
				return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_bloc-catliste.tpl');
			}
		}
	}

	public function hookDisplayNav($params)
	{
		if($this->IsPSVersion('>=','1.6')) {
			$this->context->smarty->assign(
				array(
					'prestablog_title_nav' => Configuration::get($this->name.'_titlepageblog_'.(int)$this->context->language->id)
				)
			);

			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_nav-top.tpl');
		}
	}
	
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/css/module.css', 'all');

		
		smartyRegisterFunction($this->context->smarty, 'function', 'PrestaBlogUrl', array('PrestaBlog', 'prestablog_url'));
		smartyRegisterFunction($this->context->smarty, 'function', 'PrestaBlogAjaxSearchUrl', array('PrestaBlog', 'prestablog_ajax_search_url'));

		// permettre de determiner les infos de partage pour facebook, 
		// uniquement si on est sur une news
		if(	isset($this->context->controller->module->name)
			&& $this->context->controller->module->name == $this->name
			&& Tools::getValue('id')) {
			$this->News = new NewsClass((int)Tools::getValue('id'), (int)$this->context->cookie->id_lang);
			$FrontUrlBase = (Configuration::get('PS_SSL_ENABLED') ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).__PS_BASE_URI__;
			if(file_exists(_PS_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$this->News->id.'.jpg'))
				$NewsImageUrl = $FrontUrlBase.'modules/prestablog/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$this->News->id.'.jpg';
			else
				$NewsImageUrl = $FrontUrlBase.'img/logo.jpg';

		 	$this->context->smarty->assign(
		 			array(
						'prestablog_news_meta'		=> $this->News,
						'prestablog_news_meta_img' => $NewsImageUrl,
						'prestablog_news_meta_url' => PrestaBlog::prestablog_url(
																		array(
																				"id"		=> $this->News->id,
																				"seo"		=> $this->News->link_rewrite,
																				"titre"	=> $this->News->title
																			)
																	),
	 				)
	 		);
	 		return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_header-meta-og.tpl');
		}
			
		////// A FINIR : liaison fonction recherche
		/*
		if(isset($this->context->controller->php_self) && $this->context->controller->php_self == 'search')
			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_header-search.tpl');
		*/
	}
	
	public function hookDisplayTop($params) {
		//return $this->showSlide();
	}

	public function hookDisplayHome($params) {
		if(Configuration::get($this->name.'_homenews_actif')) {
			return $this->showSlide();
		}
	}

	public function showSlide() {
		if($this->slideNews()) {
			$ConfigThemeArray = objectToArray($this->_getConfigXmlTheme(Configuration::get($this->name.'_theme')));
			if(is_array($ConfigThemeArray['js']))
				foreach($ConfigThemeArray['js'] As $kjs => $vjs)
					$this->context->controller->addJS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/js/'.$vjs);
			elseif($ConfigThemeArray['js'] != '')
				$this->context->controller->addJS(_MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/js/'.$ConfigThemeArray['js']);

			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_slide.tpl');
		}
	}
	
	public function slideNews() {
		$Liste = array();
		$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
		
		$Liste = NewsClass::getListe(
										(int)($this->context->language->id), 
										1, // actif only
										1, // slide
										$ConfigTheme, 
										0, // limit start
										(int)Configuration::get($this->name.'_homenews_limit'), // limit stop
										'n.`date`', 
										'desc',
										NULL, // date début
										Date("Y/m/d H:i:s"), // date fin
										NULL,
										1,
										(int)Configuration::get('prestablog_slide_title_length'),
										(int)Configuration::get('prestablog_slide_intro_length')
									);
									
									
	
		if(sizeof($Liste)) {
			$this->context->smarty->assign(
				array(
					'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
					'md5pic' => md5(time()),
					'prestablog_config_xml' => objectToArray($ConfigTheme),
					$this->name.'_theme_dir' => _MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/',
					'ListeBlogNews' => $Liste
				)
			);
			return true;
		}
		else
			return false;
	}
	
	public function blocRss() {
		if(Configuration::get('prestablog_allnews_rss')) {
			$this->context->smarty->assign(
				array(
					'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
					$this->name.'_theme_dir' => _MODULE_DIR_.$this->name.'/themes/'.Configuration::get($this->name.'_theme').'/'
				)
			);
			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_bloc-rss.tpl');
		}
	}
	
	public function hookDisplayLeftColumn($params)
	{
		$result = NULL;
		
		$sbl = unserialize(Configuration::get($this->name.'_sbl'));
		if(sizeof($sbl))
			foreach($sbl as $ks => $vs) {
				if($vs != '')
					$result .= $this->$vs();
			}
		
		return $result;
	}
	
	public function hookDisplayRightColumn($params)
	{
		$result = NULL;
		
		$sbr = unserialize(Configuration::get($this->name.'_sbr'));
		if(sizeof($sbr))
			foreach($sbr as $ks => $vs) {
				if($vs != '')
					$result .= $this->$vs();
			}
		
		return $result;
	}
	
	public function hookDisplayFooterProduct($params) {
		if($this->IsPSVersion(">=","1.6")) {
			$listeNewsLinked = NewsClass::getNewsProductLinkListe((int)Tools::getValue('id_product'), true);
			if (
					Configuration::get($this->name.'_producttab_actif') 
					&& sizeof($listeNewsLinked)
				) {
				$returnliste = array();
				foreach($listeNewsLinked as $knews => $vnews) {
					$lang = (int)$this->context->language->id;
					$News = new NewsClass((int)$vnews);
					$LangListeNews = unserialize($News->langues);
					
					if(in_array($lang, $LangListeNews)) {
						//$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
						$paragraph = $paragraph_crop = $News->paragraph[$lang];
						
						if(		(Tools::strlen(trim($paragraph)) == 0)
							&&	(Tools::strlen(trim(strip_tags($News->content[$lang]))) >= 1)
							) {
							$paragraph_crop = trim(strip_tags($News->content[$lang]));
						}
						
						$returnliste[(int)$vnews] = array(
							'id' => 	$News->id,
							'url' => 	PrestaBlog::prestablog_url(
													array(
															"id"		=> $News->id,
															"seo"		=> $News->link_rewrite[$lang],
															"titre"		=> $News->title[$lang]
														)
										),
							'title' =>	$News->title[$lang],
							'paragraph_crop' => PrestaBlog::cleanCut($paragraph_crop, (int)Configuration::get('prestablog_news_intro_length'), ' [...]'),
							'image_presente' => file_exists($this->ModulePath.'/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$News->id.'.jpg'),
						);
					}
				}
				$this->context->smarty->assign(
					array(
						'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/',
						'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
						'listeNewsLinked' => $returnliste
					)
				);
				return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_product-footer.tpl');
			}
		}
	}	

	public function hookDisplayProductTab($params) {
		if($this->IsPSVersion("<","1.6")) {
			$listeNewsLinked = NewsClass::getNewsProductLinkListe((int)Tools::getValue('id_product'), true);
			if (
					Configuration::get($this->name.'_producttab_actif') 
					&& sizeof($listeNewsLinked)
				)
				return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_product-tab.tpl');
		}
	}
	
	public function hookDisplayProductTabContent($params) {
		if($this->IsPSVersion("<","1.6")) {
			$listeNewsLinked = NewsClass::getNewsProductLinkListe((int)Tools::getValue('id_product'), true);
			if (
					Configuration::get($this->name.'_producttab_actif') 
					&& sizeof($listeNewsLinked)
				) {
				$returnliste = array();
				foreach($listeNewsLinked as $knews => $vnews) {
					$lang = (int)$this->context->language->id;
					$News = new NewsClass((int)$vnews);
					$LangListeNews = unserialize($News->langues);
					
					if(in_array($lang, $LangListeNews)) {
						//$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
						$paragraph = $paragraph_crop = $News->paragraph[$lang];
						
						if(		(Tools::strlen(trim($paragraph)) == 0)
							&&	(Tools::strlen(trim(strip_tags($News->content[$lang]))) >= 1)
							) {
							$paragraph_crop = trim(strip_tags($News->content[$lang]));
						}
						
						$returnliste[(int)$vnews] = array(
							'id' => 	$News->id,
							'url' => 	PrestaBlog::prestablog_url(
													array(
															"id"		=> $News->id,
															"seo"		=> $News->link_rewrite[$lang],
															"titre"		=> $News->title[$lang]
														)
										),
							'title' =>	$News->title[$lang],
							'paragraph_crop' => PrestaBlog::cleanCut($paragraph_crop, (int)Configuration::get('prestablog_news_intro_length'), ' [...]'),
							'image_presente' => file_exists($this->ModulePath.'/themes/'.Configuration::get('prestablog_theme').'/up-img/'.$News->id.'.jpg'),
						);
					}
				}
				$this->context->smarty->assign(
					array(
						'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/',
						'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
						'listeNewsLinked' => $returnliste
					)
				);

				return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_product-tab-content.tpl');
			}
		}
	}
	
	public function hookDisplayFooter($params) {
		if(Configuration::get($this->name.'_footlastnews_actif')) {
			$ConfigTheme = $this->_getConfigXmlTheme(Configuration::get($this->name.'_theme'));
			
			$tri_champ = 'n.`date`';
			$tri_ordre = 'desc';
			$Liste = NewsClass::getListe(
											(int)($this->context->language->id), 
											1, // actif only
											0, // slide
											$ConfigTheme, 
											0, // limit start
											(int)Configuration::get($this->name.'_footlastnews_limit'), // limit stop
											$tri_champ, 
											$tri_ordre,
											NULL, // date début
											Date("Y/m/d H:i:s"), // date fin
											NULL,
											1,
											(int)Configuration::get('prestablog_footer_title_length'),
											(int)Configuration::get('prestablog_footer_intro_length')
										);
			
			$this->context->smarty->assign(
					array(
						'prestablog_config' => Configuration::getMultiple(array_keys($this->Configurations)),
						'md5pic' => md5(time()),
						'ListeBlocLastNews' => $Liste,
						'prestablog_theme_dir' => _MODULE_DIR_.'prestablog/themes/'.Configuration::get('prestablog_theme').'/'
						)
				);
			return $this->display(__FILE__, 'themes/'.Configuration::get($this->name.'_theme').'/tpl/module_footer-lastliste.tpl');
		}
		
	}
	
	public function hookModuleRoutes() {
		return self::$ModuleRoutes;
	}
	
	public function getStaticModuleRoutes($ModuleRoutes) {
		return self::$ModuleRoutes;
	}
	
	public static $ModuleRoutes = array(
		'prestablog-blog-root' => array(
			'controller' =>	null,
			'rule' =>		'{controller}',
			'keywords' => array(
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller'),
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-news' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{urlnews}-n{n}',
			'keywords' => array(
				'urlnews'		=>	array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				'n'				=>	array('regexp' => '[0-9]+', 'param' => 'id'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller'),
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-date' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/y{y}-m{m}',
			'keywords' => array(
				'y'				=>	array('regexp' => '[0-9]{4}', 'param' => 'y'),
				'm'				=>	array('regexp' => '[0-9]+', 'param' => 'm'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller'),
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-date-pagignation' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{start}p{p}y{y}-m{m}',
			'keywords' => array(
				'y'				=>	array('regexp' => '[0-9]{4}', 'param' => 'y'),
				'm'				=>	array('regexp' => '[0-9]+', 'param' => 'm'),
				'start'			=>	array('regexp' => '[0-9]+', 'param' => 'start'),
				'p'				=>	array('regexp' => '[0-9]+', 'param' => 'p'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller')
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-pagignation' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{start}p{p}',
			'keywords' => array(
				'start'			=>	array('regexp' => '[0-9]+', 'param' => 'start'),
				'p'				=>	array('regexp' => '[0-9]+', 'param' => 'p'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller')
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-catpagination' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{urlcat}-{start}p{p}-c{c}',
			'keywords' => array(
				'c'				=>	array('regexp' => '[0-9]+', 'param' => 'c'),
				'urlcat'		=>	array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				'start'			=>	array('regexp' => '[0-9]+', 'param' => 'start'),
				'p'				=>	array('regexp' => '[0-9]+', 'param' => 'p'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller')
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-cat' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{urlcat}-c{c}',
			'keywords' => array(
				'c'				=>	array('regexp' => '[0-9]+', 'param' => 'c'),
				'urlcat'		=>	array('regexp' => '[_a-zA-Z0-9-\pL]*'),
				'controller'	=>	array('regexp' => 'blog', 'param' => 'controller')
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-rss-root' => array(
			'controller' =>	null,
			'rule' =>		'{controller}',
			'keywords' => array(
				'controller'	=>	array('regexp' => 'rss', 'param' => 'controller'),
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		),
		'prestablog-blog-rss' => array(
			'controller' =>	null,
			'rule' =>		'{controller}/{rss}',
			'keywords' => array(
				'rss'			=>	array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'rss'),
				'controller'	=>	array('regexp' => 'rss', 'param' => 'controller')
			),
			'params' => array(
				'fc' => 'module',
				'module' => 'prestablog'
			)
		)
	);

	public static function getImgFlagByIso($iso) {
		if((int)Language::getIdByIso(Tools::strtolower($iso)) > 0)
			return '<img src="../img/l/'.(int)Language::getIdByIso(Tools::strtolower($iso)).'.jpg" />';
		elseif(file_exists(dirname(__FILE__).'/img/l/'.Tools::strtolower($iso).'.png'))
			return '<img src="../modules/prestablog/img/l/'.Tools::strtolower($iso).'.png" />';
		else
			return '<img src="../img/l/none.jpg" />';
	}
	
	public static function cleanCut($string,$length,$cutString = '...') {
		if(Tools::strlen($string) <= $length)
			return $string;
		$str = Tools::substr($string,0,$length-Tools::strlen($cutString)+1);
		return Tools::substr($str,0,strrpos($str,' ')).$cutString;
	}
	
	private function dumpMySQLBlog() {
		$dump = '/** Backup from PrestaBlog module at '.date("Y-m-d H:i:s").' */'."\n";
		$dump .= "\n".'SET NAMES \'utf8\';'."\n";

		// Find all tables
		$tables = Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'prestablog_%\'');
		$found = 0;
		foreach ($tables as $table)
		{
			$table = current($table);
			// Skip tables which do not start with _DB_PREFIX_
			if (Tools::strlen($table) < Tools::strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, Tools::strlen(_DB_PREFIX_)) != 0)
				continue;
			// Export the table schema
			$schema = Db::getInstance()->executeS('SHOW CREATE TABLE `'.$table.'`');
			
			$fields = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$table.'`');
			
			if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table']))
			{
				return false;
			}
			//$dump .= '/** Scheme for table '.$schema[0]['Table']." */\n";
			//$dump .= 'DROP TABLE IF EXISTS `'.$schema[0]['Table'].'`;'."\n";
			//$dump .= $schema[0]['Create Table'].";\n\n";


			$data = Db::getInstance()->query('SELECT * FROM `'.$schema[0]['Table'].'`', false);
			$sizeof = DB::getInstance()->NumRows();
			$lines = explode("\n", $schema[0]['Create Table']);
			$dump .= 'TRUNCATE `'.$schema[0]['Table'].'`;'."\n";
			if ($data && $sizeof > 0)
			{
				// Export the table data
				$outFields = '(';
				foreach($fields As $vFields)
					$outFields .= '`'.$vFields["Field"].'`,';
				$outFields = rtrim($outFields,',');
				$outFields .= ')';
				$dump .= 'INSERT INTO `'.$schema[0]['Table'].'` '.$outFields.' VALUES ';
				$i = 1;
				while ($row = DB::getInstance()->nextRow($data))
				{
					$s = '(';
					
					foreach ($row as $field => $value)
					{
						$tmp = "'".pSQL($value, true)."',";
						if ($tmp != "'',")
							$s .= $tmp;
						else
						{
							foreach ($lines as $line)
								if (strpos($line, '`'.$field.'`') !== false)
								{	
									if (preg_match('/(.*NOT NULL.*)/Ui', $line))
										$s .= "'',";
									else
										$s .= 'NULL,';
									break;
								}
						}
					}
					$s = rtrim($s, ',');

					if ($i % 200 == 0 && $i < $sizeof)
						$s .= ");\nINSERT INTO `".$schema[0]['Table']."` VALUES\n";
					elseif ($i < $sizeof)
						$s .= "),\n";
					else
						$s .= ");\n";

					$dump .= $s;
					++$i;
				}
			}

			$found++;
		}
		if ($found == 0)
		{
			return false;
		}
		
		return $dump;
	}
	
	private function dirSize($directory) {
		$size = 0;
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
			$size+=$file->getSize();
		}
		return $size;
	} 
	
	private function rrmdir($directory) {
		foreach(glob($directory . '/*') as $file) {
			if(is_dir($file))
				$this->rrmdir($file);
			else
				unlink($file);
		}
		rmdir($directory);
	}
	
	private function rcopy($src, $dst) {
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					$this->rcopy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}
}

function objectToArray( $object ) {
	if( !is_object( $object ) && !is_array( $object ) )
	{
		return $object;
	}
	if( is_object( $object ) )
	{
		$object = get_object_vars( $object );
	}
	return array_map( 'objectToArray', $object );
}



/*
	echo '<pre style="font-size:11px;text-align:left">';
		print_r();
	echo '</pre>';
*/



?>

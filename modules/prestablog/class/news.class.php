<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class NewsClass extends ObjectModel
{
	public	$id;
	public	$id_shop = 1;
	public	$title;
	public	$langues;
	public	$paragraph;
	public	$content;
	public	$date;
	public	$meta_title;
	public	$meta_description;
	public	$meta_keywords;
	public	$link_rewrite;
	public	$categories = array();
	public	$products_liaison = array();
	public	$slide = 0;
	public	$actif = NULL;
	public	$actif_langue = 0;
	
	protected	$table = 'prestablog_news';
	protected	$identifier = 'id_prestablog_news';
	
	public static	$table_static = 'prestablog_news';
	public static	$identifier_static = 'id_prestablog_news';
	
	public static $definition = array(
		'table' => 'prestablog_news',
		'primary' => 'id_prestablog_news',
		'multilang' => true,
		'fields' => array(
			'id_shop' =>			array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'date' =>				array('type' => self::TYPE_DATE,		'validate' => 'isDateFormat',		'required' => true),
			'langues' =>			array('type' => self::TYPE_STRING,		'validate' => 'isSerializedArray',	'required' => true),
			'slide' =>				array('type' => self::TYPE_BOOL,		'validate' => 'isBool',				'required' => true),
			'actif' =>				array('type' => self::TYPE_BOOL,		'validate' => 'isBool',				'required' => true),
			
			// Lang fields
			'title' =>				array('type' => self::TYPE_STRING,	'lang' => true, 'validate' => 'isString',	'size' => 255),
			'meta_title' =>			array('type' => self::TYPE_STRING,	'lang' => true, 'validate' => 'isString',	'size' => 255),
			'meta_description' =>	array('type' => self::TYPE_STRING,	'lang' => true, 'validate' => 'isString',	'size' => 255),
			'meta_keywords' =>		array('type' => self::TYPE_STRING,	'lang' => true, 'validate' => 'isString',	'size' => 255),
			'link_rewrite' =>		array('type' => self::TYPE_STRING,	'lang' => true, 'validate' => 'isLinkRewrite',	'size' => 255),
			'content' =>			array('type' => self::TYPE_HTML,	'lang' => true, 'validate' => 'isString'),
			'paragraph' =>			array('type' => self::TYPE_HTML,	'lang' => true, 'validate' => 'isString'),
		)
	);
	
	public function copyFromPost()
	{
		/** Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/** Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (Tools::getValue($field.'_'.(int)($language['id_lang'])))
						$this->{$field}[(int)($language['id_lang'])] = Tools::getValue($field.'_'.(int)($language['id_lang']));
		}
	}
	
	// le NoLang signifie qu'un article peut ne pas être traduit
	static public function getCountListeAllNoLang(
										$only_actif = 0, 
										$only_slide = 0, 
										$date_debut = NULL,
										$date_fin = NULL, 
										$Categorie = NULL
									)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$actif='';
		if ($only_actif)
			$actif = 'AND n.`actif` = 1';
		$slide='';
		if ($only_slide)
			$slide = 'AND n.`slide` = 1';
			
		$categorie='';
		if ($Categorie)
			$categorie = 'AND cc.`categorie` = '.$Categorie;
		
		$between_date='';
		if (!empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) BETWEEN \''.$date_debut.'\' AND \''.$date_fin.'\'';
		elseif (empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) <= \''.$date_fin.'\'';
		elseif (!empty($date_debut) && empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) >= \''.$date_debut.'\'';
		
		$Value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
		SELECT count(DISTINCT n.id_prestablog_news) As `count`
		FROM `'._DB_PREFIX_.self::$table_static.'` n
		LEFT JOIN `'._DB_PREFIX_.'prestablog_correspondancecategorie` cc
			ON (n.`'.self::$identifier_static.'` = cc.`news`)
		WHERE n.`'.self::$identifier_static.'` > 0
		'.$multiboutique_filtre.'
		'.$actif.'
		'.$slide.'
		'.$categorie.'
		'.$between_date);
		
		return $Value["count"];
	}
	
	static public function getTitleNews($id, $id_lang)
	{
		if (empty($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$Value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
			SELECT nl.`title`
			FROM `'._DB_PREFIX_.self::$table_static.'` n
			JOIN `'._DB_PREFIX_.self::$table_static.'_lang` nl 
				ON (n.`'.self::$identifier_static.'` = nl.`'.self::$identifier_static.'`)
			WHERE 
				nl.`id_lang` = '.(int)($id_lang).'
			AND	n.`'.self::$identifier_static.'` = '.(int)$id);
		
		return $Value["title"];
	}
	
	static public function getProductLinkListe($news, $active=false) {
		$Return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	`id_product`
		FROM `'._DB_PREFIX_.self::$table_static.'_product`
		WHERE `'.self::$identifier_static.'` = '.(int)($news));
		
		$Return2 = array();
		foreach($Return1 As $Key => $Value) {
			$Product = new Product((int)$Value["id_product"]);
			
			if((int)$Product->id)
				if($active) {
					if($Product->active)
						$Return2[] = $Value["id_product"];
				}
				else
					$Return2[] = $Value["id_product"];
			else
				NewsClass::removeProductLinkDeleted((int)$Value["id_product"]);
		}
		return $Return2;
	}
	
	static public function getNewsProductLinkListe($product, $active=false) {
		$Return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	`'.self::$identifier_static.'`
		FROM `'._DB_PREFIX_.self::$table_static.'_product`
		WHERE `id_product` = '.(int)($product));
		
		$Return2 = array();
		foreach($Return1 As $Key => $Value) {
			$News = new NewsClass((int)$Value["id_prestablog_news"]);
			
			if($active) {
				if($News->actif)
					$Return2[] = $Value["id_prestablog_news"];
			}
			else
				$Return2[] = $Value["id_prestablog_news"];
		}
		return $Return2;
	}
	
	static public function removeProductLinkDeleted($product) {
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
						DELETE FROM `'._DB_PREFIX_.self::$table_static.'_product` 
						WHERE `id_product` = '.(int)$product);
	}
	
	static public function updateProductLinkNews($news, $product) {
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
						INSERT INTO `'._DB_PREFIX_.self::$table_static.'_product` 
							(`'.self::$identifier_static.'`, `id_product`) 
						VALUES ('.(int)$news.', '.(int)$product.')');
	}
	
	static public function removeAllProductsLinkNews($news) {
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
						DELETE FROM `'._DB_PREFIX_.self::$table_static.'_product` 
						WHERE `'.self::$identifier_static.'` = '.(int)$news);
	}
	
	static public function removeProductLinkNews($news, $product) {
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
						DELETE FROM `'._DB_PREFIX_.self::$table_static.'_product` 
						WHERE `'.self::$identifier_static.'` = '.(int)$news.' AND `id_product` = '.(int)$product);
	}
	
	static public function getCountListeAll(
										$id_lang = NULL, 
										$only_actif = 0, 
										$only_slide = 0, 
										$date_debut = NULL,
										$date_fin = NULL, 
										$Categorie = NULL,
										$actif_langue = 0
									)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$actif='';
		if ($only_actif)
			$actif = 'AND n.`actif` = 1';
		$actif_lang='';
		if ($actif_langue)
			$actif_lang = 'AND nl.`actif_langue` = 1';
		$slide='';
		if ($only_slide)
			$slide = 'AND n.`slide` = 1';
			
		$categorie='';
		if ($Categorie)
			$categorie = 'AND cc.`categorie` = '.$Categorie;
		
		$between_date='';
		if (!empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) BETWEEN \''.$date_debut.'\' AND \''.$date_fin.'\'';
		elseif (empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) <= \''.$date_fin.'\'';
		elseif (!empty($date_debut) && empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) >= \''.$date_debut.'\'';
		
		$lang='';
		if (empty($id_lang))
			$lang = 'AND nl.`id_lang` = '.(int)Configuration::get('PS_LANG_DEFAULT');
		elseif ($id_lang == 0)
			$lang = '';
		else
			$lang = 'AND nl.`id_lang` = '.(int)$id_lang;
		
		$Value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
		SELECT count(DISTINCT nl.id_prestablog_news) As `count`
		FROM `'._DB_PREFIX_.self::$table_static.'_lang` nl
		LEFT JOIN `'._DB_PREFIX_.self::$table_static.'` n
			ON (n.`'.self::$identifier_static.'` = nl.`'.self::$identifier_static.'`)
		LEFT JOIN `'._DB_PREFIX_.'prestablog_correspondancecategorie` cc
			ON (n.`'.self::$identifier_static.'` = cc.`news`)
		WHERE 1=1 
		'.$multiboutique_filtre.'
		'.$lang.'
		'.$actif.'
		'.$actif_lang.'
		'.$slide.'
		'.$categorie.'
		'.$between_date);
		
		return $Value["count"];
	}
	
	static public function getListe(
										$id_lang = NULL, 
										$only_actif = 0, 
										$only_slide = 0, 
										$ConfigTheme, 
										$limit_start = 0, 
										$limit_stop = NULL, 
										$tri_champ = 'n.`date`', 
										$tri_ordre = 'desc', 
										$date_debut = NULL,
										$date_fin = NULL, 
										$Categorie = NULL,
										$actif_langue = 0,
										$title_length = 80,
										$intro_length = 150
									)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$Module = new PrestaBlog();
		
		$Liste = array();
		
		$actif='';
		if ($only_actif)
			$actif = 'AND n.`actif` = 1';
		$actif_lang='';
		if ($actif_langue)
			$actif_lang = 'AND nl.`actif_langue` = 1';
		$slide='';
		if ($only_slide)
			$slide = 'AND n.`slide` = 1';
			
		$categorie='';
		if ($Categorie)
			$categorie = 'AND cc.`categorie` = '.$Categorie;
			
		$between_date='';
		if (!empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) BETWEEN \''.$date_debut.'\' AND \''.$date_fin.'\'';
		elseif (empty($date_debut) && !empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) <= \''.$date_fin.'\'';
		elseif (!empty($date_debut) && empty($date_fin))
			$between_date = 'AND TIMESTAMP(n.`date`) >= \''.$date_debut.'\'';
		
		$limit='';
		if (!empty($limit_stop))
			$limit = 'LIMIT '.(int)$limit_start.', '.(int)$limit_stop;
		
		$lang='';
		if (empty($id_lang))
			$lang = 'AND nl.`id_lang` = '.(int)(int)Configuration::get('PS_LANG_DEFAULT');
		elseif ($id_lang == 0)
			$lang = '';
		else
			$lang = 'AND nl.`id_lang` = '.(int)$id_lang;
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	DISTINCT(nl.`id_prestablog_news`), n.*, nl.*,
				LEFT(nl.`title`, '.(int)$title_length.') As title
		FROM `'._DB_PREFIX_.self::$table_static.'_lang` nl
		LEFT JOIN `'._DB_PREFIX_.self::$table_static.'` n
			ON (n.`'.self::$identifier_static.'` = nl.`'.self::$identifier_static.'`)
		LEFT JOIN `'._DB_PREFIX_.'prestablog_correspondancecategorie` cc
			ON (n.`'.self::$identifier_static.'` = cc.`news`)
		WHERE 1=1 
		'.$multiboutique_filtre.'
		'.$lang.'
		'.$actif.'
		'.$actif_lang.'
		'.$slide.'
		'.$categorie.'
		'.$between_date.'
		ORDER BY '.$tri_champ.' '.$tri_ordre.'
		'.$limit);
		
		if(sizeof($Liste)) {
			foreach($Liste As $Key => $Value) {
				$Liste[$Key]["categories"] = CorrespondancesCategoriesClass::getCategoriesListeName((int)$Value["id_prestablog_news"], (int)$context->language->id, 1);
				
				$Liste[$Key]["paragraph"] = $Value["paragraph"];
				$Liste[$Key]["paragraph_crop"] = $Value["paragraph"];
				
				if(		(Tools::strlen(trim($Value["paragraph"])) == 0)
					&&	(Tools::strlen(trim(strip_tags($Value["content"]))) >= 1)
					) {
					$Liste[$Key]["paragraph_crop"] = trim(strip_tags($Value["content"]));
				}
				
				if(Tools::strlen(trim($Liste[$Key]["paragraph_crop"])) > (int)$intro_length) {
					$Liste[$Key]["paragraph_crop"] = PrestaBlog::cleanCut($Liste[$Key]["paragraph_crop"], (int)$intro_length, ' [...]');
				}
				if(file_exists($Module->ModulePath.'/themes/'.Configuration::get($Module->name.'_theme').'/up-img/'.$Value[self::$identifier_static].'.jpg'))
					$Liste[$Key]["image_presente"] = 1;
				if(Tools::strlen(trim(strip_tags($Value["content"]))) >= 1)
					$Liste[$Key]["link_for_unique"] = 1;
			}
		}
		
		return $Liste;
	}
	
	public function registerTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'` (
		`'.$this->identifier.'` int(10) unsigned NOT NULL auto_increment,
		`id_shop` int(10) unsigned NOT NULL,
		`date` datetime NOT NULL,
		`langues` text NOT NULL,
		`actif` tinyint(1) NOT NULL DEFAULT \'1\',
		`slide` tinyint(1) NOT NULL DEFAULT \'0\',
		PRIMARY KEY (`'.$this->identifier.'`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'_lang` (
		`'.$this->identifier.'` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`title` varchar(255) NOT NULL,
		`paragraph` text NOT NULL,
		`content` text NOT NULL,
		`meta_description` text NOT NULL,
		`meta_keywords` text NOT NULL,
		`meta_title` text NOT NULL,
		`link_rewrite` text NOT NULL,
		`actif_langue` tinyint(1) NOT NULL DEFAULT \'1\',
		PRIMARY KEY (`'.$this->identifier.'`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'_product` (
		`'.$this->identifier.'_product` int(10) unsigned NOT NULL auto_increment,
		`'.$this->identifier.'` int(10) unsigned NOT NULL,
		`id_product` int(10) unsigned NOT NULL,
		PRIMARY KEY (`'.$this->identifier.'_product`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		$Langues = Language::getLanguages(true);
		if(sizeof($Langues)) {
			$LangueUse = Array();
			foreach($Langues As $Value) {
				$LangueUse[] = $Value["id_lang"];
			}
			
			if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
				INSERT INTO `'._DB_PREFIX_.$this->table.'` 
					(`'.$this->identifier.'`, `id_shop`, `date` , `langues` , `actif`, `slide`) 
				VALUES
					(1, 1, DATE_ADD(NOW(), INTERVAL -3 DAY), \''.serialize($LangueUse).'\', 1, 1),
					(2, 1, DATE_ADD(NOW(), INTERVAL -2 DAY), \''.serialize($LangueUse).'\', 1, 1),
					(3, 1, DATE_ADD(NOW(), INTERVAL -1 DAY), \''.serialize($LangueUse).'\', 1, 1)'))
				return false;
			
			$title = Array (
				1 => "Curabitur venenatis ut elit quis tempus, sed eget sem pretium",
				2 => "Ut eget dui vel ligula fringilla iaculis quis a eros",
				3 => "Lorem ipsum dolor sit amet, consectetur adipiscing elit"
			);
			
			$paragraph = Array (
				1 => "Praesent fringilla adipiscing leo. Vestibulum eget venenatis risus. Aliquam tristique erat ac odio suscipit tempus. Nullam faucibus libero tortor, eget volutpat lacus molestie non",
				2 => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nec mauris vel erat egestas posuere. \r\n\r\nUt eget dui vel ligula fringilla iaculis quis a eros.",
				3 => "Curabitur venenatis sed est at porta. Donec eu tincidunt nibh. Praesent vel aliquet leo. Donec ut dolor quis tortor aliquet tincidunt vel id mauris. \r\n\r\nMorbi gravida eros eu dolor rhoncus tempor."
			);
			
			$content = Array (
				1 => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eget pretium lectus, sed bibendum augue. In sollicitudin convallis blandit.</p><h2>Curabitur venenatis ut elit quis tempus.</h2><p>Sed eget sem pretium, consequat ante sit amet, accumsan nunc. Vestibulum adipiscing dapibus tortor, eget lacinia neque dapibus auctor. Integer a dui in tellus dignissim dictum eu eu orci. Integer venenatis libero a justo rutrum, eu facilisis libero aliquam. Praesent sit amet elit nunc. Vestibulum aliquam turpis tellus, sed sagittis velit suscipit molestie. Nullam eleifend convallis sodales. Aenean est magna, molestie quis viverra vitae, hendrerit nec dui.</p><p><strong>Praesent fringilla adipiscing leo. </strong></p><p>Vestibulum eget venenatis risus. Aliquam tristique erat ac odio suscipit tempus. Nullam faucibus libero tortor, eget volutpat lacus molestie non.<br /> <em>Phasellus euismod eu urna nec aliquet.</em></p><p>Aenean in rutrum dolor. Nulla eleifend pulvinar mauris, hendrerit tempus odio pretium vitae. Suspendisse blandit volutpat nisi. Pellentesque dignissim nibh consectetur metus rhoncus, eget venenatis ligula convallis. Fusce ullamcorper augue nec semper gravida.</p><p>Pellentesque semper leo at nulla commodo sodales. Integer purus sem, scelerisque in commodo eu, volutpat a nisi. Fusce placerat orci in neque condimentum, non consectetur massa adipiscing. Aenean vestibulum eros a ligula mattis imperdiet. Aenean sapien nibh, cursus ut mattis in, eleifend non diam.</p><h3>Vestibulum aliquam sem diam, eu sagittis quam luctus eu.</h3><p><img src=\"/modules/prestablog/img/demo/photo-mode.jpg\" alt=\"photo de mode\" style=\"display: block; margin-left: auto; margin-right: auto;\" /></p><p>Suspendisse porta libero et est fringilla commodo. Donec congue massa in nisi aliquet dapibus. Cras aliquet posuere justo, a iaculis orci malesuada a. Ut ultricies tempus tempor. Pellentesque sit amet purus et tortor eleifend hendrerit. Curabitur aliquet rhoncus dolor, eget mollis ante malesuada eget. Suspendisse id orci est. Nulla erat sapien, aliquam porta pharetra at, ultricies id odio. Nam sed libero id magna egestas sodales vel quis ipsum. Mauris sit amet mauris eu odio sodales venenatis. Mauris consequat dolor nisi, at pharetra diam sollicitudin vitae. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed nec felis porttitor, facilisis metus sit amet, aliquam ipsum. Phasellus sed ante non nunc commodo fermentum et nec odio. Maecenas eleifend venenatis iaculis.</p><ul><li>Nulla facilisi. Fusce at consequat odio.</li><li>Donec id fermentum urna.</li><li>Integer nec augue volutpat</li></ul><p><img class=\"f_left\" src=\"/modules/prestablog/img/demo/photo-mode2.jpg\" alt=\"photo de mode 2\"  style=\"display: block; margin-left: auto; margin-right: auto;\" /></p><p> ultrices ipsum at, elementum nisi. Nam vel eros eu dui mollis ultrices.</p><p>Cras venenatis fermentum mauris, quis faucibus arcu. Aenean a lectus vel dui dapibus gravida quis a urna. Curabitur euismod arcu nec est fringilla commodo. Morbi consectetur id enim vel sagittis. Aenean at velit at lacus blandit volutpat. Aliquam in nibh enim. Sed ligula nisi, porttitor et vehicula ut, mattis id mauris.</p><h4>Aenean iaculis nibh ac lobortis dignissim.</h4><p>In posuere pharetra libero, scelerisque iaculis purus cursus eget. Morbi sed vestibulum enim.</p><ol><li>Ut facilisis nibh vel tortor malesuada commodo.</li><li>Maecenas pretium tincidunt eros vel elementum. </li><li>Pellentesque in lectus lectus. </li><li>Nullam ac metus libero. Ut magna lorem, pulvinar ut dictum semper, vulputate a magna.</li></ol><p>Aliquam volutpat est urna, eget feugiat ante suscipit in. In varius tortor eu nunc volutpat, sit amet hendrerit tellus accumsan. <strong>Nunc at feugiat massa, eu porttitor nisi.</strong> Duis neque dui, vulputate in gravida a, luctus convallis sem.</p><h2>All embed video possible : vimeo, youtube etc.</h2><p><iframe src=\"http://player.vimeo.com/video/85251723?portrait=0\" width=\"870\" height=\"400\"></iframe></p>",
				2 => "<div id=\"lipsum\"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce a diam hendrerit eros feugiat ornare ac non dui. Aliquam euismod mi vel dapibus lacinia. Quisque vitae erat tellus. Fusce eget arcu vitae odio mattis vestibulum id eu mi. Nullam volutpat cursus risus, a vulputate libero. Nulla vel tincidunt nulla, vitae accumsan massa. Nunc hendrerit, ipsum ut placerat venenatis, metus tellus varius elit, ac aliquet diam nisl eu metus.</p><h2>Curabitur venenatis sed est at porta. Donec eu tincidunt nibh.</h2><p>Praesent vel aliquet leo. Donec ut dolor quis tortor aliquet tincidunt vel id mauris. Morbi gravida eros eu dolor rhoncus tempor. Praesent vestibulum neque a libero mattis tristique. Proin commodo lorem vestibulum, molestie erat in, condimentum leo.</p><h3>All embed format supported : Vidéo Dailymotion, youtube, vimeo etc.</h3><p><iframe src=\"http://www.dailymotion.com/embed/video/xz5zar\" width=\"870\" height=\"400\"></iframe></p><p>Aliquam eu porttitor justo.</p></div><div id=\"lipsum\"><strong>Etiam a dui aliquam, ultricies purus sed, pharetra odio.</strong></div><div><ul><li>Etiam sit amet tempus lacus.</li><li>Suspendisse ac eros ut erat luctus hendrerit id sit amet nulla.</li><li>Sed molestie, leo sit amet imperdiet vulputate, tortor</li><li>ligula viverra ipsum, eu molestie magna elit et tortor.</li></ul></div><div>Curabitur nulla leo, auctor id dapibus sed, varius sit amet mauris. Maecenas pulvinar nunc id risus sagittis, non convallis nisl pharetra. Sed eu leo luctus, ornare nunc vel, mattis nisi.</div><div><p>Proin ut sem urna. Aliquam erat volutpat. Donec pretium, ligula sed suscipit adipiscing, erat arcu feugiat velit, eu dapibus metus nulla vitae orci. Integer hendrerit lacus metus, ac pulvinar massa sagittis ac. Praesent enim nunc, adipiscing id rutrum id, vestibulum ut leo. Quisque laoreet aliquet magna, a suscipit augue consectetur sit amet. Pellentesque nibh nisl, elementum quis turpis quis, tempus accumsan mauris.</p><p><img src=\"/modules/prestablog/img/demo/photo-bebe.jpg\" alt=\"description alt pour le bebe\" width=\"535\" height=\"357\"  style=\"display: block; margin-left: auto; margin-right: auto;\" /></p><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc placerat sollicitudin nibh, eget pulvinar nunc pellentesque non. Vivamus posuere est at ipsum mattis, ac tincidunt tortor accumsan. Sed suscipit nisi orci, in interdum urna vulputate non. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nec mauris vel erat egestas posuere. Ut eget dui vel ligula fringilla iaculis quis a eros.</p><p>Pellentesque facilisis ornare erat a bibendum. Sed placerat mauris eget libero cursus pulvinar. Maecenas vitae sem nec felis placerat dignissim vel et enim. Curabitur eu lorem urna.</p><p>n non lacinia est. Curabitur facilisis leo ut diam facilisis commodo. Duis et augue mi. Fusce id orci libero. Nunc dapibus nisi augue, sed pretium augue scelerisque non. Etiam mollis purus magna, vitae rhoncus leo suscipit eu. Integer et neque id risus lacinia sollicitudin. Suspendisse consectetur mauris a magna egestas convallis. Aenean interdum nisl non sapien sodales, sit amet ultrices neque commodo. Integer commodo, felis eu feugiat ullamcorper, sapien ligula tempus quam, sed vestibulum orci lacus quis quam. Ut quis pellentesque diam. Quisque non venenatis nisl, ut tincidunt justo.</p></div>",
				3 => "<div id=\"lipsum\"><p>Lorapibus lacinipsum dolor sit amet, ac non dui. Aliquam euismod mi vel dapibus lacinia. Quisque vitae erat tellus.</p><p>Fusce eget arcu vitae odio mattis vestibulum id eu mi. Nullam volutpat cursus risus, a vulputate libero.</p><p>Nulla vel tincidunt nulla, vitae accumsan massa. Nunc hendrerit, ipsum ut placerat venenatis, metus tellus varius elit, ac aliquet diam nisl eu metus.</p><h2>Video youtube, vimeo etc.</h2><p><iframe src=\"http://www.youtube.com/embed/zJahlKPCL9g\" width=\"870\" height=\"400\"></iframe></p><h2>Curabitur venenatis sed est at porta. Donec eu tincidunt nibh.</h2><p>Praesent vel aliquet leo. Donec ut dolor quis tortor aliquet tincidunt vel id mauris. Morbi gravida eros eu dolor rhoncus tempor. Praesent vestibulum neque a libero mattis tristique. Proin commodo lorem vestibulum, molestie erat in, condimentum leo.</p><h2>Aliquam eu porttitor justo.</h2></div><div id=\"lipsum\"><strong>Etiam a dui aliquam, ultricies purus sed, pharetra odio.</strong></div><div><ul><li>Etiam sit amet tempus lacus.</li><li>Suspendisse ac eros ut erat luctus hendrerit id sit amet nulla.</li><li>Sed molestie, leo sit amet imperdiet vulputate, tortor</li><li>ligula viverra ipsum, eu molestie magna elit et tortor.</li></ul></div><div>Curabitur nulla leo, auctor id dapibus sed, varius sit amet mauris. Maecenas pulvinar nunc id risus sagittis, non convallis nisl pharetra. Sed eu leo luctus, ornare nunc vel, mattis nisi.</div><div><p>Proin ut sem urna. Aliquam erat volutpat. Donec pretium, ligula sed suscipit adipiscing, erat arcu feugiat velit, eu dapibus metus nulla vitae orci. Integer hendrerit lacus metus, ac pulvinar massa sagittis ac. Praesent enim nunc, adipiscing id rutrum id, vestibulum ut leo. Quisque laoreet aliquet magna, a suscipit augue consectetur sit amet. Pellentesque nibh nisl, elementum quis turpis quis, tempus accumsan mauris.</p><p><img src=\"/modules/prestablog/img/demo/photo-mac.jpg\" alt=\"description alt pour le bebe\" style=\"display: block; margin-left: auto; margin-right: auto;\" /></p><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc placerat sollicitudin nibh, eget pulvinar nunc pellentesque non. Vivamus posuere est at ipsum mattis, ac tincidunt tortor accumsan. Sed suscipit nisi orci, in interdum urna vulputate non. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum nec mauris vel erat egestas posuere. Ut eget dui vel ligula fringilla iaculis quis a eros.</p><p>Pellentesque facilisis ornare erat a bibendum. Sed placerat mauris eget libero cursus pulvinar. Maecenas vitae sem nec felis placerat dignissim vel et enim. Curabitur eu lorem urna.</p><p>n non lacinia est. Curabitur facilisis leo ut diam facilisis commodo. Duis et augue mi. Fusce id orci libero. Nunc dapibus nisi augue, sed pretium augue scelerisque non. Etiam mollis purus magna, vitae rhoncus leo suscipit eu. Integer et neque id risus lacinia sollicitudin. Suspendisse consectetur mauris a magna egestas convallis. Aenean interdum nisl non sapien sodales, sit amet ultrices neque commodo. Integer commodo, felis eu feugiat ullamcorper, sapien ligula tempus quam, sed vestibulum orci lacus quis quam. Ut quis pellentesque diam. Quisque non venenatis nisl, ut tincidunt justo.</p></div>"
			);
			
			$meta_description = Array (
				1 => "Praesent fringilla adipiscing leo. Vestibulum eget venenatis risus.",
				2 => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
				3 => "Praesent vel aliquet leo. Donec ut dolor quis tortor aliquet tincidunt vel id mauris."
			);
			
			$meta_keywords = Array (
				1 => "Curabitur, venenatis, ut elit, quis tempus, sed eget, sem pretium",
				2 => "Ut eget, dui vel, ligula, fringilla, iaculis, quis a eros",
				3 => "Morbi, gravida, eros eu, dolor, rhoncus, tempor"
			);
			
			$meta_title = Array (
				1 => "Curabitur venenatis ut elit quis tempus, sed eget sem pretium",
				2 => "Ut eget dui vel ligula fringilla iaculis quis a eros",
				3 => "Morbi gravida eros eu dolor rhoncus tempor"
			);
			
			$link_rewrite = Array (
				1 => "curabitur-venenatis-ut-elit-quis-tempus-sed-eget-sem-pretium",
				2 => "ut-eget-dui-vel-ligula-fringilla-iaculis-quis-a-eros",
				3 => "lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit"
			);
			
			$sql_values = 'VALUES ';
			for($i=1; $i<=3; $i++) {
				foreach($Langues As $Value) {
					$sql_values.= '
						(
							'.$i.', 
							'.$Value["id_lang"].', 
							\''.pSQL($title[$i]).'\', 
							\''.pSQL($paragraph[$i]).'\',
							\''.$content[$i].'\',
							\''.pSQL($meta_description[$i]).'\',
							\''.pSQL($meta_keywords[$i]).'\',
							\''.pSQL($meta_title[$i]).'\',
							\''.pSQL($link_rewrite[$i]).'\',
							1
						),';
				}
			}
			$sql_values = rtrim($sql_values, ',');
			if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
				INSERT INTO `'._DB_PREFIX_.$this->table.'_lang` 
					(
						`'.$this->identifier.'`, 
						`id_lang`, 
						`title`, 
						`paragraph`, 
						`content`,
						`meta_description`,
						`meta_keywords`,
						`meta_title`,
						`link_rewrite`,
						`actif_langue`
					)
				'.$sql_values))
				return false;
		}
		
		return true;
	}
	
	public function deleteTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table))
			return false;
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table.'_lang`'))
			return false;
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table.'_product`'))
			return false;
			
		return true;
	}
	
	public function changeEtat($Field) {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			UPDATE `'._DB_PREFIX_.$this->table.'` SET `'.$Field.'`=CASE `'.$Field.'` WHEN 1 THEN 0 WHEN 0 THEN 1 END 
			WHERE `'.$this->identifier.'`='.(int)($this->id))
			)
			return false;
		return true;
	}
	
	public function razEtatLangue($id_news) {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			UPDATE `'._DB_PREFIX_.$this->table.'_lang` SET `actif_langue` = 0
			WHERE `'.$this->identifier.'`= '.(int)($id_news))
			)
			return false;
		
		return true;
	}
	
	public function changeActiveLangue($id_news, $id_lang) {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			UPDATE `'._DB_PREFIX_.$this->table.'_lang` SET `actif_langue` = 1
			WHERE `'.$this->identifier.'`= '.(int)($id_news).'
			AND `id_lang` = '.(int)($id_lang))
			)
			return false;
		
		return true;
	}
}

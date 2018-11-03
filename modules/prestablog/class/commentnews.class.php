<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class CommentNewsClass extends ObjectModel
{
	public		$id;
	public		$news;
	public		$date;
	public		$name;
	public		$url;
	public		$comment;
	public		$actif = 0;
	
	protected 	$table = 'prestablog_commentnews';
	protected 	$identifier = 'id_prestablog_commentnews';
	
	protected static	$table_static = 'prestablog_commentnews';
	protected static	$identifier_static = 'id_prestablog_commentnews';
	
	public static $definition = array(
		'table' => 'prestablog_commentnews',
		'primary' => 'id_prestablog_commentnews',
		'fields' => array(
			'date' =>			array('type' => self::TYPE_DATE,		'validate' => 'isDateFormat',	'required' => true),
			'news' =>			array('type' => self::TYPE_INT,			'validate' => 'isUnsignedId',	'required' => true),
			'actif' =>			array('type' => self::TYPE_INT,			'validate' => 'isInt'),
			'name' =>			array('type' => self::TYPE_STRING,		'validate' => 'isString',	'required' => true, 'size' => 255),
			'url' =>			array('type' => self::TYPE_STRING,		'validate' => 'isUrlOrEmpty',	'size' => 255),
			'comment' =>		array('type' => self::TYPE_HTML,		'validate' => 'isString'),
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
	
	public function registerTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			CREATE TABLE `'._DB_PREFIX_.$this->table.'` (
			`'.$this->identifier.'` int(10) unsigned NOT NULL auto_increment,
			`news` int(10) unsigned NOT NULL,
			`date` datetime NOT NULL,
			`name` varchar(255) NOT NULL,
			`url` varchar(255),
			`comment` text NOT NULL,
			`actif` int(1) NOT NULL DEFAULT \'-1\',
			PRIMARY KEY (`'.$this->identifier.'`))
			ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
			
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			CREATE TABLE `'._DB_PREFIX_.$this->table.'_abo` (
			`'.$this->identifier.'_abo` int(10) unsigned NOT NULL auto_increment,
			`news` int(10) unsigned NOT NULL,
			`id_customer` int(10) unsigned NOT NULL,
			PRIMARY KEY (`'.$this->identifier.'_abo`))
			ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;

      if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
      INSERT INTO `'._DB_PREFIX_.$this->table.'` 
         (
         	`'.$this->identifier.'`, 
				`news`, 
				`date`, 
				`name`, 
				`url`, 
				`comment`, 
				`actif`
         ) 
      VALUES 
         (1, 3, NOW(), \'Lorem ipsum\', \'http://www.prestashop.com\', \'Aliquam eu porttitor justo. Etiam a dui aliquam, ultricies purus sed, pharetra odio. Etiam sit amet tempus lacus.\r\n\r\nSuspendisse ac eros ut erat luctus hendrerit id sit amet nulla. Sed molestie, leo sit amet imperdiet vulputate, tortor ligula viverra ipsum, eu molestie magna elit et tortor. \r\n\r\nCurabitur nulla leo, auctor id dapibus sed, varius sit amet mauris. Maecenas pulvinar nunc id risus sagittis, non convallis nisl pharetra. Sed eu leo luctus, ornare nunc vel, mattis nisi.\', 1),
			(2, 3, NOW(), \'Lorem ipsum\', \'\', \'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nunc placerat sollicitudin nibh, eget pulvinar nunc pellentesque non. Vivamus posuere est at ipsum mattis, ac tincidunt tortor accumsan. Sed suscipit nisi orci,\', -1)'
         ))
         return false;
		
		return true;
	}
	
	public function deleteTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table))
			return false;
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table.'_abo'))
			return false;
			
		return true;
	}
	
	static public function getCountListeAll($only_actif = NULL, $only_news = NULL)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$actif="";
		if ((int)$only_actif>-2)
			$actif = 'AND cn.`actif` = '.(int)$only_actif;
		$news="";
		if ($only_news)
			$news = 'AND cn.`news` = '.(int)$only_news;
		
		$Value = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
			SELECT count(cn.`'.self::$identifier_static.'`) As `count`
			FROM `'._DB_PREFIX_.self::$table_static.'` cn
			LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (cn.`news` = n.`id_prestablog_news`)
			WHERE 1=1
			'.$multiboutique_filtre.'
			'.$news.'
			'.$actif.'
			ORDER BY cn.`date` DESC');
		
		return $Value["count"];
	}
	//&activeget=0&slideget=0&activeCommentget=-2&commentListe&start=10&p=2
	
	static public function getListe($only_actif = NULL, $only_news = NULL)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$actif="";
		if ((int)$only_actif>-2)
			$actif = 'AND cn.`actif` = '.(int)$only_actif;
		$news="";
		if ($only_news)
			$news = 'AND cn.`news` = '.(int)$only_news;
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	cn.*
		FROM `'._DB_PREFIX_.self::$table_static.'` cn
		LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (cn.`news` = n.`id_prestablog_news`)
		WHERE 1=1
		'.$multiboutique_filtre.'
		'.$news.'
		'.$actif.'
		ORDER BY cn.`date` DESC');
		
		return $Liste;
	}
	
	static public function getListeNavigate($only_actif = NULL,
											$limit_start = 0, 
											$limit_stop = NULL )
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;

		$actif="";
		if ((int)$only_actif>-2)
			$actif = 'AND cn.`actif` = '.(int)$only_actif;
		$limit='';
		if (!empty($limit_stop))
			$limit = 'LIMIT '.(int)$limit_start.', '.(int)$limit_stop;
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	cn.*
		FROM `'._DB_PREFIX_.self::$table_static.'` cn
		LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (cn.`news` = n.`id_prestablog_news`)
		WHERE 1=1
		'.$multiboutique_filtre.'
		'.$actif.'
		ORDER BY cn.`date` DESC
		'.$limit);
		
		return $Liste;
	}
	
	static public function getNewsFromComment($id_comment) {
		$Row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT	c.`news`
		FROM `'._DB_PREFIX_.self::$table_static.'` c
		WHERE c.`'.self::$identifier_static.'`='.(int)$id_comment);
		
		return $Row["news"];
	}
	
	static public function getListeNonLu($only_news = NULL)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;
		$news="";
		if ($only_news)
			$news = 'AND c.`news` = '.(int)$only_news;
		
		// if (empty($id_lang))
		// 	$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	c.*
		FROM `'._DB_PREFIX_.self::$table_static.'` c
		LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (c.`news` = n.`id_prestablog_news`)
		WHERE c.`actif` = -1
		'.$multiboutique_filtre.'
		'.$news.'
		ORDER BY c.`date` DESC');
		
		return $Liste;
	}
	
	static public function getListeDisabled($only_news = NULL)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;
		$news="";
		if ($only_news)
			$news = 'AND c.`news` = '.(int)$only_news;
		
		// if (empty($id_lang))
		// 	$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	c.*
		FROM `'._DB_PREFIX_.self::$table_static.'` c
		LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (c.`news` = n.`id_prestablog_news`)
		WHERE c.`actif` = 0
		'.$multiboutique_filtre.'
		'.$news.'
		ORDER BY c.`date` DESC');
		
		return $Liste;
	}
	
	static public function insertComment(
										$news,
										$date,
										$name,
										$url,
										$comment,
										$actif=-1
										)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			INSERT INTO `'._DB_PREFIX_.self::$table_static.'` 
				(
					`news`,
					`date`,
					`name`,
					`url`,
					`comment`,
					`actif`
				)
			VALUES 
				(
					'.(int)$news.', 
					\''.pSQL($date).'\', 
					\''.pSQL($name).'\', 
					\''.pSQL($url).'\', 
					\''.pSQL($comment).'\', 
					'.(int)$actif.'
				)');
	}
	
	static public function listeCommentAbo($news=null)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND n.`id_shop` = '.(int)$context->shop->id;
		
		$where_news='';
		
		if(isset($news))
			$where_news='AND a.`news` = '.(int)$news;
			
		$Liste = Db::getInstance()->ExecuteS('
				SELECT a.`id_customer`
				FROM `'._DB_PREFIX_.self::$table_static.'_abo` a
				LEFT JOIN `'._DB_PREFIX_.'prestablog_news` n ON (a.`news` = n.`id_prestablog_news`)
				WHERE 1=1
				'.$multiboutique_filtre.'
				'.$where_news);
				
		$Liste2=array();
		if(sizeof($Liste)) {
			foreach($Liste As $Value) {
				$Liste2[] = $Value["id_customer"];
			}
		}
		return $Liste2;
	}
	
	static public function listeCommentMailAbo($news=null)
	{
		$where_news='';
		
		if(isset($news))
			$where_news='WHERE	A.`news` = '.(int)$news;
		
		$Liste = Db::getInstance()->ExecuteS('
			SELECT	DISTINCT A.`id_customer`, C.`email`
			FROM `'._DB_PREFIX_.self::$table_static.'_abo` AS A
			LEFT JOIN `'._DB_PREFIX_.'customer` AS C
				ON (A.`id_customer` = C.`id_customer`)
			'.$where_news);
				
		$Liste2=array();
		if(sizeof($Liste)) {
			foreach($Liste As $Value) {
				$Liste2[$Value["id_customer"]] = $Value["email"];
			}
		}
		return $Liste2;
	}
	
	static public function insertCommentAbo(
										$news,
										$id_customer
										)
	{
		$Abo = Db::getInstance()->ExecuteS('
				SELECT	*
				FROM `'._DB_PREFIX_.self::$table_static.'_abo`
				WHERE	`news` = '.(int)$news.'
					AND	`id_customer` = '.(int)$id_customer);
		
		if(!sizeof($Abo))
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			INSERT INTO `'._DB_PREFIX_.self::$table_static.'_abo` (`news`,`id_customer`)
			VALUES ('.(int)$news.', '.(int)$id_customer.')');
	}
	
	static public function deleteCommentAbo(
										$news,
										$id_customer
										)
	{
		return Db::getInstance()->Execute('
				DELETE FROM `'._DB_PREFIX_.self::$table_static.'_abo`
				WHERE	`news` = '.(int)$news.'
					AND	`id_customer` = '.(int)$id_customer);
	}
	
	public function changeEtat($Field, $force_value) {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
				UPDATE `'._DB_PREFIX_.$this->table.'` SET `'.$Field.'`='.(int)$force_value.'
				WHERE `'.$this->identifier.'`='.(int)($this->id))
				)
				return false;
		return true;
	}
}

<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class	AntiSpamClass extends ObjectModel
{
	public		$id;
	public 		$id_shop = 1;
	public		$question;
	public		$reply;
	public		$checksum;
	public		$actif = 1;
	
	protected 	$table = 'prestablog_antispam';
	protected 	$identifier = 'id_prestablog_antispam';
	
	protected static	$table_static = 'prestablog_antispam';
	protected static	$identifier_static = 'id_prestablog_antispam';
	
	public static $definition = array(
		'table' => 'prestablog_antispam',
		'primary' => 'id_prestablog_antispam',
		'multilang' => true,
		'fields' => array(
			'id_shop' =>		array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'actif' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			
			// Lang fields
			'question' =>		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			'reply' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 255),
			'checksum' =>		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 32),
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
				foreach ($this->fieldsValidateLang AS $field => $validation) {
					if (Tools::getValue($field.'_'.(int)($language['id_lang'])))
						$this->{$field}[(int)($language['id_lang'])] = Tools::getValue($field.'_'.(int)($language['id_lang']));
				}
		}
	}
	
	public function registerTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'` (
		`'.$this->identifier.'` int(10) unsigned NOT NULL auto_increment,
		`id_shop` int(10) unsigned NOT NULL,
		`actif` tinyint(1) NOT NULL DEFAULT \'1\',
		PRIMARY KEY (`'.$this->identifier.'`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'_lang` (
		`'.$this->identifier.'` int(10) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`question` varchar(255) NOT NULL,
		`reply` varchar(255) NOT NULL,
		`checksum` varchar(32) NOT NULL,
		PRIMARY KEY (`'.$this->identifier.'`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		return true;
	}
	
	public function deleteTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table))
			return false;
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table.'_lang`'))
			return false;
			
		return true;
	}
	
	static public function getListe($id_lang = NULL, $only_actif = 0)
	{
		$context = Context::getContext();
		$multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;

		$actif="";
		if ($only_actif)
			$actif = 'AND c.`actif` = 1';
			
		if (empty($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	c.*, cl.*
		FROM `'._DB_PREFIX_.self::$table_static.'` c
		JOIN `'._DB_PREFIX_.self::$table_static.'_lang` cl ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
		WHERE cl.id_lang = '.(int)($id_lang).'
		'.$multiboutique_filtre.'
		'.$actif);
		
		return $Liste;
	}
	
	static public function getAntiSpamByChecksum($checksum) {
		$context = Context::getContext();
		$multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
				SELECT	c.*, cl.*
				FROM `'._DB_PREFIX_.self::$table_static.'` c
				JOIN `'._DB_PREFIX_.self::$table_static.'_lang` cl ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
				WHERE cl.checksum = \''.pSQL(Trim($checksum)).'\'
				'.$multiboutique_filtre.'
				;');
	}
	
	public function changeEtat($Field) {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
			UPDATE `'._DB_PREFIX_.$this->table.'` SET `'.$Field.'`=CASE `'.$Field.'` WHEN 1 THEN 0 WHEN 0 THEN 1 END 
			WHERE `'.$this->identifier.'`='.(int)($this->id))
			)
			return false;
		return true;
	}
	
	public function reloadChecksum() {
		$Liste = Array();
		$Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.self::$table_static.'_lang`');
		
		foreach($Liste As $AntiSpam)
			Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
				UPDATE `'._DB_PREFIX_.$this->table.'_lang`
					SET `checksum` = \''.md5((int)$AntiSpam[$this->identifier].(int)$AntiSpam["id_lang"]._COOKIE_KEY_.$AntiSpam["question"]).'\'
				WHERE `'.$this->identifier.'`='.(int)$AntiSpam[$this->identifier].'
					AND `id_lang`='.(int)$AntiSpam["id_lang"]);
	}
}

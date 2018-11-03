<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class CorrespondancesCategoriesClass extends ObjectModel
{
	public		$id;
	public		$categorie;
	public		$news;
	
	protected 	$table = 'prestablog_correspondancecategorie';
	protected 	$identifier = 'id_prestablog_correspondancecategorie';
	
	protected static	$table_static = 'prestablog_correspondancecategorie';
	protected static	$identifier_static = 'id_prestablog_correspondancecategorie';
	
	static public function getCategoriesListe($news) {
		$Return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	`categorie`
		FROM `'._DB_PREFIX_.self::$table_static.'`
		WHERE `news` = '.(int)($news));
		
		$Return2 = array();
		foreach($Return1 As $Key => $Value) {
			$Return2[] = $Value["categorie"];
		}
		return $Return2;
	}
	
	static public function getCategoriesListeName($news, $lang, $only_actif = 0) {
		$actif="";
		if ($only_actif)
			$actif = 'AND c.`actif` = 1';
			
		$Return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT	cl.`title`, cl.`link_rewrite`, cc.`categorie`
		FROM `'._DB_PREFIX_.self::$table_static.'` as cc
		LEFT JOIN `'._DB_PREFIX_.'prestablog_categorie` as c
			ON (cc.`categorie` = c.`id_prestablog_categorie`)
		LEFT JOIN `'._DB_PREFIX_.'prestablog_categorie_lang` as cl
			ON (cc.`categorie` = cl.`id_prestablog_categorie`)
		WHERE cc.`news` = '.(int)$news.'
			AND cl.`id_lang` = '.(int)$lang.'
			'.$actif.'
		ORDER BY cl.`title`');
		
		$Return2 = array();
		foreach($Return1 As $Key => $Value) {
			$Return2[$Value["categorie"]]["title"] = $Value["title"];
			$Return2[$Value["categorie"]]["link_rewrite"] = ($Value["link_rewrite"]!=""?$Value["link_rewrite"]:$Value["title"]);
		}
		return $Return2;
	}
	
	static public function delAllCategoriesNews($news) {
		Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.self::$table_static.'` WHERE `news`='.(int)$news);
	}	

	static public function delAllCorrespondanceNewsAfterDelCat($cat) {
		Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DELETE FROM `'._DB_PREFIX_.self::$table_static.'` WHERE `categorie`='.(int)$cat);
	}
	
	static public function updateCategoriesNews($categories, $news) {
		if(sizeof($categories)) {
			foreach($categories As $Key => $Value) {
				Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
					INSERT INTO `'._DB_PREFIX_.self::$table_static.'` 
						(`categorie`, `news`) 
					VALUES ('.(int)$Value.', '.(int)$news.')');
			}
		}
	}
	
	public function registerTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		CREATE TABLE `'._DB_PREFIX_.$this->table.'` (
		`'.$this->identifier.'` int(10) unsigned NOT NULL auto_increment,
		`categorie` int(10) unsigned NOT NULL,
		`news` int(10) unsigned NOT NULL,
		PRIMARY KEY (`'.$this->identifier.'`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;
		
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
		INSERT INTO `'._DB_PREFIX_.$this->table.'` 
			(`'.$this->identifier.'`, `categorie`, `news`) 
		VALUES
			(1, 2, 1),
			(2, 3, 2),
			(3, 4, 3),
			(4, 1, 3)'
			))
			return false;
			
		return true;
	}
	
	public function deleteTablesBdd() {
		if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('DROP TABLE `'._DB_PREFIX_.$this->table))
			return false;
		
		return true;
	}
}

<?php

/**
 *
 * Module FAQ for PrestaShop
 * Developed by Rafael Correia and ameliorate by Joel Gaujard (http://www.joelgaujard.info)
 * Hosted on SourceForge : http://sourceforge.net/projects/modprestafaq/
 *
 */

class Faq extends Module
{

	/* @var boolean error */
	protected $error = false;

	function __construct()
	{
		$this->name = 'faq';
		$this->tab = 'content_management';
		$this->author = 'Rafael  & Joël';
		$this->version = '1.4.3';

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('F.A.Q.');
		$this->description = $this->l('Adds this module to have a faq in your site');
		$this->confirmUninstall = $this->l('All questions and answers would be deleted. Do you really want to uninstall this module ?');
	}

	public function install()
	{
		if (!parent::install()
				OR !$this->_installDB()
				OR !$this->registerHook('leftColumn')
				OR !$this->registerHook('header')
			)
			return false;
		return true;
	}
	private function _installDB()
	{
		$query = '
		CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'faq (
			`id_faq` int(10) NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(`id_faq`)
		) ENGINE=MyISAM default CHARSET=utf8';
		if (!Db::getInstance()->Execute($query))
			return false;
		$query = '
		CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'faq_lang (
			`id_faq` int(10) NOT NULL AUTO_INCREMENT,
			`id_lang` int(10) NOT NULL,
			`question` varchar(128) NOT NULL,
			`answer` text NOT NULL,
			PRIMARY KEY(`id_faq`,`id_lang`)
		) ENGINE=MyISAM default CHARSET=utf8';
		if (!Db::getInstance()->Execute($query))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall()
				OR !$this->_uninstallDB()
			)
			return false;
		return true;
	}
	private function _uninstallDB()
	{
		$query1 = 'DROP TABLE '._DB_PREFIX_.'faq';
		$query2 = 'DROP TABLE '._DB_PREFIX_.'faq_lang';
		if (Db::getInstance()->Execute($query2))
			return Db::getInstance()->Execute($query1);
		return false;
	}


	function getFaq($id_lang = NULL)
	{
		$result = array();
		/* Get id */
		if (!$faqs = Db::getInstance()->ExecuteS('SELECT `id_faq` FROM '._DB_PREFIX_.'faq'))
			return false;
		$i = 0;
		foreach ($faqs AS $faq)
		{
			$result[$i]['id'] = $faq['id_faq'];
			/* Get multilingual text */
			$sql = 'SELECT `id_lang`, `question`, `answer`
				FROM '._DB_PREFIX_.'faq_lang
				WHERE `id_faq`='.intval($faq['id_faq']);
			if (isset($id_lang) AND is_numeric($id_lang) AND intval($id_lang) > 0)
				$sql .= ' AND `id_lang` = '.intval($id_lang);
			if (!$texts = Db::getInstance()->ExecuteS($sql))
				return false;
			foreach ($texts AS $text)
			{
				$result[$i]['question_'.$text['id_lang']] = $text['question'];
				$result[$i]['answer_'.$text['id_lang']] = $text['answer'];
			}
			$i++;
		}
		return $result;
	}

	function addFaq()
	{
		/* Url registration */
		if (!Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'faq VALUES ()') OR !$lastId = mysql_insert_id())
			return false;
		/* Multilingual text */
		$languages = Language::getLanguages();
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		if (!$languages)
			return false;
		foreach ($languages AS $language)
		{
			$question = Tools::getValue('question_'.$language['id_lang']) ? Tools::getValue('question_'.$language['id_lang']) : Tools::getValue('question_'.$defaultLanguage);
			$answer = Tools::getValue('answer_'.$language['id_lang']) ? Tools::getValue('answer_'.$language['id_lang']) : Tools::getValue('answer_'.$defaultLanguage);
			$query = 'INSERT INTO '._DB_PREFIX_.'faq_lang
				VALUES ('.$lastId.', '.$language['id_lang'].', \''.addslashes($question).'\' , \''.addslashes($answer).'\')';
			if (!Db::getInstance()->Execute($query))
				return false;
		}
		return true;
	}

	function updateFaq()
	{
		/* Multilingual texts */
		$languages = Language::getLanguages();
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		if (!$languages)
			return false;
		foreach ($languages AS $language)
		{
			$question = Tools::getValue('question_'.$language['id_lang']) ? Tools::getValue('question_'.$language['id_lang']) : Tools::getValue('question_'.$defaultLanguage);
			$answer = Tools::getValue('answer_'.$language['id_lang']) ? Tools::getValue('answer_'.$language['id_lang']) : Tools::getValue('answer_'.$defaultLanguage);
			$id_faq = intval(Tools::getValue('id'));
			$query = 'UPDATE '._DB_PREFIX_.'faq_lang
				SET `question`=\''.addslashes($question).'\'
					, `answer`=\''.addslashes($answer).'\'
				WHERE `id_faq`='.$id_faq.'
					AND `id_lang`='.intval($language['id_lang']);
			if (!Db::getInstance()->Execute($query))
				return false;
		}
		return true;
	}

	function deleteFaq()
	{
		$id_faq = intval(Tools::getValue('id'));
		if (Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'faq WHERE `id_faq`='.$id_faq))
			return Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'faq_lang WHERE `id_faq`='.$id_faq);
		return false;
	}


	function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>
			<script type="text/javascript" src="'.$this->_path.'faq.js"></script>';

		$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');

		/* Add a FAQ */
		if (Tools::isSubmit('submitFaqAdd'))
		{
			if (!Tools::getValue('question_'.$defaultLanguage))
				$this->_html .= $this->displayError($this->l('You must fill a question.'));
			else
			{
				if ($this->addFaq())
					$this->_html .= $this->displayConfirmation($this->l('The question has been added successfully'));
				else
					$this->_html .= $this->displayError($this->l('An error occured during question creation'));
			}
		}
		/* Update a FAQ */
		elseif (Tools::isSubmit('submitFaqUpdate'))
		{
			if (!Tools::getValue('question_'.$defaultLanguage))
				$this->_html .= $this->displayError($this->l('You must fill a question.'));
			else
			{
				if (!Tools::getValue('id') OR !is_numeric(Tools::getValue('id')) OR !$this->updateFaq())
					$this->_html .= $this->displayError($this->l('An error occured during faq updating'));
				else
					$this->_html .= $this->displayConfirmation($this->l('The question has been updated successfully'));
			}
		}
		/* Delete a faq*/
		elseif (Tools::getValue('id'))
		{
			if (!is_numeric(Tools::getValue('id')) OR !$this->deleteFaq())
				$this->_html .= $this->displayError($this->l('An error occurred during faq deletion'));
			else
				$this->_html .= $this->displayConfirmation($this->l('The question has been deleted successfully'));
		}

		$this->_list();
		$this->_displayForm();
		$this->_html .= $this->_displayCredits();

		return $this->_html;
	}

	private function _displayForm()
	{
		global $cookie;
		$iso = Language::getIsoById(intval($cookie->id_lang));
		/* Language */
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$divLangName = 'question¤aanswer';

		/* TinyMCE Script */
		$iso = Language::getIsoById((int)($cookie->id_lang));
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		$this->_html .=  '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>';

		$this->_html .= '
		<script type="text/javascript">
			id_language = Number('.$defaultLanguage.');
		</script>
		<fieldset class="space">
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Question').'</legend>
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'">
				<label>'.$this->l('Question :').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
					<div id="question_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input size="50" type="text" name="question_'.$language['id_lang'].'" id="questionInput_'.$language['id_lang'].'" value="'.(($this->error AND isset($_POST['question_'.$language['id_lang']])) ? $_POST['question_'.$language['id_lang']] : '').'" /><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'question', true);
		/* Answer, with TinyMCE */
		$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Answer :').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
				<div id="aanswer_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">
						<textarea class="rte" cols="80" rows="30" id="answerInput_'.$language['id_lang'].'" name="answer_'.$language['id_lang'].'">'.htmlentities(stripslashes((($this->error AND isset($_POST['answer_'.$language['id_lang']])) ? $_POST['answer_'.$language['id_lang']] : '')), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>';
		$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'aanswer', true);
		$this->_html .= '
					</div><div class="clear space">&nbsp;</div>
				<div class="margin-form">
					<input type="hidden" name="id" id="id" value="'.($this->error AND isset($_POST['id']) ? $_POST['id'] : '').'" />
					<input type="submit" class="button" name="submitFaqAdd" value="'.$this->l('Insert').'" id="submitFaqAdd" />
					<input type="submit" class="button disable" name="submitFaqUpdate" value="'.$this->l('Edit this faq').'" disabled="disbaled" id="submitFaqUpdate" />
				</div>
			</form>
		</fieldset>';
	}

	private function _list()
	{
		$faqs = $this->getFaq();

		global $currentIndex, $cookie, $adminObj;
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		if ($faqs)
		{
			$this->_html .= '
			<script type="text/javascript">
				var currentUrl = \''.$currentIndex.'&configure='.$this->name.'\';
				var token=\''.$adminObj->token.'\';
				var faqs = new Array();
			';
			$aaa='';
			foreach ($faqs AS $faq)
			{
				$aaa .= 'faqs['.$faq['id'].'] = new Array(';
				$i=0;
				foreach ($languages AS $language)
				{
					if ($i>0)
						$aaa .= ",";
					$aaa .= $language['id_lang'];
					$question = isset($faq['question_'.$language['id_lang']]) ? $faq['question_'.$language['id_lang']] : '';
					$aaa .= ',\''.addslashes($question).'\'';
					$answer = isset($faq['answer_'.$language['id_lang']]) ? $faq['answer_'.$language['id_lang']] : '';
					$aaa .= ',\''.addslashes(str_replace(array("\r\n", "\r", "\n"), "", $answer)).'\'';
					$i++;
				}
				$aaa .= ');';
			}
			$aaa .= '</script>';
			$this->_html .= $aaa;
		}
		if (!$faqs)
			$this->_html .= '
			<p class="warning">'.$this->l('There are no questions yet').'</p>';
		else
		{
			$this->_html .= '
			<h3 class="blue space">'.$this->l('faq list').'</h3>
			<table class="table">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Question').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>';
			foreach ($faqs AS $faq)
			{
				$question = isset($faq['question_'.$cookie->id_lang]) ? $faq['question_'.$cookie->id_lang] : $faq['question_'.$defaultLanguage];
				$this->_html .= '
				<tr>
					<td>'.$faq['id'].'</td>
					<td>'.$question.'</td>
					<td>
						<img src="'._PS_ADMIN_IMG_.'edit.gif" alt="" onclick="faqEdition('.$faq['id'].')" style="cursor: pointer" />
						<img src="'._PS_ADMIN_IMG_.'delete.gif" alt="" onclick="faqDeletion('.$faq['id'].')" style="cursor: pointer" />
					</td>
				</tr>';
			}
			$this->_html .= '
			</table>
			<input type="hidden" id="languageFirst" value="'.$languages[0]['id_lang'].'" />
			<input type="hidden" id="languageNb" value="'.sizeof($languages).'" />';
		}
	}

	private function _displayCredits()
	{
		$output = '
		<br class="clear" /><br/>
		<form action="#" method="post" style="width: 95%;">
			<fieldset class="widthfull">
				<legend>'.$this->l('Credits').'</legend>
				<p>
					'.$this->l('This module is hosted on').'
					<a href="https://sourceforge.net/projects/modprestafaq/"><b>source forge</b> - https://sourceforge.net/projects/modprestafaq/</a>.
				</p>
				<p>
					<a href="http://www.prestashop.com/forums/viewthread/33081/" style="text-decoration: underline;">
						'.$this->l('Please send feedback, bugs or other stuff on related thread in PrestaShop forum.').'
					</a>
				</p>
				'.$this->_displayTranslators().'
				<p>
					'.$this->l('If you translate this module in your language, please upload your language file on PrestaShop forum. Many thanks in anticipation!').'
				</p>
			</fieldset>
		</form>';
		return $output;
	}

	private function _displayTranslators()
	{
		$translators = array(
			"pt" => array(
				"fullname" => "Rafael Correia",
				"url" => "http://www.idcoded.com/"
			)
			,"fr" => array(
				"fullname" => "Joël Gaujard",
				"url" => "http://www.joelgaujard.info"
			)
			,"id" => array(
				"fullname" => "Zaid",
				"url" => "http://www.pusat-herbal.com"
			)
			,"de" => array(
				"fullname" => "Michael Bangiev",
				"url" => "http://www.silbersaiten.de/prestashop-e-commerce.html"
			)
			,"ru" => array(
				"fullname" => "newtc",
				"url" => "http://www.prestashop.com/forums/member/45153/newtc/"
			)
			,"nl" => array(
				"fullname" => "Rob Vermelis",
				"url" => "mailto:rob@beauty4all.eu"
			)
			,"es" => array(
				"fullname" => "Michel Martins",
				"url" => "mailto:edmidimar@gmail.com"
			)
			,"se" => array(
				"fullname" => "Hjalmarsson, Mats",
				"url" => "http://mohsart.se"
			)
			,"it" => array(
				"fullname" => "Di Giuseppe Luigi",
				"url" => "http://www.libridipsicologia.it/"
			)
			,"sk" => array(
				"fullname" => "Mojš Ladislav",
				"url" => "http://mhl.sk/"
			)
			,"dk" => array(
				"fullname" => "Elund Christensen",
				"url" => "http://prospeed.dk/"
			)
			,"pl" => array(
				"fullname" => "Jakub Gzyl",
				"url" => "mailto:jakub6@gmail.com"
			)
			,"tr" => array(
				"fullname" => "Caglar Utku Guler",
				"url" => "mailto:caglarutkuguler@gmail.com"
			)
			,"gr" => array(
				"fullname" => "Vitoratos Dimitrios",
				"url" => "http://www.greekairsoft.gr/"
			)
			,"ro" => array(
				"fullname" => "Robertino Vasilescu",
				"url" => "http://www.caprice-shop.ro/"
			)
			,"hu" => array(
				"fullname" => "Mojš Ladislav",
				"url" => "http://lightproject.eu"
			)
			,"ca" => array(
				"flagUrl" => 'http://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/Flag_of_Catalonia.svg/20px-Flag_of_Catalonia.svg.png',
				"fullname" => "francesc",
				"url" => "http://www.prestashop.com/forums/user/20750-francesc/"
			)
			,"si" => array(
				"fullname" => "volkmartin",
				"url" => "http://www.prestashop.com/forums/user/131107-volkmartin/"
			)
			,"lt" => array(
				"fullname" => "dadydream",
				"url" => "http://svajukas.com/"
			)
			,"cs" => array(
				"fullname" => "Robert Klíma",
				"url" => "http://www.mlsounkov.cz"
			)
			,"ua" => array(
				"fullname" => "asenik",
				"url" => "http://www.prestashop.com/forums/user/243357-asenik/"
			)
		);
		if (empty($translators) OR count($translators) < 1)
			return '';
		$output = '
		<p>
			<h3>'.$this->l('Traductors list!').' ('.count($translators).')</h3>
			<ul>';
		foreach ($translators AS $lang_iso => $translator)
		{
			$flagUrl = 'http://www.google.com/images/flags/'.$lang_iso.'_flag.png';
			if (isset($translator['flagUrl']) AND strval($translator['flagUrl']) != '')
				$flagUrl = $translator['flagUrl'];
			$output .= '
			<li style="line-height: 24px;">
				<img width="20" height="13" src="'.$flagUrl.'" alt="'.$lang_iso.'" title="'.$lang_iso.'" style="vertical-align: baseline;" />
				<a target="_blank" href="'.$translator["url"].'">
					<b>'.$translator["fullname"].'</b> - '.preg_replace('/^mailto:/', '',$translator["url"]).'
				</a>';
			$output .= '</li>';
		}
		$output .= '</ul>
		</p>';
		return $output;
	}


	public function hookHeader($params)
	{
		return $this->display(__FILE__, $this->name.'-header.tpl');
	}

	public function hookLeftColumn($params)
	{
		return $this->display(__FILE__, $this->name.'-column.tpl');
	}
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookTop($params)
	{
		global $cookie, $smarty;
		$id_lang = intval($cookie->id_lang);
		$faqs = $this->getFaq($id_lang);
		foreach ($faqs AS $key => $faq)
			$result[$key] = $faq['question_'.$id_lang];
		$smarty->assign('faqs', $result);
		return $this->display(__FILE__, $this->name.'-top.tpl');
	}
	public function hookFooter($params)
	{
		return $this->display(__FILE__, $this->name.'-footer.tpl');
	}

}

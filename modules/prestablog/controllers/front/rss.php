<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class PrestaBlogRssModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	// public $display_column_left = true;
	// public $display_column_right = true;
	public $display_header = false;
	public $display_footer = false;
	
	public function display() {
		include_once(_PS_MODULE_DIR_.'prestablog/prestablog.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/news.class.php');
		include_once(_PS_MODULE_DIR_.'prestablog/class/categories.class.php');

		if(Tools::getValue("rss") && !CategoriesClass::IsCategorieValide((int)Tools::getValue("rss"))) {
			Tools::redirect('index.php?controller=404');
		}
		else
			header("Content-type: application/xml; charset=utf-8");

		$News = NewsClass::getListe(
										(int)$this->context->cookie->id_lang, 
										1, // actif only
										0, // homeslide
										"", 
										NULL, // limit start
										NULL, // limit stop
										'n.`date`', 
										'desc',
										NULL, // date début
										Date('Y-m-d H:i:s'), // date fin
										(Tools::getValue("rss") ? (int)Tools::getValue("rss") : NULL),
										1,
										(int)Configuration::get('prestablog_rss_title_length'),
										(int)Configuration::get('prestablog_rss_intro_length')
									);
		
		$PrestaBlog = New PrestaBlog();
		$PrestaBlog->InitLangueModule((int)$this->context->cookie->id_lang);

		echo '<rss version="2.0">
		<channel>
			<title>'.$PrestaBlog->RssLangue["channel_title"].'</title>
			<pubDate>'.date("r").'</pubDate>
			<link>'.Tools::getShopDomainSsl(true).__PS_BASE_URI__.'</link>'.(Tools::getValue("rss") ? '
			<category>'.CategoriesClass::getCategoriesName((int)$this->context->cookie->id_lang, (int)Tools::getValue("rss")).'</category>' : '').'
			<image>
				<url>'.Tools::getShopDomainSsl(true).__PS_BASE_URI__.'img/logo.jpg</url>
				<title>'.$PrestaBlog->RssLangue["channel_title"].'</title>
				<link>'.Tools::getShopDomainSsl(true).__PS_BASE_URI__.'</link>
			</image>';

		if(sizeof($News)) {
			foreach($News As $NewsItem) {
				echo '
			<item>
				<title>'.$NewsItem["title"].'</title> 
				<pubDate>'.date("r", strtotime($NewsItem["date"])).'</pubDate>';
				if(sizeof($NewsItem["categories"])) {
					foreach($NewsItem["categories"] As $KeyCat => $ValCat) {
					echo '
				<category>'.$ValCat.'</category>';
					}
				}
				
				echo'
				<link>'.PrestaBlog::prestablog_url(
					array(
							"id"		=> $NewsItem["id_prestablog_news"],
							"seo"		=> $NewsItem["link_rewrite"],
							"titre"		=> $NewsItem["title"]
						)
		).'</link> 
				<description>'.nl2br($NewsItem["paragraph_crop"]).'</description> 
			</item>';
			}
		}
		echo'
		</channel>
		</rss>';
	}
}

?>

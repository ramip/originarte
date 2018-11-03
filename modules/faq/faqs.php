<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/../../header.php');

$module = 'faq';

/* Get datas */
$sql = '
  SELECT f.`id_faq` AS id, fl.`question` AS question, fl.`answer` AS answer
  FROM `'._DB_PREFIX_.'faq` f
    JOIN `'._DB_PREFIX_.'faq_lang` fl ON (f.`id_faq` = fl.`id_faq` AND fl.`id_lang` = '.intval($cookie->id_lang).')
  WHERE fl.`question` <> \'\'';
$faqs = Db::getInstance()->ExecuteS($sql);

$smarty->assign(array(
  'module_dir' => _MODULE_DIR_.$module.'/',
  'faqs' => $faqs
));

$template = 'faq.tpl';
// display overload theme template if exists
if (is_file(_PS_THEME_DIR_.'modules/'.$module.'/'.$template))
  $smarty->display(_PS_THEME_DIR_.'modules/'.$module.'/'.$template);
else
  $smarty->display(_PS_MODULE_DIR_.$module.'/'.$template);

include(dirname(__FILE__).'/../../footer.php');

?>
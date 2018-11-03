<?php
/**
 * @author HDClic
 * @copyright permanent www.hdclic.com
 * @version Release: $Revision: 1.5 / 1.6 $
 */

class CategoriesClass extends ObjectModel
{
   public      $id;
   public      $id_shop = 1;
   public      $title;
   public      $meta_title;
   public      $meta_description;
   public      $meta_keywords;
   public      $link_rewrite;
   public      $description;
   public      $actif = 1;
   public      $parent;
   public      $image_presente = false;
   
   protected   $table = 'prestablog_categorie';
   protected   $identifier = 'id_prestablog_categorie';
   
   protected static  $table_static = 'prestablog_categorie';
   protected static  $identifier_static = 'id_prestablog_categorie';
   
   public static $definition = array(
      'table' => 'prestablog_categorie',
      'primary' => 'id_prestablog_categorie',
      'multilang' => true,
      'fields' => array(
         'id_shop' =>      array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
         'actif' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
         'parent' =>       array('type' => self::TYPE_INT,  'validate' => 'isUnsignedId'),
         
         // Lang fields
         'title' =>        array('type' => self::TYPE_STRING,  'lang' => true, 'validate' => 'isString', 'size' => 255),
         'meta_title' =>   array('type' => self::TYPE_STRING,  'lang' => true, 'validate' => 'isString', 'size' => 255),
         'meta_description' =>   array('type' => self::TYPE_STRING,  'lang' => true, 'validate' => 'isString', 'size' => 255),
         'meta_keywords' =>      array('type' => self::TYPE_STRING,  'lang' => true, 'validate' => 'isString', 'size' => 255),
         'link_rewrite' =>    array('type' => self::TYPE_STRING,  'lang' => true, 'validate' => 'isLinkRewrite',  'size' => 255),

         'description' =>  array('type' => self::TYPE_HTML,    'lang' => true, 'validate' => 'isString'),
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
      `id_shop` int(10) unsigned NOT NULL,
      `actif` tinyint(1) NOT NULL DEFAULT \'1\',
      `parent` int(10) unsigned NOT NULL,
      PRIMARY KEY (`'.$this->identifier.'`))
      ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
         return false;
      
      if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
      CREATE TABLE `'._DB_PREFIX_.$this->table.'_lang` (
      `'.$this->identifier.'` int(10) unsigned NOT NULL,
      `id_lang` int(10) unsigned NOT NULL,
      `title` varchar(255) NOT NULL,
      `meta_description` text NOT NULL,
      `meta_keywords` text NOT NULL,
      `meta_title` text NOT NULL,
      `link_rewrite` text NOT NULL,
      `description` text,
      PRIMARY KEY (`'.$this->identifier.'`, `id_lang`))
      ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
         return false;

      if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
      INSERT INTO `'._DB_PREFIX_.$this->table.'` 
         (`'.$this->identifier.'`, `id_shop`, `actif`, `parent`) 
      VALUES 
         (1,1,1,0),
         (2,1,1,0),
         (3,1,1,0),
         (4,1,1,1)'
         ))
         return false;
         
      $Langues = Language::getLanguages(true);
      if(sizeof($Langues)) {        
         $title = Array (
            1 => "Hightech",
            2 => "Mode",
            3 => "Bébés",
            4 => "Apple"
         );
                 
         $meta_description = Array (
            1 => "Lorem ipsum dolor sit amet, consectetur adipisicing elit.",
            2 => "Praesent vel aliquet leo. Donec ut dolor quis.",
            3 => "Curabitur venenatis sed est at porta.",
            4 => "Praevel alit leo. Donec ut dolor quis torel id mauris."
         );
         
         $meta_keywords = Array (
            1 => "hightech",
            2 => "mode",
            3 => "bebe",
            4 => "Morbi, gravida, eros eu, dolor, rhoncus, tempor"
         );
         
         $meta_title = Array (
            1 => "Tout sur le HighTech",
            2 => "Tout sur la Mode",
            3 => "Tout sur la puériculture",
            4 => "Tout sur Apple"
         );
         
         $link_rewrite = Array (
            1 => "hightech",
            2 => "mode",
            3 => "bebes",
            4 => "apple"
         );

         $description = Array (
            1 => "<h1>Lorem ipsum dolor sit amet, consectetur adipisicing elit</h1><p>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>",
            2 => "<h1><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span></h1><p><span>Sed eget pretium lectus, sed bibendum augue. In sollicitudin convallis blandit. Curabitur venenatis ut elit quis tempus. Sed eget sem pretium, consequat ante sit amet, accumsan nunc. Vestibulum adipiscing dapibus tortor, eget lacinia neque dapibus auctor. Integer a dui in tellus dignissim dictum eu eu orci. Integer venenatis libero a justo rutrum, eu facilisis libero aliquam. Praesent sit amet elit nunc. Vestibulum aliquam turpis tellus, sed sagittis velit suscipit molestie. Nullam eleifend convallis sodales. Aenean est magna, molestie quis viverra vitae, hendrerit nec dui.</span></p>",
            3 => "<h1>Curabitur venenatis sed est at porta</h1><p>Donec eu tincidunt nibh. Praesent vel aliquet leo. Donec ut dolor quis tortor aliquet tincidunt vel id mauris. Morbi gravida eros eu dolor rhoncus tempor. Praesent vestibulum neque a libero mattis tristique. Proin commodo lorem vestibulum, molestie erat in, condimentum leo.</p>",
            4 => "<h1>Donec eu tincidunt nibh. Praesent vel aliquet leo.</h1><p>Donec ut dolor quis tortor aliquet tincidunt vel id mauris. Morbi gravida eros eu dolor rhoncus tempor. Praesent vestibulum neque a libero mattis tristique. Proin commodo lorem vestibulum, molestie erat in, condimentum leo.</p>"
         );
         
         $sql_values = 'VALUES ';
         for($i=1; $i<=4; $i++) {
            foreach($Langues As $Value) {
               $sql_values.= '
                  (
                     '.$i.', 
                     '.$Value["id_lang"].', 
                     \''.pSQL($title[$i]).'\', 
                     \''.pSQL($meta_description[$i]).'\',
                     \''.pSQL($meta_keywords[$i]).'\',
                     \''.pSQL($meta_title[$i]).'\',
                     \''.pSQL($link_rewrite[$i]).'\',
                     \''.pSQL($description[$i]).'\'
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
                  `meta_description`, 
                  `meta_keywords`, 
                  `meta_title`, 
                  `link_rewrite`, 
                  `description`
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
         
      return true;
   }
   
   static public function isCategoriesExist($id_lang = NULL, $name)
   {
      if (empty($id_lang))
         $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

      $context = Context::getContext();
      $multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;
      
      $Row = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
      SELECT   cl.`'.self::$identifier_static.'`
      FROM `'._DB_PREFIX_.self::$table_static.'_lang` cl
      LEFT JOIN `'._DB_PREFIX_.self::$table_static.'` c ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
      WHERE cl.`title` = \''.pSQL($name).'\'
      '.$multiboutique_filtre.'
      AND cl.`id_lang` = '.(int)($id_lang));
      
      if(sizeof($Row))
         return $Row[self::$identifier_static];
      else
         return false;
   }
   
   static public function getCategoriesName($id_lang = NULL, $id_prestablog_categorie)
   {
      if (empty($id_lang))
         $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
      
      $Row = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
      SELECT   cl.title
      FROM `'._DB_PREFIX_.self::$table_static.'_lang` cl
      WHERE cl.id_lang = '.(int)($id_lang).'
      AND cl.`'.self::$identifier_static.'` = '.(int)$id_prestablog_categorie);
      
      if(sizeof($Row))
         return $Row["title"];
      else
         return false;
   }
   
   static public function getCategoriesMetaTitle($id_lang = NULL, $id_prestablog_categorie)
   {
      if (empty($id_lang))
         $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
      
      $Row = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
      SELECT   cl.meta_title
      FROM `'._DB_PREFIX_.self::$table_static.'_lang` cl
      WHERE cl.id_lang = '.(int)($id_lang).'
      AND cl.`'.self::$identifier_static.'` = '.(int)$id_prestablog_categorie);
      
      if(sizeof($Row))
         return $Row["meta_title"];
      else
         return false;
   }
   
   static public function getListeNoLang($only_actif = 0, $parent = 0)
   {
      $Module = new PrestaBlog();
      $actif="";
      if ($only_actif)
         $actif = 'AND c.`actif` = 1';

      $context = Context::getContext();
      $multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;
      
      $Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
      SELECT   c.*, cl.*
      FROM `'._DB_PREFIX_.self::$table_static.'` c
      JOIN `'._DB_PREFIX_.self::$table_static.'_lang` cl ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
      WHERE c.`'.self::$identifier_static.'` > 0
      AND c.`parent` = '.(int)$parent.'
      '.$multiboutique_filtre.'
      '.$actif);
      
      if(sizeof($Liste)) {
         foreach($Liste As $Key => $Value) {
            if(file_exists($Module->ModulePath.'/themes/'.Configuration::get($Module->name.'_theme').'/up-img/c/'.$Value[self::$identifier_static].'.jpg'))
               $Liste[$Key]["image_presente"] = 1;

            // on récupère les sous-cat
            $Liste[$Key]["children"] = self::getListe($only_actif, (int)$Value["id_prestablog_categorie"]);
         }
      }
      
      return $Liste;
   }

   static public function getNombreNewsDansCat($categorie = 0) {
      $context = Context::getContext();
      $nombre_news = Db::getInstance(_PS_USE_SQL_SLAVE_)->GetRow('
         SELECT COUNT(DISTINCT nl.`id_prestablog_news`) AS `value`
         FROM `'._DB_PREFIX_.'prestablog_news_lang` As nl
         LEFT JOIN `'._DB_PREFIX_.'prestablog_correspondancecategorie` As co
            ON (co.news = nl.id_prestablog_news)
         LEFT JOIN `'._DB_PREFIX_.'prestablog_categorie` As c
            ON (co.categorie = c.id_prestablog_categorie)
         LEFT JOIN `'._DB_PREFIX_.'prestablog_news` As n
            ON (nl.id_prestablog_news = n.id_prestablog_news)
         WHERE n.`actif` = 1 
            AND nl.`id_lang` = '.(int)$context->cookie->id_lang.'
            AND nl.`actif_langue` = 1
            AND TIMESTAMP(n.`date`) <= \''.Date("Y/m/d H:i:s").'\'
            AND   c.`actif` = 1
            AND   c.id_prestablog_categorie = '.(int)$categorie);

      return (int)$nombre_news["value"];
   }

   static public function getNombreNewsRecursifCat($categorie = 0) {
      $news = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
         SELECT   c.*
         FROM `'._DB_PREFIX_.self::$table_static.'` c
         WHERE c.`parent` = '.(int)$categorie);

      $Rcount = (int)self::getNombreNewsDansCat((int)$categorie);

      if(sizeof($news)) {
         foreach ($news as $key => $value) {
            $Rcount = $Rcount + (int)self::getNombreNewsRecursifCat((int)$value["id_prestablog_categorie"]);
         }
      }      
      
      return $Rcount;
   }

   static public function getListeNoArbo($only_actif = 0, $id_lang = 0)
   {
      $Module = new PrestaBlog();
      $actif="";
      if ($only_actif)
         $actif = 'AND c.`actif` = 1';
      $langue="";
      if ($id_lang)
         $langue = 'AND cl.`id_lang` = '.(int)$id_lang;

      $context = Context::getContext();
      $multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;
      
      $Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
      SELECT   c.*, cl.*
      FROM `'._DB_PREFIX_.self::$table_static.'` c
      JOIN `'._DB_PREFIX_.self::$table_static.'_lang` cl ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
      WHERE c.`'.self::$identifier_static.'` > 0
      '.$multiboutique_filtre.'
      '.$actif.'
      '.$langue);
      
      if(sizeof($Liste)) {
         foreach($Liste As $Key => $Value) {
            if(file_exists($Module->ModulePath.'/themes/'.Configuration::get($Module->name.'_theme').'/up-img/c/'.$Value[self::$identifier_static].'.jpg'))
               $Liste[$Key]["image_presente"] = 1;
         }
      }
      
      return $Liste;
   }

   static public function getBreadcrumb($branche) {
      $context = Context::getContext();
      $breadcrumb = "";

      $branche = preg_split('/\./', $branche);

      foreach ($branche as $key => $value) {
         $Categorie = new CategoriesClass((int)$value, (int)$context->cookie->id_lang);
         $breadcrumb .= '&nbsp;>&nbsp;<a href="'.PrestaBlog::prestablog_url(
                                 array(
                                       "c"            => $Categorie->id,
                                       "titre"        => ($Categorie->link_rewrite!=''?$Categorie->link_rewrite:$Categorie->title),
                                    )
                              ).'">'.$Categorie->title.'</a>';
      }

      return ltrim($breadcrumb, '&nbsp;>&nbsp');
   }

   static public function getBranche($idC, $branche='') {
      $context = Context::getContext();
      $Categorie = new CategoriesClass((int)$idC, (int)$context->cookie->id_lang);
      $branche = $Categorie->id.($branche?'.'.$branche:'');

      if((int)$Categorie->parent > 0)
         $branche = self::getBranche((int)$Categorie->parent, $branche);

      return $branche;
   }
   
   static public function getListe($id_lang = NULL, $only_actif = 0, $parent = 0, $branchPrevious=Null)
   {
      $Module = new PrestaBlog();
      $actif="";
      if ($only_actif)
         $actif = 'AND c.`actif` = 1';
         
      if (empty($id_lang))
         $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

      $context = Context::getContext();
      $multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;
      
      $Liste = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
      SELECT   c.*, cl.*,
         LEFT(cl.`title`, '.(int)Configuration::get('prestablog_cat_title_length').') As title
      FROM `'._DB_PREFIX_.self::$table_static.'` c
      JOIN `'._DB_PREFIX_.self::$table_static.'_lang` cl ON (c.`'.self::$identifier_static.'` = cl.`'.self::$identifier_static.'`)
      WHERE cl.`id_lang` = '.(int)($id_lang).'
      AND c.`parent` = '.(int)$parent.'
      '.$multiboutique_filtre.'
      '.$actif.'
      ORDER BY cl.`title`');
      
      if(sizeof($Liste)) {
         foreach($Liste As $Key => $Value) {
            if($branchPrevious)
               $Liste[$Key]["branch"] = $branchPrevious.'.'.(int)$Value["id_prestablog_categorie"];
            else
               $Liste[$Key]["branch"] = (int)$Value["id_prestablog_categorie"];

            $Liste[$Key]["description_crop"] = trim(strip_tags($Value["description"]));

            $Liste[$Key]["nombre_news"] = (int)self::getNombreNewsDansCat((int)$Value["id_prestablog_categorie"]);

            $Liste[$Key]["nombre_news_recursif"] = (int)self::getNombreNewsRecursifCat((int)$Value["id_prestablog_categorie"]);
            
            if(Tools::strlen(trim($Liste[$Key]["description_crop"])) > (int)Configuration::get('prestablog_cat_intro_length')) {
               $Liste[$Key]["description_crop"] = PrestaBlog::cleanCut($Liste[$Key]["description_crop"], (int)Configuration::get('prestablog_cat_intro_length'), ' [...]');
            }

            if(file_exists($Module->ModulePath.'/themes/'.Configuration::get($Module->name.'_theme').'/up-img/c/'.$Value[self::$identifier_static].'.jpg'))
               $Liste[$Key]["image_presente"] = 1;

            // on récupère les sous-cat
            $Liste[$Key]["children"] = self::getListe($id_lang, $only_actif, (int)$Value["id_prestablog_categorie"], $Liste[$Key]["branch"]);
         }
      }
      
      return $Liste;
   }
   
   public function changeEtat($Field) {
      if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
         UPDATE `'._DB_PREFIX_.$this->table.'` SET `'.$Field.'`=CASE `'.$Field.'` WHEN 1 THEN 0 WHEN 0 THEN 1 END 
         WHERE `'.$this->identifier.'`='.(int)($this->id))
         )
         return false;
      return true;
   }
   
   static public function IsCategorieValide($Categorie) {
      $context = Context::getContext();
      $multiboutique_filtre = 'AND c.`id_shop` = '.(int)$context->shop->id;

      $Row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
      SELECT   *
      FROM `'._DB_PREFIX_.self::$table_static.'` c
      WHERE c.`'.self::$identifier_static.'` = '.(int)$Categorie.'
      AND c.`actif`=1
      '.$multiboutique_filtre);
      
      if(sizeof($Row))
         return true;
      else
         return false;
   }

   public function _displaySelectArboCategories($Liste, $parent = 0, $Decalage = 0, $label="Top level", $name_select = "parent", $onChange="", $selected=0, $brancheDisabled=false, $branchPrevious=null) {
      $_html = '';
      if($Decalage==0) {
         $_html .= '<select name="'.$name_select.'" '.($onChange!=""?'onchange="'.$onChange.'"':'').'>';
         $_html .= ' <option value="0" '.((int)$parent==0?'selected':'').' style="font-style:italic;font-weight:bold;">'.$label.'</option>';    
      }

      foreach($Liste As $Key => $Value) {
         if(
                  (int)$Value[self::$identifier_static] == (int)Tools::getValue('idC')
               || (
                        strpos($Value["branch"], $branchPrevious) !== false
                     && strpos($Value["branch"], Tools::getValue('idC').'.') !== false
                  )
               || strpos($Value["branch"], Tools::getValue('idC').'.') !== false
            )
            $brancheDisabled = true;
         else
            $brancheDisabled = false;

         if(!Tools::getValue('idC'))
            $brancheDisabled = false;
         
         $_html .= '<option '.($brancheDisabled?'disabled':'value="'.$Value[self::$identifier_static].'"').' '.((int)$Value[self::$identifier_static]==(int)$parent || (int)$Value[self::$identifier_static]==(int)$selected ?'selected':'').'>';

         for($i=0;$i<=$Decalage;$i++)
            $_html .= '&nbsp;&nbsp;&nbsp;';
         $_html .= $Value["title"].'</option>';
         if(sizeof($Value["children"])) { 
            $_html .= $this->_displaySelectArboCategories($Value["children"], $parent, $Decalage+1, $label, $name_select, $onChange, $selected, $brancheDisabled, $Value["branch"]);
         }
      }

      if($Decalage==0)
         $_html .= '</select>';
      return $_html;
   }
}

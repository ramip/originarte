<?php

/**

 * $ModDesc

 * 

 * @version		$Id: file.php $Revision

 * @package		modules

 * @subpackage	$Subpackage.

 * @copyright	Copyright (C) December 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.

 * @license		GNU General Public License version 2

 */

if (!defined('_CAN_LOAD_FILES_')){

	define('_CAN_LOAD_FILES_',1);

}    

/**

 * loffblike Class

 */	

class LofFBLike extends Module {


	/**

	 * @var LofParams $_params;

	 *

	 * @access private;

	 */

	private $_params = '';	

	public $site_url = '';	

	

	/**

	 * @var array $_postErrors;

	 *

	 * @access private;

	 */

	private $_postErrors = array();		

	

	/**

	 * @var string $__tmpl is stored path of the layout-theme;

	 *

	 * @access private 

	 */	

	

   /**

    * Constructor 

    */

	function __construct(){

		$this->name = 'loffblike';

		parent::__construct();			

		$this->tab = 'LandOfCoder';				

		$this->version = '1.0.0';

		$this->displayName = $this->l('Lof FaceBook Like Box Module');

		$this->description = $this->l('Lof FaceBook Like Box Module');

		if( file_exists( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/params.php' ) && !class_exists("LofParams", false) ){

			if( !defined("LOF_FB_LIKE_LOAD_LIB_PARAMS") ){				

				require( _PS_ROOT_DIR_.'/modules/'.$this->name.'/libs/params.php' );

				define("LOF_FB_LIKE_LOAD_LIB_PARAMS",true);

			}

		}		

		$this->_params = new LofParams( $this->name );		   

	}

  

   /**

    * process installing 

    */

	function install(){		

		if (!parent::install())

			return false;
		if(!$this->registerHook('leftColumn'))
			return false;
		return true;
	}

	

	/*

	 * register hook right comlumn to display slide in right column

	 */

	function hookrightColumn($params){		

		return $this->processHook( $params,"rightColumn");

	}
	

	

	/*

	 * register hook left comlumn to display slide in left column

	 */

	function hookleftColumn($params){		

		return $this->processHook( $params,"leftColumn");

	}

	

	function hooktop($params){		

		return $this->processHook( $params,"top");

	}

	

	function hookfooter($params){		

		return $this->processHook( $params,"footer");

	}

	

	function hookcontenttop($params){ 		

		return $this->processHook( $params,"contenttop");

	}

  	

	function hooklofTop($params){

		return $this->processHook( $params,"lofTop");

	}

		

	function hookHome($params){

		return $this->processHook( $params,"home");

	}

	function hookloffblike1($params){

		return $this->processHook( $params,"loffblike1");

	}

	function hookloffblike2($params){

		return $this->processHook( $params,"loffblike2");

	}

	

	function hookHeader($params) {
	
		$params = $this->_params;
		$theme 			= $params->get( 'module_theme' , 'default');
		$showMode      = $params->get( 'showMode' , 'ticker');
	}

	

    

    function getData( $params ){		



		$tmp 		       	= $params->get( 'module_height', '300' );

		$moduleHeight       =( $tmp=='auto' ) ? 'auto' : (int)$tmp;

		$Height             =( $tmp=='auto' ) ? 'auto' : (int)$tmp;

		$tmp                = $params->get( 'module_width', '182' );

		$Width              =( $tmp=='auto') ? 'auto': (int)$tmp;

		$moduleWidth        =( $tmp=='auto') ? 'auto': (int)$tmp;

		$moduleCenter       =( $tmp=='auto') ? 'auto': ((int)$tmp-200);

        

        $title 			    = $params->get( 'title', 'Lof FaceBook Like Box');

		$bdcolor 			= $params->get( 'bdcolor', '#fff');

		$lofcolor 			= $params->get( 'lofcolor', 'light');

		$numfans 			= $params->get( 'numfans', 6);

		$loffaces 			= $params->get( 'loffaces', 1);

		$lofstream 			= $params->get( 'lofstream', 0);

		$lofheader 			= $params->get( 'lofheader', 1);

		$theme 			    = $params->get( 'module_theme', 'default');

		$loflink            = $params->get( 'loflink' , 'http://www.facebook.com/LeoTheme');

		$bgdark = "";

        $bdcolor = substr($bdcolor, 1);

        

        if($lofcolor == 'dark'){

            $bgdark = "#333333";

        }else{

            $bgdark = "";

        }

		if( $loffaces==1 ){

            $loffaces = true;

		}else{

            $loffaces = false;

		}

        if( $lofstream==1 ){

            $lofstream = true;

		}else{

            $lofstream = false;

		}

        if( $lofheader==1 ){

            $lofheader = true;

		}else{

            $lofheader = false;

		}

		$lofsource	="http://www.facebook.com/plugins/likebox.php?href=".$loflink."&amp;width=".$moduleWidth .

					"&amp;colorscheme=".$lofcolor."&amp;show_faces=".$loffaces .

					"&amp;border_color=%23".$bdcolor."&amp;connections=".$numfans."&amp;stream=".$lofstream."&amp;header=".$lofheader."&amp;height=".$moduleHeight;



		$data ='';

		$data = '<div class="facebookif block orange"><h3 class="title_block">Facebook</h3><div class="block_content"><iframe src="'.$lofsource.'" scrolling="no" frameborder="0" allowTransparency="true" class="FB_SERVER_IFRAME" style="border:none; overflow:hidden; width:'.$moduleWidth.'px; height:'.$moduleHeight.'px;background:'.$bgdark.'"></iframe></div></div>';

		return $data;

	}

    

	/**

    * Proccess module by hook

    * $pparams: param of module

    * $pos: position call

    */

	function processHook($pparams, $pos="home"){

		global $smarty;                  

		//load param

		

		$params = $this->_params;

		$this->site_url = Tools::htmlentitiesutf8('http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);

		// get params

		$tmp 		       	= $params->get( 'module_height', '182' );

		$moduleHeight       =( $tmp=='auto' ) ? 'auto' : (int)$tmp;

		$Height             =( $tmp=='auto' ) ? 'auto' : (int)$tmp;

		$tmp                = $params->get( 'module_width', '300' );

		$Width              =( $tmp=='auto') ? 'auto': (int)$tmp;

		$moduleWidth        =( $tmp=='auto') ? 'auto': (int)$tmp;

		$moduleCenter       =( $tmp=='auto') ? 'auto': ((int)$tmp-200);

        

        

		$title 			    = $params->get( 'title' , 'Lof FaceBook Like Box');

        $numfans 			= $params->get( 'numfans' , 6);

		$bdcolor 			= $params->get( 'bdcolor' , '#f6f6f6');

		$lofcolor 			= $params->get( 'lofcolor' , 'light');

		$loffaces 			= $params->get( 'loffaces' , 1);

		$lofstream 			= $params->get( 'lofstream' , 1);

		$lofheader 			= $params->get( 'lofheader' , 1);

		$theme 			    = $params->get( 'module_theme' , 'default');

		$loflink            = $params->get( 'loflink' , 'http://www.facebook.com/LeoTheme');

		$blockid            = $this->id;

		$prfSlide           = $pos;

		// template asignment variables

		$smarty->assign( array(	

						      'modName'         => $this->name,

                              'loflink'         => $loflink,

							  'bdcolor' 		=> $bdcolor,

							  'lofcolor' 		=> $lofcolor,

							  'loffaces' 		=> $loffaces,

							  'lofstream' 		=> $lofstream,

							  'lofheader' 		=> $lofheader,

							  'moduleWidth'     => $moduleWidth,

							  'lofstream'       => $lofstream,

							  'moduleHeight'	=> $moduleHeight,

							  'params'		    => $params,

							  'site_url'		=> $this->site_url,
							  'title'		    => $title,
							  'numfans'		    => $numfans,
							  'theme'           => $params->get( 'module_theme' , 'default')
						));
		// render for content layout of module	
        $data = $this->getData($params);	
	    return $data;					
	}
   /**

    * Get list of sub folder's name 
    */
	public function getFolderList( $path ) {
		$items = array();
		$handle = opendir($path);
		if (! $handle) {
			return $items;
		}
		while (false !== ($file = readdir($handle))) {
			if (is_dir($path . $file))
				$items[$file] = $file;
		}
		unset($items['.'], $items['..'], $items['.svn']);
		
		return $items;
	}
	
   /**
    * Render processing form && process saving data.
    */	
	public function getContent() {
		$html = "";
		if (Tools::isSubmit('submit')) {
			$this->_postValidation();

			if (!sizeof($this->_postErrors)) {													
		        $definedConfigs = array(
                  'module_theme'  	     => '',	          	                                                              
                  'title'  	             => '',	          	                                                              
                  'loflink'  	         => '',	          	                                                              
                  //
		          'module_width'         => '182',                                       
		          'module_height'        => '300',                                       
		          'bdcolor'              => '',                                       
		          'lofcolor'             => '',                                       
		          'loffaces'             => '',                                       
		          'showMode'             => '',                                       
		          'layout'               => '',                                       
		          'lofstream'            => '',                                       
		          'lofheader'            => '',                                      
		          'numfans'              => '6',                                      
		        );
		        foreach( $definedConfigs as $config => $key ){
		            if(strlen($this->name.'_'.$config)>=32){
		              echo $this->name.'_'.$config;
		            }else{
		              Configuration::updateValue($this->name.'_'.$config, Tools::getValue($config), true);  
		            } 		      		
		    	}
		        $html .= '<div class="conf confirm">'.$this->l('Settings updated successful').'</div>';
			
			} else {
			
				foreach ($this->_postErrors AS $err) {
				
					$html .= '<div class="alert error">'.$err.'</div>';
				}
			}
			// reset current values.
			$this->_params = new LofParams( $this->name );	
		}
			
		return $html.$this->_getFormConfig();
	}
	
	/**
	 * Render Configuration From for user making settings.
	 *
	 * @return context
	 */
	private function _getFormConfig(){		
		$html = '';
		 
	    //$themes=$this->getFolderList( dirname(__FILE__)."/tmpl/" );

	    ob_start();
	    include_once dirname(__FILE__).'/config/loffblike.php'; 
	    $html .= ob_get_contents();
	    ob_end_clean(); 
		return $html;
	}
    
	/**
     * Process vadiation before saving data 
     */
	private function _postValidation() {
	
		if (!Validate::isCleanHtml(Tools::getValue('module_height')))
			$this->_postErrors[] = $this->l('The module height you entered was not allowed, sorry');
		if (!Validate::isCleanHtml(Tools::getValue('module_width')))
			$this->_postErrors[] = $this->l('The module width you entered was not allowed, sorry');                   							
	}
	
   /**
    * Get value of parameter following to its name.
    * 
	* @return string is value of parameter.
	*/
	public function getParamValue($name, $default=''){
		return $this->_params->get( $name, $default );	
	}	  	  		
} 
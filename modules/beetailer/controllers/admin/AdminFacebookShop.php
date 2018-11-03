<?php
class AdminFacebookShopController extends ModuleAdminController {
	public function initContent()
	{
		$this->context->smarty->assign('apiKey', Configuration::get('CONNECTION_TOKEN'));
		$this->context->smarty->assign('storeUrl', _PS_BASE_URL_.__PS_BASE_URI__);
		parent::initContent();
	}
}
?>

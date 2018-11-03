<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

require_once dirname(__FILE__).'/../../inc/om/models/BeezupomLog.php';

class AdminBeezupLogController extends ModuleAdminController
{
    protected function l(
        $string,
        $class = null,
        $addslashes = false,
        $htmlentities = true
    ) {
        if (_PS_VERSION_ >= '1.7') {
            return Context::getContext()->getTranslator()->trans($string);
        } else {
            return parent::l($string, $class, $addslashes, $htmlentities);
        }
    }


    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'beezupom_log';
        $this->className = 'BeezupomLog';
        $this->context = Context::getContext();
        $this->allow_export = true;
        $this->deleted = false;
        $this->filter_modules_list[] = 'beezup';

        $this->fields_list = array(
            'id_beezupom_log' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ),
            'beezup_order_id' => array(
                'title'      => $this->l('BeezUP Order Id'),
                'width'      => 'auto',
                'filter_key' => 'beezup_order_id',

            ),
            'message_type'    => array(
                'title' => $this->l('Message Type'),
                'width' => 'auto',

            ),
            'message'         => array(
                'title' => $this->l('Message'),
                'width' => 'auto',

            ),
            'date'            => array(
                'title' => $this->l('Date'),
                'width' => 'auto',
                'type'  => 'datetime',

            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text'    => $this->l('Delete selected'),
                'confirm' => $this->l(
                    'Would you like to delete the selected items?'
                ),
            ),
        );

        parent::__construct();
    }

    public function postProcess()
    {
        parent::postProcess();
    }


    public function renderList()
    {
        $this->no_link = true;
        $this->list_no_link = true;
        $this->initToolbar();
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function init()
    {
        parent::init();
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();

        return true;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}

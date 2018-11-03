<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

function smarty_filter_groups($tpl_source)
{
    $smarty = Context::getContext()->smarty;

    if (BeezupGlobals::$smartyPrefilterActive && Tools::getIsset('id_product_attribute')) {
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');

        if (Configuration::get('PS_FORCE_SMARTY_2')) {
            $groups = $smarty->get_template_vars('groups');
        } else {
            $groups = $smarty->getTemplateVars('groups');
        }

        if ($groups) {
            $sql
                = 'SELECT `id_attribute` 
            FROM `'._DB_PREFIX_.'product_attribute_combination` 
            WHERE `id_product_attribute` = '.$id_product_attribute;

            $ids = Db::getInstance()->ExecuteS($sql);
            if ($ids && is_array($ids)) {
                $id_list = array();
                foreach ($ids as $id) {
                    if (Tools::getIsset($id['id_attribute'])) {
                        $id_list[] = (int)$id['id_attribute'];
                    }
                }
                foreach ($groups as &$group) {
                    foreach ($group['attributes'] as $id => $attr) {
                        if (in_array($id, $id_list)) {
                            $group['default'] = (int)$id;
                            break;
                        }
                    }
                }
                $smarty->assign('groups', $groups);
                BeezupGlobals::$smartyPrefilterActive = false;
            }
        }
    }

    return $tpl_source;
}

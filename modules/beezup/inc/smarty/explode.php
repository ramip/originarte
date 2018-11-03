<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

function smarty_function_explode($params)
{
    $smarty = Context::getContext()->smarty;
    if (empty($params['str'])) {
        if (method_exists($smarty, "trigger_error")) {
            $smarty->trigger_error("explode: missing str parameter");
        }

        return;
    }
    $str = $params['str'];

    if (empty($params['var'])) {
        if (method_exists($smarty, "trigger_error")) {
            $smarty->trigger_error("explode: missing var parameter");
        }

        return;
    }
    $var = $params['var'];

    if (empty($params['delim'])) {
        if (method_exists($smarty, "trigger_error")) {
            $smarty->trigger_error("explode: missing delim parameter");
        }

        return;
    }
    $delim = $params['delim'];

    $content = explode($delim, $str);

    $smarty->assign($var, $content);
}

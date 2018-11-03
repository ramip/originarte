<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

function smarty_function_array_to_options($params)
{
    $smarty = Context::getContext()->smarty;
    if (!isset($params['content']) || empty($params['content'])) {
        if (method_exists($smarty, "trigger_error")) {
            $smarty->trigger_error("array_to_options: missing content parameter");
        }

        return;
    }
    if (!is_array($params['content'])) {
        if (method_exists($smarty, "trigger_error")) {
            $smarty->trigger_error(
                "array_to_options: content parameter must be an array"
            );
        }

        return;
    }
    $content = $params['content'];

    $selected = isset($params['selected']) ? $params['selected']
        : null;

    $ret = '';

    foreach ($content as &$line) {
        if (is_array($line) && array_key_exists('name', $line)) {
            $ret .= '<option value="'
                .htmlentities(
                    (isset($line['value']) ? $line['value']
                        : $line['name']),
                    ENT_COMPAT,
                    'UTF-8'
                ).'"';

            if (isset($line['title'])) {
                $ret .= ' title="'.htmlentities(
                    $line['title'],
                    ENT_COMPAT,
                    'UTF-8'
                ).'"';
            } else {
                $ret .= ' title="'.htmlentities(
                    $line['name'],
                    ENT_COMPAT,
                    'UTF-8'
                ).'"';
            }

            if ((array_key_exists('value', $line) && $line['value'] == $selected)
                || (!isset($line['value']) && $line['name'] == $selected)
            ) {
                $ret .= ' selected="selected"';
            }

            $ret .= '>'.htmlentities($line['name'], ENT_COMPAT, 'UTF-8')
                ."</option>\r\n";
        } else {
            $ret .= '<option value="'.htmlentities($line, ENT_COMPAT, 'UTF-8')
                .'" title="'.htmlentities(
                    $line,
                    ENT_COMPAT,
                    'UTF-8'
                ).'" '.($selected == $line
                    ? 'selected="selected"' : '').'>'.htmlentities(
                        $line,
                        ENT_COMPAT,
                        'UTF-8'
                    )."</option>\r\n";
        }
    }

    return $ret;
}

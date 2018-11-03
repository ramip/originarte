<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

print '<pre>';

foreach (glob(dirname(__FILE__).'/*.log') as $sFile) {
    print PHP_EOL.$sFile;

    print PHP_EOL.Tools::file_get_contents($sFile);
}

print '</pre>';

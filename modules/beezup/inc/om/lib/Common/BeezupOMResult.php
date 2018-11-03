<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMResult extends BeezupOMDataHandler
{
    public function __call($sMethod, $aArgs)
    {
        throw new BadMethodCallException(
            sprintf(
                'Unimplemented method %s::%s',
                get_class($this),
                $sMethod
            )
        );
    }
}

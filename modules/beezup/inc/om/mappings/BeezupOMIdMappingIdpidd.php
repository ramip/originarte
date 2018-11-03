<?php
/**
 * @author      BeezUP <support@beezup.com>
 * @copyright   2018 BeezUP
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @category    BeezUP
 * @package     beezup
 */

class BeezupOMIdMappingIdpidd extends BeezupOMIdMappingField
{
    public function findAll($sSelector)
    {
        $aReturn = array($this->getNewIdProductAndAttribute($sSelector));
        foreach (array_filter($aReturn) as $nIndex => $aValue) {
            if (!is_array($aValue) || count($aValue) != 2) {
                unset($aReturn[$nIndex]);
                continue;
            }
            list($nIdProduct, $nIdProductAttribute) = $aValue;
            if (!$this->existsProduct($nIdProduct, $nIdProductAttribute)) {
                unset($aReturn[$nIndex]);
            }
        }

        return array_values($aReturn);
    }


    protected function getNewIdProductAndAttribute($sSelector)
    {
        $nIdProduct = 0;
        $nIdProductAttribute = 0;
        $aElement = stristr($sSelector, '_') ? explode('_', $sSelector)
            : array($sSelector, 0);
        if (count($aElement) != 2) {
            return array(0, 0);
        }

        return array_map('intval', $aElement);
    }
}
